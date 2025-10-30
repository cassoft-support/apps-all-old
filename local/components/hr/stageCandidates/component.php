<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logComp.txt";
p('start', 'stage', $log);
p($arParams,'params', $log);

if (!empty($arParams['member_id']) && !empty($arParams['app'])) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
    $smartClass = new \CSlibs\B24\Smarts\SmartProcess($auth, "");
    $resSmartGuide= $smartClass->smartGuide();
    $resStageSmartAll= $smartClass->smartCategoryStageName($resSmartGuide['Найм персонала']['entityTypeId']);

    foreach ($resStageSmartAll as $category => $valStage){
        foreach ($valStage as $key =>$val){
$stageSmartAll[$key]=$val."-(<b>".$category."</b>)";
                         }}
  //  d($stageSmartAll);

    $filterUser = [
//'ACTIVE' => 1,
        'USER_TYPE' => 'employee'
    ];
    $selectUser = ['ID', 'LAST_NAME', 'NAME', 'SECOND_NAME'];
    $orderUser = ['ID' => 'asc'];
    foreach ($auth->batch->getTraversableList('user.get', $orderUser, $filterUser, $selectUser, 6000) as $value) {
        $userAll[$value["ID"]] = $value['LAST_NAME'] . " " . $value['NAME'] . " " . $value['SECOND_NAME'];
    }

    $typeStage = array(
        "new_action" => "Начальная стадия",
        "action" => "Активная  стадия",
        "end_plus" => "Упешная стадия",
        "close" => "Объект не активен",
    );

    $filter = [
        'ACTIVE' => 'Y'
    ];
    foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'stage', [], $filter, 6000) as $value) {
        $resStage = array();
        $result = array();
     //  $resStageDeal = json_decode($value['PROPERTY_VALUES']['CS_STAGE_DEAL'], true);
        $resStageSmarts = json_decode($value['PROPERTY_VALUES']['CS_STAGE_SMART'], true);
        //  $debug->console($resStage);
//$arEntity = CSRest::call($methodList, $paramsListType);
//d($resStageSmarts);
//foreach( $arEntity['result'] as $key => $value){
        $date = date('d.m.Y', strtotime($value['TIMESTAMP_X']));
        $result['ID'] = $value['ID'];
        $result['MODIFIED_BY'] = $userAll[$value['MODIFIED_BY']];
        $result['DATE_MODIFIED'] = $date;
        $result['CS_COLOR'] = $value['PROPERTY_VALUES']['CS_COLOR'];
        $result['TYPE_STAGE_NAME'] = $typeStage[$value['PROPERTY_VALUES']['CS_TYPE_STAGE']];
        $result['CS_STAGE_DEAL'] = $value['PROPERTY_VALUES']['CS_STAGE_DEAL'];
        $result['CS_STAGE_SMART'] = $resStageSmarts;
        $result['CS_TYPE_STAGE'] = $value['PROPERTY_VALUES']['CS_TYPE_STAGE'];
        if (!empty($resStageSmarts)) {
            if (count($resStageSmarts) === 1) {
                $result['STAGE_SMART_NAME'] = $stageSmartAll[$resStageSmarts[0]];
            } else {
                foreach ($resStageSmarts as $stageKey) {
                   // d($stageKey);
                    $result['STAGE_SMART_NAME'] = $stageSmartAll[$stageKey] . ", " . $result['STAGE_SMART_NAME'];
                }
            }
        }
//$result['CS_STAGE_DEAL_key']=$resStage;

        $result['NAME'] = $value['NAME'];
        $resEntity[] = $result;
    }
    //$debug->console($resEntity);
    $arResult['entity'] = $resEntity;
   // d($arResult);
//$arResult['stageDeal'] = $stageDeal;
   // $arResult['UserAutStage'] = $memberId;
    $this->IncludeComponentTemplate();
}
?>