<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
echo gethostbyname("chat-api.ext.vertis.yandex.net");
//from telethon.sync import TelegramClient
//from telethon.tl.functions.contacts import ImportContactsRequest
//from telethon.tl.types import InputPhoneContact
//
//# ⚠️ Вставь сюда свои данные
//api_id = 123456        # <-- замени на свой api_id
//api_hash = 'your_hash_here'  # <-- замени на свой api_hash
//target_phone = '+77001112233'  # <-- номер, который хочешь проверить
//
//client = TelegramClient('check_session', api_id, api_hash)
//client.start()  # Введёшь код, который придёт в Telegram
//
//# Добавляем номер во временные контакты
//contact = InputPhoneContact(client_id=0, phone=target_phone, first_name='Test', last_name='Check')
//result = client(ImportContactsRequest([contact]))
//
//# Проверка
//if result.users:
//    print("✅ Этот номер есть в Telegram")
//else:
//    print("❌ Этого номера нет в Telegram");




// Данные для отправки сообщения

// Данные для отправки сообщения
//$data = [
//    'sender' => [
//        'name' => 'CassoftBot'
//    ],
//    'recipient' => [
//        'id' => 'bc6aafd46c0072f64a73386ff935d731'
//    ],
//    'message' => [
//        'type' => 'text',
//        'text' => 'Тестовое сообщение от CassoftBot333'
//    ]
//];
//
//$url = 'https://chat-api.ext.vertis.yandex.net/api/v1/chats/operator/message?token=DVapro123';
//
//$ch = curl_init($url);
//
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, [
//    'Content-Type: application/json',
//    'Authorization: Bearer krs0613-61a1a55d6648382551cef2b7935abb40c91f2ac2'
//]);
//
//// ⛔ Отключаем SSL-проверку (только для отладки, не для продакшн)
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//
//// Выполнение запроса и отладка
//$response = curl_exec($ch);
//
//if (curl_errno($ch)) {
//    echo "❌ cURL ошибка: " . curl_error($ch) . "\n";
//} else {
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    echo "✅ HTTP Code: $httpCode\n";
//    echo "Ответ API:\n";
//    var_dump($response);
//}
//
//curl_close($ch);


