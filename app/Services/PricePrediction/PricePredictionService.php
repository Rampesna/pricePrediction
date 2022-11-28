<?php

namespace App\Services\PricePrediction;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ITransformService;
use App\Interfaces\PricePrediction\IPricePredictionService;
use App\Models\Eloquent\Transform;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class PricePredictionService extends BasePricePredictionService implements IPricePredictionService
{
    /**
     * @var $transformService
     */
    private $transformService;

    /**
     * @param ITransformService $transformService
     */
    public function __construct(ITransformService $transformService)
    {
        parent::__construct();
        $this->transformService = $transformService;
    }

    /**
     * @param mixed $brand
     * @param mixed $model
     * @param mixed $kilometerFrom
     * @param mixed $kilometerTo
     * @param mixed $yearFrom
     * @param mixed $yearTo
     * @param mixed $fuelTypes
     * @param mixed $gearBoxes
     * @param mixed $powerFrom
     * @param mixed $powerTo
     * @param mixed $bodyType
     * @param mixed $doors
     *
     * @return ServiceResponse
     */
    public function getByParameters(
        $brand,
        $model,
        $kilometerFrom,
        $kilometerTo,
        $yearFrom,
        $yearTo,
        $fuelTypes,
        $gearBoxes,
        $powerFrom,
        $powerTo,
        $bodyType,
        $doors
    ): ServiceResponse
    {
        putenv('WEBDRIVER_CHROME_DRIVER=/usr/bin/chromedriver');
        set_time_limit(3600);
        $endpoint = $this->mobileDeUrl;
        $priceList = [];

        //$car = collect();
        //$response = $client->post('',[
        //    'query' => [
        //    'yearFrom' => $car->registration_date - 2, (registration_date='2015-01-01')
        //    'yearTo' => $car->registration_date + 2, (sadece yil alinacak)
        //    'kilometerFrom' => $car->km - 5000,
        //    'kilometerTo' => $car->km + 5000,
        //    'powerFrom' => $car->kiloWatt - 40, (PS -> kW a cevirilip getirilecek)
        //    'powerTo' => $car->kiloWatt + 40,
        //    ]
        //]);

        $targetBrand = $this->transformService->getTargetValue('brand', $brand, 'mobilede')->getData();
        $targetModel = $this->transformService->getTargetValue('model', $model, 'mobilede')->getData();
        $targetFuelTypes = collect($fuelTypes)->map(function ($fuelType) {
            return $this->transformService->getTargetValue('fuelTypes', $fuelType, 'mobilede')->getData();
        })->all();
        $targetGearBoxes = collect($gearBoxes)->map(function ($gearBox) {
            return $this->transformService->getTargetValue('gearBoxes', $gearBox, 'mobilede')->getData();
        })->all();
        $targetBodyType = $this->transformService->getTargetValue('bodyType', $bodyType, 'mobilede')->getData();
        $targetDoors = $this->transformService->getTargetValue('doors', $doors, 'mobilede')->getData();

        $parameters = [
            'ms' => $targetBrand . ';' . $targetModel,
            'ml' => $kilometerFrom . ':' . $kilometerTo,
            'fr' => $yearFrom . ':' . $yearTo,
            'ft' => implode(' ', $targetFuelTypes),
            'tr' => implode(' ', $targetGearBoxes),
            'powertype' => 'kw',
            'pw' => $powerFrom . ':' . $powerTo,
            'c' => $targetBodyType,
            'cn' => 'DE',
            'sortOption.sortBy' => 'searchNetGrossPrice',
            'sortOption.sortOrder' => 'ASCENDING',
            'isSearchRequest' => 'true',
            'page' => 1
        ];



        //$chromeDriver->manage()->window()->minimize();
        $mobileDeLastUrl = $endpoint . '?' . http_build_query($parameters) . ($targetDoors && $targetDoors != '' ? '&' . $targetDoors : '');
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(['--headless', '--disable-gpu', '--window-size=1920,1080', '--no-sandbox', '--disable-dev-shm-usage']);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $chromeDriver = ChromeDriver::start($capabilities);
        $chromeDriver->get($mobileDeLastUrl);
        $sources = $chromeDriver->getPageSource();

        preg_match_all('~<span class=\"h3 u-block\">(.*?)&nbsp;€</span>~', $sources, $prices);

        foreach ($prices[1] as $price) {
            $priceList[] = (int)str_replace('.', '', $price);
        }

        $chromeDriver->quit();
        $autoScoutLastUrl = '';
        if (count($priceList) < 15) {
            $targetBrand = $this->transformService->getTargetValue('brand', $brand, 'autoscout')->getData();
            $targetModel = $this->transformService->getTargetValue('model', $model, 'autoscout')->getData();
            $endpoint = $this->autoScoutUrl . '/' . $targetBrand . '/' . $targetModel;
            $targetFuelTypes = collect($fuelTypes)->map(function ($fuelType) {
                return $this->transformService->getTargetValue('fuelTypes', $fuelType, 'autoscout')->getData();
            })->all();
            $targetGearBoxes = collect($gearBoxes)->map(function ($gearBox) {
                return $this->transformService->getTargetValue('gearBoxes', $gearBox, 'autoscout')->getData();
            })->all();
            $targetBodyType = $this->transformService->getTargetValue('bodyType', $bodyType, 'autoscout')->getData();
            $targetDoors = $this->transformService->getTargetValue('doors', $doors, 'autoscout')->getData();

            $parameters = [
                'kmfrom' => $kilometerFrom,
                'kmto' => $kilometerTo,
                'fregfrom' => $yearFrom,
                'fregto' => $yearTo,
                'fuel' => implode(' ', $targetFuelTypes),
                'gear' => implode(' ', $targetGearBoxes),
                'powertype' => 'kw',
                'powerfrom' => $powerFrom,
                'powerto' => $powerTo,
                'body' => $targetBodyType,
                'sort' => 'price',
                'desc' => "0",
                'cy' => "D",
                'page' => 1,
            ];

            $autoScoutLastUrl = $endpoint . '?' . http_build_query($parameters) . ($targetDoors && $targetDoors != '' ? '&' . $targetDoors : '');
            $response = $this->client->get($autoScoutLastUrl, [
                'headers' => [
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36 OPR/91.0.4516.95',
                ],
            ]);

            $clean1 = str_replace(["\n", "\t", "\r", "  "], null, $response->getBody()->getContents());
            $clean2 = str_replace(["&quot;"], null, $clean1);
            $clean3 = preg_replace('~{(.*?)}~', null, $clean2);
            $cleanResult = preg_replace('~{(.*?)}~', null, $clean3);

            preg_match_all('~<article (.*?)</article>~', $cleanResult, $articles);

            if ($articles[1]) {
                foreach ($articles[1] as $article) {
                    preg_match('~<p class="Price_price__WZayw" .*?>(.*?)</p>~', $article, $priceObject);
                    $priceWithCurrency = str_replace(',-', null, $priceObject[1]);
                    $priceWithoutCurrency = explode(' ', $priceWithCurrency)[1];
                    $price = intval(str_replace('.', null, $priceWithoutCurrency));
                    $priceList[] = $price;
                }
            }
        }

        if (count($priceList) == 0) {
            $averagePrice = "0";
        } else {
            $averagePrice = array_sum($priceList) / count($priceList);
        }

        return new ServiceResponse(
            true,
            'Price prediction is successful.',
            200,
            [
                'mobileDeResultsUrl' => $mobileDeLastUrl,
                'autoscoutResultsUrl' => $autoScoutLastUrl,
                'avarage' => intval($averagePrice)
            ]
        );
    }
}
