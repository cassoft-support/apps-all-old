<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");

function itemsData($arParams)
{
    $log = __DIR__ . "/logAjax.txt";

    p($arParams, "start", $log);
//d($arParams);
    if ($arParams['member_id']) {
        $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
//        $params=[
//            'ENTITY'=>'candidates', 'PROPERTY'=> 'stage', 'NAME'=> 'Стадия кандидата', 'TYPE'=> 'S'
//        ];
//      $arFieldsAdd=$auth->CScore->call('entity.item.property.add', $params);

      $arFields=$auth->CScore->call('entity.item.property.get', ['ENTITY'=>'candidates']);
      foreach ($arFields as $kF){
          $fields[$kF['PROPERTY']] = $kF['NAME'];
      }
       // d($fields);
        foreach ($auth->batch->getTraversableList('entity.item.get',  [], [], ['*'], 6000, ['ENTITY'=>'stage']) as $vStage) {
          // d($vStage);
            $stage[$vStage['ID']]['NAME']= $vStage['NAME'];
            $stage[$vStage['ID']]['COLOR']= $vStage['PROPERTY_VALUES']['CS_COLOR'];
        }

        foreach ($auth->batch->getTraversableList('entity.item.get',  [], [], ['*'], 6000, ['ENTITY'=>'vacancy']) as $value) {
          //  pr($value);
            p($value , "value", $log);
            $result['items'][$value['ID']]= $value['PROPERTY_VALUES'];
            $result['items'][$value['ID']]['ID']= $value['ID'];
            $result['items'][$value['ID']]['NAME']= $value['NAME'];
            $result['items'][$value['ID']]['ACTIVE']= $value['ACTIVE'];

            if($value['PROPERTY_VALUES']['stage']){

                $result['items'][$value['ID']]['stageColor']= $stage[$value['PROPERTY_VALUES']['stage']]['COLOR'];
                $result['items'][$value['ID']]['stageName']= $stage[$value['PROPERTY_VALUES']['stage']]['NAME'];
            }

        }

    }

    return $result;
}