<?php

namespace App\Services\PricePrediction;

use App\Core\ServiceResponse;
use App\Interfaces\PricePrediction\IPricePredictionService;
use App\Models\Eloquent\Transform;
use Facebook\WebDriver\Chrome\ChromeDriver;

class PricePredictionService extends BasePricePredictionService implements IPricePredictionService
{
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
        $powerTo
    ): ServiceResponse
    {
        set_time_limit(3600);
        $endpoint = $this->mobileDeUrl;
        $priceList = [];

        $parameters = [
            $targetBrand = Transform::where('relation_type', 'brand')->where('relation_id', $brand)->where('target_system', 'mobilede')->first()->target_value,
            $targetModel = Transform::where('relation_type', 'model')->where('relation_id', $model)->where('target_system', 'mobilede')->first()->target_value,
            'ms' => $targetBrand . ';' . $targetModel,
            'ml' => $kilometerFrom . ':' . $kilometerTo,
            'fr' => $yearFrom . ':' . $yearTo,
            'ft' => implode(' ', $fuelTypes),
            'tr' => implode(' ', $gearBoxes),
            'powertype' => 'kw',
            'pw' => $powerFrom . ':' . $powerTo,
            'cn' => 'DE',
            'isSearchRequest' => 'true',
            'page' => 1
        ];

        $chromeDriver = ChromeDriver::start();
        $chromeDriver->manage()->window()->minimize();
        $chromeDriver->get($endpoint . '?' . http_build_query($parameters));
        $sources = $chromeDriver->getPageSource();

        preg_match_all('~<span class=\"h3 u-block\">(.*?)&nbsp;€</span>~', $sources, $prices);

        foreach ($prices[1] as $price) {
            $priceList[] = (int)str_replace('.', '', $price);
        }

        $chromeDriver->quit();

        if (count($priceList) < 15) {
            $targetBrand = Transform::where('relation_type', 'brand')->where('relation_id', $brand)->where('target_system', 'autoscout')->first()->target_value;
            $targetModel = Transform::where('relation_type', 'model')->where('relation_id', $model)->where('target_system', 'autoscout')->first()->target_value;
            $endpoint = $this->autoScoutUrl . '/' . $targetBrand . '/' . $targetModel;
            $parameters = [
                'kmfrom' => $kilometerFrom,
                'kmto' => $kilometerTo,
                'fregfrom' => $yearFrom,
                'fregto' => $yearTo,
                'fuel' => implode(' ', $fuelTypes),
                'gear' => implode(' ', $gearBoxes),
                'powertype' => 'kw',
                'powerfrom' => $powerFrom,
                'powerto' => $powerTo,
                'page' => 1,
            ];

            $response = $this->client->get($endpoint . '?' . http_build_query($parameters), [
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

        $averagePrice = array_sum($priceList) / count($priceList);

        return new ServiceResponse(
            true,
            'Price prediction is successful.',
            200,
            intval($averagePrice)
        );
    }
}
