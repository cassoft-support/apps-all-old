<?php
namespace Cassoft\Api\Cian;

class CianApiHandler {

    public CianApi $cian;

    public function __construct(CianApi $cian) {
        $this->cian = $cian;
    }

    public function getLink(array $dealIds) {
        if(empty($dealIds)) {
            return $this->cian->getErrorMessage('Нет id сделок');
        }

        $report = $this->getLastReport();
        if(isset($report['status']) && $report['status'] === 'error') {
            return $report;
        }
        $ads = $report['result']['offers'];
        $adsUrlAll = array_filter(array_column($ads, 'url', 'externalId'));
        return array_intersect_key($adsUrlAll, array_flip($dealIds));
    }

    public function getAdsReport(array $dealIds) {
        if(empty($dealIds)) {
            return $this->cian->getErrorMessage('Нет id сделок');
        }
        $report = $this->getLastReport();
        if(isset($report['status']) && $report['status'] === 'error') {
            return $report;
        }
        $ads = $report['result']['offers'];
        $map = array_column($ads, 'externalId');
        $map = array_flip(array_filter(($map)));
        $keys = array_intersect_key($map, array_flip($dealIds));
        return array_intersect_key($ads, array_flip($keys));
    }

    private function getLastReport() {
        try {
            return $this->cian->getLastReport();
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