<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logMessageAdd.txt";
p($arParams, "start", $log);

if ($arParams['app'] && $arParams['auth']['member_id']) {
    foreach ($arParams['data']['BOT'] as $keyBot => $valBot) {
        $botId = $keyBot;
    }
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    $chatCianId = explode('|', $arParams['data']['PARAMS']['CHAT_ENTITY_ID']);
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
            'PROPERTY_CS_CIAN_LINE'=>$chatCianId[1]
        ],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];

//    $dataMes = [
//        "chatId"=> $chatCianId[2],
//
//    ];
//
//
//p($dataMes , "dataMes", $log);
    $keyCian = "Authorization: Bearer " . $resSetup['CS_KEY_CIAN'];

    $ch = curl_init('https://public-api.cian.ru/v1/get-chat?chatId=' . $chatCianId[2]);
    //  $ch = curl_init('https://public-api.cian.ru/v1/get-chat?chatId='.http_build_query($dataMes));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyCian));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    //$res = json_encode($res, JSON_UNESCAPED_UNICODE);
    $result = json_decode($res, true);
    p($result, "result", $log);
    if ($result['result']['chat']['offer']['externalId']) {
        $externalId = $result['result']['chat']['offer']['externalId'];
        $resExternalId = explode('_', $externalId);
        $objectId = '';
        $dealId = '';
        $assignedId = '';
        $title = '';
      //  p($resExternalId, "resExternalId", $log);
        if (count($resExternalId) == 1) {
            $dealId = $externalId;
        }
        if ($resExternalId[0] === 'D') {
            $dealId = $resExternalId[1];
        }
        if ($resExternalId[0] === 'O') {
            $objectId = $resExternalId[1];
        }
        if (!empty($dealId)) {
            $deal = $auth->CScore->call('crm.deal.get', ['ID' => $dealId]);
          //  p($deal, "deal", $log);
            $dealMess = "[URL=/crm/deal/details/" . $deal['ID'] . "/]Сделка №" . $deal['ID'] . "[/URL]\n";
            $assignedId = $deal['ASSIGNED_BY_ID'];
            $title = "[B]" . $deal['TITLE'] . "[/B]\n";
            $objectId = $deal['UF_CRM_CS_DEAL_PRODUCT_ID'];
        }
        if (!empty($objectId)) {
            foreach ($auth->CScore->call('crm.product.property.list') as $valProperty) {
                $arProperty["PROPERTY_" . $valProperty["ID"]] = $valProperty["CODE"];
                $arPropertyCode[$valProperty["CODE"]] = "PROPERTY_" . $valProperty["ID"] ;
            }
          //  p($arProperty, "arProperty", $log);
            $price = '';
            $img = '';
            $filter['ACTIVE']='Y';
            foreach ($auth->CScore->call('crm.product.get', ['ID' => $objectId]) as $key => $val) {
                if (empty($dealId)) {
                    if ($arProperty[$key] === 'NAME' && !empty($val)) {
                        $title = "[B]" . $val . "[/B]\n";
                    }
                }
                    if ($arProperty[$key] === 'CS_PRICE' && !empty($val['value'])) {
                        $filter["<=".$key]=$val['value']*1.1;
                        $filter['>'.$key]=$val['value']*0.9;
                        $price = "Стоимость объекта:[B] " . number_format($val['value'], 0, '.', ' ') . "[/B]\n";
                    }
                if($arProperty[$key] === 'CS_OBJECT_TYPE' && !empty($val['value'])){
                    $filter[$key]=$val['value'];
                }
                if($arProperty[$key] === 'CS_ROOMS' && !empty($val['value'])){
                    $filter[$key]=$val['value'];
                }
                if ($arProperty[$key] === 'CS_PHOTO' && !empty($val[0]['value'])) {
                   // p($val, "photo", $log);
                    $img = explode('|', $val[0]['value'])[1];
                }

            }
            $i=0;
            $messObjectAll='';
            p($filter , "filter", $log);
                foreach ($auth->CScore->call('crm.product.list', ['order' => ['TIMESTAMP_X' => 'DESC'],'filter' => $filter,
                    'select' => ['ID', 'NAME', 'TIMESTAMP_X', $arPropertyCode['CS_PRICE'],$arPropertyCode['CS_ASSIGNED_BY'], ]]) as $key => $product){
                    p($product , "product", $log);
                    if ($i<25 && $product['ID'] !== $objectId){
                        $messObjectAll =$messObjectAll. "[URL=" .ADDRESS_SITE_BROKCI."/pub/catalog/viewCard.php?type=c&object_id={$product["ID"]}&user_id={$product[$arPropertyCode['CS_ASSIGNED_BY']]['value']}&memberId={$arParams['auth']['member_id']}&app=brokci]{$product['NAME']}[/URL]\n";
                        $idObjectC[]=$product["ID"];
                    }


                }
            $idObjectsOb = implode(",", $idObjectC);
            $messObjectList = "\n[URL=" .ADDRESS_SITE_BROKCI."/pub/catalog/viewList.php?object_id_p={}&object_id_c={$idObjectsOb}&user_id={$assignedId}&memberId={$arParams['auth']['member_id']}&app=brokci]Подборка для клиента[/URL]\n";

        }
p($messObjectAll , "messObjectAll", $log);

            $mess = $title . $dealMess . "[URL=" . $result['result']['chat']['offer']['url'] . "]ссылка на Объявление[/URL]\n\n";

        $blocks[]=  ["IMAGE" => [
            "NAME" => 'фото объекта',
            "LINK" => $img,
            "PREVIEW" => $img,
            "WIDTH" => 100,
            "HEIGHT" => 100,
        ]];
        $blocks[]=  ["MESSAGE" => $mess, ];
        $blocks[]=  ["DELIMITER" =>[ "SIZE" => 400, ]];
        if(!empty($messObjectAll)) {
            $blocks[] = ["MESSAGE" => "Похожие объекты",];
            $blocks[] = ["MESSAGE" => $messObjectAll,];
            $blocks[] = ["DELIMITER" => ["SIZE" => 400,]];
            $blocks[] = ["MESSAGE" => $messObjectList,];
        }
            $paramsMes = [
                'BOT_ID' => $botId,
                'DIALOG_ID' => $arParams['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => 'Обращение по объекту',
                'ATTACH' => [
                    "ID" => 1,
                    "COLOR_TOKEN" => "primary",
                    "COLOR" => "#d70b0b",
                    "BLOCKS" => $blocks
//                        [
//                        ["MESSAGE" => $mess,],
//                        ["DELIMITER" =>[ "SIZE" => 200, ] ],
//                        ["IMAGE" => [
//                                "NAME" => 'фото объекта',
//                                "LINK" => $img,
//                                "PREVIEW" => $img,
//                                "WIDTH" => 100,
//                                "HEIGHT" => 100,
//                            ]
//                        ]
//                    ]
                ],
                'KEYBOARD' => '',
                'MENU' => '',
                'SYSTEM' => 'Y',
                'URL_PREVIEW' => 'Y'
            ];
p($paramsMes , "paramsMes", $log);
            $messBot = $auth->CScore->call('imbot.message.add', $paramsMes);
            p($messBot, "messBot", $log);
            $userAddChat = $auth->CScore->call('imbot.chat.user.add',
                array(
                    'CHAT_ID' => $arParams['data']['PARAMS']['CHAT_ID'],
                    'USERS' => [$assignedId],
                    'BOT_ID' => $botId,
                ));
            p($userAddChat, "userAddChat", $log);
        }



}