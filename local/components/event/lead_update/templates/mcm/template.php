<?php
define(NOT_CHECK_PERMISSIONS, true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/components/event/lead_add/templates/mcm/guide.php');

$log = __DIR__ . "/logLeadUp.txt";
//p('start', 'start', $log);
//p($_REQUEST, date('c'), $log);
////p($arParams, 'params', $log);
$memberId = $arParams['auth']['member_id'];
$CloudApp = $arParams['app'];
//d($arParams);
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
$arLead = $auth->CScore->call('crm.lead.get', ['ID'=> $arParams['leadId']]);
//p($arLead, 'lead', $log);
if($memberId === 'b176cc28ddd836fa2c7d93e81fff15df' && !empty($arLead) && empty($arLead["UF_CRM_CS_TG_ID_USER"])){
    if(!empty($arLead["IM"])){
        foreach ($arLead["IM"] as $im){
            if($im['VALUE_TYPE'] === 'TELEGRAM' && !empty($im['VALUE']) && $im['VALUE'] !== 'None'){
                $params=[
                   'ID' => $arLead['ID'],
                'fields' => [
                    "UF_CRM_CS_TG_ID_USER"=>$im['VALUE']
                    ]
                ];
                p($params, 'params', $log);
                $arLeadUp = $auth->CScore->call('crm.lead.update', $params);
                p($arLeadUp , "arLeadUp", $log);
            }
        }
    }

}
//    $idGroup='-1002377480862';
//    $keyBot = '7776881648:AAGovDHouhj3q-OeCOYxIATlD0Xkq8UyNTI'; $leadLink = '<a href="https://' . $arParams['auth']['domain'] . '/crm/lead/details/' . $arLead['ID'] . '/">Создан новый лид №' . $arLead['TITLE'] . '</a>';
//    $linkTel = '<a href="tel:' . $arLead['PHONE']['0']['VALUE'] . '">' . $arLead['PHONE']['0']['VALUE'] . '</a>';
//    $message = $leadLink . "\n Имя: <strong>" . $arLead['NAME'] . "</strong>\n тел: " . $linkTel;
//}
//if($memberId === '986202eae13f5a77690fd2e208d23890' && !empty($arLead)) {
//    //staprop
//
//    $idGroup='-1002636125156';
//    $keyBot = '5830465058:AAFxwYJYDZgf-m8uLTG_YWctGKAjhD3wRCM';
//  //  p($keyBot , "keyBot", $log);
//    $leadLink = '<a href="https://' . $arParams['auth']['domain'] . '/crm/lead/details/' . $arLead['ID'] . '/">Создан новый лид - ' . $arLead['TITLE'] . '</a>';
//    $linkTel = '<a href="tel:' . $arLead['PHONE']['0']['VALUE'] . '">' . $arLead['PHONE']['0']['VALUE'] . '</a>';
//    $region = "Регион интереса: ".$regionMap[$arLead['UF_CRM_1696051119229'][0]]."\n";
//    $country = "Страна: ".$countryMap[$arLead['UF_CRM_1644839701548']]."\n";
//    $lang = "Язык: ".$langMap[$arLead['UF_CRM_1644839852091']]."\n";
//    $send = "Мессенджер: ".$sendToMap[$arLead['UF_CRM_1658820303']]."\n";
//    $city = "Город: ".$arLead['UF_CRM_1644839746626']."\n";
//    $comment = "Комментарий: ".$arLead['COMMENTS']."\n";
//    $wish = "Пожелания: ".$arLead['UF_CRM_1632393769490']."\n";
//    $source = "Источник: ".$sourceMap[$arLead['SOURCE_ID']]."\n";
//    $message = $leadLink . "\n Имя: <strong>" . $arLead['NAME'] . "</strong>\n тел: " . $linkTel."\n".$send.$wish.$comment.$region.$country.$city.$lang.$source;
//}
//if(!empty($idGroup) && !empty($keyBot) && !empty($message)) {
//
//
//   // $resSend=   mesSendGroupTg($message, $idGroup, $keyBot);
//
//    if($arLead['UF_CRM_1696051119229'][0] ==7164){
//        $idGroupTg = '-1002338360376';
//        $resSend=   mesSendGroupTg($message, $idGroupTg, $keyBot);
//        p($resSend , "resSendDub", $log);
//
//    }
//}
//
//function mesSendGroupTg($message, $idGroup, $keyBot){
//    $log = __DIR__."/logMesSendGoupTg.txt";
//    $data = [
//        'chat_id' => $idGroup,
//        'text' => $message,
//        'parse_mode' => 'HTML',
//        'disable_web_page_preview' => 'true'
//    ];
//    p($data, "start", $log);
//    $dataStr = http_build_query($data);
////    $zapros = "https://api.telegram.org/bot".$keyBot."/sendMessage?".$dataStr;
////    p($zapros , "zapros", $log);
////    $response = file_get_contents("https://api.telegram.org/bot".$keyBot."/sendMessage?".$dataStr);
////   p($response , "response", $log);
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $keyBot . "/sendMessage");
//    curl_setopt($ch, CURLOPT_POST, 1);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//    $response = curl_exec($ch);
//    curl_close($ch);
//
//    $responseData = json_decode($response, true);
//    p($responseData, "responseData", $log);
//    if ($responseData['ok']) {
//        echo "Message sent successfully!";
//    } else {
//        echo "Error: " . $responseData['description'];
//    }
//    return $responseData;
//}