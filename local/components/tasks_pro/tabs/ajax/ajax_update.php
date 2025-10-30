<?php

    define(NOT_CHECK_PERMISSIONS, true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");

    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/ajaxUpdate.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    $arParams = json_decode($_POST['request'], true);
$_REQUEST = json_decode($arParams['UserAut'], true);
    $memberId = $arParams['member_id'];
    $CloudApp = $arParams['app'];
d($arParams);
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
//$application = $auth->core->call('entity.item.get', ['ENTITY'=> 'application','SORT'=> [],'FILTER'=> ['ID'=>3006]] )->getResponseData()->getResult()->getResultData()[0];
//d($application);
//$appInstall = new \CSlibs\B24\Install\appInstall($auth, $arParams['app']);
////$result['placement'] = $appInstall->placementInstall();
//$result['event'] = $appInstall->eventInstall();
//d($result);

//$application = $auth->CScore->call('crm.status.list',[ 'filter' =>['ENTITY_ID' => 'DYNAMIC_180_STAGE_30']]);
$application = $auth->CScore->call('crm.type.get',[ 'id' =>60]);
d($application);

//$HlPropertyType = new \Cassoft\Services\HlService('app_auth_params');
//$installApp = $HlPropertyType->installApp($CloudApp);
////d($instalApp);
//$HlEntities = new \Cassoft\Services\HlService('entity_list');
//$entities = $HlEntities->entityList($instalApp['UF_ENTITY']);
//$HlField = new \Cassoft\Services\HlService('install_user_field_type');
//$fieldType = $HlField->getByIdList($instalApp['UF_USER_FIELD_TYPE']);
//d($fieldType);

//$propHandlerUrl ="https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/";
//foreach ($fieldType as $key =>$fieldsVal) {
//    $fieldsParams = [];
//    $options = [];
//    d($fieldsVal['UF_OPTIONS']);
//    foreach ($fieldsVal['UF_OPTIONS'] as $keyOp) {
//        d($keyOp);
//        $resOptions = explode(":", $keyOp);
//        d($resOptions);
//        $options[$resOptions[0]] = $resOptions[1];
//    }
//    $fieldsParams = [
//        'USER_TYPE_ID' => $fieldsVal['UF_USER_TYPE_ID'],
//        'HANDLER' => $propHandlerUrl . $fieldsVal['UF_HANDLER_FILE'],
//        'TITLE' => $fieldsVal['UF_TITLE'],
//        'OPTIONS' => $options,
//        'DESCRIPTION' => $fieldsVal['DESCRIPTION'],
//    ];
//    d($fieldsParams);
//$placementParams = [
//    'PLACEMENT' => 'REST_APP_URI',
//    'HANDLER' => 'https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/appUri.php',
//    'TITLE' => 'Открытие приложения в слайдере',
//    'DESCRIPTION' => ''
//];
//$addPlacement = $auth->CScore->call('placement.bind', $placementParams);
//   $userfieldTypeList = $auth->CScore->call('placement.get');
//    d($userfieldTypeList);

//cs_request_form
//    $HlFieldsSmart = new \CSlibs\B24\HL\HlService('install_fields_smarts');
//        $smartFieldsSmartAll=[];
//        $smartFieldsSmartAll = $HlFieldsSmart->getByIdList(82);
//        d($smartFieldsSmartAll);

$paramsFields = [
    'moduleId' => 'crm',
    'field'=>[
        'entityId' => 'CRM_46',
        'fieldName' => "UF_CRM_46_CS_REQUEST_FORM",
        'userTypeId' => 'rest_4_cs_request_form',
        'xmlId' => 'CS_REQUEST_FORM',
        'sort' => 200,
        'multiple' => 'N',
        'mandatory' => 'N',
        'showFilter' => 'N',
        'showInList' => 'Y',
        'editInList' => 'N',
        'isSearchable' => 'N',
        'editFormLabel' => [
            "ru" => "tets form",
        ],
        'settings' => "",
        'enum' => ""
    ]
];



//$smartfieldAdd = $auth->CScore->call("app.info")['ID'];
//d($smartfieldAdd);

/*$filter=[
  //  'order'=>[],
    'filter'=>[
       // 'FIELD_NAME' => 'UF_CRM_CS_TYPE_DT'
         'USER_TYPE_ID' => 'enumeration'
    ]];
$order =[];
*/

//d($fieldsUserGuide);

//$fields = $CSRest->call('crm.deal.userfield.list',['filter'=>['USER_TYPE_ID' => 'enumeration']])['result'];
//foreach ($fields as $kfields){
//    foreach ($kfields['LIST'] as $kList){
//        $guide[$kfields['FIELD_NAME']][$kList['ID']]=$kList['VALUE'];
//    }
//}
//$HlTypeFields = new \CSlibs\B24\HL\HlService('crm_userfield_types');
//$typeFields = $HlTypeFields->makeFieldToFieldSmart("ID", "UF_CODE");
//d($typeFields);

//$userfield = $auth->CScore->call("userfieldconfig.list", ['moduleId'=>"crm", 'filter'=>['entityId'=>'CRM_46',]] );
////$userfield = $CSRest->call("userfieldconfig.getTypes",['moduleId'=>"crm"] );
//d($userfield);
//$userfield = $CSRest->call("crm.type.list", $params );
//d($userfield);

//    if (!empty($userfieldTypeList)) {
//        foreach ($userfieldTypeList as $key => $value) {
//            $y++;
//          //  d($value);
//            if ($value['USER_TYPE_ID'] == $fieldsParams['USER_TYPE_ID'] and $value['HANDLER'] == $fieldsParams['HANDLER']) {
//                $searchType = true;
//                d('true');
//
//            }
//        }
//    }
//
//}
//$fields = $auth->core->call('crm.item.list', ['entityTypeId'=> 130, 'select'=>['*'], 'order'=> [], 'filter'=> [], 'start'=> 0])->getResponseData()->getResult()->getResultData();
/*$filter=[];
foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'setup', [], $filter, 6000) as  $value) {
    d($value);
}
$bath =$auth->batch->getTraversableListEntity('entity.item.get', 'setup', [], $filter, 6000);
d($bath);
$filter = [
    //  'COMPANY_TYPE' => 'CS_PROVIDER'
    'LOGIC' => 'AND',
    array('COMPANY_TYPE' => 'CS_PROVIDER'),
    array('COMPANY_TYPE' => 'CS_PROVIDER_CLIENT')
];
//$list = $CSRest->call("crm.company.list", ["filter"=>$filter]);

d($list);
    //установка хранилищ
//$entityInstall= entityInstall($CSRest, $arParams['app'], $arEntityInstall);
d($entityInstall);
//$entityList = $CSRest->call('entity.get');
d($entityList);
*/
//$smartUp = $CSRest->call("crm.item.update", ['entityTypeId' => 130, 'id' => 572, 'fields' => ['parentId162' => false, 'ufCrm18CsDateChange' => strtoTime(date("d.m.YTH:i:s"))]]);

//загрузка шаблонов документов
/*
$file = base64_encode(file_get_contents("/pub/cs.docx"));
//$smartDecimal = "T" . dechex(130);
$smartDecimal = dechex(130);
$fieldsDoc=[
    'name' => 'Базовый договор СмартCw', //название шаблона (обязательное).
//'file' => $file, //контент файла, закодированный в base64 (обязательное). Как альтернативу, контент файла можно передать в multipart / form-data. В этом случае его не надо кодировать в base64.
'file' => base64_encode(file_get_contents('/home/bitrix/www/pub/cs.docx')),
'code' => 'CS_DOC_BASE_DSw', //символьный код шаблона.
'numeratorId' => 1, //идентификатор нумератора (обязательное).
   'entityTypeId'=> ['D_162_12', 2, $smartDecimal, "D162", 'd_130', "D_131" ],
  //  'entityTypeId'=> 'DYNAMIC_8',
  //  'entityTypeId'=> 'DYNAMIC_8',
'region' => 'ru', //страна (обязательное).
'users' => '', //массив видимости. По умолчанию пусто.
'active' => 'Y', //Y/N флаг активности. По умолчанию Y.
'withStamps' => 'Y', //Y/N ставить печати и подписи. По умолчанию N
'sort' => '1', //индекс сортировки.
];

$docAdd=$CSRest->call('crm.documentgenerator.template.add',['fields'=>$fieldsDoc]);
d($docAdd);
$docList=$CSRest->call('crm.documentgenerator.template.list',['select' => ['entityTypeId', 'id'], 'filter' => ['id'=>82], 'order' => [], 'start' => 0]);
d($docList);
*/
//$quideApp=$CSRest->call('entity.item.property.get', ['ENTITY'=> 'application']);
 //d($quideApp);

//$quideCargo=$CSRest->call('entity.item.property.get', ['ENTITY'=> 'cargo']);
//d($quideCargo);
/*
$propHandlerUrl = 'https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/';
$userFieldsTypeAdd= userFieldsTypeAdd($CSRest, $userFieldTypeLogisticsPro, $propHandlerUrl);
d($userFieldsTypeAdd);
*/
//$fields = $CSRest->call('userfieldtype.list',['filter'=>[]])['result']; // Список пользовательских типов полей

//$fields = $CSRest->call('userfieldconfig.getTypes',['moduleId'=>'crm']); // Список всех доступных типов полей
//$fields = $auth->CScore->call('crm.deal.userfield.delete',['id'=> 1018]);
//$fields = $auth->CScore->call('crm.deal.userfield.list',['filter'=>[]]);
d($fields);
//$fields = $CSRest->call('crm.requisite.userfield.list',['filter'=>[]])['result'];
//$fields = $CSRest->call("crm.requisite.userfield.delete",['id'=> 1080])['result'];
//$fields = $CSRest->call('crm.userfield.list',['filter'=>[]])['result'];
//$fields = $CSRest->call("crm.requisite.preset.list",['filter'=>[]])['result'];
//$fields = $CSRest->call("crm.enum.ownertype",['filter'=>[]])['result'];
//$fields = $CSRest->call("crm.requisite.preset.field.fields")['result'];
//$fields = $CSRest->call("crm.requisite.fields")['result'];



//$fields = $CSRest->call("crm.deal.get",['id'=>136])['result'];
//$fields = $CSRest->call("crm.address.fields")['result'];

//$fields = $CSRest->call("task.item.userfield.gettypes",[])['result'];
/*
$fields = $CSRest->call('task.item.userfield.add',[
		'PARAMS'=>[

			'USER_TYPE_ID' => 'enumeration',
			'FIELD_NAME' => 'CS_LIST',
			'XML_ID' => 'CS_TASK_FIELD',
			'EDIT_FORM_LABEL' => ['en'=>'CS New task field', 'ru'=>'CS Новое поле список'],
			'LABEL' => 'New task field',
			"LIST"=> [
                    [ "VALUE"=> "WhatsApp" ],
                    [ "VALUE"=> "Telegram" ],
                    [ "VALUE"=> "Email" ],
                    [ "VALUE"=> "SMS" ],
                ],

	]]);

*/

//            ,'17' => [
//    'STATUS_ID' => 'QUILTY_LOADING_CLIENT',
//    'NAME' => 'Срыв по вине заказчика на погрузке',
//    'COLOR' => '#7d0e0e',
//    'SYSTEM' => 'N',
//    'SEMANTICS' => 'F',
//]
//            ,'18' => [
//    'STATUS_ID' => 'RETURN_LOADING',
//    'NAME' => 'Возврат товара в пункт загрузки',
//    'COLOR' => '#640b0b',
//    'SYSTEM' => 'N',
//    'SEMANTICS' => 'F',
//],



//                    $fieids = [
//                        "STATUS_ID"=> "PERFORMER_SEARCH",
//                        "ENTITY_ID"=> "DEAL_STAGE_20",
//
//"NAME"=> "Поиск перевозчика",
//            "SORT"=> 21,
//            "SYSTEM"=> "N",
//"COLOR"=> "#ace9fb",
//            "SEMANTICS"=> "",
//            "CATEGORY_ID"=>  20,
//                    ];

$fieids = [
//    "ENTITY_ID"=> "DYNAMIC_130_STAGE_34",
//    'STATUS_ID' => 'DT130_34:RETURN_LOADING',
//    'NAME' => 'Возврат товара в пункт загрузки',
//    "SORT"=> 18,
//    'COLOR' => '#640b0b',
//    'SYSTEM' => 'N',
//    'SEMANTICS' => 'F',
//

// "ENTITY_ID"=> "DYNAMIC_130_STAGE_34",
//    'STATUS_ID' => 'DT130_34:NLOADING_PERFORMER',
//    'NAME' => 'Срыв по вине Перевозчика на погрузке',
//    "SORT"=> 16,
//    'COLOR' => '#800000',
//    'SYSTEM' => 'N',
//    'SEMANTICS' => 'F',


    "ENTITY_ID"=> "DYNAMIC_130_STAGE_34",
    'STATUS_ID' => 'DT130_34:NLOADING_CLIENT',
    'NAME' => 'Срыв по вине Заказчика на погрузке',
    "SORT"=> 17,
    'COLOR' => '#7d0e0e',
    'SYSTEM' => 'N',
    'SEMANTICS' => 'F',
];
//            [ENTITY_ID] => DYNAMIC_157_STAGE_20
//[STATUS_ID] => DT157_20:NEW
//[NAME] => Документ создан
//[NAME_INIT] => Начало
//[SORT] => 10
//            [SYSTEM] => Y
//[COLOR] => #22b9ff
//            [SEMANTICS] =>
//            [CATEGORY_ID] => 20
            //Обновление
   //         $resStatusUp = $CSRest->call( "crm.status.add", ['fields' => $fieids]);

         //  $resStatusUp = $CSRest->call( "crm.status.delete", ['ID' => 1106])['result'];
  //  $resStatusUp = $CSRest->call( "crm.status.list", ['filter'=>['ENTITY_ID' => 'STATUS']])['result'];
//d($resStatusUp);

//$fields = $CSRest->call("crm.requisite.userfield.list",[])['result'];
//$fields = $CSRest->call("crm.company.userfield.list",['filter'=>['XML_ID' => 'CS_MESSAGE_CHANNEL']])['result'][0]['LIST'];
//$fields = $CSRest->call("user.userfield.delete",['id'=> 628])['result'];
//$fields = $CSRest->call('entity.item.property.delete',['ENTITY'=> 'cargo', 'PROPERTY'=> 'UF_CS_LOADING_DATES_TYPE']);
//$fields = $CSRest->call('entity.item.property.add',['ENTITY'=> 'stage_apps', 'PROPERTY'=> 'CS_COLOR', 'NAME'=> 'Цвет Стадии', 'TYPE'=> 'S']);
//$fields = $CSRest->call('entity.item.property.add',['ENTITY'=> 'car_base', 'PROPERTY'=> 'owner', 'NAME'=> 'Собственник физ.лицо', 'TYPE'=> 'S']);
//$fields = $CSRest->call('entity.item.property.add',['ENTITY'=> 'application', 'PROPERTY'=> 'UF_CS_STATUS_ATI', 'NAME'=> 'Статус ATI', 'TYPE'=> 'S']);
//$fields = $auth->core->call('entity.item.property.add',['ENTITY'=> 'application', 'PROPERTY'=> 'UF_CS_LOCALITY_UNLOAD', 'NAME'=> 'Населенный пункт разгрузки', 'TYPE'=> 'S']);
//$fields = $auth->core->call('entity.item.property.add',['ENTITY'=> 'application', 'PROPERTY'=> 'UF_CS_LOCALITY_LOAD', 'NAME'=> 'Населенный пункт загрузки', 'TYPE'=> 'S']);
//$fields = $auth->core->call('entity.item.property.add',['ENTITY'=> 'application', 'PROPERTY'=> 'UF_CS_REGION_LOAD', 'NAME'=> 'Регион загрузки', 'TYPE'=> 'S']);
//$fields = $auth->core->call('entity.item.property.add',['ENTITY'=> 'application', 'PROPERTY'=> 'UF_CS_REGION_UNLOAD', 'NAME'=> 'Регион разгрузки', 'TYPE'=> 'S']);
//$fieldsList = $auth->core->call('entity.item.property.get', ['ENTITY'=> 'application'])->getResponseData()->getResult()->getResultData();
//d($fieldsList);
//d($resStatusUp);

// -------------------------------------------------------------------------- install smart ---------------------------
/*
$params =[ 'select'=>['*'], 'order'=>[], 'filter'=>[], 'start'=> 0 ];
$resSmart = $CSRest->call("crm.type.list", $params )['result']['types'];
//$resSmartFields = $CSRest->call("crm.type.fields", ['id'=>6] )['result'];
//d($resSmartFields);
//d($resSmart);
foreach ($resSmart as $kSmart){
  //  d($kSmart['title']);
    $resSmartType = $CSRest->call("crm.type.get", ['id'=> $kSmart['id']])['result']['type'];

    $resSmartFields = $CSRest->call("crm.type.fields", ['id'=>$kSmart['id']] )['result'];
    $resCategory = $CSRest->call("crm.category.list", ['entityTypeId' =>$kSmart['entityTypeId'] ]); //создание категорий в смартпроцессах
    d($resCategory);
  //  d($resSmartType);

   // d($resSmartFields);
}

//d($guideSection);
$appFields = "UF_LOGISTICS_PRO";
$HlQ = new \Cassoft\Services\HlService('product_property_type');

$getVocabulary = $HlQ->makeIdToField('UF_CODE');
//d($getVocabulary);
$HlSmart = new \Cassoft\Services\HlService('install_smart');
$smartApp = $HlSmart->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => []
])->fetchAll();

foreach ($smartApp as $keyFields){
    if($keyFields[$appFields] == 1){
        $params =[
            'fields'=>[
        'entityTypeId'=>$keyFields['UF_ENTITY_TYPE_ID'],
        'title' => $keyFields['UF_TITLE'], // Счета поставщиков
                'code' => $keyFields['UF_CODE'], // accountProvider
                    'isCategoriesEnabled' => $keyFields['UF_IS_CATEGORIES_ENABLED'], // 1
            'isStagesEnabled'  => $keyFields['UF_IS_STAGES_ENABLED'], // 1
            'isBeginCloseDatesEnabled' => $keyFields['UF_IS_BEGIN_CLOSE_DATES_ENABLED'], // 1
            'isClientEnabled' => $keyFields['UF_IS_CLIENT_ENABLED'], // 1
            'isUseInUserfieldEnabled' => $keyFields['UF_IS_USE_IN_USERFIELD_ENABLED'], // 1
            'isLinkWithProductsEnabled' => $keyFields['UF_IS_LINK_WITH_PRODUCTS_ENABLED'], // 1
            'isMycompanyEnabled' => $keyFields['UF_IS_MYCOMPANY_ENABLED'], // 1
            'isDocumentsEnabled' => $keyFields['UF_IS_DOCUMENTS_ENABLED'], // 1
            'isSourceEnabled' =>  $keyFields['UF_IS_SOURCE_ENABLED'], // 1
            'isObserversEnabled' => $keyFields['UF_IS_OBSERVERS_ENABLED'], // 1
            'isRecyclebinEnabled' => $keyFields['UF_IS_RECYCLEBIN_ENABLED'], // 1
            'isAutomationEnabled' => $keyFields['UF_IS_AUTOMATION_ENABLED'], // 1
            'isBizProcEnabled' => $keyFields['UF_IS_BIZ_PROC_ENABLED'], // 1
            'isSetOpenPermissions' => $keyFields['UF_IS_SET_OPEN_PERMISSIONS'], // 0
            'isPaymentsEnabled' => $keyFields['UF_IS_PAYMENTS_ENABLED'], // 0
          'isCountersEnabled' => $keyFields['UF_IS_COUNTERS_ENABLED'], // 0

        ]
    ];
     //   d($params);
    }

}

$fieldsCat=[
    'name' => "Оплата по факту",
    'sort' => 20,
'isDefault' => 'N'
];


//$resCategory = $CSRest->call("crm.category.add", ['entityTypeId'=> 130, 'fields'=>$fieldsCat] ); //создание категорий в смартпроцессах

//d($resCategory);
//$fieldDel = $CSRest->call("crm.status.delete", ['id'=>1148]);
$fields=[
    "ENTITY_ID" => 'DYNAMIC_146_STAGE_50',
    'STATUS_ID' => 'DT146_50:WORK',
    'NAME' => 'Взято в работу',
    'NAME_INIT' => 'WORK',
    'SORT' => 20,
    'SYSTEM' => 'N',
    'COLOR' => '#88b9ff',
    'SEMANTICS' => ''
    ];

//$fieldAdd = $CSRest->call("crm.status.add", [ 'fields' =>$fields]); //создание стадий в смартпроцессах
*/

//$fieldAdd = $CSRest->call("disk.file.get",['id'=> 2204] );
//d($fieldAdd);
/*
 "crm.status.add",
		{

 *

$fieldAdd = $CSRest->call("crm.requisite.preset.field.add",['preset'=>["ID"=>5],"fields"=> ["FIELD_NAME"=> "UF_CRM_CS_VAT",
		"FIELD_TITLE"=>"С НДС","IN_SHORT_LIST"=>"Y","SORT"=> 580,]]);
d($fieldAdd);



/*
$isPlacementSliderExist = false;
$placementSliderParams = [
    'PLACEMENT' => 'REST_APP_URI',
    'HANDLER' => 'https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/formSlider.php',
    'TITLE' => $arParams['app'],
    'DESCRIPTION' => 'cassoft.ru'
];
$getPlacement = $request->call('placement.get');
if (!empty($getPlacement['0'])) {
    foreach ($getPlacement as $placement) {
        if (($placement['handler'] == $placementParams['HANDLER']) && ($placement['placement'] == $placementParams['PLACEMENT'])) {
            $isPlacementExist = true;
        }
        if (($placement['handler'] == $placementSliderParams['HANDLER']) && ($placement['placement'] == $placementSliderParams['PLACEMENT'])) {
            $isPlacementSliderExist = true;
        }
    }
}
if (!$isPlacementExist) {
    $addPlacement = $request->call('placement.bind', $placementParams);
}

if (!$isPlacementSliderExist) {
    $addPlacementSlider = $request->call('placement.bind', $placementSliderParams);
}

*/
//$fieldUp = $CSRest->call("crm.deal.update",['ID'=> 262, 'fields'=>["PARENT_ID_131"=> "1",], 'params'=> [ "REGISTER_SONET_EVENT"=> "N" ] ])['result'];
//d($fieldUp);
 //$fieldAdd = fieldsAdd($CSRest, $requisite_fields, "requisite"); //создание полей для сущности
   // $fieldAdd = fieldsAdd($CSRest, $quote_fields, "quote"); //создание полей для сущности
  //  $fieldAdd = fieldsAdd($CSRest, $deal_fields, "deal"); //создание полей для сущности
  //  $fieldAdd = fieldsAdd($CSRest, $lead_fields, "lead"); //создание полей для сущности
//    $fieldAdd = fieldsAdd($CSRest, $contact_fields, "contact"); //создание полей для сущности
  //  $fieldAdd = fieldsAdd($CSRest, $company_fields, "company"); //создание полей для сущности
  //  $fieldAdd = userFieldsAdd($CSRest, $user_fields); //создание полей пользователя
//d($fieldAdd);

//$smartProcess = new \Cassoft\SmartProcess\smartProcessAuth($auth, $smart);
//$guide=$smartProcess->smartGuide();
//d($guide);
//$entityTypeIdDoc = $guide['Договора (Заказчики)']['entityTypeId'];
//$entityTypeIdDocPr = $guide['Договора (Поставщики)']['entityTypeId'];
////d($entityTypeIdDoc);
//$entityTypeId = $entityTypeIdDocPr;
//$status=[];
//$smartCategory = $CSRest->call("crm.category.list", ["entityTypeId" => $entityTypeId])['result']['categories'];
//foreach ($smartCategory as $kSmart) {
////d($kSmart);
//$catG[$kSmart['id']]= $kSmart['name'];
//    $smartStatus = $CSRest->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $kSmart['id']]])['result'];
//   // d($smartStatus);
//    foreach ($smartStatus as $kStage) {
//        if (!$kStage['SEMANTICS']) {
//            if ($kStage['NAME'] !== 'Договор подписан' && $kStage['NAME'] !== 'Договор в работе' && $kStage['NAME'] !== 'Заканчивается срок договора' && $kStage['NAME'] !== 'Приложение подписано') {
//                $statusG[$kStage['STATUS_ID']]=$kStage['NAME'];
//                $status[] = $kStage['STATUS_ID'];
//
//            }
//        }
//    }
//}
//d($status);
////$smartStageIdStatusActiveDog = $smartProcess->smartStageIdStatusActiveDog($entityTypeIdDoc, 12);
////$categoryId = $smartCategoryDoc['Долгосрочные'];
////$categoryIdApp = $smartCategoryDoc['Приложение'];
//$import=[];
//$params = [
//    'entityTypeId' => $entityTypeId, 'select' => ['*'], 'order' => [],
//    'filter' => ['stageId' =>$status],
//    'start' => 0
//];
//$resSmartDocList = $CSRest->call("crm.item.list", $params);
//d($resSmartDocList['total']);
////$import=$resSmartDocList['result']['items'];
//foreach ($resSmartDocList['result']['items'] as $key => $itemSmartVal) {
//  //  d($itemSmartVal);
//    $import[$itemSmartVal['assignedById']]['Договора (Заказчики)'][$catG[$itemSmartVal['categoryId']]][$statusG[$itemSmartVal['stageId']]][$itemSmartVal['id']] = $itemSmartVal['title'];
//    $i++;
//}
//if($resSmartDocList['total']>0) {
//    $margin = ceil(($resSmartDocList['total']) / 50);
//d($margin);
//    $m = 1;
//    $start = 50;
//    for ($m = 1; $m <= $margin; $m++) {
//        $params['start'] = $start;
//        $resSmartDocList = $CSRest->call('crm.item.list', $params);
//        //echo '<pre>'; print_r($getSmart['total']);  echo '</pre>';
//
//       d("Цикл " . $m);
//
//        $start = $start + 50;
//
//        foreach ($resSmartDocList['result']['items'] as $key => $itemSmartVal) {
//            $import[$itemSmartVal['assignedById']]['Договора (Заказчики)'][$catG[$itemSmartVal['categoryId']]][$statusG[$itemSmartVal['stageId']]][$itemSmartVal['id']] = $itemSmartVal['title'];
//            $i++;
//        }
//    }
//}
//d($import);
//d($i);
//// $smartStageIdStatus = $smartProcess->smartStageIdStatus($smartDoc, $categoryId);
//$smartStageIdStatus = $smartProcess->smartStageIdStatusActiveDog($smartDoc, $categoryId);
//file_put_contents(__DIR__ . "/logExControl.txt", print_r(date("d.m.YTH:i:s") . "\n", true));
//file_put_contents(__DIR__ . "/logExControl.txt", print_r($smartStageIdStatus, true), FILE_APPEND);
//// file_put_contents(__DIR__ . "/logExControl.txt", print_r($smartDocList, true), FILE_APPEND);
//
//if(!empty($smartDocList)) {
//file_put_contents(__DIR__ . "/logExControl.txt", print_r("smartDocList", true), FILE_APPEND);
//foreach ($smartDocList as $keyDL) {
//    $idDoc = $keyDL['id'];
//    $docAppAll = $keyDL['ufCrm' . $smartDocId . 'CsAppsBasisDoc'];
//    if($smartStageIdStatus[$keyDL['stageId']]) {
//        $resActive = $smartStageIdStatus[$keyDL['stageId']];
//    }
//}
//
//d($smartStageIdStatusActiveDog);
//$guide = $smartProcess->smartGuide(); //справочник смартпроцессов
//d($smart_fields);
//$userfieldAdd = $smartProcess->smartFieldsAdd($smart_fields);//создание полей для смарта

//d($userfieldAdd);

//d($userfieldAdd);

//$fields = $CSRest->call("crm.item.fields", ['entityTypeId'=> 162])['result'];

//$params=[
//    'PARENT_ID_162'=> false
//];
//$smartUp = $CSRest->call("crm.item.update",['entityTypeId' => 130, 'id' => 474, 'fields' => $params]);
d($smartUp);
/*

//$userfield = $CSRest->call('entity.item.property.delete', ['ENTITY'=> 'cargo', 'PROPERTY'=>'UF_CS_START_DATE'])['result'];
d($guide);


////$dealCategoryAdd = dealAddCategory($auth, $category, $statusDeals ); // сощдаем категории и  стадии сделок

  //  $smartsList = $CSRest->call("crm.type.list" );
  // $userfield = $CSRest->call("crm.userfield.fields" );
  // $userfieldType = $CSRest->call("crm.userfield.types" )['result'];

//[STATUS_ID] => SENT
/*
$filter =[
    'filter'=>["ENTITY_ID"=> 'QUOTE_STATUS',
        "NAME" => 'Предложение отправлено', ]
];
$statusList = $CSRest->call("crm.status.list",$filter )['result'][0]['STATUS_ID'];
d($statusList);
 //  d($fieldAdd);
   // crm.type.list({order: ?{} = null, filter: ?{} = null, start: ?number = 0})
$smart ='130';

  //  $tabs = bindSmartAdd($auth, $smart); //вкладка в смарт-процуссе
   // $tabs = bindAdd($auth);

/*                                                                                  //Справочник типов компании
    foreach ($statusCompany as $key =>$value){
        d($value);

        $filter =[
            'filter'=>[
                "ENTITY_ID"=> $value["fields"]["ENTITY_ID"],
                "STATUS_ID"=> $value["fields"]["STATUS_ID"],
                 ]
];
        $statusList = $CSRest->call("crm.status.list",$filter );

        if(empty($statusList['result'])){
            $statusAdd = $CSRest->call("crm.status.add",$value );
            d($statusAdd);
        }else{
            $value['id']=$statusList['result']['0']['ID'];
            $statusAdd = $CSRest->call("crm.status.update",$value );
        }
    }

                                                //Справочник типов контакта
    foreach ($statusContact as $key =>$value){
        d($value);

        $filter =[
            'filter'=>[
                "ENTITY_ID"=> $value["fields"]["ENTITY_ID"],
                "STATUS_ID"=> $value["fields"]["STATUS_ID"],
                 ]
];
        $statusList = $CSRest->call("crm.status.list",$filter );

        if(empty($statusList['result'])){
            $statusAdd = $CSRest->call("crm.status.add",$value );
            d($statusAdd);
        }else{
            $value['id']=$statusList['result']['0']['ID'];
            $statusAdd = $CSRest->call("crm.status.update",$value );
        }
    }

     //   order: { "SORT": "ASC" },
       // filter: { "MANDATORY": "N" }


//({order: ?{} = null, filter: ?{} = null, start: ?number = 0}
//$smartList = $CSRest->call("crm.type.list");
/*
foreach ($dealFielLogistics as $key =>$fields){
    d($fields);
    $dealFieldAdd = $CSRest->call("crm.deal.userfield.add",$fields);
    d($dealFieldAdd);
}

*/
//список шаблонов реквизитов
//$presetList = $auth->core->call("crm.requisite.preset.list",['order'=>[], 'filter'=>[], 'select'=>[],])->getResponseData()->getResult()->getResultData();
//$presetList = $CSRest->call("crm.requisite.preset.list",['order'=>[], 'filter'=>[], 'select'=>[],])['result'];
//$addresstypeList = $CSRest->call("crm.enum.addresstype",[])['result'];
//$smartStatus  = $CSRest->call('crm.status.list',['filter'=>[ 'ENTITY_ID' => 'DYNAMIC_131_STAGE_38']])['result'];
//d($smartStatus);

 /*
 *
 *
 *
    $filter=[];
//$entity = 'cargo';
//$entity = 'application';
$entity = 'payment_terms';
d($entity);
foreach ( $auth->batch->getTraversableListEntity(
        'entity.item.get',
       // 'application',
    $entity,
        [],
        $filter,
        6000
    ) as $value
) {
  ' => '', //usleep(30000);-
    $dell=$CSRest->call('entity.item.delete', [
        'ENTITY'=> $entity,
	'ID'=> $value['ID']
    ]);
d($value['ID']);
}
*/
//$CSRest->call("crm.documentgenerator.template.getfields",['id'=> 4, 'entityTypeId'=>31, 'values' => []]);

//////////////////---------- создание евнта на событие-----------------------
///
/*
    $handler = "https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/";
    foreach ($event as $keyEvent => $valEvent) {
        $isEventExist = false;
        $eventParmas = [];
        $eventParmas = [
            'event' => $keyEvent,
            'handler' => $handler . $valEvent
        ];

         // d($eventParmas);
      //  $eventGet = $auth->core->call('event.get')->getResponseData()->getResult()->getResultData();
         // d($eventGet);
       // foreach ($eventGet as $event) {
         /*
             $eventDel = [
                'event' => $event['event'],
                'handler' => $event['handler']
            ];
            $addEvent = $auth->core->call('event.unbind', $eventDel)->getResponseData()->getResult()->getResultData();
        }
           */
          /*  if (($event['handler'] == $handler . $valEvent) && ($event['event'] == $keyEvent)) {
                $isEventExist = true;
                   d("true");
                break;
            }
        }
        if (!$isEventExist) {

            $addEvent = $auth->core->call('event.bind', $eventParmas)->getResponseData()->getResult()->getResultData();
            // d("add");
            d($addEvent);
      //  }
    }


    //----------------

    /*
        //SUPPLIER поставщик
        //  [ENTITY_ID] => COMPANY_TYPE
        //[STATUS_ID] => SUPPLIER
        // d($_POST);
        $resCategoryList = $auth->core->call(
        //   "catalog.documentcontractor.list",
        //      "crm.company.list",
            "crm.status.entity.types",
         array(
             "select" => ["*"],
             "filter" => ['entityTypeId' => 3],
             "order" => [],
         )
        )->getResponseData()->getResult()->getResultData();
        //  d($resCategoryList);
        $resStatusList = $auth->core->call(
            "crm.status.list",
            array(
                'order' => ["SORT" => "ASC"],
                'filter' => ["ENTITY_ID" => "COMPANY_TYPE",]
                //     'filter' => []
            )
        )->getResponseData()->getResult()->getResultData();
        d($resStatusList);

        // $token = 'f44d3cb9238b4aa2941216854cac56f8'; //a3d9943e3b454bddbdf630e4027c95f2
        /*  $token = 'a3d9943e3b454bddbdf630e4027c95f2';
          $CSCurl = new \CSCurl($token);
          if ($_POST['event']) {
              $arAccount = $CSCurl->callEvent($_POST['event']);
          } else {
              $arAccount = $CSCurl->callAccount();
          }
      */
    // $debug->printR($CSCurl, "CSCurl");
    // $debug->printR($arAccount, "Result");


    //  require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/install/logistics_pro/updateApp/test.php");
    // require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/install/logistics_pro/updateApp/updateTabs.php");

    //  $resultInstall = json_encode($arAccount);
    //  echo $resultInstall;
?>

