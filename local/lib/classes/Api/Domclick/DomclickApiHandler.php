<?php

namespace Cassoft\Api\Domclick;

class DomclickApiHandler
{

    public DomclickApi $domclick;

    public function __construct(DomclickApi $domclick)
    {
        $this->domclick = $domclick;
    }

    public function getLink(array $dealIds): array
    {

        if (empty($dealIds)) {
            return $this->domclick->getErrorMessage('Нет id сделок');
        }
        $response = $this->getLastReport();
        if (isset($response['status']) && $response['status'] === 'error') {
            return $response;
        }
        $adsUrlAll = [];
        $ads = $response['OfferList']['Offer'];
        foreach ($ads as $ad) {
            if (empty($ad['Publication']['DomclickURL'])) {
                continue;
            }
            $adsUrlAll[$ad['ExternalId']] = $ad['Publication']['DomclickURL'];
        }
        return array_intersect_key($adsUrlAll, array_flip($dealIds));
    }

    public function getAdsReport(array $dealIds)
    {
        if (empty($dealIds)) {
            return $this->domclick->getErrorMessage('Нет id сделок');
        }

        $response = $this->getLastReport();
        if (isset($response['status']) && $response['status'] === 'error') {
            return $response;
        }
        $ads = $response['OfferList']['Offer'];
        $map = array_flip($dealIds);
        $report = [];
        foreach ($ads as $ad) {
            if (array_key_exists($ad['ExternalId'], $map)) {
                $report[] = $ad;
                unset($map[$ad['ExternalId']]);
            }
        }


        return $report;
        //$map = array_flip(array_column($ads, 'ExternalId'));
        //$keys = array_intersect_key($map, array_flip($dealIds));
        //return array_intersect_key($ads, array_flip($keys));
    }

    public function getLastReport()
    {
        try {
            return $this->domclick->getLastReport();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->getErrorMessage($e);
        }
    }

    private function getErrorMessage($e): array
    {
        $error = [];
        $response = $e->getResponse();
        $error['status'] = 'error';
        $error['code'] = $response->getStatusCode();
        $error['reason'] = $response->getReasonPhrase();
        $error['message'] = json_decode($response->getBody()->getContents(), true);
        return $error;
    }
}