<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logReqBank.txt";

$accessToken='ZjE5MDRlN2MtYjgxMC00ZGU4LTgxZTUtZTVmMDAwMWJlMGE1MzE0MzgzYmUtNTNiNi00Mjk0LWFmYzMtNTljNmRlOGNjN2Zj';
$id=json_encode('8ce3be7b-f1bb-4879-84f1-acda01b2a14a');
//$id=json_encode('40702810370010261148');
// URL API, к которому вы хотите обратиться
//$apiUrl = 'https://api.modulbank.ru/v1/account-info/balance/8ce3be7b-f1bb-4879-84f1-acda01b2a14a'; //
//$apiUrl = 'https://api.modulbank.ru/v1/operation-history/8ce3be7b-f1bb-4879-84f1-acda01b2a14a'; //
$apiUrl = 'https://api.modulbank.ru/v1/operation-upload/sign';
//$apiUrl = 'https://api.modulbank.ru/v1/account-info/balance-details/8ce3be7b-f1bb-4879-84f1-acda01b2a14a'; //
//$apiUrl = 'https://api.modulbank.ru/v1/account-info/balance/40702810370010261148'; //
//$apiUrl = 'https://api.modulbank.ru/v1/account-info/balance/'; //
//$apiUrl = 'https://api.modulbank.ru/v1/account-info'; //
//$apiUrl = 'https://api.modulbank.ru/v1/documents';
pr($apiUrl, '');
//[id] => 24534659-8cd4-4dde-b9d3-b27b01902fb2
// Данные, которые вы хотите отправить (если требуется)
// Путь к файлу с подписью
$signatureFile = 'cas.p7s';
// Чтение содержимого файла подписи
$signatureContent = file_get_contents($signatureFile);
// Преобразование в Base64
//$signatureBase64 = base64_encode(htmlspecialchars($signatureContent));
$signatureBase64 = base64_encode($signatureContent);
pr($signatureBase64, '');
$postData = [
    "Operations"=> [
    "c7054b79-ac43-4f81-a1d2-b28002fdc5b8",
],
"SignBase64"=> $signatureBase64,
];
$ch = curl_init();
// Установка параметров cURL
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));

// Выполнение запроса и получение ответа
$response = curl_exec($ch);
pr($response, 'response');
// Проверка на ошибки
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    // Декодирование JSON-ответа
    $responseData = json_decode($response, true);
    pr($responseData); // Вывод данных
}

// Закрытие cURL-сессии
curl_close($ch);

/*
$methodAndKey = 'operation-history/8ce3be7b-f1bb-4879-84f1-acda01b2a14a';
$methodAndKey = 'account-info';
//$methodAndKey = 'account-info/balance/58c20343-5d3b-422c-b98b-a5ec037df782';

$params = [
   // 'category' => 'Debet',
 //   'records'  => 10,
];

$isSandbox = 1;

##### Тут, скорее всего, ничего не надо менять! #######

$encoded = json_encode($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.modulbank.ru/v1/' . $methodAndKey);

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
];
if ($isSandbox) {
    $headers[] = 'sandbox: on';
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$output = curl_exec($ch);
pr($output, 'output');
curl_close($ch);

##### Результат #######
$ans = json_decode($output, true);

pr($ans);*/