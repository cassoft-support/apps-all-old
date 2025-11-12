<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST, "start", $log);
//$keyAuth = "Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOjEwMTgsImNvbXBhbnlfaWQiOjM0ODIyNCwiaWF0IjoxNzMyNzgyNzUxfQ.fi8Y7hnKbllMNR-NCYsiy5a2ME_yvN2K2zUHauFi4mDCyQOS2AnEh8VYPJcH47CWexFCT_Wtdql0MZHt_8Jvu_lpYBFtVn7a-iqsXTyGMLsaXsU307KjXl-9xxU-c7mcyl-gEfGjpK6cQmcSZunjuROJ4fTYm3qczJvUxSZvca-sWOfH7PRz5pdGS13MFDi1oVZJ5sH1YBg4reivj-EphZ87UzMXKM9Ic6FV2AWWRaOIcBSDF1z8pgaMoHBwzj5r0CRMSLSg6iKZOONqjTgmsl6g2e7lJ3inIvmqRT0XPGdPP6niDXW4VX6OwlCC3mgwpRuifK3z3EJpu0DAr2OeSA";
////$ch = curl_init('https://public-api.domclick.ru/chats/v1/chats/'.$chatId[2]); //подписаться
//$dataMes = [
//    //"url" =>'https://app.cassoft.ru/cassoftApp/market/domclickMessager/in/6b15cf5c0d6cb45b1f6d14ebb633b6de.php',
//    "url" =>'https://app.cassoft.ru/cassoftApp/market/domclickMessager/in/d17d1d15669be52925cf091ae22002d4.php',
//  "types"=> [
//    "new_messages"
//  ],
//  "description"=> "gepta"
//];
////
//$get = array(
//    'agent_id'  => 4985660,
//);

//$ch = curl_init('https://public-api.domclick.ru/chats/v1/chats/?' . http_build_query($get)); //подписаться
//$ch = curl_init('https://public-api.domclick.ru/chats/v1/agents/'); //подписаться
//$ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/'); //подписаться
//$ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/unsubscribe'); //подписаться
//curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($keyAuth, 'Content-Type: application/json' ));
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataMes, JSON_UNESCAPED_UNICODE));
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_HEADER, false);
//$res = curl_exec($ch);
//curl_close($ch);
//
////$res = json_encode($res, JSON_UNESCAPED_UNICODE);
//$result = json_decode($res, true);
//p($result , "result", $log);
//pr($result , "result");

?>
<script>
   window.location.href = 'https://cassoft.ru/app_domclick_messager/';
</script>
