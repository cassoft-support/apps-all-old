<?php
namespace CSlibs\B24\Auth;
use Bitrix24\SDK;

class B24Client
{
    private $bitrix24;
    public $appParams;

    public function __construct($app, $domain, $accessToken)
    {
        $this->appParams = $this->setAppParams($app);
        p($this->appParams, "start",__DIR__."/logB24Client.txt");
        $this->bitrix24 = new Bitrix24();
        $this->bitrix24->setApplicationId($this->appParams['UF_B24_APPLICATION_ID']);
        $this->bitrix24->setApplicationSecret($this->appParams['UF_B24_APPLICATION_SECRET']);
        $this->bitrix24->setDomain($domain);
        $this->bitrix24->setAccessToken($accessToken);
    }

    public function getClient()
    {
        return $this->bitrix24;
    }
    private function setAppParams(string $app){
        $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $appParams = $HlApp->hl::getList([
            'select' => ['*'],
            'order' => [],
            'filter' => [
                'UF_APP_NAME' => $app
            ],
            'limit' => 1
        ])->fetch();

        return $appParams;
    }
}
