<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");

function itemsData($arParams)
{
    $log = __DIR__ . "/logAjax.txt";
    p("deal_Fields", "start", $log);
    p($arParams, "params", $log);
//d($arParams);
    if ($arParams['req']['member_id']) {
        $auth = new \CSlibs\B24\Auth\Auth($arParams['req']['app'], [], $arParams['req']['member_id']);
//        $params=[
//            'ENTITY'=>'candidates', 'PROPERTY'=> 'stage', 'NAME'=> 'Стадия кандидата', 'TYPE'=> 'S'
//        ];
//      $arFieldsAdd=$auth->CScore->call('entity.item.property.add', $params);

      $arFields=$auth->CScore->call('entity.item.property.get', ['ENTITY'=>'candidates']);
      foreach ($arFields as $kF){
          $fields[$kF['PROPERTY']] = $kF['NAME'];
      }
       // d($fields);
        foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'stage', [], [], 6000) as $vStage) {
          // d($vStage);
            $stage[$vStage['ID']]['NAME']= $vStage['NAME'];
            $stage[$vStage['ID']]['COLOR']= $vStage['PROPERTY_VALUES']['CS_COLOR'];
        }

        foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'candidates', [], ['ACTIVE'=>'Y'], 6000) as $value) {
            //d($value);
            $result['items'][$value['ID']]= $value['PROPERTY_VALUES'];
            $result['items'][$value['ID']]['ID']= $value['ID'];
            $result['items'][$value['ID']]['NAME']= $value['NAME'];
            if($value['PROPERTY_VALUES']['phone']){
                $phoneRes=[];
            $phoneRes = json_decode($value['PROPERTY_VALUES']['phone'], true);
            if(count($phoneRes)>1){
                foreach ($phoneRes as $vPhone){
                    $phone =$phone.$vPhone.", ";
                }
            }else{
                $phone =$phoneRes[0];
            }
            }
            $result['items'][$value['ID']]['phone']= $phone;
            if($value['PROPERTY_VALUES']['stage']){

                $result['items'][$value['ID']]['stageColor']= $stage[$value['PROPERTY_VALUES']['stage']]['COLOR'];
                $result['items'][$value['ID']]['stageName']= $stage[$value['PROPERTY_VALUES']['stage']]['NAME'];
            }

        }

    }

    return $result;
}