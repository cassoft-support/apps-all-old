<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit('600');
ini_set('memory_limit', '1024M');
ini_set('max_input_vars', '5000');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$_SERVER['DOCUMENT_ROOT'] = '/var/www/bitirx-brokci/data/www/app.cassoft.ru';

// Проверяем путь
$prologPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
if (!file_exists($prologPath)) {
    die("Файл не найден: $prologPath\n");
}

require_once($prologPath);

$log = __DIR__ . "/logTemp.txt";

$arParams['member_id'] ='b176cc28ddd836fa2c7d93e81fff15df';
$authParams = [];

$auth = new \CSlibs\B24\Auth\Auth('mcm', $authParams, $arParams['member_id'], 'tgstat');

$json = file_get_contents('https://app.cassoft.ru/local/components/event/tgstat/templates/desctop/data.json');
$arData  = json_decode($json, true);
//pr($arData , '');
p(count($arData)."\n" , 'start', $log);
foreach ($arData as $key => $item){
 //   p($item , 'start', $log);
$data = [];
$data = [
'ENTITY'=> 'events',
'NAME'=>$item['telegram_id'],
'PROPERTY_VALUES'=>[
'telegram_id' => $item["telegram_id"],
'first_name' => $item["first_name"],
'last_name' => $item["last_name"],
'username' => $item["username"],
'is_bot' => $item["is_bot"],
'is_premium' => $item["is_premium"],
'user_link' => $item["user_link"],
'subscribed' => $item["subscribed"],
'unsubscribe_date' => $item["unsubscribe_date"],
'days_in_channel' => $item["days_in_channel"],
"utm_link"=> $item["utm_link"],
"utm_source"=> $item["utm_source"],
"utm_medium"=> $item["utm_medium"],
"utm_campaign"=> $item["utm_campaign"],
"utm_content"=> $item["utm_content"],
"utm_term"=> $item["utm_term"],
'bot_id' => $item['bot_id'],//'Источник заявки (например: бот, канал, реклама и т.п.)',
'source_id' => $item['source_id'],//'Источник заявки (например: бот, канал, реклама и т.п.)',
    'subscription_date' =>$item["subscribe_date"]
]];


//p($data , 'data', $log);
$itemAdd = $auth->CScore->call( 'entity.item.add', $data);
//p($itemAdd , "itemAdd", $log2);

    if (($key + 1) % 20 === 0) {
        echo "Пауза на 2 секунды...\n";
        p($itemAdd , $key."-".$item["telegram_id"], $log);
    }

}

