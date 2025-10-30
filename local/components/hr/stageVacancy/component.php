<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logComp.txt";
p($arParams, "start", $log);

$memberId = $arParams['auth']['member_id'];
if($arParams['app'] && $memberId) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $memberId);
    $methodUser = "user.get";
    $paramsUser = array(
        'filter' => [
//'ACTIVE' => 1,
            'USER_TYPE' => 'employee'
        ],
        'select' => [],
//'select' =>[], 
        'order' => ['ID' => 'asc']
    );
    $users = $auth->CScore->call($methodUser, $paramsUser);
    foreach ($users['result'] as $key => $value) {
        $userAll[$value["ID"]] = $value['LAST_NAME'] . " " . $value['NAME'] . " " . $value['SECOND_NAME'];
    }

    $entity = "stage_fav";
    /*
    $methodD="entity.item.property.add";
    $paramsD=array(
      'ENTITY'=>$entity,
      'PROPERTY'=>  'CS_COLOR',
     'NAME'=> 'Цвет',
     'TYPE'=> 'S'
    );
    $arEntityD = $auth->CScore->call($methodD, $paramsD);

    $methodQ="entity.item.property.get";
    $paramsQ=array(
      'ENTITY'=>$entity,
    );
    $arEntityQ = $auth->CScore->call($methodQ, $paramsQ);
                        foreach($arEntityQ['result'] as $keyQ => $valueQ){
                          $resEntityQ[$valueQ['PROPERTY']]=$valueQ['NAME'];
                        }
    d($arEntityQ);
    //d($resEntity'entity.update', {'ENTITY': 'dish', 'ACCESS': {U1:'W',AU:'R'}});Q);
    */
    /*
    $paramsListAll=array(
      'ENTITY'=>'stage_fav',
      'ACCESS'=> [
        'U1'=>'W',
        'AU'=>'W'
        ]
    );
    $arEntityAll = $auth->CScore->call('entity.update', $paramsListAll);

    $paramsListAll=array(
      'ENTITY'=>'stage_fav',
    );
    $arEntityAll = $auth->CScore->call('entity.rights', $paramsListAll);

    d($arEntityAll);
    */
    $typeStage = array(
        "new_action" => "Начальная стадия",
        "action" => "Активная  стадия",
        "end_plus" => "Упешная стадия",
        "end_minus" => "Отрицательная  стадия (объект активен)",
        "end_close" => "Отрицательная  стадия (объект не активен)",
    );

    $entity = "stage_fav";
    $methodList = "entity.item.get";
    $paramsListType = array(
        'ENTITY' => $entity,
        'FILTER' => [
            'ACTIVE' => 'Y'
        ]
    );
    $arEntity = $auth->CScore->call($methodList, $paramsListType);

    foreach ($arEntity['result'] as $key => $value) {
        $date = date('d.m.Y', strtotime($value['TIMESTAMP_X']));
        $result['ID'] = $value['ID'];
        $result['MODIFIED_BY'] = $userAll[$value['MODIFIED_BY']];
        $result['DATE_MODIFIED'] = $date;
        $result['CS_COLOR'] = $value['PROPERTY_VALUES']['CS_COLOR'];
        $result['CS_TYPE_STAGE'] = $typeStage[$value['PROPERTY_VALUES']['CS_TYPE_STAGE']];
        $result['NAME'] = $value['NAME'];
        $resEntity[] = $result;
    }

    $arResult['entity'] = $resEntity;
    //   $arResult['resEntityQ']= $resEntityQ;
    $arResult['UserAut'] = $UserA;

    //  d("brokci_settings");


    $this->IncludeComponentTemplate();
}
?>