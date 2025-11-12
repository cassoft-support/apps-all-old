<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$log = __DIR__ . "/logTemp.txt";
//p($arParams, 'start', $log);
p("start", 'start', $log);
//file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
$memberId = $arParams['auth']['member_id'];
if ($arParams['app'] && $memberId) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $memberId);
    $setup = $auth->CScore->call('entity.item.get', ['ENTITY' => "setup",])[0]['PROPERTY_VALUES'];

    $lead = $auth->CScore->call("crm.lead.get", [ 'id' => $arParams['data']['FIELDS']['ID']]);
    p($lead, 'lead', $log);
   // if (($lead['SOURCE_ID'] == 5 || $lead['SOURCE_ID'] == 3) && $lead['STATUS_SEMANTIC_ID'] === 'P' && $lead['ASSIGNED_BY_ID'] == 27) {
    if ($lead['STATUS_SEMANTIC_ID'] === 'P' && $lead['ASSIGNED_BY_ID'] == 2877) {
        $filter=[
            "ACTIVE" => 'Y',
            'PROPERTY_year' => date('Y'),
            'PROPERTY_month' => date('n'),
        ];
        foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'transfer_stat', ['ID' => 'ASC'], $filter, 60000) as $value) {
          //  p($value, "transfer_stat", $log);
            $userStat[$value["PROPERTY_VALUES"]["user_id"]]['deal'] = $value["PROPERTY_VALUES"]["deal"];
            $userStat[$value["PROPERTY_VALUES"]["user_id"]]['lead'] = $value["PROPERTY_VALUES"]["lead"];
            $userStat[$value["PROPERTY_VALUES"]["user_id"]]['volume'] = $value["PROPERTY_VALUES"]["volume"];
            $userStat[$value["PROPERTY_VALUES"]["user_id"]]['volume_day'] = $value["PROPERTY_VALUES"]["volume_day"];
            $userStat[$value["PROPERTY_VALUES"]["user_id"]]['ID'] = $value["ID"];
//            if($value["PROPERTY_VALUES"]["user_id"] ==49){
//                $value["PROPERTY_VALUES"]["volume"]=3;
//            }
//            if($value["PROPERTY_VALUES"]["user_id"] ==45){
//                $value["PROPERTY_VALUES"]["volume"]=4;
//            }
//            if($value["PROPERTY_VALUES"]["user_id"] ==51){
//                $value["PROPERTY_VALUES"]["rating"]=40;
//            }
//            if($value["PROPERTY_VALUES"]["user_id"] ==47){
//                $value["PROPERTY_VALUES"]["rating"]=45;
//            }
//            if($value["PROPERTY_VALUES"]["user_id"] ==365){
//                $value["PROPERTY_VALUES"]["volume"]=1;
//            }
            if($value["PROPERTY_VALUES"]["rating"]>0) {
                $arElement[$value["PROPERTY_VALUES"]["user_id"]]['rating'] = $value["PROPERTY_VALUES"]["rating"];
                if ($value["PROPERTY_VALUES"]["volume_day"] > 0) {
                    $val = $value["PROPERTY_VALUES"]["volume_day"] * 100 / $value["PROPERTY_VALUES"]["rating"];
                    // $arElement[$value["PROPERTY_VALUES"]["user_id"]]['volume'] = $value["PROPERTY_VALUES"]["volume"]*100/$value["PROPERTY_VALUES"]["rating"];
                    $arElementN[$val][$value["PROPERTY_VALUES"]["rating"]][] = $value["PROPERTY_VALUES"]["user_id"];
                } else {
                    // $arElement[$value["PROPERTY_VALUES"]["user_id"]]['volume'] = 0;
                    $arElementN[0][$value["PROPERTY_VALUES"]["rating"]][] = $value["PROPERTY_VALUES"]["user_id"];
                }
            }
        }
        p($arElementN, 'stat1', $log);
        ksort($arElementN);
        p($arElementN, 'arElementN', $log);
        $first = array_shift($arElementN);//извлекаем первый элемент из массива
        krsort($first);
        p($first, 'first', $log);
        $second = array_shift($first);
        $resId=array_shift($second);
        p($resId, 'resId', $log);

        $leadUp = $auth->CScore->call("crm.lead.update", [ 'id' =>$lead['ID'], 'fields'=> ['ASSIGNED_BY_ID'=>$resId]]);
        if($userStat[$resId]['lead']){
            $allLead= json_decode($userStat[$resId]['lead'],true);
            $allLead[]=$lead['ID'];
        }else{
            $allLead[]=$lead['ID'];
        }
        $valueSumm = $userStat[$resId]['volume']+1;
        $value = $userStat[$resId]['volume_day']+1;
        $paramsUp=[
            'ENTITY' => 'transfer_stat',
            'ID' => $userStat[$resId]['ID'],
            'PROPERTY_VALUES' => [
                'volume_day' => $value,
                'volume' => $valueSumm,
                'lead' => json_encode($allLead),
            ]];

        p($paramsUp, 'paramsUp', $log);
        $entityAdd = $auth->CScore->call('entity.item.update', $paramsUp);


    }
}
?>

