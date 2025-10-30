<?php

namespace Cassoft\Api\Dadata;

use Exception;

class DadataApiHandler
{
    public \Cassoft\Api\Dadata\DadataApi $Dadata;
    public array $tokens;
    public function __construct()
    {
    }
    public function init()
    {
        $authParams = array_shift($this->tokens);
        $this->Dadata = new \Cassoft\Api\Dadata\DadataApi($authParams['token']);
        return $this;
    }
    public function changeToken()
    {
    }
    public function setTokenFromHl()
    {
        $hl = new \Cassoft\Services\HlService('cs_dadata');
        $this->tokens = $this->changeHlData($hl->getVocabulary());
        return $this;
    }
    private function changeHlData(array $hlData)
    {
        $result = [];
        foreach ($hlData as $item) {
            $authParams = [];
            $authParams['token'] = $item['UF_CS_DADATA_KEY'];
            $authParams['secret'] = $item['UF_CS_DADATA_TOKEN'];
            $result[] = $authParams;
        }
        return $result;
    }
    public function getSuggestionsAddress(string $address)
    {
        var_dump($address);
        try {
            return $this->Dadata->suggestionsAddress($address);
        } catch (Exception $e) {
            $this->init();
            return $this->getSuggestionsAddress($address);
        }
    }

    /**
     * Get the value of tokens
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}