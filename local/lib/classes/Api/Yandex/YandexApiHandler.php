<?php
namespace Cassoft\Api\Yandex;

class YandexApiHandler {

    public YandexApi $yandex;

    public function __construct(YandexApi $yandex) {
        $this->yandex = $yandex;
    }

    public function getLink(array $dealIds) {
        if(empty($dealIds)) {
            return $this->yandex->getErrorMessage('Нет id сделок');
        }
        $report = $this->getOffers();
        if(isset($report['status']) && $report['status'] === 'error') {
            return $report;
        }

        $ads = $report['listing']['snippets'];
        $ads = array_column($ads, 'offer');
        foreach($ads as $key => $value) {
            if(isset($value['state']['errors'])) {
               unset($ads[$key]); 
            }
        }
        $adsUrlAll = array_filter(array_column($ads, 'url', 'internalId'));
        return array_intersect_key($adsUrlAll, array_flip($dealIds));

    }


    public function getAdsReport(array $dealIds) {
        if(empty($dealIds)) {
            return $this->yandex->getErrorMessage('Нет id сделок');
        }
        $report = $this->getOffers();
        if($report['status'] === 'error') {
            return $report;
        }
        $ads = $report['listing']['snippets'];
        $ads = array_column($ads, 'offer');
        $map = array_flip(array_column($ads, 'internalId'));
        $keys = array_intersect_key($map, array_flip($dealIds));
        return array_intersect_key($ads, array_flip($keys));
    }

    private function getOffers() {
        try {
            return $this->yandex->getOffers();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $error = [];
            $response = $e->getResponse();
            $error['status'] = 'error';
            $error['code'] = $response->getStatusCode();
            $error['reason'] = $response->getReasonPhrase();
            $error['message'] = json_decode($response->getBody()->getContents(), true);
            return $error;
        }
    }

}
