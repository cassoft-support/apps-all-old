<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $date = date("d.m.YTH:i");
    $log = __DIR__ . "/logComp.txt";
    p('start', 'start', $log);
    p($arParams, 'params', $log);
if($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    $params = [
        'ENTITY' => 'setup',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
//d($resSetup);
    if (empty($resSetup)) {
        $paramsAdd = [
            'ENTITY' => 'setup',
            'NAME' => 'setup',
        ];
        $resSetupAdd = $auth->CScore->call('entity.item.add', $paramsAdd);
        $resSetup = $auth->CScore->call('entity.item.get', $params);
    }
    // d($resSetup);
    $arResult['PROP'] = $resSetup['PROPERTY_VALUES'];
    if($arParams['app'] === 'change_assigned'){
        $resProp=[];
       foreach ($auth->CScore->call('entity.item.property.get', $params) as $prop){
           $resProp[]=$prop['PROPERTY'];
       }
        p($resProp, "resProp", $log);
        if(!in_array('CS_COMMENT_ADD', $resProp)){
            p($resProp, "propAdd", $log);
            $paramsAddProp=[
                'ENTITY' => 'setup',
                'PROPERTY' => 'CS_COMMENT_ADD',
                'NAME' => 'Публиковать событие в ТаймЛайн',
                'TYPE' => 'S'
            ];
       $propAdd   =  $auth->CScore->call('entity.item.property.add', $paramsAddProp);
            p($propAdd , "propAdd", $log);
            $arResult['PROP']['CS_COMMENT_ADD']=false;
        }

    }
    $arResult['ID'] = $resSetup['ID'];

    $arResult['member_id'] = $arParams['auth']['member_id'];

    p($arResult, "arResult", $log);
    $tempList = "";
    if (empty($arParams['tempList'])) {
        $tempList = $arParams['tempList'];
    }
    $this->IncludeComponentTemplate($arParams['tempList']);
}