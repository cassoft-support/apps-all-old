<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandlerTb.txt";

$update = json_decode(file_get_contents('php://input'), true);
p($update , "start", $log);

//https://api.telegram.org/botYOUR_TOKEN/getUpdates
//
//$idGroup='-1002659134259';
//$keyBot = '7776881648:AAGovDHouhj3q-OeCOYxIATlD0Xkq8UyNTI';
//
//$message = "Сообщение прочитал" ;
//
//$data = [
//    'chat_id' =>$idGroup,
//    'text' => "Сообщение прочитал",
////    'parse_mode' => 'HTML',
////    'disable_web_page_preview'=>'true'
//];
//p($data , "data", $log);
//$dataStr = http_build_query($data);
//
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$keyBot."/sendMessage");
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//$response = curl_exec($ch);
//curl_close($ch);
//
//$responseData = json_decode($response, true);
//p($responseData , "responseData", $log);