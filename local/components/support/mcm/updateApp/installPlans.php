<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once 'vendor/autoload.php';
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;
$file_log  = $_SERVER["DOCUMENT_ROOT"] .$componentPath."/logInstallPlans.txt";
file_put_contents($file_log, print_r("instal-Plans",true));
echo "<pre>"; print_r($_REQUEST); echo "</pre>";
file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
if ($_POST) {
    $result = [];
    $authParams = $_POST['authParams'];
    $dom = $authParams['domain'];
    //$dom ="brokci.bitrix24.ru";
    $HlPropertiesType = new \Cassoft\Services\HlService('product_property_type');
    $propertiesType = $HlPropertiesType->makeFieldToField('ID', 'UF_CODE');


    $CloudApplication = new \Cloud\App\CloudApplication('brokci_2');
    $log = new Logger('rest_estate_test');
    $log->pushHandler(new StreamHandler('log.log', Logger::ERROR));

    $client = HttpClient::create(['http_version' => '2.0']);
    $traceableClient = new \Symfony\Component\HttpClient\TraceableHttpClient($client);
    $traceableClient->setLogger($log);

    $appProfile = new \Bitrix24\SDK\Core\Credentials\ApplicationProfile(
        $CloudApplication->getB42_application_id(),
        $CloudApplication->getB42_application_secret(),
        new \Bitrix24\SDK\Core\Credentials\Scope(
            $CloudApplication->getB42_application_scope()
        )
    );
    $token = new \Bitrix24\SDK\Core\Credentials\AccessToken(
        $authParams['access_token'],
        $authParams['refresh_token'],
        0
    );

    $domainREQUEST = "https://" . $dom;
    $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createForOAuth($token, $appProfile, $domainREQUEST);

    try {
        $apiClient = new \Bitrix24\SDK\Core\ApiClient($credentials, $traceableClient, $log);
        $errorHandler = new \Bitrix24\SDK\Core\ApiLevelErrorHandler($log);
        $ed = new \Symfony\Component\EventDispatcher\EventDispatcher();
        $ed->addListener(
            \Bitrix24\SDK\Events\AuthTokenRenewedEvent::class,
            static function (\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event) {
                //var_dump('AuthTokenRenewed!');
                //print($event->getRenewedToken()->getAccessToken()->getAccessToken() . PHP_EOL);
            }
        );

        $core = new \Bitrix24\SDK\Core\Core($apiClient, $errorHandler, $ed, $log);
        $app = new \Bitrix24\SDK\Services\Main\Service\Main($core, $log);
        $batch = new \Bitrix24\SDK\Core\Batch($core, $log);
        $request = new \Cloud\Bitrix24Api\Bitrix24Request($core);
    } catch (\Throwable $exception) {
        $result['auth_error'] = $exception->getMessage();
        //d($exception->getMessage());
        // print(sprintf('error: %s', $exception->getMessage()) . PHP_EOL);
        // print(sprintf('class: %s', get_class($exception)) . PHP_EOL);
        //print(sprintf('trace: %s', $exception->getTraceAsString()) . PHP_EOL);
    }
    $HlEntities = new \Cassoft\Services\HlService('entity_list');
    $entities = $HlEntities->hl::getList([
        'select' => ['*'],
        'order' => [],
        'filter' => [
            'UF_CS_APP_NAME' => 'plans'
        ]
    ])->fetchAll();

    foreach ($entities as $entity) {
        $entityParams = [
            'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
            'NAME' => $entity['UF_CS_ENTITY_NAME'],
            'ACCESS' => [
                'AU' => 'W'
            ]
        ];

        $needInstall = false;
        $entityGet = $request->call('entity.get', $entityParams);
        if ($entityGet == 'error_entity_not_found - entity not found') {
            $needInstall = true;
        }

        if ($needInstall === true) {
            $entityInstall = $request->call('entity.add', $entityParams);
        } else {
            $entityUpdate = $request->call('entity.update', $entityParams);
        }

        $HlEntityProperties = new \Cassoft\Services\HlService($entity['UF_CS_TABLE_NAME']);
        $entityProperties = $HlEntityProperties->hl::getList([
            'select' => ['*'],
            'order' => [],
            'filter' => []
        ])->fetchAll();

        foreach ($entityProperties as $property) {
            $propertyParam = [
                'ENTITY' => $entityParams['ENTITY'],
                'PROPERTY' => $property['UF_CS_PROPERTY'],
                'NAME' => $property['UF_CS_NAME'],
                'TYPE' => $propertiesType[$property['UF_CS_TYPE']],
            ];
            $propertyGet = $request->call('entity.item.property.get', $propertyParam);
            if ($propertyGet == 'error_property_not_found - property not found') {
                $propertyAdd = $request->call('entity.item.property.add', $propertyParam);
            } else {
                $propertyAdd = $request->call('entity.item.property.update', $propertyParam);
            }
        }
        if ($entity['UF_CS_ENTITY_CODE'] === 'stage_fav') {
            $HlStageFav = new \Cassoft\Services\HlService('stage_favourites');
            $itemList = $HlStageFav->makeFieldToValue('UF_CS_PROPERTY');

            $itemParams = [
                'ENTITY'=> 'stage_fav',
                'SORT'=> ['ID'=> 'ASC'],
                'FILTER'=> []
            ];
            $stageFavGet = $request->call('entity.item.get', $entityParams);
            if (!empty($stageFavGet)) {
                $stageFavList = [];
                foreach ($stageFavGet as $val) {
                    $stageFavList[$val['PROPERTY_VALUES']['CS_CODE']] = $val;
                }
                unset($val);
            }
            $updateItems = [];
            $addItems = [];
            foreach ($itemList as $key => $item) {
                $propertyValues = [
                    'CS_CODE' => $item['UF_CS_PROPERTY'],
                    'CS_TYPE_STAGE' => $item['UF_CS_TYPE_STAGE'],
                    'CS_COLOR' => $item['UF_CS_COLOR'],
                ];
                if (empty($stageFavList[$key])) {
                    $addItems[] = [
                        'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
                        'NAME' => $item['UF_CS_NAME'],
                        'CODE' => $item['UF_CS_PROPERTY'],
                        'PROPERTY_VALUES' => $propertyValues,
                    ];
                } else {
                    $updateItems[] = [
                        'ID' => $stageFavList[$key]['ID'],
                        'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
                        'NAME' => $item['UF_CS_NAME'],
                        'CODE' => $item['UF_CS_PROPERTY'],
                        'PROPERTY_VALUES' => $propertyValues,
                    ];
                }
            }
            if (!empty($updateItems)) {
                foreach ($batch->addEntityItems('entity.item.update', $updateItems) as $key => $val) {
                }
            }
            if (!empty($addItems)) {
                foreach ($batch->addEntityItems('entity.item.add', $addItems) as $key => $val) {
                }
            }
        }
    }

    $result['plans'] = 'success';
    echo json_encode($result);
}
