<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__."/component.txt";
p($arParams, "start", $log);
if($arParams['member_id'] && $arParams['app']) {

    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
  
    $arResult = [];
    $arResult["app"] = $arParams['app'];
if($arParams['type'] === 'smart') {
    //  $clientAppAdm = getAuthHl($arParams['app'], $arParams['auth']['member_id']);
    //  $authAdm = new Auth($arParams['app'], $clientAppAdm, __DIR__ . '/');
    $params = json_decode($arParams['~PLACEMENT_OPTIONS'], true);
    //  $CSRest = new \CSRest($arParams['app']);
    $smartId = explode('_', $params['ENTITY_ID'])[1];
    $paramsType = ['select' => ['*'], 'order' => [], 'filter' => ['id' => $smartId], 'start' => 0];
    $entityTypeId = $auth->CScore->call("crm.type.list", $paramsType)['types'][0]['entityTypeId'];
    $arResult["id"] = $arParams['id'];
    $arResult["smartId"] = $smartId;
    $arResult["entityTypeId"] = $entityTypeId;
    $smart = $auth->CScore->call("crm.item.get", array('entityTypeId' => $entityTypeId, 'id' => $arParams['id']))['item'];
    //  d($smart['ufCrm'.$smartId.'CsScanDoc']);
    $arPhoto=$smart['ufCrm' . $smartId . 'CsScanDoc'];
//pr($arPhoto);
} else {
    $arResult["id"] = $arParams['id'];
    $element = $auth->CScore->call("crm.".$arParams['type'].".get", array('id' => $arParams['id']));
//pr($element, '');
    $arPhoto = $element['UF_CRM_CS_SCAN_DOC'];
}
p($arPhoto, "arPhoto", $log);
    $arResult["scanDoc"]=json_encode([]);
if($arPhoto) {
    $resPhoto=json_decode($arPhoto, true);
    $arResult["scanDoc"] = $arPhoto;
    $arResult["link"] = json_decode($arPhoto, true);;
}
    $arResult["type"] = $arParams['type'];
//pr($arResult);
    $this->IncludeComponentTemplate();

}