<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Install/tools.php");

$date=date("d.m.YTH:i");
$file_log = __DIR__."/component.txt";

if($arParams['member_id']) {
    $memberId = $arParams['member_id'];
   /* if($arParams['auth']){
        $clientApp = [
            'DOMAIN' => $arParams['auth']['domain'],
            'member_id' => $arParams['auth']['member_id'],
            'AUTH_ID' => $arParams['auth']['access_token'],
            'REFRESH_ID' => ""
        ];
    }*/
    if($arParams['AUTH_ID']){
        $clientApp = [
            'DOMAIN' => $arParams['DOMAIN'],
            'member_id' => $arParams['member_id'],
            'AUTH_ID' => $arParams['AUTH_ID'],
            'REFRESH_ID' => $arParams['REFRESH_ID'],
        ];
    }
if($clientApp) {
    $auth = new Auth($arParams['app'], $clientApp, __DIR__ . '/');

    $arResult = [];
    $arResult["app"] = $arParams['app'];
if($arParams['type'] === 'smart') {
    //  $clientAppAdm = getAuthHl($arParams['app'], $arParams['auth']['member_id']);
    //  $authAdm = new Auth($arParams['app'], $clientAppAdm, __DIR__ . '/');
    $params = json_decode($arParams['~PLACEMENT_OPTIONS'], true);
    //  $CSRest = new \CSRest($arParams['app']);
    $smartId = explode('_', $params['ENTITY_ID'])[1];
    $paramsType = ['select' => ['*'], 'order' => [], 'filter' => ['id' => $smartId], 'start' => 0];
    $entityTypeId = $auth->core->call("crm.type.list", $paramsType)->getResponseData()->getResult()->getResultData()['types'][0]['entityTypeId'];


    $arResult["smartElId"] = $arParams['id'];
    $arResult["smartId"] = $smartId;
    $arResult["entityTypeId"] = $entityTypeId;
    $smart = $auth->core->call("crm.item.get", array('entityTypeId' => $entityTypeId, 'id' => $arParams['id']))->getResponseData()->getResult()->getResultData()['item'];
    //  d($smart['ufCrm'.$smartId.'CsScanDoc']);
    $arPhoto=$smart['ufCrm' . $smartId . 'CsScanDoc'];
//d($arResult);
} elseif($arParams['type'] === 'deal'){
    $arResult["deal_id"] = $arParams['deal_id'];
    $deal = $auth->core->call("crm.deal.get", array('id' => $arParams['deal_id']))->getResponseData()->getResult()->getResultData();

    $arPhoto=$deal['UF_CRM_CS_SCAN_DOC'];
} elseif($arParams['type'] === 'contact'){
    $arResult["contact_id"] = $arParams['contact_id'];
    $contact = $auth->core->call("crm.contact.get", array('id' => $arParams['contact_id']))->getResponseData()->getResult()->getResultData();

    $arPhoto=$contact['UF_CRM_CS_SCAN_DOC'];
}elseif($arParams['type'] === 'company'){
    $arResult["company_id"] = $arParams['company_id'];
    $company = $auth->core->call("crm.company.get", array('id' => $arParams['company_id']))->getResponseData()->getResult()->getResultData();

    $arPhoto=$company['UF_CRM_CS_SCAN_DOC'];
}


    $arResult["scanDoc"]=json_encode([]);
if($arPhoto) {
    foreach ($arPhoto as $photo) {

        //   d($photo);
        $photoInfo = explode('|', $photo);
        $resScanDoc[] = [
            'photo_id' => $photoInfo['0'],
            'photo_link' => $photoInfo['1'],
        ];
        $resLink[] = $photoInfo['1'];
    }
    $arResult["scanDoc"] = json_encode($resScanDoc);
    $arResult["link"] = $resLink;
}

    $this->IncludeComponentTemplate();
}
}