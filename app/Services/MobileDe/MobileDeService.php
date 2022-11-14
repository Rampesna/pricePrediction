<?php

namespace App\Services\MobileDe;

use App\Core\ServiceResponse;
use App\Interfaces\IMobileDeService;
use Facebook\WebDriver\Chrome\ChromeDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;

class MobileDeService extends BaseMobileDeService implements IMobileDeService
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
        $endpoint = $this->searchUrl;
        $priceList = [];

        set_time_limit(3600);
        for ($pageCounter = 1; $pageCounter <= 10; $pageCounter++) {
            $parameters = [
                'ms' => $brand . ';' . $model,
                'ml' => $kilometerFrom . ':' . $kilometerTo,
                'fr' => $yearFrom . ':' . $yearTo,
                'ft' => implode(' ', $fuelTypes),
                'tr' => implode(' ', $gearBoxes),
                'powertype' => 'kw',
                'pw' => $powerFrom . ':' . $powerTo,
                'cn' => 'DE',
                'isSearchRequest' => 'true',
                'page' => $pageCounter
            ];

            $chromeDriver = ChromeDriver::start();
            $chromeDriver->manage()->window()->minimize();
            $chromeDriver->get($endpoint . '?' . http_build_query($parameters));
            $sources = $chromeDriver->getPageSource();

            preg_match_all('~<span class=\"h3 u-block\">(.*?)&nbsp;€</span>~', $sources, $prices);

            foreach ($prices[1] as $price) {
                $priceList[] = (int)str_replace('.', '', $price);
            }
        }

        $chromeDriver->quit();

        $averagePrice = array_sum($priceList) / count($priceList);

        return new ServiceResponse(
            true,
            'Average price calculated successfully.',
            200,
            intval($averagePrice)
        );
    }
}
