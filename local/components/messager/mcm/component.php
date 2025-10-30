<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/logCompIn.txt";
$logIn = __DIR__ . "/logCompOut.txt";
//p($arParams, 'start', $log);
if($arParams['tempList'] === 'sendIn'){
    p($arParams, 'start', $log);
}else{
    p($arParams, 'start', $logIn);
}


if($arParams["tempList"] !=='calback') {
    if ($arParams['app'] && $arParams['member_id']) {
        p($arParams['app'], "app", $log);
        $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
       // p($auth, "auth", $log);
        $typeCRM = $arParams['~PLACEMENT'];
        $resPlacement = json_decode($arParams['~PLACEMENT_OPTIONS'], true);
        p($resPlacement, "resPlacement", $log);
        if ($typeCRM === 'USERFIELD_TYPE') {
            $params['id'] = $resPlacement['ENTITY_DATA']['entityId'];
        } else {
            $params['id'] = $resPlacement['ID'];
        }

        if ($arParams['~PLACEMENT'] === 'CRM_DEAL_DETAIL_ACTIVITY' || $resPlacement['ENTITY_DATA']['entityTypeId'] == 2) {
            $params['entityTypeId'] = 2;
            $itemDeal = $auth->CScore->call('crm.item.get', $params)['item'];
            if (!empty($itemDeal['contactIds'])) {
                foreach ($itemDeal['contactIds'] as $contactId) {
                    $paramsContact = [
                        'entityTypeId' => 3,
                        'id' => $contactId
                    ];
                    $itemContact = $auth->CScore->call('crm.item.get', $paramsContact)['item'];
                    if (!empty($itemContact['fm'])) {
                        foreach ($itemContact['fm'] as $res) {
                            if ($res['typeId'] === 'PHONE') {
                                $contactAll[$itemContact['name'] . " " . $itemContact['lastName'] . " " . $itemContact['secondName']][$res['id']] = $res['value'];
                            }
                        }
                        // pr($itemContact, '');
                        // $contactAll[]
                    }
                }
            }
        }


        // $item = $auth->CScore->call('crm.item.get', $params)['item'];

        // pr($item, '');
        $resListProfile = profileCsMcm($arParams['member_id']);
        if ($resListProfile) {
            foreach ($resListProfile as $profile) {
                $guide[$profile['UF_PROFILE_ID']] = $profile['UF_PROFILE_NAME'];
            }
            //  pr($guide, '');
        }

    }

    $arResult['contactAll'] = $contactAll;
    p($arResult, "arResult", $log);
}
$this->IncludeComponentTemplate($arParams["tempList"]);
?>