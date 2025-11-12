<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logAddKey.txt";
p($_POST, "start", $log);

if($_POST['app'] && $_POST['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['member_id']);
    $hhClass = new \CSlibs\App\HR\hhClass($auth, $_POST['app'], $_POST['user_id']);
    $hhKey = $hhClass->hhKey($_POST['app']);
    $authorizationCode = $_POST['keyHH'];
    $redirectUri = 'https://app.cassoft.ru/cassoftApp/market/hr/ajax/handlerHh.php';

// URL для получения токена
    $url = 'https://hh.ru/oauth/token';

// Параметры POST-запроса
    $postFields = [
        'grant_type' => 'authorization_code',
        'client_id' => $hhKey['ID'],
        'client_secret' => $hhKey['KEY'],
        'code' => $authorizationCode,
        'redirect_uri' => $redirectUri,
    ];

// Инициализация cURL
    $ch = curl_init($url);

// Настройка параметров cURL
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);

// Выполнение запроса и получение ответа
    $response = curl_exec($ch);

// Проверка на ошибки
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        // Декодирование JSON-ответа
        $responseData = json_decode($response, true);
        p($responseData , "responseData", $log);
    }
    $setupKey= json_decode($_POST['setupKeyHH'],true);
// Закрытие cURL-сессии
    curl_close($ch);
$dateClose = strtotime(date('c'))+$responseData['expires_in'];
    $setupKey[$_POST['user_id']] =[
            'access_token'=>   $responseData['access_token'],
            'refresh_token' => $responseData['refresh_token'],
             'date_close' => $dateClose,
    ];

 //   $setupKey[$_POST['user_id']] = $_POST['keyHH'];
p($setupKey , "setupKey", $log);
    $paramsUp = [
        'ENTITY' => 'setup',
        'ID'=> $_POST['id'],
        'PROPERTY_VALUES'=>[
            'CS_HH_KEY' => json_encode($setupKey)
        ]
    ];
    p($paramsUp, "paramsUp", $log);
    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
}