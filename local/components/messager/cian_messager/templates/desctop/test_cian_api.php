<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logTestApi.txt";

// ========== НАСТРОЙКИ ==========
$chatId = 211975382; // Тестовый chatId
$member_id = 'a74856ff01a11150820364c31e77bf6a'; // Из лога

p("=== ТЕСТ API ЦИАН ===", "start", $log);
p("ChatId: " . $chatId, "chatId", $log);

// Получаем API ключ
$auth = new \CSlibs\B24\Auth\Auth('cian_messager', [], $member_id);
$params = [
    'ENTITY' => 'setup_messager',
    'sort' => [],
    'filter' => [],
];
$resSetup = $auth->CScore->call('entity.item.get', $params)[0];
$apiKey = $resSetup['PROPERTY_VALUES']['CS_KEY_CIAN'];

p("API Key получен", "apiKey", $log);

// Запрос к API ЦИАН для получения информации о чате
$url = "https://public-api.cian.ru/v1/get-chat/{$chatId}";
p("URL: " . $url, "url", $log);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$apiKey}", "Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

p("HTTP Code: " . $httpCode, "httpCode", $log);
p($response, "response_raw", $log);

$chatData = json_decode($response, true);
p($chatData, "chatData_decoded", $log);

// Выводим результат на экран
echo "<pre>";
echo "HTTP Code: {$httpCode}\n\n";
echo "Response:\n";
print_r($chatData);
echo "</pre>";

p("=== КОНЕЦ ТЕСТА ===", "end", $log);
