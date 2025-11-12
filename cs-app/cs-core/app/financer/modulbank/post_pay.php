<?php
// post_pay.php

// Получаем данные из POST-запроса
$merchant = $_POST['merchant'] ?? '';
$amount = $_POST['amount'] ?? '';
$description = $_POST['description'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$success_url = $_POST['success_url'] ?? '';
$client_email = $_POST['client_email'] ?? '';
$client_name = $_POST['client_name'] ?? '';
$client_phone = $_POST['client_phone'] ?? '';
$unix_timestamp = $_POST['unix_timestamp'] ?? '';
$salt = $_POST['salt'] ?? '';
$testing = $_POST['testing'] ?? '';
$callback_url = $_POST['callback_url'] ?? '';
$callback_on_failure = $_POST['callback_on_failure'] ?? '';
$lifetime = $_POST['lifetime'] ?? '';
$signature = $_POST['signature'] ?? '';

// Секретный ключ (должен быть в .env или конфиге)
$secretKey = '2C71BACA7FB4C8053768FCF07542E36A';

// Формируем массив данных
$data = [
    'merchant' => $merchant,
    'amount' => $amount,
    'description' => $description,
    'order_id' => $order_id,
    'success_url' => $success_url,
    'client_email' => $client_email,
    'client_name' => $client_name,
    'client_phone' => $client_phone,
    'unix_timestamp' => $unix_timestamp,
    'salt' => $salt,
    'testing' => $testing,
    'callback_url' => $callback_url,
    'callback_on_failure' => $callback_on_failure,
    'lifetime' => $lifetime,
    'signature' => $signature,
];

// Отправляем POST-запрос к банку
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://pay.modulbank.ru/pay');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Отправляем запрос
$response = curl_exec($ch);

// Проверяем ошибки
if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo "Ошибка при выполнении запроса: $error";
    exit;
}

curl_close($ch);

// Возвращаем ответ
header('Content-Type: text/plain');
echo $response;