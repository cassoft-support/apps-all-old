<?php

namespace CSlibs\B24\Auth;

class CScore
{
    private $core;
    private $requestTime;
    private $nextRequestTime = 0;
    public function __construct($core)
    {
        $this->core = $core;
//        file_put_contents(__DIR__."/logCScore.txt", print_r("core", true));
//        file_put_contents(__DIR__."/logCScore.txt", print_r($core, true), FILE_APPEND);

    }
    public function call($method, $params = null)
    {
//        file_put_contents(__DIR__."/logCScoreCall.txt", print_r("call", true));
//        file_put_contents(__DIR__."/logCScoreCall.txt", print_r($method, true), FILE_APPEND);
        if ($this->nextRequestTime !== 0) {
            time_sleep_until($this->nextRequestTime);
        }
        $result = [];
        if ($params === null) {
            try {
                $Response = $this->core->call($method);
                $result = $Response->getResponseData()->getResult();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
        } else {
            try {
                $Response = $this->core->call($method, $params);
                $result = $Response->getResponseData()->getResult();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
        }
        $this->setNextRequestTime();
        return $result;
    }
    public function callCount($method, $params = null)
    {
//        file_put_contents(__DIR__."/logCScoreCall.txt", print_r("call", true));
//        file_put_contents(__DIR__."/logCScoreCall.txt", print_r($method, true), FILE_APPEND);
        if ($this->nextRequestTime !== 0) {
            time_sleep_until($this->nextRequestTime);
        }
        $result = [];
        if ($params === null) {
            try {
                $Response = $this->core->call($method);
                $result = $Response->getResponseData()->getPagination()->getTotal();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
        } else {
            try {
                $Response = $this->core->call($method, $params);
                $result = $Response->getResponseData()->getPagination()->getTotal();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
        }
        $this->setNextRequestTime();
        return $result;
    }
    private function setNextRequestTime()
    {
        $this->requestTime = microtime(true);
        $this->nextRequestTime = $this->requestTime + 0.5;
    }

    public function batch($method, $params = null){
        $resItem = $this->callCount($method, $params);
       // pr($resItem);
        $margin = ceil($resItem / 50);
        $m = 1;
        $start = 0;
        $itemsAll=[];
        for ($m = 1; $m <= $margin; $m++) {
            $params['start'] = $start;
            $items=[];
            $items = $this->call($method, $params);

            $start = $start + 50;
            $itemsAll = array_merge($itemsAll, $items);
        }
        return $itemsAll;
    }

}

