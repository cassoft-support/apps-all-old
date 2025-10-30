<?php

namespace Cassoft\Api\Avito;

class AvitoApiHandler
{
 // $file_log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/cron.scripts/marketing.ads_report_brokci_pro/logAvitoApiHandler.txt";
  
  

    public AvitoApi $avito;

    public function __construct(AvitoApi $avito)
    {
        $this->avito = $avito;
    }

    public function getLink(array $dealIds)
    {
        if (empty($dealIds)) {
            return $this->avito->getErrorMessage('Нет id сделок');
        }

        $response = $this->getReport();
        if (isset($response['status']) && $response['status'] === 'error') {
            return $response;
        }
        $ads = $response['report']['ads'];
        $adsUrlAll = array_column($ads, 'url', 'ad_id');
        return array_intersect_key($adsUrlAll, array_flip($dealIds));
    }

    public function getAdsReport(array $dealIds)
    {
        if (empty($dealIds)) {
            return $this->avito->getErrorMessage('Нет id сделок');
        }

        $response = $this->getReport();
        file_put_contents("logAvitoApiHandler.txt", print_r( $response, true), FILE_APPEND);
        if (isset($response['status']) && $response['status'] === 'error') {
            return $response;
        }
        $ads = $response['report']['ads'];
        $map = array_flip(array_column($ads, 'ad_id'));
        $keys = array_intersect_key($map, array_flip($dealIds));
        return array_intersect_key($ads, array_flip($keys));
    }
    public function getAdsLastReport(array $dealIds)
    {
        if (empty($dealIds)) {
            return $this->avito->getErrorMessage('Нет id сделок');
        }
        $auth = $this->autorizate();
        if (isset($auth['error'])) {
            return $this->avito->getErrorMessage($auth);
        }

        $this->avito->setToken($auth['access_token']);
        $this->avito->setTokenType($auth['token_type']);

        $userInfo = $this->getUserInfo();
        if (isset($userInfo['status']) && $userInfo['status'] === 'error') {
            return $userInfo;
        }

        $this->avito->setUserId($userInfo['id']);
        $lastReportInfo = $this->getLastReport();
        if (isset($lastReportInfo['status']) && $lastReportInfo['status'] === 'error') {
            // return $lastReportInfo;
        }

        $response = $this->getReportById($lastReportInfo['report']['id']);
        return $response;
        $ads = $response['report']['ads'];
        $map = array_flip(array_column($ads, 'ad_id'));
        $keys = array_intersect_key($map, array_flip($dealIds));
        // return array_intersect_key($ads, array_flip($keys));
    }
    public function getReport()
    {
      file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/log.txt", print_r("auth\n", true), FILE_APPEND);
        $auth = $this->autorizate();
        file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/log.txt", print_r($auth, true), FILE_APPEND);
        if (isset($auth['error'])) {
            return $this->avito->getErrorMessage($auth);
        }
       // $this->avito->setToken($auth['access_token']);
        $this->avito->setToken($auth['access_token']); 
        $this->avito->setTokenType($auth['token_type']);

        $userInfo = $this->getUserInfo();
        if (isset($userInfo['status']) && $userInfo['status'] === 'error') {
            return $userInfo;
        }
        $this->avito->setUserId($userInfo['id']);

        //return $this->getLastReport();
        $reportList =  $this->getReportsList();
        if (isset($reportList['status']) && $reportList['status'] === 'error') {
            return $reportList;
        }
        foreach ($reportList['reports'] as $reportStatus) {
            if (!empty($reportStatus['finished_at'])) {
                $reportId = $reportStatus['id'];
            }
        }
        //$reportId = $reportList['reports']['1']['id'];
        return $this->getReportById($reportId);
    }

    private function autorizate()
    {
        try {
            return $this->avito->autorizate();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->getErrorMessage($e);
        }
    }

    private function getReportById($id)
    {
        try {
            return $this->avito->getReportById($id);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->getErrorMessage($e);
        }
    }

    private function getUserInfo()
    {
        try {
            return $this->avito->getUserInfo();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->getErrorMessage($e);
        }
    }

    private function getLastReport()
    {
        try {
            return $this->avito->getLastReport();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->getErrorMessage($e);
        }
    }

    private function getReportsList()
    {
        try {
            return $this->avito->getReportsList();
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
