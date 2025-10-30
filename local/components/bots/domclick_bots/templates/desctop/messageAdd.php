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
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];
    $chatId = explode('|', $arParams['data']['PARAMS']['CHAT_ENTITY_ID']);

    $keyAuth = "Authorization: Bearer " . $resSetup['CS_KEY_DC'];
    $ch = curl_init('https://public-api.domclick.ru/chats/v1/chats/' . $chatId[2]); //подписаться
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyAuth, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    //$res = json_encode($res, JSON_UNESCAPED_UNICODE);
    $result = json_decode($res, true);
    p($result, "result", $log);
    if ($result['result']['offer']['external_id']) {

        $externalId = $result['result']['offer']['external_id'];
        $resExternalId = explode('_', $externalId);
        $objectId = '';
        $dealId = '';
        $assignedId = '';
        $title = '';
        p($resExternalId, "resExternalId", $log);
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
            p($deal, "deal", $log);
            $dealMess = "[URL=/crm/deal/details/" . $deal['ID'] . "/]Сделка №" . $deal['ID'] . "[/URL]\n";
            $assignedId = $deal['ASSIGNED_BY_ID'];
            $title = "[B]" . $deal['TITLE'] . "[/B]\n";
            $objectId = $deal['UF_CRM_CS_DEAL_PRODUCT_ID'];
        }
        if (!empty($objectId)) {
            foreach ($auth->CScore->call('crm.product.property.list') as $arProperty) {
                $arProperty["PROPERTY_" . $arProperty["ID"]] = $arProperty["CODE"];
            }
            $price = '';
            $img = '';
            foreach ($auth->CScore->call('crm.product.get', ['ID' => 658]) as $key => $val) {
                if (empty($dealId)) {

                    if ($arProperty[$key] === 'NAME' && !empty($val)) {
                        $title = "[B]" . $val . "[/B]\n";
                    }
                    if ($arProperty[$key] === 'CS_PRICE' && !empty($val['value'])) {
                        $price = "Стоимость объекта:[B] " . number_format($val['value'], 0, '.', ' ') . "[/B]\n";
                    }
                }
                if ($arProperty[$key] === 'CS_PHOTO' && !empty($val[0]['value'])) {
                    $img = explode('|', $val[0]['value'])[1];
                }

            }

            if (empty($assignedId)) {
                foreach ($result['result']['users'] as $arUser) {
                    if ($arUser['role_description'] === 'Продавец') {
                        p($arUser['display_name'], "user", $log);

                    }
                }
            }


            $mess = $title . $dealMess . "[URL=" . $result['result']['offer']['url'] . "]ссылка на объявление[/URL]\n";
            $paramsMes = [
                'BOT_ID' => $botId,
                'DIALOG_ID' => $arParams['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => 'Обращение по объекту',
                'ATTACH' => [
                    "ID" => 1,
                    "COLOR_TOKEN" => "primary",
                    "COLOR" => "#d70b0b",
                    "BLOCKS" => [
                        [
                            "MESSAGE" => $mess,
                            "IMAGE" => [
                                "NAME" => $result['result']['offer']['title'],
                                "LINK" => $result['result']['offer']['img_url'],
                                "PREVIEW" => $result['result']['offer']['img_url'],
                                "WIDTH" => 1000,
                                "HEIGHT" => 638,
                            ]
                        ],
                    ]
                ],
                'KEYBOARD' => '',
                'MENU' => '',
                'SYSTEM' => 'Y',
                'URL_PREVIEW' => 'Y'
            ];
            p($paramsMes, "paramsMes", $log);
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
//        "offer": {
//            "id": 0,
//      "external_id": "string",
//      "title": "string",
//      "address": "string",
//      "url": "string",
//      "img_url": "string",
//      "price": 0
//    },


    }
}