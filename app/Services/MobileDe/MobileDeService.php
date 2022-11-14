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
        putenv('WEBDRIVER_CHROME_DRIVER=');
        $chromeDriver = ChromeDriver::start();
        $chromeDriver->get('https://www.mobile.de');


        $endpoint = $this->baseUrl;
        $endpoint = 'https://suchen.mobile.de/fahrzeuge/search.html?dam=0&isSearchRequest=true&ms=3500%3B73%3B%3B&ref=quickSearch&sb=rel&vc=Car';
        $priceList = [];

        for ($pageCounter = 1; $pageCounter <= 50; $pageCounter++) {
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
                'page' => $pageCounter,
            ];

            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36 OPR/91.0.4516.95',
                ],
            ]);

            $clean1 = str_replace(["\n", "\t", "\r", "  "], null, $response->getBody()->getContents());
            $cleanResult = str_replace(["&quot;"], null, $clean1);
//            $clean3 = preg_replace('~{(.*?)}~', null, $clean2);
//            $cleanResult = preg_replace('~{(.*?)}~', null, $clean3);

            preg_match('~<iframe .*?>(.*?)</iframe>~', $cleanResult, $checkForCaptcha);

            if (count($checkForCaptcha) == 2) {
                preg_match('~<meta http-equiv=.*?; URL=\'(.*?)\'\" />~', $cleanResult, $metaContentData);

                $newEndpoint = $this->baseUrl . $metaContentData[1];
                $newEndpointResponse = $this->client->get($newEndpoint, [
                    'headers' => [
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                        'Accept-Encoding' => 'gzip, deflate, br',
                        'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36 OPR/91.0.4516.95',
                        'Cookie' => 'optimizelyEndUserId=oeu1668167949054r0.39836729830024264; vi=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjaWQiOiIzOWU3N2VkZS1iZTFjLTQ1ZWUtYWI1OC0xNTBiODM5ZDFhNmUiLCJpYXQiOjE2NjgxNjc5NTB9.YWgCfgCvX17pxEyO24AWjRmZsOp2mcrY5rPK91mvM_g; mdeConsentDataGoogle=1; mdeConsentData=CPiTjqUPiTjqUEyAHADECpCgAP_AAELAAAYgJHtd_H__bX9v-f7_6ft0eY1f9_r37uQzDhfNs-8F3L_W_LwX_2E7NF36tq4KmR4ku1LBIUNtHMnUDUmxaokVrzHsak2cpzNKJ_BkknsZe2dYGF9vm5tj-QKZ7_5_d3f52T_9_9v-39z33913v3d93-_13Ljd_5_9H_v_fR_b8_Kf9_7-_4v8_____3_e______9_-BecAcABQAIAAaABFACYAFsBeYBISAgAAsACoAGQAOAAiABkADwAIgATwAqgDDAH6AkQBkgDJwGXBoAoATAAuAHVASIAycRAEACYAdUBIgDJxUAMAJgAXAF5jIAQATAF5joCoACwAKgAZAA4ACIAGQAPAAfABEACeAFUALgAYgBMADDAH6ARYBIgDJAGTgMuIQCQAFgAZABEAEwAKoAXAAxAJEAZOSgFgALAAyABwAEQAPAAiABVAC4AGIBIgDJykBIABYAFQAMgAcABEADIAHgARAAngBSACqAGIAfoBFgEiAMkAZOAy4A.YAAAAAAAD4AAAKcAAAAA; _gcl_au=1.1.852727085.1668167952; ces=-; iom_consent=0103ff03ff&1668167960756; _fbp=fb.1.1668167960961.493313191; _tt_enable_cookie=1; _ttp=4fd2476e-acef-43a6-adce-ba0e8ee4ba15; cto_axid=zaQZGLAsg-t1zq8bJyoaQTmYNUbEXGKc; hsstp=1; bm_sz=8FDF977835988345BEF3DA868A2201D2~YAAQkfAQAjQX2W6EAQAAZTqvdREbUxIbSjYBROl7TQf9DtI528nKvxNDx9mevV3Wv/lu9BfnM+tVaVsmf6RL0TLlEBT/7d1NvlXf+eB/FLJGzNHoClJY2x9aLBkYp/0LNVy3F1g+fsGWvLL8/Xn58cL8IrB2X20p9qTXQUm9y7FDy4pArtuP+/7FfIrOgW7wRspXFcffC2DgicqwrPNsGCe/hOZP+ccusu8I/Fz/Vu4IgVc+ZnNvJGUj8+W/HNw0VPylbGkTHYRH4GyYOY1pUZ1MQg7JghITp1sRnyiYmxlVAA==~3748663~3425591; mobile.LOCALE=de; bm_mi=6A95BB1DFB54FF5C8DF801740738261B~YAAQkfAQAgoY2W6EAQAAOzyvdRFX0KF+C719zuugPnmItfdeOY0M1VCK/WbXcnFOCT6zI8pcVORDq9s7NjQCZcIWkTUFtko6JA62AW2M5pOncapbopT3kPjIIucktnziO3ejnZbbT1WJLOaeVI8Q0kZZp1FSEzulkZ2ircxXwHbdG1gpmXdv7st2vIfiW1gb1d4rtdurxmpSWlgutIeXURIoNE4+yWFocdYYJbtYbltFVCb9FyIWzGzs+00fe3WWoiY33rQL/rqZO1xrCqSiguuQ9v31wtXNDZYJI/VfUeSbJG6E5y2NelrYY+ckUoRaVXw6LQfCOgGJJtnGJo2s5ktE~1; POPUPCHECK=1668508130436; _gid=GA1.2.416613277.1668421731; lux_uid=166842173091779532; ak_bmsc=147BC70E64998637CE4D4C8FCF814309~000000000000000000000000000000~YAAQi/AQAq6i+3KEAQAAZGy3dRFAG3v34sE9ZrJSadRi2DQ07r4znXkxt25rQBwY4sLEck+YhuWl9VI1wYDZQDu+RG144uOvFM0+Ke0y9FnK6b4bupdacE9vy7vhSB+lr9btDm0e0ThvIQeEbGCd2gs59Ks8zgetpxU/J45epZHI2kH4nWb7I6xFU+fEcLTwzDUdF+fEMiXy3meBJ8rxbpUkUr+M07kYa21pzyaSnzxKCaAh7pdnTahuaoD0tWBJA4TA3+f6R6XVfcyZqu4doNllfXicFBkoudwWQ/W7eKZ1CQw+HZAuslicPtajhrUh1S4R45nigGn57dg4g2RTphTqtoY3ubTMpoVLP2My1jt+mpPYnj7Y3wBwuKDrAxIMA5zKkc1V+u0I0j0ZS5zmvpCp5Zq9WsmTJg6rYQotRDC3hVN1JrKKkMB1BwwxCy7fA/bAa0jCM/DU9RUkbZKHgHVxbyEsQsk0vxPGS6N/w9SjgGpbappPRg==; _abck=812107A7019D76104F789512F8F925F0~0~YAAQi/AQAsWl+3KEAQAA/3K3dQg7Dz96qcUDMcKxNQcJjvRBG7pUgmlX3/0lMKu2ErzG6NqeenleffZDk6o36rdpQVL+HkK3Cq3K21QY1HdowRcHqhmbwEP6x+5weJLdqQXfhKE9o2/KbGXn2O/79jfkK6ap1CGgeKPiV3SSQwQU5Ml/s94PalyETJKoEdUQejD5ro907lgruIae4syaJYbsSR8jV1Jh4swgJY9yEmPdYw6TVKPo08tkrkY+oQj9hr0N6AD2YRM8rPBXWBTgU//cg8KrGZ7B1jvQTKFtj5TSU9f5ROxQgC3AVI8OzlPKYMV/Ko4bC/c5DMUCQL2EUSpVkmwPcRAmW4lyw0epBZP3Lbb1iiK7u6+Jz3/7O8t/C8how5h8EIpB83NOsB5NofAcxVeCUT0+~-1~-1~-1; ioam2018=0009ab9f08b348321636e3917:1695556760634:1668167960634:.mobile.de:8:mobile:DE/DE/OB/S/P/S/G:noevent:1668422267795:wfdhfe; _ga_2H40T2VTNP=GS1.1.1668421730.3.1.1668422268.0.0.0; _ga=GA1.2.1015121615.1668167952; _uetsid=223a07a0640711edba4fa95ea02e5552; _uetvid=47311a9061b811ed9a77ffc8094fdaa5; bm_sv=EEF5610B3795C8FEA1FDBA4305493EDC~YAAQhvAQAo90sW6EAQAA46y8dRH/uWQOLFa6jlpxJaKofvaboxncLrTZryyXLP3dQzgfobUJXjZPWe4slOHbi+nqdrJWlzodz4X885chxscvWWwaFLHai8tTM+sy/+sAI/4Hsks6lvpwjC0d5b5AHlzUi5bJvel2lwX1WrXKMi5nCzjvbaDfQqrMFhaKfMZbZJd14wPHTXi8Gs8nSKwykeTnCM8DZb5a+5LvoU8gtcL7Q7OPcybLKLN1PBR1wAaE~1'
                    ],
                ]);

                return new ServiceResponse(
                    true,
                    'MobileDe Test Service',
                    200,
                    $newEndpointResponse->getBody()->getContents()
                );

                preg_match_all('~<script>(.*?)</script>~', $cleanResult, $scriptTagList);

                preg_match('~var i = (.*?);~', $scriptTagList[0][0], $iValueObject);
                $iValue = intval($iValueObject[1]);

                preg_match('~Number\((.*?)\);~', $scriptTagList[0][0], $jBetween);
                preg_match_all('~\"(.*?)\"~', $jBetween[1], $jNumbers);
                $numbersSum = 0;
                foreach ($jNumbers[1] as $number) {
                    $numbersSum += intval($number);
                }
                $jValue = $iValue + $numbersSum;

                preg_match('~JSON.stringify\(\{\"bm-verify\": \"(.*?)\",\"pow\": j~', $cleanResult, $bmVerifyObject);
                $bmVerifyBodyData = $bmVerifyObject[1];

                $locationResponse = $this->client->post('suchen.mobile.de/_sec/verify?provider=interstitial', [
                    'headers' => [
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                        'Accept-Encoding' => 'gzip, deflate, br',
                        'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36 OPR/91.0.4516.95',
                        'Cookie' => 'optimizelyEndUserId=oeu1668167949054r0.39836729830024264; vi=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjaWQiOiIzOWU3N2VkZS1iZTFjLTQ1ZWUtYWI1OC0xNTBiODM5ZDFhNmUiLCJpYXQiOjE2NjgxNjc5NTB9.YWgCfgCvX17pxEyO24AWjRmZsOp2mcrY5rPK91mvM_g; mdeConsentDataGoogle=1; mdeConsentData=CPiTjqUPiTjqUEyAHADECpCgAP_AAELAAAYgJHtd_H__bX9v-f7_6ft0eY1f9_r37uQzDhfNs-8F3L_W_LwX_2E7NF36tq4KmR4ku1LBIUNtHMnUDUmxaokVrzHsak2cpzNKJ_BkknsZe2dYGF9vm5tj-QKZ7_5_d3f52T_9_9v-39z33913v3d93-_13Ljd_5_9H_v_fR_b8_Kf9_7-_4v8_____3_e______9_-BecAcABQAIAAaABFACYAFsBeYBISAgAAsACoAGQAOAAiABkADwAIgATwAqgDDAH6AkQBkgDJwGXBoAoATAAuAHVASIAycRAEACYAdUBIgDJxUAMAJgAXAF5jIAQATAF5joCoACwAKgAZAA4ACIAGQAPAAfABEACeAFUALgAYgBMADDAH6ARYBIgDJAGTgMuIQCQAFgAZABEAEwAKoAXAAxAJEAZOSgFgALAAyABwAEQAPAAiABVAC4AGIBIgDJykBIABYAFQAMgAcABEADIAHgARAAngBSACqAGIAfoBFgEiAMkAZOAy4A.YAAAAAAAD4AAAKcAAAAA; _gcl_au=1.1.852727085.1668167952; ces=-; iom_consent=0103ff03ff&1668167960756; _fbp=fb.1.1668167960961.493313191; _tt_enable_cookie=1; _ttp=4fd2476e-acef-43a6-adce-ba0e8ee4ba15; cto_axid=zaQZGLAsg-t1zq8bJyoaQTmYNUbEXGKc; hsstp=1; bm_sz=8FDF977835988345BEF3DA868A2201D2~YAAQkfAQAjQX2W6EAQAAZTqvdREbUxIbSjYBROl7TQf9DtI528nKvxNDx9mevV3Wv/lu9BfnM+tVaVsmf6RL0TLlEBT/7d1NvlXf+eB/FLJGzNHoClJY2x9aLBkYp/0LNVy3F1g+fsGWvLL8/Xn58cL8IrB2X20p9qTXQUm9y7FDy4pArtuP+/7FfIrOgW7wRspXFcffC2DgicqwrPNsGCe/hOZP+ccusu8I/Fz/Vu4IgVc+ZnNvJGUj8+W/HNw0VPylbGkTHYRH4GyYOY1pUZ1MQg7JghITp1sRnyiYmxlVAA==~3748663~3425591; mobile.LOCALE=de; bm_mi=6A95BB1DFB54FF5C8DF801740738261B~YAAQkfAQAgoY2W6EAQAAOzyvdRFX0KF+C719zuugPnmItfdeOY0M1VCK/WbXcnFOCT6zI8pcVORDq9s7NjQCZcIWkTUFtko6JA62AW2M5pOncapbopT3kPjIIucktnziO3ejnZbbT1WJLOaeVI8Q0kZZp1FSEzulkZ2ircxXwHbdG1gpmXdv7st2vIfiW1gb1d4rtdurxmpSWlgutIeXURIoNE4+yWFocdYYJbtYbltFVCb9FyIWzGzs+00fe3WWoiY33rQL/rqZO1xrCqSiguuQ9v31wtXNDZYJI/VfUeSbJG6E5y2NelrYY+ckUoRaVXw6LQfCOgGJJtnGJo2s5ktE~1; ioam2018=0009ab9f08b348321636e3917:1695556760634:1668167960634:.mobile.de:7:mobile:DE/DE/OB/S/P/S/G:noevent:1668421730434:e5p8ek; POPUPCHECK=1668508130436; _gid=GA1.2.416613277.1668421731; lux_uid=166842173091779532; _ga=GA1.1.1015121615.1668167952; _uetsid=223a07a0640711edba4fa95ea02e5552; _uetvid=47311a9061b811ed9a77ffc8094fdaa5; _ga_2H40T2VTNP=GS1.1.1668421730.3.1.1668421738.0.0.0; _abck=812107A7019D76104F789512F8F925F0~-1~YAAQi/AQAo+h+3KEAQAAXWq3dQgn4aP/Xf0ANjiG/c84XKpRHl/0eiKMpbY3j4So03svBIxHMZ6fluFBjikQkYrK9MvH0Kq6jg3m3zcXBu1Gfg0CGBdH/GGtzBqekIc9Nf5j6gnfwYF2+WRRj8B1N5DHpHaIodadoRiyDmiJODGm74g5vOOuZv6VIFkIHm1c2KiYWNBN/hwagkPhvLdZbNGF6c1w9frl3UG2NQno8w5MUl7KxhxmvV0hm1X7gkwUzJ4VBFnI+RaspyZ7NMdMh4W/TsdFMpcNVoI8JinFwVwecZBqTK1B6CuIxr6DSZaKlra+/9ItwJvFRiqRt07x+BBQih5Kbb4+tXgYlhdjaOYPojt7AdAI39S1nIFSK7sT+OkkJwJgv1e7+LCTMBbG//iStE3t+Q+v~0~-1~-1; ak_bmsc=147BC70E64998637CE4D4C8FCF814309~000000000000000000000000000000~YAAQi/AQAuSh+3KEAQAA4mq3dRE1+bqj4kOIvoiCjP3rczngFNk/H+uF6H7hZ2kS8hDC5TVCbQL0Ju0K9DO34JUN+ksNBIQ2QrA01WATyceCNrflp+FCjQ/3u91TqgQfV4cn3YH1uqQpmZBVl9x7Cz1gbfcESr+qO+L+aPf8y29BcV5L674Bzm1Ck9k44lBAg1tKMIlilTBRdUiq9Nr8ELmd93rhwoaOzM/ha8+3huFvmF1X4dXcPki4XSdYpq9bh6k+R6Y1C1BEVI1iTD6yozVfXEJIXincpISCU4KwRK9Qj8JLZ+C/pRcKeYxopSNHozX/tkx/DHC4Ee+tVH+h9MJa447WPYLKSzqsqeNB4hzo2L9lJ1GwgVDRcNVLnxGM3QG7zpaZMLhQFD6cj7RgVHQ4F4pS6dOtQdvXRaWVZ8XE25pNIuV0SsyP3g3ru146hKhSZ1MNtMY0HPEbnkP6cXCyxsYguo80RvbN+/uzIlpwiCz7EBs=; bm_sv=EEF5610B3795C8FEA1FDBA4305493EDC~YAAQi/AQAuWh+3KEAQAA4mq3dREqUjeKhH857ZqhfBjMYQxOVYPQIJCPwcGFS5e0pA5uz3Gn2tjvjbzT13GQA4FcIUhtptk8yoHsgisT4VgGQUlc38MBVn8tjMbPcUJxASu50lr33XoFp5y34BlZM0GyEjCHPPMFRsC6k0Ke3qpED18JPEPBjQkVx1/0IiYicijzud5xcJdM5GKmk7zn+9UfDdawy6tqBXPzjRSNx3ezRMHk4hXQdeP2780jdcM=~1'
                    ],
                    RequestOptions::JSON => [
                        'bm-verify' => $bmVerifyBodyData,
                        'pow' => $jValue,
                    ],
                ]);

                return new ServiceResponse(
                    true,
                    'MobileDe Test Service',
                    200,
                    [
                        'bm-verify' => $bmVerifyObject[1],
                        'pow' => $jValue,
                        'location' => $locationResponse->getStatusCode(),
                    ]
                );
            } else {
                return new ServiceResponse(
                    true,
                    'MobileDe Test Service',
                    200,
                    $checkForCaptcha
                );
            }

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
            'Average price calculated successfully.',
            200,
            intval($averagePrice)
        );
    }
}
