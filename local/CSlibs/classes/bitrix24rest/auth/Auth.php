<?php
namespace CSlibs\B24\Auth;
//require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Cloud/AppCS/CloudApplication.php';
//use  Cloud\AppCS;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Bitrix24\SDK\Core\Credentials\Credentials;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;
use Bitrix24\SDK\Application\Contracts\Bitrix24Accounts\Entity\Bitrix24AccountInterface;
use Bitrix24\SDK\Core\Batch;
use Bitrix24\SDK\Core\BulkItemsReader\BulkItemsReaderBuilder;
use Bitrix24\SDK\Core\CoreBuilder;
use Bitrix24\SDK\Core\Credentials\AuthToken;
use Bitrix24\SDK\Core\Credentials\ApplicationProfile;
use Bitrix24\SDK\Core\Credentials\WebhookUrl;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
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
    public $app_code;
    public $member;
    public $core;
    public $batch;
    public $CScore;

    public $scopeCode;
    private $logName;
    private $idClient;
    private $logPath;
    public $log;
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


    public function __construct($app, $authParams=[], $member="", $app_code='')
    {


        p($member, "start",  __DIR__."/logAuthNew.txt");
        p($authParams, "authParams",  __DIR__."/logAuthNew.txt");
        p($_REQUEST, "REQUEST",  __DIR__."/logAuthNew.txt");
//        console($authParams, "authParams");
//        console($_REQUEST, "REQUEST");

        $this->log =  __DIR__."/logAuthNew.txt";
        $this->appName = $app;
        $this->app_code = $app_code;
        $this->member = $member;
        $this->appParams = $this->setAppParams($app, $member);
       // $this->idClient = $authParams['ID'];
        $this->logName = $app;
        $this->logPath = $_SERVER["DOCUMENT_ROOT"]."/local/a_logs/auth/logAuthError_".$app.".log";


       // if ($this->setAuthParams($authParams)) {
            $this->startAuth();
       // }
    }
    private function setAppParams(string $app, string $member){
        $main="";
        p($_REQUEST, "start", __DIR__."/logAuthParams.txt");
        p($this->member, "member", __DIR__."/logAuthParams.txt");
        if($_REQUEST['member_id']){
            $memberId = $_REQUEST['member_id'];
        }
        if($_REQUEST['memberId']){
            $memberId = $_REQUEST['memberId'];
        }
        if($member){
            $memberId = $member;
        }
        $client = $this->getAuthAdm($app, $memberId);
        p($client, "client", __DIR__."/logAuthParams.txt");
        if($client['UF_MAIN'] == 1){
            $main="_main";
        }
        p($main, "main", __DIR__."/logAuthParams.txt");
        $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        if(!empty($this->app_code)){
            $app_code = $this->app_code;
        }else{
            $app_code = $app;
        }

        $paramsAuth=[
            'select' => ['*'],
            'order' => [],
            'filter' => [
                'UF_APP_NAME' => $app_code
            ],
            'limit' => 1
        ];
        $appParams = $HlApp->hl::getList($paramsAuth)->fetch();
        p($appParams, "appParams", __DIR__."/logAuthParams.txt");
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

//    private function setAuthParams($authParams)
//    {
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("authParams\n", true), FILE_APPEND);
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authParams, true), FILE_APPEND);
//        $authKeysSearch = array_intersect_key($authParams, array_flip($this->authKeys));
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("authKeysSearch\n", true), FILE_APPEND);
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authKeysSearch, true), FILE_APPEND);
//        if (count($authKeysSearch) == 5) {
//            $this->setAuthParamsAuth($authParams);
//            return true;
//        }
//        $requestKeysSearch = array_intersect_key($authParams, array_flip($this->requestKeys));
//        if (count($requestKeysSearch) == 4) {
//            $this->setAuthParamsFromRequest($authParams);
//            return true;
//        }
//        if(empty($authParams) && !empty($this->member)){
//            $authAdmParams = $this->getAuthAdm($this->appName, $this->member);
//            $this->setAuthParamsFromHl($authAdmParams);
//            return true;
//        }
//        return false;
//    }
    private function getAuthAdm($appName, $memberId)
    {
        file_put_contents(__DIR__."/logGetAuthAdm.txt", print_r($appName, true));
        file_put_contents(__DIR__."/logGetAuthAdm.txt", print_r($this->app_code, true), FILE_APPEND);
        $appAccess = 'app_' . $appName . '_access';
        $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
        $params=[
            'select' => ['*'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId,
            ],
            'limit' => 1
        ];
        if(!empty($this->app_code)){
            $params['filter']['UF_APP'] = $this->app_code;
        }
        file_put_contents(__DIR__."/logGetAuthAdm.txt", print_r($params, true), FILE_APPEND);
        $clientsApp = $HlClientApp->hl::getList($params,)->fetch();
        file_put_contents(__DIR__."/logGetAuthAdm.txt", print_r($clientsApp, true), FILE_APPEND);
        return $clientsApp;
    }
    private function setAuthParamsFromHl($authParams)
    {
        $this->member_id = $authParams['UF_CS_CLIENT_PORTAL_MEMBER_ID'];
        if(!empty($authParams['UF_CS_CLIENT_PORTAL_DOMAIN'])){
            $this->domen = $authParams['UF_CS_CLIENT_PORTAL_DOMAIN'];
        }else{
            $this->domen = $authParams['UF_CS_CLIENT_PORTAL_DOMEN'];
        }

        $this->access_token = $authParams['UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'];
        $this->refresh_token = $authParams['UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'];
//        $bitrix24Account = [
//            '0'=>'',
//            '1'=>$authParams['UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'],
//            '2'=>'',
//            '3'=>'',
//            '4'=>'',
//        ];
        return ;
    }
//    private function setAuthParamsAuth($authParams)
//    {
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r("setAuthParamsAuth\n", true), FILE_APPEND);
////        file_put_contents(__DIR__."/logAuthAllApp.txt", print_r($authParams, true), FILE_APPEND);
//        $this->member_id = $authParams['member_id'];
//        $this->domen = $authParams['domen'];
//        $this->access_token = $authParams['access_token'];
//        $this->refresh_token = $authParams['refresh_token'];
//        return;
//    }
//    private function setAuthParamsFromRequest($authParams)
//    {
//        $this->member_id = $authParams['member_id'];
//        $this->domen = $authParams['DOMAIN'];
//        $this->access_token = $authParams['AUTH_ID'];
//        $this->refresh_token = $authParams['REFRESH_ID'];
//        return;
//    }
    private function initFromAccountAdm(ApplicationProfile $applicationProfile, $token,
                                                   $domain,
                                ?EventDispatcherInterface $eventDispatcher = null,
                                ?LoggerInterface  $logger = null
    ): ServiceBuilder
    {

        if ($eventDispatcher === null) {
            $eventDispatcher = new EventDispatcher();
        }
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $b24Res= (new ServiceBuilderFactory($eventDispatcher, $logger))->init(
                $applicationProfile,
                $token,
                $domain
            );
      //  console($b24Res, "b24Res");
        return $b24Res;
    }
    public function startAuth()
    {
        $result = [];
        $log = new Logger($this->logName);
        $log->pushHandler(new StreamHandler($this->logPath, Logger::ERROR));
       // $client = HttpClient::create(['http_version' => '2.0']);

     //   $traceableClient = new \Symfony\Component\HttpClient\TraceableHttpClient($client);
    //    $traceableClient->setLogger($log);
        $appProfile = new \Bitrix24\SDK\Core\Credentials\ApplicationProfile(
            $this->appParams['UF_B24_APPLICATION_ID'],
            $this->appParams['UF_B24_APPLICATION_SECRET'],
            new \Bitrix24\SDK\Core\Credentials\Scope(
                $this->scopeCode
            )
        );
//        console($appProfile, "appProfile");
//        console(Request::createFromGlobals(), "REQ");
    //    $errorHandler = new \Bitrix24\SDK\Core\ApiLevelErrorHandler($log);
   //     $ed = new \Symfony\Component\EventDispatcher\EventDispatcher();
        try {
            $request = Request::createFromGlobals();
           // console($request->request->get('member_id'), "member_id");
           // console($request, "request");

            if(!empty($this->member)){
                $authAdmParams = $this->getAuthAdm($this->appName, $this->member);
               $reg= $this->setAuthParamsFromHl($authAdmParams);
             //  console($reg, "reg");
                $token = new \Bitrix24\SDK\Core\Credentials\AuthToken(
                   $this->access_token,
                    $this->refresh_token,
//                    '78ad066700591408005a33fe0000000800000795c2a9622eb2920dc46d41f8b7695da9',
//                '682c2e6700591408005a33fe00000008000007b3644aaec4e5322ce243b05d0ff1b81d',
                    3600,
                    0
                );
               // console($token, "token");
               // $credentials = Credentials::createFromOAuth($token, $appProfile, $this->domen);

//                console($appProfile, "appProfile");
//                console($this->domen, "domain");
             // console($credentials, "bitrix24Account");
              // $b24 = \Bitrix24\SDK\Services\ServiceBuilderFactory::getServiceBuilder($credentials);


                $b24 = ServiceBuilderFactory::initFromAccountAdm($appProfile, $token, $this->domen);
              //  $b24 = $this->initFromAccountAdm($appProfile, $token, $this->domen);
//               console($b24, "b24");
//                $appInfo = $b24->core->call('user.current')->getResponseData()->getResult();
//               console($appInfo, 'info');
//console($b24, "b24");
               // AuthToken::initFromWorkflowRequest регистрация через REQUIST['auth]
            }else{
                $authAdmParams = $this->getAuthAdm($this->appName, $this->member);
                $reg= $this->setAuthParamsFromHl($authAdmParams);

                $b24 = ServiceBuilderFactory::createServiceBuilderFromPlacementRequest($request, $appProfile);
              //  console($b24, "b24");
              //  $appInfo = $b24->core->call('user.current')->getResponseData()->getResult();
              //  console($appInfo, 'info');
            }

        $this->CScore = new \CSlibs\B24\Auth\CScore($b24->core);
        $this->core = $b24->core;
       $this->batch = new Batch($b24->core, $log);



//      //  $token = new \Bitrix24\SDK\Core\Credentials\AccessToken(
//        $token = new \Bitrix24\SDK\Core\Credentials\AuthToken(
//            $this->access_token,
//            $this->refresh_token,
//            0
//        );
//        p($token, "start", __DIR__."/logAuthAllApp1.txt");
//      //  p($appProfile, "appProfile", __DIR__."/logAuthAllApp1.txt");
//        $domainREQUEST = "https://" . $this->domen;
//      //  $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createForOAuth($token, $appProfile, $domainREQUEST);
//        $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createFromOAuth($token, $appProfile, $domainREQUEST);


//            p($credentials, "credentials", __DIR__."/logAuthAllApp1.txt");
//            p($traceableClient, "traceableClient", __DIR__."/logAuthAllApp1.txt");
//            p("try", "client", __DIR__."/logAuthAllApp1.txt");
//            $requestIdGenerator = new DefaultRequestIdGenerator();
//            p($requestIdGenerator, "requestIdGenerator", __DIR__."/logAuthAllApp1.txt");
//            $apiClient = new \Bitrix24\SDK\Core\ApiClient($credentials, $traceableClient, $requestIdGenerator, $log);
//            p($apiClient, "apiClient", __DIR__."/logAuthAllApp1.txt");
//            $errorHandler = new \Bitrix24\SDK\Core\ApiLevelErrorHandler($log);
//            $ed = new \Symfony\Component\EventDispatcher\EventDispatcher();
//            $updateToken = '';
//            $ed->addListener(
//                \Bitrix24\SDK\Events\AuthTokenRenewedEvent::class,
//                //$this->setNewAuthParams(\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event)
//                function (\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event) {
//                    $this->access_token_new = $event->getRenewedToken()->getAccessToken()->getAccessToken();
//                    $this->refresh_token_new = $event->getRenewedToken()->getAccessToken()->getRefreshToken();
//
//
//                    if (!empty($this->access_token_new) && !empty($this->refresh_token_new && !empty($this->idClient))) {
//                        $HlClientAppCASSOFT = new \CSlibs\B24\HL\HlService('app_' . $this->appName . '_access');
//                        $admUp = $HlClientAppCASSOFT->hl::update(
//                            $this->idClient,
//                            [
//                                'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $this->access_token_new,
//                                'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $this->refresh_token_new,
//                                'UF_DATE_UP' => date('d.m.YTH:i')
//                            ]
//                        );
////                        file_put_contents(__DIR__."logAuth.txt", print_r("admUp\n", true), FILE_APPEND);
////                        file_put_contents(__DIR__."logAuth.txt", print_r($admUp, true), FILE_APPEND);
//                    }
//                }
//            );
//            $b24 = ServiceBuilderFactory::createServiceBuilderFromPlacementRequest(Request::createFromGlobals(), $appProfile);
//            console($b24, "b24");
//            $this->core = new \Bitrix24\SDK\Core\Core($b24, $errorHandler, $ed, $log);
//            $app = new \Bitrix24\SDK\Services\Main\Service\Main($this->core, $log);
//            $this->batch = new \Bitrix24\SDK\Core\Batch($this->core, $log);
//           // $this->CScore = new \CSlibs\B24\Auth\CScore($this->core);
//            $this->CScore = new \CSlibs\B24\Auth\CScore($b24->core);
//            p($this->CScore, "CScore", __DIR__."/logAuthAllApp1.txt");


//            $appInfo = $this->CScore->call('app.info');
//            console($appInfo);
            $result['status'] = true;
           // $result['message'] = $appInfo->getResponseData()->getResult()->getResultData();
           // $result['message'] = $appInfo->getResponseData()->getResult();
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




