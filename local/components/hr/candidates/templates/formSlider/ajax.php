<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");

function itemData($arParams)
{
    $log = __DIR__ . "/logAjax.txt";
    p("deal_Fields", "start", $log);
    p($arParams, "params", $log);
//d($arParams);
    if ($arParams['req']['member_id']) {
        $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['req']['member_id']);

//      $arFields=$auth->CScore->call('entity.item.property.get', ['ENTITY'=>'candidates']);
//      foreach ($arFields as $kF){
//          $fields[$kF['PROPERTY']] = $kF['NAME'];
//      }
       // d($fields);
        foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'stage', [], [], 6000) as $vStage) {
          //  d($vStage);
            $stage[$vStage['ID']]['NAME']= $vStage['NAME'];
            $stage[$vStage['ID']]['COLOR']= $vStage['PROPERTY_VALUES']['CS_COLOR'];
        }

        $resItem=$auth->CScore->call('entity.item.get', ['ENTITY'=>'candidates', 'filter'=>['ID'=>$arParams['candidateId']]])[0];
         // d($resItem);
            $result['item']= $resItem['PROPERTY_VALUES'];
            $result['item']['ID']= $resItem['ID'];
            if($resItem['PROPERTY_VALUES']['phone']){
            $phone = json_decode($resItem['PROPERTY_VALUES']['phone'], true);
            }

        $result['item']['phone']= $phone;
    }

    return $result;
}