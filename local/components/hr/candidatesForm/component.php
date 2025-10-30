<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$log = __DIR__."/component.txt";
//d($arParams);
if($arParams['member_id'] && $arParams['app']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
    $arResult = [];
    $arResult["app"] = $arParams['app'];
    $arResult["smartElId"] = $arParams['id'];
    $arResult["smartId"] = $arParams['EntityId'];
    $arResult["member_id"] = $arParams['member_id'];

    $params = [
        'ENTITY' => 'candidates',
        'sort' => [],
        'filter' => [
            'PROPERTY_smart_id'=>$arParams['id']
        ],
    ];
    $resCandidates = $auth->CScore->call('entity.item.get', $params)[0];
    $candidates = $resCandidates['PROPERTY_VALUES'];
    $arResult["item"] = $candidates;
    $arResult["item"]['fio'] = $candidates['last_name']. " ".$candidates['name']." ".$candidates['second_name'];
    $arResult["item"]['id'] = $resCandidates['ID'];
//d($candidates);
 /*   [name] => Без имени
    [last_name] =>
    [second_name] =>
    [contact_id] => 1
    [password] =>
    [code_auth] =>
    [phone] => ["+78005502287"]
    [email] =>
    [phone_check] =>
    [email_check] =>
    [code_email_auth] =>
    [date_auth] =>
    [birthdate] =>
    [photo] =>
    [personal_check] =>
    [inn] =>
    [resume] =>
    [cover_letter] =>
    [assigned_by_id] => 41
    [stage] =>
    [vacancy] =>
    [lead_id] =>
    [smart_id] => 3
    [deal_id] => 1
 */
//if($arParams['type'] === 'smart') {
//    //  $clientAppAdm = getAuthHl($arParams['app'], $arParams['auth']['member_id']);
//    //  $authAdm = new Auth($arParams['app'], $clientAppAdm, __DIR__ . '/');
//    $params = json_decode($arParams['~PLACEMENT_OPTIONS'], true);
//    //  $CSRest = new \CSRest($arParams['app']);
//    $smartId = explode('_', $params['ENTITY_ID'])[1];
//    $paramsType = ['select' => ['*'], 'order' => [], 'filter' => ['id' => $smartId], 'start' => 0];
//    $entityTypeId = $auth->core->call("crm.type.list", $paramsType)->getResponseData()->getResult()->getResultData()['types'][0]['entityTypeId'];
//
//
//    $arResult["smartElId"] = $arParams['id'];
//    $arResult["smartId"] = $smartId;
//    $arResult["entityTypeId"] = $entityTypeId;
//    $smart = $auth->core->call("crm.item.get", array('entityTypeId' => $entityTypeId, 'id' => $arParams['id']))->getResponseData()->getResult()->getResultData()['item'];
//    //  d($smart['ufCrm'.$smartId.'CsScanDoc']);
//    $arPhoto=$smart['ufCrm' . $smartId . 'CsScanDoc'];
////d($arResult);
//} elseif($arParams['type'] === 'deal'){
//    $arResult["deal_id"] = $arParams['deal_id'];
//    $deal = $auth->core->call("crm.deal.get", array('id' => $arParams['deal_id']))->getResponseData()->getResult()->getResultData();
//
//    $arPhoto=$deal['UF_CRM_CS_SCAN_DOC'];
//} elseif($arParams['type'] === 'contact'){
//    $arResult["contact_id"] = $arParams['contact_id'];
//    $contact = $auth->CScore->call("crm.contact.get", array('id' => $arParams['contact_id']))->getResponseData()->getResult()->getResultData();
//
//    $arPhoto=$contact['UF_CRM_CS_SCAN_DOC'];
//}elseif($arParams['type'] === 'company'){
//    $arResult["company_id"] = $arParams['company_id'];
//    $company = $auth->CScore->call("crm.company.get", array('id' => $arParams['company_id']))->getResponseData()->getResult()->getResultData();
//
//    $arPhoto=$company['UF_CRM_CS_SCAN_DOC'];
//}
//
//
//    $arResult["scanDoc"]=json_encode([]);
//if($arPhoto) {
//    foreach ($arPhoto as $photo) {
//
//        //   d($photo);
//        $photoInfo = explode('|', $photo);
//        $resScanDoc[] = [
//            'photo_id' => $photoInfo['0'],
//            'photo_link' => $photoInfo['1'],
//        ];
//        $resLink[] = $photoInfo['1'];
//    }
//    $arResult["scanDoc"] = json_encode($resScanDoc);
//    $arResult["link"] = $resLink;
//}

    $this->IncludeComponentTemplate();

}