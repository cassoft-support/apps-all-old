<?php
namespace CSlibs\B24\Auth;
//require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Cloud/AppCS/CloudApplication.php';
//use  Cloud\AppCS;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;

class Auth
{
    private $member_id;
    private $domen;
    private $access_token;
    private $refresh_token;
    private $access_token_new;
    private $refresh_token_new;
    public $appParams;
    public $appName;
    public $member;
    public $core;
    public $batch;
    public $CScore;
    public $scopeCode;
    public $log = __DIR__."/logAuthNew.txt";
    private $logName;
    private $idClient;
    private $logPath;
//    private $hlKeys = [
//        'UF_CS_CLIENT_PORTAL_MEMBER_ID',
//        'UF_CS_CLIENT_PORTAL_DOMEN',
//        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
//        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
//    ];
    private $requestKeys = [
        'member_id',
        'DOMAIN',
        'AUTH_ID',
        'REFRESH_ID'
    ];
    private $authKeys = [
        'access_token',
        'refresh_token',
        'expires_in',
        'domain',
        'member_id'
    ];


    public function __construct($app, $authParams, $member="" )
    {

        $this->appParams = $this->setAppParams($app);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("app", true));
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($this->appParams, true), FILE_APPEND);
        p($authParams, "start", $this->log);
       $this->appName = $app;
       $this->member = $member;
        $this->idClient = $authParams['ID'];
        $this->logName = $app;
        $this->logPath = $_SERVER["DOCUMENT_ROOT"]."/local/a_logs/auth/logAuthError_".$app.".log";


        if ($this->setAuthParams($authParams)) {
            $this->startAuth();
        }
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
        $hlScope = new \CSlibs\B24\HL\HlService('app_scope');
        $scopeRes = $hlScope->hl::getList([
            'select' => ['UF_CODE'],
            'order' => [],
            'filter' => [
                'ID' => $appParams['UF_B24_APPLICATION_SCOPE']
            ],
        ]);
      //  file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($scopeRes->fetch(), true), FILE_APPEND);
        while ($scope = $scopeRes->fetch()) {

            $scopeCode[] = $scope['UF_CODE'];
        }
      //  file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($this->scopeCode, true), FILE_APPEND);
        $this->scopeCode = $scopeCode;
        return $appParams;
    }

    private function setAuthParams($authParams)
    {
  file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("authParams\n", true), FILE_APPEND);
  file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authParams, true), FILE_APPEND);
        $authKeysSearch = array_intersect_key($authParams, array_flip($this->authKeys));
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("authKeysSearch\n", true), FILE_APPEND);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authKeysSearch, true), FILE_APPEND);
        if (count($authKeysSearch) == 5) {
            $this->setAuthParamsAuth($authParams);
            return true;
        }
        $requestKeysSearch = array_intersect_key($authParams, array_flip($this->requestKeys));
        if (count($requestKeysSearch) == 4) {
            $this->setAuthParamsFromRequest($authParams);
            return true;
        }
        if(empty($authParams) && !empty($this->member)){
            $authAdmParams = $this->getAuthAdm($this->appName, $this->member);
            $this->setAuthParamsFromHl($authAdmParams);
            return true;
        }
        return false;
    }
private function getAuthAdm($appName, $memberId)
{
    $appAccess = 'app_' . $appName . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId],
        'limit' => 1
],
    )->fetch();
    return $clientsApp;
}
    private function setAuthParamsFromHl($authParams)
    {
        $this->member_id = $authParams['UF_CS_CLIENT_PORTAL_MEMBER_ID'];
        $this->domen = $authParams['UF_CS_CLIENT_PORTAL_DOMEN'];
        $this->access_token = $authParams['UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'];
        $this->refresh_token = $authParams['UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'];
        return;
    }
    private function setAuthParamsAuth($authParams)
    {
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("setAuthParamsAuth\n", true), FILE_APPEND);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authParams, true), FILE_APPEND);
        $this->member_id = $authParams['member_id'];
        $this->domen = $authParams['domen'];
        $this->access_token = $authParams['access_token'];
        $this->refresh_token = $authParams['refresh_token'];
        return;
    }
    private function setAuthParamsFromRequest($authParams)
    {
        $this->member_id = $authParams['member_id'];
        $this->domen = $authParams['DOMAIN'];
        $this->access_token = $authParams['AUTH_ID'];
        $this->refresh_token = $authParams['REFRESH_ID'];
        return;
    }
    public function startAuth()
    {
        $result = [];
        $log = new Logger($this->logName);
        $log->pushHandler(new StreamHandler($this->logPath, Logger::ERROR));
        file_put_contents(__DIR__."/logAuthAllAppLoger.txt", print_r(Logger::ERROR, true), FILE_APPEND);
        file_put_contents(__DIR__."/logAuthAllAppLoger.txt", print_r($log, true), FILE_APPEND);
        
        $client = HttpClient::create(['http_version' => '2.0']);

        $traceableClient = new \Symfony\Component\HttpClient\TraceableHttpClient($client);
        $traceableClient->setLogger($log);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("app--".$this->appParams['UF_B24_APPLICATION_ID']."\n", true), FILE_APPEND);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("secret--".$this->appParams['UF_B24_APPLICATION_SECRET']."\n", true), FILE_APPEND);
        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($this->scopeCode, true), FILE_APPEND);

        $appProfile = new \Bitrix24\SDK\Core\Credentials\ApplicationProfile(
            $this->appParams['UF_B24_APPLICATION_ID'],
            $this->appParams['UF_B24_APPLICATION_SECRET'],

             new \Bitrix24\SDK\Core\Credentials\Scope(
                $this->scopeCode
            )
        );
        $token = new \Bitrix24\SDK\Core\Credentials\AccessToken(
            $this->access_token,
            $this->refresh_token,
            0
        );

        $domainREQUEST = "https://" . $this->domen;
        $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createForOAuth($token, $appProfile, $domainREQUEST);
        try {
            $apiClient = new \Bitrix24\SDK\Core\ApiClient($credentials, $traceableClient, $log);
            $errorHandler = new \Bitrix24\SDK\Core\ApiLevelErrorHandler($log);
            $ed = new \Symfony\Component\EventDispatcher\EventDispatcher();
            $updateToken = '';
            $ed->addListener(
                \Bitrix24\SDK\Events\AuthTokenRenewedEvent::class,
                //$this->setNewAuthParams(\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event)
                function (\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event) {
                    $this->access_token_new = $event->getRenewedToken()->getAccessToken()->getAccessToken();
                    $this->refresh_token_new = $event->getRenewedToken()->getAccessToken()->getRefreshToken();


                    if (!empty($this->access_token_new) && !empty($this->refresh_token_new && !empty($this->idClient))) {
                        $HlClientAppCASSOFT = new \CSlibs\B24\HL\HlService('app_' . $this->appName . '_access');
                        $admUp = $HlClientAppCASSOFT->hl::update(
                            $this->idClient,
                            [
                                'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $this->access_token_new,
                                'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $this->refresh_token_new,
                                'UF_DATE_UP' => date('d.m.YTH:i')
                            ]
                        );
//                        file_put_contents(__DIR__."logAuth.txt", print_r("admUp\n", true), FILE_APPEND);
//                        file_put_contents(__DIR__."logAuth.txt", print_r($admUp, true), FILE_APPEND);
                    }
                }
            );

            $this->core = new \Bitrix24\SDK\Core\Core($apiClient, $errorHandler, $ed, $log);
            $app = new \Bitrix24\SDK\Services\Main\Service\Main($this->core, $log);
            $this->batch = new \Bitrix24\SDK\Core\Batch($this->core, $log);
            $this->CScore = new \CSlibs\B24\Auth\CScore($this->core);
//            file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("CScore\n", true), FILE_APPEND);
//            file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($this->CScore, true), FILE_APPEND);

            $appInfo = $this->core->call('app.info');
            $result['status'] = true;
            $result['message'] = $appInfo->getResponseData()->getResult()->getResultData();
        } catch (\Throwable $exception) {
            $result['status'] = false;
            $result['message'] = $exception->getMessage();
        }
        return $result;
    }

    public function getAuth()
    {
        return [
            $this->member_id,
            $this->domen,
            $this->access_token,
            $this->refresh_token,
            $this->access_token_new,
            $this->refresh_token_new
        ];
    }

}


