<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/SelfProg/Hbk.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/pub/cassoftApp/brokci/vendor/autoload.php';
require_once("tools/tools.php");
require_once('tools/HlbkForRealEst.php');
require_once 'PropertyCatalog.php';
require 'FieldCrmDeal.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
$domainApp = "https://city.brokci.ru";

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;

//$HlClientParams = new \Cassoft\Services\HlService('client_marketing_settings');
//$HlClientAccess = new \Cassoft\Services\HlService('app_brokci_accesses');
//скрипт запускается при установке приложения, добавляет свойства товара и новый раздел в товарный каталог


class CApplication
{
    public $dealUpdateHandler = APP_REG_URL; //url обработчика событий изменения сделки, равен url самого приложения (константа задана в файле tools.php)
    public $arB24App;
    public $arAccessParams = array(); //параметры для авторизации
    public $currentUser = 0;
    private $b24_error = '';
    public $isTokenRefreshed = false; //ключ обновления кода доступа
    private $logDIr = '/logs/installBrokci/';
    public $add_count = 0; //количество добавленных полей
    public $error_count = 0; //количество ошибок
    public $error_message = '';
    //public $domainApp = "https://cas.brokci.ru";
    //public $existingXmlIds = array(); //массив XML_ID уже существующих свойств товара в облаке
    //public $obB24Product; //экземпляр класса Bitrix24Product для запросов к серверу облака
    //public $obB24Event; //экземпляр класса obB24Event для запросов к серверу облака
    //public $bitrix24deal; //экземпляр класса bitrix24deal для запросов к серверу облака
    //public $obB24Batch; //экземпляра класса Bitrix24Batch для запросов к серверу облака
    public $catalogId = null; //id товарного каталога



    private function checkB24Auth()
    {
        $this->isTokenRefreshed = false;
        //создание специального класса для выполнения запросов (arB24App)
        $this->arB24App = getBitrix24($this->arAccessParams, $this->isTokenRefreshed, $this->b24_error);
        return $this->b24_error === true;
    }

    //логирование
    public function addLog($message)
    {
        $logPath = $_SERVER['DOCUMENT_ROOT'] . $this->logDIr . date('Y-m-d') . ".txt";
        file_put_contents($logPath, $message . PHP_EOL, FILE_APPEND);
    }

    //функция отдает список всех значений одного поля хайблока
    public function getValuesList($tableName, $fieldName)
    {
        //создание экземпляра для работы с хайблоками
        $hl = \Cassoft\SelfProg\HlbkForRealEst::getHlbk($tableName);

        $result = array();
        $res = $hl::getList([
            'select' => array('*'),
            'filter' => array()
        ]);
        while ($row = $res->fetch()) {
            $result[$row["ID"]] = $row[$fieldName];
        }
        return $result;
    }

    //функция получает id товарного каталога
    public function getCatalogId()
    {
        $catalogId = $this->obB24Product->getCatalogId();

        if (!$catalogId) {
            $this->addLog('Не найден id товарного каталога');
            $ajaxResult = array(
                "error" => 1,
                "message" => 'Не найден id товарного каталога'
            );
            exit(json_encode($ajaxResult));
        } else {
            $this->addLog('id товарного каталога равен ' . $catalogId);
            $this->catalogId = $catalogId;
        }
    }

    //функция добавляет свойство товара в облако
    public function addProperty($xmlId, $name, $type, $multiple, $arValues = null)
    {
        if (in_array($xmlId, $this->existingXmlIds)) {
            $this->addLog('Свойство с XML_ID = ' . $xmlId . ' уже существует');
            return; //не добавляем уже существующие
        }

        $fields = array(
            "ACTIVE" => 'Y',
            "IBLOCK_ID" => $this->catalogId,
            "NAME" => $name,
            "SORT" => 10,
            "PROPERTY_TYPE" => $type,
            "MULTIPLE" => $multiple,
            "XML_ID" => $xmlId,
            "VALUES" => $arValues
        );

        $result = $this->obB24Product->addProductProperty($fields);

        if ($result["result"]) {
            $this->addLog('Добавлено свойство ' . $xmlId . ' (' . $name . '), id = ' . $result["result"]);
            $this->add_count++;
        }
        if ($result["error"]) {
            $this->error_count++;
            $this->addLog('Ошибка добавления свойства ' . $xmlId . ' (' . $name . ')' . PHP_EOL . print_r($result["error"], true));
            $this->error_message .= 'Ошибка добавления свойства ' . $name . "\n";
        }
        return;
    }


    //функция добавляет поле сделки
    public function addDealField($arExistFields, $name, $description, $type, $multiple, $edit, $show)
    {
        if (!array_key_exists($name, $arExistFields)) {
            $result = false;
            try {
                $result = $this->bitrix24deal->addDealField($name, $description, $type, $multiple, $edit, $show);
            } catch (Exception $e) {
                $this->addLog('Ошибка добавления поля сделки: ' . $description);
            }
            if ($result) $this->addLog('Добавлено поле сделки: ' . $description);
            else $this->addLog('Ошибка добавления поля сделки: ' . $description);
        } else $this->addLog('Поле сделки "' . $description . '" уже существует');
    }

    //авторизация и установка переменных
    public function start()
    {
        if ($_REQUEST["action"]) {
            //авторизация при ajax запросах
            $this->arAccessParams = $_REQUEST;
        }

        //создание специального класса arB24App методом checkB24Auth()
        $this->checkB24Auth();
    }
}

$application = new CApplication();


if (!empty($_REQUEST)) {
    if (!$_REQUEST["DOMAIN"] && !$_REQUEST["domain"] && !$_REQUEST["auth"]) //если не передан адрес
    {
        exit('Данные для авторизации устарели. Обновите страницу');
    }


    //запуск и авторизация в облаке 

    $application->start();
    //var_dump($_REQUEST);

    if ($_REQUEST["action"] == 'addPropertyes') {

        $application->addLog('-----------------------------------------------------------------------------------------------------------------------');
        $application->addLog(date('d.m.Y H:i:s') . ' Начало работы скрипта по добавлению свойств товара');
        $application->addLog('member_id клиента: ' . $application->arAccessParams["member_id"]);
        $application->addLog('domain клиента: ' . $application->arAccessParams["domain"]);
        $application->addLog('domain приложения: ' . $domainApp);

        //---------------------------- добавление полей сделки ----------------------------

        //поиск уже установленных полей, чтобы исключить их повторную установку
        //создание экземпляра класса bitrix24deal для запросов к серверу облака

        $application->bitrix24deal = new \Bitrix24\bitrix24deal\bitrix24deal($application->arB24App);
        $arExistFields = $application->bitrix24deal->getFields();

        //---------------------------- добавление свойств товара ----------------------------

        //создание экземпляра класса Bitrix24Product для запросов к серверу облака
        $application->obB24Product = new \Bitrix24\Bitrix24Product\Bitrix24Product($application->arB24App);
        //создание экземпляра класса Bitrix24Event для запросов к серверу облака
        $application->obB24Event = new \Bitrix24\Bitrix24Event\Bitrix24Event($application->arB24App);

        //получение id товарного каталога
        $application->getCatalogId();

        //--------------- получение массива XML_ID уже существующих свойств ---------------
        //создание экземпляра класса Bitrix24Batch для запросов к серверу облака
        $application->obB24Batch = new \Bitrix24\Bitrix24Batch\Bitrix24Batch($application->arB24App);
        $application->obB24Batch->addProductPropertyesListCall(
            0,
            array("SORT" => "ASC"),
            $arFilter = array(
                "IBLOCK_ID" => $application->catalogId
            )
        );
        $res = $application->obB24Batch->call();
        $arResPropertyes = $res[0]["data"];
        foreach ($arResPropertyes as $property) {
            $application->existingXmlIds[] = $property["XML_ID"];
        }

        //------------------ списки свойств для их добавления в облако --------------------


        $CloudApplication = new \Cloud\App\CloudApplication('brokci_2');

        $log = new Logger('rest_estate');
        $logPath = 'logs/' .  str_replace('.', '_', $_REQUEST['domain']) . '.log';
        $log->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

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
            $_REQUEST['access_token'],
            $_REQUEST['refresh_token'],
            0
        );
        $domainREQUEST = "https://" . $_REQUEST['domain'];
        $credentials = \Bitrix24\SDK\Core\Credentials\Credentials::createForOAuth($token, $appProfile, $domainREQUEST);
        try {
            $apiClient = new \Bitrix24\SDK\Core\ApiClient($credentials, $traceableClient, $log);
            $errorHandler = new \Bitrix24\SDK\Core\ApiLevelErrorHandler($log);
            $ed = new \Symfony\Component\EventDispatcher\EventDispatcher();
            $ed->addListener(
                \Bitrix24\SDK\Events\AuthTokenRenewedEvent::class,
                static function (\Bitrix24\SDK\Events\AuthTokenRenewedEvent $event) {
                    var_dump('AuthTokenRenewed!');
                    print($event->getRenewedToken()->getAccessToken()->getAccessToken() . PHP_EOL);
                }
            );

            $core = new \Bitrix24\SDK\Core\Core($apiClient, $errorHandler, $ed, $log);

            $app = new \Bitrix24\SDK\Services\Main\Service\Main($core, $log);
            $batch = new \Bitrix24\SDK\Core\Batch($core, $log);
            $log->debug('================================');

            $propertyListCatalogCore = $core->call('crm.product.property.list');
            $propertyListCatalog = $propertyListCatalogCore->getResponseData()->getResult()->getResultData();

            $propertyMapXml = array_column($propertyListCatalog, 'ID', 'XML_ID');
            $propertyMapCode = array_column($propertyListCatalog, 'ID', 'CODE');
            $propertyListCatalogId = [];
            foreach ($propertyListCatalog as $propertyCatalog) {
                $propertyListCatalogId[$propertyCatalog['ID']] = $propertyCatalog;
            }
            $HlPropertyList = new \Cassoft\Services\HlService('product_property_list');
            $propertyList = $HlPropertyList->hl::getList([
                'select' => ['*'],
                'order' => ['ID' => 'ASC'],
                'filter' => [
                    'UF_CATALOG' => '1'
                ]
            ])->fetchAll();
            $application->addLog("Начинаем создание свойство товара");
            $log->debug('Начинаем создание свойство товара');

            $propertyList = array_chunk($propertyList, 50);

            foreach ($propertyList as $propertyGroup) {
                foreach ($propertyGroup as $property) {
                    $fieldId = false;
                    //var_dump($property);
                    $PropertyCatalog = new PropertyCatalog($property);
                    //$fieldId = (key_exists($PropertyCatalog->xmlIdOld, $propertyMapXml)) ? $propertyMapXml[$PropertyCatalog->xmlIdOld] : $propertyMapCode[$PropertyCatalog->code];
                    $fieldId = array_key_exists($PropertyCatalog->code, $propertyMapCode);
                    if ($fieldId !== false) {
                        $fieldId = $propertyMapCode[$PropertyCatalog->code];
                        if ($PropertyCatalog->type === 'L') {
                            $PropertyCatalog->combineValues($propertyListCatalogId[$fieldId]['VALUES']);
                        }
                        $batch->addCommand('crm.product.property.update', [
                            'ID' => $fieldId,
                            'FIELDS' => $PropertyCatalog->fields
                        ]);
                    } else {
                        $batch->addCommand('crm.product.property.add', [
                            'FIELDS' => $PropertyCatalog->fields
                        ]);
                    }
                }
                foreach ($batch->getTraversable(true) as $queryCnt => $queryResultData) {
                    /*
					d(sprintf(' single query number %s: ', $queryCnt) . PHP_EOL);
					d(sprintf(
						' time |start: %s |duration %s |',
						$queryResultData->getTime()->getDateStart()->format('H:i:s'),
						$queryResultData->getTime()->getDuration(),
					) . PHP_EOL);
		
					d(sprintf(' result: %s', $queryResultData->getResult()->getResultData()[0]) . PHP_EOL);
					d($propertyGroup[$queryCnt]);
					d(sprintf(' --') . PHP_EOL);
					*/
                }
                $batch->clearCommands();
            }

            $application->addLog("Начинаем создание свойств сделки");
            $log->debug('Начинаем создание свойство сделки');

            // Добовляем поля в сделку
            $fieldListDeal = $core->call('crm.deal.userfield.list', [
                'order' => [],
                'filter' => []
            ])->getResponseData()->getResult()->getResultData();



            $fieldMapCode = array_column($fieldListDeal, 'ID', 'FIELD_NAME');

            $fieldListDealId = [];
            foreach ($fieldListDeal as $fieldDeal) {

                $fieldListDealId[$fieldDeal['ID']] = $fieldDeal;
            }
            $fieldList = $HlPropertyList->hl::getList([
                'select' => ['*'],
                'order' => ['ID' => 'ASC'],
                'filter' => [
                    'UF_DEAL' => '1'
                ]
            ])->fetchAll();
            $fieldList = array_chunk($fieldList, 50);
            foreach ($fieldList as $fieldGroup) {
                foreach ($fieldGroup as $field) {
                    //var_dump($field);

                    $FieldCrmDeal = new FieldCrmDeal($field);
                    $fieldId = (key_exists($FieldCrmDeal->code, $fieldMapCode)) ? $fieldMapCode[$FieldCrmDeal->code] : null;
                    if ($fieldId !== null) {
                        if ($FieldCrmDeal->type === 'enumeration') {
                            $FieldCrmDeal->combineValues($fieldListDealId[$fieldId]['LIST']);
                        }
                        $batch->addCommand('crm.deal.userfield.update', [
                            'ID' => $fieldId,
                            'FIELDS' => $FieldCrmDeal->fields
                        ]);
                    } else {
                        $batch->addCommand('crm.deal.userfield.add', [
                            'FIELDS' => $FieldCrmDeal->fields
                        ]);
                    }
                }
                foreach ($batch->getTraversable(true) as $queryCnt => $queryResultData) {
                    /*
					d(sprintf(' single query number %s: ', $queryCnt) . PHP_EOL);
					d(sprintf(
						' time |start: %s |duration %s |',
						$queryResultData->getTime()->getDateStart()->format('H:i:s'),
						$queryResultData->getTime()->getDuration(),
					) . PHP_EOL);
		
					d(sprintf(' result: %s', $queryResultData->getResult()->getResultData()[0]) . PHP_EOL);
					d($fieldGroup[$queryCnt]);
					d(sprintf(' --') . PHP_EOL);
					*/
                }
                $batch->clearCommands();
            }
        } catch (\Throwable $exception) {
            $log->debug($exception->getMessage());
            //print(sprintf('error: %s', $exception->getMessage()) . PHP_EOL);
            //print(sprintf('class: %s', get_class($exception)) . PHP_EOL);
            //print(sprintf('trace: %s', $exception->getTraceAsString()) . PHP_EOL);
        }

        // тут заканчиваем добовлять поля ГРР 20.02.2022


        //------------------------------- добавление раздела товаров -------------------------------



        $xmlId = 'CS_REAL_ESTATE_CATALOG';
        $name = 'Объекты недвижимости';

        //получение уже существующих разделов каталога
        $application->obB24Batch->addProductSectionsListCall(
            0,
            array("SORT" => "ASC"),
            $arSelect = array(),
            $arFilter = array(
                "CATALOG_ID" => $application->catalogId
            )
        );


        $res = $application->obB24Batch->call();
        $arResSections = $res[0]["data"];
        $isSectionExist = false;

        foreach ($arResSections as $section) {
            if ($section["XML_ID"] === 'CS_REAL_ESTATE_CATALOG') {
                $isSectionExist = true;
                break;
            }
        }

        //добавление раздела каталога в облако
        if (!$isSectionExist) {
            $fields = array(
                "CATALOG_ID" => $application->catalogId,
                "NAME" => $name,
                "XML_ID" => $xmlId,
            );
            $result = $application->obB24Product->addProductSection($fields);

            if ($result["result"]) {
                $application->addLog('Добавлен раздел каталога ' . $xmlId . ' (' . $name . '), id = ' . $result["result"]);
            }

            if ($result["error"]) {
                $application->error_count++;
                $application->addLog('Ошибка добавления раздела каталога ' . $xmlId . ' (' . $name . ')' . PHP_EOL . print_r($result["error"], true));
                $application->error_message .= 'Ошибка добавления раздела каталога ' . "\n";
            }
        } else {
            $application->addLog('Раздел каталога с XML_ID = ' . $xmlId . ' уже существует');
        }

        //------------------------- установка поля Объект недвижимости ---------------

        $application->addLog("установка поля Объект недвижимости");
        $handlerUrl = $domainApp . '/pub/cassoftApp/brokci/object.php';
        $type = 'object_brokci';
        $propCode = 'UF_CRM_CS_DEAL_OBJECT_BUT'; //max length with prefix UF_CRM_ 20 char
        $method = 'userfieldtype.add';

        $params = array(
            'USER_TYPE_ID' => 'object_brokci',
            'HANDLER' => $domainApp . '/pub/cassoftApp/brokci/object.php',
            'TITLE' => 'Объект недвижимости',
            'OPTIONS' => array(
                'height' => 100,
            ),
            'DESCRIPTION' => 'Поле по созданию и редактированию объекта недвижимости ' . $type
        );


        //$resultDell = $application->arB24App->call('userfieldtype.delete', $params);
        $resultAdd = $application->arB24App->call('userfieldtype.list');
        $application->addLog(print_r($resultAdd, true));
        if ($resultAdd['result']['0']) {
            foreach ($resultAdd['result'] as $key => $value) {
                if ($value['USER_TYPE_ID'] == $type and $value['HANDLER'] == $handlerUrl) {
                    //echo "есть такой тип поля";
                } else {

                    $resultAddPropType = $application->arB24App->call($method, $params);
                }
            }
        } else {
            $resultAddPropType = $application->arB24App->call($method, $params);
        }


        $resultAdd = $application->arB24App->call('userfieldtype.list');

        $application->addLog('результат добавления '  . print_r($resultAdd, true));

        if ($resultAddPropType['result'] == true) {
            $userfieldList = $application->arB24App->call('crm.deal.userfield.list', array(
                'filter' => array(
                    'FIELD_NAME' => "UF_CRM_CS_DEAL_OBJECT_BUT"
                )
            ));
            //echo 'property type ' . $type . ' успешно установлен <br>';
            if ($userfieldList['result']['0']) {
                $application->addLog('Пользовательское поле уже создано '  . print_r($resultAddPropType, true));
            } else {
                $application->addLog(date('d.m.Y H:i:s') . 'property type ' . $type . ' успешно установлен <br>');


                $resultAddProp = $application->arB24App->call(
                    'crm.deal.userfield.add',
                    array(
                        'fields' => array(
                            'USER_TYPE_ID' => $type,
                            'FIELD_NAME' => $propCode,
                            'XML_ID' => $propCode,
                            'MANDATORY' => 'N',
                            'SHOW_IN_LIST' => 'Y',
                            'EDIT_IN_LIST' => 'Y',
                            'EDIT_FORM_LABEL' => 'Объект недвижимости CS',
                            'LIST_COLUMN_LABEL' => 'Привязка объекта недвижимости',
                            'SETTINGS' => array()
                        )
                    )
                );
                $application->addLog('Пользовательское поле создано '  . print_r($resultAddPropType, true));
            }
        }

        // --------------регистрая обработчика для для слайдера ------------------

        $placement = 'REST_APP_URI';
        $url = $domainApp . '/pub/cassoftApp/brokci/pages/formObject.php';


        $bindGet = $application->arB24App->call('placement.get');

        if ($bindGet['result']['0']) {
            foreach ($bindGet['result'] as $key => $value) {
                if ($value['placement'] == $placement and $value['handler'] == $url) {
                    $application->addLog('Есть событие '  . print_r($value['placement'], true));
                } else {
                    $bindAdd = $application->arB24App->call(
                        'placement.bind',
                        [
                            'PLACEMENT' => $placement,
                            'HANDLER' => $url
                        ]
                    );
                    $application->addLog('Создано событие  '  . print_r($placement, true));
                }
            }
        } else {
            $bindAdd = $application->arB24App->call(
                'placement.bind',
                [
                    'PLACEMENT' => $placement,
                    'HANDLER' => $url
                ]
            );
            $application->addLog('Создано событие  '  . print_r($placement, true));
        }



        //------------------------------- регистрация обработчика события изменения сделки -------------------------------


        //поиск уже зарегистрированных обработчиков
        $resEvents = $application->obB24Event->getEventList();
        $isEventExist = false;
        if ($resEvents["error"]) {
            $application->error_count++;
            $application->addLog('Ошибка поиска существующих обработчиков ' . PHP_EOL . print_r($resEvents["error"], true));
            $application->error_message .= 'Ошибка поиска существующих обработчиков ' . "\n";
        } else {
            foreach ($resEvents["result"] as $event) {
                if (($event["handler"] == $application->dealUpdateHandler) && ($event["event"] == 'ONCRMDEALUPDATE')) {
                    $isEventExist = true;
                    $application->addLog('Обработчик события изменения сделки уже существует');
                }
            }
        }



        //регистрация обработчика
        if (!$isEventExist) {
            $result = false;
            try {
                $result = $application->obB24Event->bindEvent('onCrmDealUpdate', $application->dealUpdateHandler);
            } catch (Exception $e) {
                $application->error_count++;
                $application->addLog('Ошибка регистрации обработчика события изменения сделки ' . $e->getMessage());
                $application->error_message .= 'Ошибка регистрации обработчика события изменения сделки ' . "\n";
            }

            if ($result["result"]) {
                $application->addLog('Зарегистрирован обработчик события изменения сделки');
            } elseif ($result["error"]) {
                $application->error_count++;
                $application->addLog('Ошибка регистрации обработчика события изменения сделки ' . PHP_EOL . print_r($resEvents["error"], true));
                $application->error_message .= 'Ошибка регистрации обработчика события изменения сделки ' . "\n";
            }
        }

        //------------------------------- окончание работы скрипта -------------------------------

        if ($application->error_count > 0) {
            $ajaxResult = array(
                "error" => 1,
                "message" => $application->error_message
            );
        } else {
            $ajaxResult = array(
                "result" => 1,
            );
        }

        $application->addLog('Всего добавлено свойств: ' . $application->add_count . ' Ошибок добавления: ' . $application->error_count);
        $application->addLog(date('d.m.Y H:i:s') . ' Окончание работы скрипта по добавлению свойств товара');
        $application->addLog(print_r($ajaxResult, true));
        //$ajaxResult2 = "{result:1}";
        //json_encode

        //$ajaxResult = [result=>1];
        echo json_encode($ajaxResult);
    }
}
