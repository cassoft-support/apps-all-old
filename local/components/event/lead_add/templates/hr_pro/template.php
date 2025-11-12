<?php
define(NOT_CHECK_PERMISSIONS, true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");

$date = date("d.m.YTH:i");
$log = __DIR__ . "/logLeadAdd.txt";
p('start', 'start', $log);
p($_REQUEST, 'reg', $log);
p($arParams, 'params', $log);
$memberId = $arParams['auth']['member_id'];
$CloudApp = $arParams['app'];
//d($arParams);
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
$lead = $auth->CScore->call('crm.lead.get', ['ID'=> $arParams['laedId']]);
p($lead, 'lead', $log);
$stageNew = $auth->CScore->call('entity.item.get', ['ENTITY'=> 'stage', 'filter'=>['PROPERTY_CS_TYPE_STAGE' => 'new_action']])[0]['ID'];


$setup = $auth->CScore->call('entity.item.get', ['ENTITY'=> 'setup'])[0];
$hr = json_decode($setup['PROPERTY_VALUES']['CS_RECRUITER'], true);
//d($hr);
$smartProcess = new \CSlibs\B24\Smarts\SmartProcess($auth, []);
$guide = $smartProcess->smartGuide();
//d($guide);
$smart = $guide['Найм персонала'];
$arStatus = $auth->CScore->call('crm.status.list',['filter'=> [ "ENTITY_ID"=> "STATUS" ]]);
foreach ($arStatus as $status){
    $resStatus[$status['NAME']]=$status['STATUS_ID'];

}

/*
foreach ($auth->batch->getTraversableList('crm.lead.list', ['ID'=>'ASC'], ['ID'=>1623,'STAGE_SEMANTIC_ID' => 'P', 'ASSIGNED_BY_ID'=>$hr], ['*'], 6000) as $arLead) {
$i++;
//d( $arLead);

//$arContact = $auth->CScore->call('crm.contact.get',['ID' => $arDeal['CONTACT_ID']]);

    $y++;
   // d($arStatus);
    if($arLead['NAME']){
        $title= "Соискатель " . $arLead['NAME']." ".$arLead['LAST_NAME']." тел.".$arLead['PHONE'][0]['VALUE'];
    }else{
        $title=$arLead['TITLE'];
    }
 $paramSmart = [
        "entityTypeId" => $smart['entityTypeId'],
        "fields" => [
            'title' => "Лид №" . $arLead['ID'] . "/ ".$title ,
            'parentId1' => $arLead['ID'],
            //'contactId' => $arContact['ID'],
            'assignedById' => $arLead['ASSIGNED_BY_ID'],//ответственный
            'categoryId' => 15,//категория
            'ufCrm' . $smart['id'] . 'CsName' => $arLead['NAME'],
            'ufCrm' . $smart['id'] . 'CsLastName' => $arLead['LAST_NAME'],
            'ufCrm' . $smart['id'] . 'CsPhone' => $arLead['PHONE'][0]['VALUE'],

        ]
    ];
d($paramSmart);
   $smartIdRes = $auth->CScore->call("crm.item.add", $paramSmart);
    d($smartIdRes);
    $smartId = $smartIdRes['item']['id'];
d($smartId);

$propertyParam = [
   'ENTITY' => 'candidates',
    'NAME' => $title,
	'PROPERTY_VALUES'=> [
    'name' => $arLead['NAME'],
    'last_name' => $arLead['LAST_NAME'],
    'second_name' => $arLead['SECOND_NAME'],
    'contact_id' => "",
    'lead_id' => $arLead['ID'],
    'smart_id' => $smartId,
    'deal_id' => "",
    'password' => "",
    'code_auth' => "",
    'phone' => json_encode(['0'=>$arLead['PHONE'][0]['VALUE']]),
    'email' => "",
    'phone_check' => "",
    'email_check' => "",
    'code_email_auth' => "",
    'date_auth' => "",
    'birthdate' => "",
    'photo' => "",
    'personal_check' => "",
    'inn' => "",
    'resume' => "",
    'cover_letter' => "",
    'assigned_by_id' => $arLead['ASSIGNED_BY_ID'],
    'stage' => "",
    'vacancy' => "",
    ]
];
$itemAdd=$auth->CScore->call('entity.item.add', $propertyParam)[0];
d($propertyParam);
d($itemAdd);
if($itemAdd){
    $paramsLead=[
        'ID'=> $arLead['ID'],
		'fields'=>[
        'STAGE_SEMANTIC_ID' => 'F',
        'STATUS_ID'=> $resStatus['Соискатель'],
            'PARENT_ID_'.$smart['entityTypeId'] => $itemAdd
    ],
		'params'=> [ "REGISTER_SONET_EVENT"=> "N" ]
    ];
   $leadUp=$auth->CScore->call('crm.lead.update', $paramsLead);
    d($leadUp);
}

}