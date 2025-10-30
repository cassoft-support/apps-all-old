<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/components/event/lead_add/templates/mcm/guide.php');

$log = __DIR__ . "/logLeadAdd.txt";
$logUp = __DIR__ . "/logLeadUp.txt";
$logTg = __DIR__ . "/logLeadTg.txt";
p('start', 'start', $log);
p($_REQUEST, 'reg', $log);
p($arParams, 'params', $log);
$memberId = $arParams['auth']['member_id'];
$CloudApp = $arParams['app'];
//d($arParams);
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
$arLead = $auth->CScore->call('crm.lead.get', ['ID'=> $arParams['leadId']]);
p($arLead, 'lead', $log);

if(!empty($arLead["IM"])){
    p($arLead['ID'], date('c'), $log);
    p($arLead['IM'], 'IM', $log);
    foreach ($arLead["IM"] as $val){
        if($val['VALUE_TYPE'] === 'IMOL'){
          $res =  explode("|", $val['VALUE']);
            p($res, 'res', $log);
          if( $res[1] === 'cs_mcm_telegram' && !empty($res[3])){
              $params=[
                  'id' => $arLead['ID'],
                  'fields' => [
                      "UF_CRM_CS_TG_ID_USER" =>$res[3],
                      "IM"=> [[ "VALUE"=>$res[3], "VALUE_TYPE"=> "TELEGRAM" ]] ,
                  ],
              ];
              p($params, 'params', $log);
              $arLeadUp = $auth->CScore->call('crm.lead.update',$params);
              p($arLeadUp, 'arLeadUp', $log);
          }

        }
        
    }
}
//if($memberId === 'b176cc28ddd836fa2c7d93e81fff15df' && !empty($arLead)){
//    $idGroup='-1002377480862';
//    $keyBot = '7776881648:AAGovDHouhj3q-OeCOYxIATlD0Xkq8UyNTI'; $leadLink = '<a href="https://' . $arParams['auth']['domain'] . '/crm/lead/details/' . $arLead['ID'] . '/">Создан новый лид №' . $arLead['TITLE'] . '</a>';
//    $linkTel = '<a href="tel:' . $arLead['PHONE']['0']['VALUE'] . '">' . $arLead['PHONE']['0']['VALUE'] . '</a>';
//    $message = $leadLink . "\n Имя: <strong>" . $arLead['NAME'] . "</strong>\n тел: " . $linkTel;
//}
if($memberId === '986202eae13f5a77690fd2e208d23890' && !empty($arLead)) {
    //staprop

    $idGroup = '-1002636125156';
    $keyBot = '5830465058:AAFxwYJYDZgf-m8uLTG_YWctGKAjhD3wRCM';
    p($keyBot, "keyBot", $logTg);
    $leadLink = '<a href="https://' . $arParams['auth']['domain'] . '/crm/lead/details/' . $arLead['ID'] . '/">Создан новый лид - ' . $arLead['TITLE'] . '</a>';
    $linkTel = '<a href="tel:' . $arLead['PHONE']['0']['VALUE'] . '">' . $arLead['PHONE']['0']['VALUE'] . '</a>';
    $region = "Регион интереса: " . $regionMap[$arLead['UF_CRM_1696051119229'][0]] . "\n";
    $country = "Страна: " . $countryMap[$arLead['UF_CRM_1644839701548']] . "\n";
    $lang = "Язык: " . $langMap[$arLead['UF_CRM_1644839852091']] . "\n";
    $send = "Мессенджер: " . $sendToMap[$arLead['UF_CRM_1658820303']] . "\n";
    $city = "Город: " . $arLead['UF_CRM_1644839746626'] . "\n";
    $comment = "Комментарий: " . $arLead['COMMENTS'] . "\n";
    $wish = "Пожелания: " . $arLead['UF_CRM_1632393769490'] . "\n";
    $source = "Источник: " . $sourceMap[$arLead['SOURCE_ID']] . "\n";
    $message = $leadLink . "\n Имя: <strong>" . $arLead['NAME'] . "</strong>\n тел: " . $linkTel . "\n" . $send . $wish . $comment . $region . $country . $city . $lang . $source;


    if (!empty($idGroup) && !empty($keyBot) && !empty($message)) {


        $resSend = mesSendGroupTg($message, $idGroup, $keyBot);
        p($resSend, "resSendTGAll", $logTg);
        if ($arLead['UF_CRM_1696051119229'][0] == 7164) {
            $idGroupTg = '-1002338360376';

            $resSendTo = mesSendGroupTg($message, $idGroupTg, $keyBot);
            $logD = __DIR__ . "/logDubay.txt";
            p($resSendTo, "resSendDub", $logD);

        }
        if ($arLead['UF_CRM_1696051119229'][0] == 7170) {
            $idGroupTg = '-4944463929';
            $resSendTo = mesSendGroupTg($message, $idGroupTg, $keyBot);
            $logI = __DIR__ . "/logIstambul.txt";
            p($resSendTo, "resSendDub", $logI);

        }
    }
}
//function mesSendGroupTg($message, $idGroup, $keyBot, $memberId){
//    $log = __DIR__."/logMesSendGoupTg".$memberId.".txt";
//    $microtime = microtime(true);
//    $timestamp = date('d-m-Y-H-i-s') . '-' . substr($microtime, -6); // Например: 12-06-2025-13-16-41-123456
//
//    // Логируем факт отправки
//    file_put_contents($log, "[$timestamp] Sending message to group $idGroup\n", FILE_APPEND);
//
//    $data = [
//        'chat_id' => $idGroup,
//        'text' => $message,
//        'parse_mode' => 'HTML',
//        'disable_web_page_preview' => 'true'
//    ];
//
//    $dataStr = http_build_query($data);
//
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
//
//    // Логируем ответ
//    file_put_contents($log, "[$timestamp] Response: " . json_encode($responseData) . "\n", FILE_APPEND);
//
//    if ($responseData['ok']) {
//        echo "Message sent successfully!";
//    } else {
//        echo "Error: " . $responseData['description'];
//    }
//
//    return $responseData;
//}
