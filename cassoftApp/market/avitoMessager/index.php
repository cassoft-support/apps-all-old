<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST, "start", $log);
//pr($_REQUEST);
//$arParams = $_REQUEST;
//$arParams["app"]="financier";
//$arParams["appType"]="financier";
//$APPLICATION->IncludeComponent(
//    "install:base",
//    "financier",
//    $arParams,
//    false
//);
//$auth = new \CSlibs\B24\Auth\Auth("cian_messager", [], $_REQUEST["member_id"]);
//$imageData = file_get_contents('https://city.brokci.ru/local/images/avatar/botDC.png');
//
//// Проверяем, удалось ли получить данные
//if ($imageData === false) {
//    return false;
//}
//
//// Кодируем данные изображения в base64
//$imageBase64 = base64_encode($imageData);
//if (isValidBase64($imageBase64)) {
//    echo "Строка base64 корректна и является изображением.";
//} else {
//    echo "Строка base64 некорректна или не является изображением.";
//}
//pr($imageBase64, '');
//$base64Image = imageToBase64('https://city.brokci.ru/local/images/avatar/test.jpg');
//pr($base64Image, '');
//pr(isValidBase64($base64Image), 'one');
//$base64Image = base64_encode(file_get_contents('https://city.brokci.ru/local/images/avatar/botCian.png'));
//pr($base64Image, '');
////pr(isValidBase64($base64Image));
//$resultEvent = $auth->CScore->call('imbot.update',
//    Array(
//        'BOT_ID' => 18,
//        'FIELDS' => Array(
//            'EVENT_HANDLER' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//             'EVENT_MESSAGE_ADD' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//               'EVENT_WELCOME_MESSAGE' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//               'EVENT_BOT_DELETE' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//               'EVENT_MESSAGE_UPDATE' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//               'EVENT_MESSAGE_DELETE' => 'https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php',
//            'PROPERTIES' => Array(
//                'NAME' => 'UpdatedBot2',
//                'PERSONAL_PHOTO' => $base64Image,
//            )
//        )
//    ));
//pr($resultEvent, '');
//$register = $auth->CScore->call('imbot.register',
//    Array(
//        'CODE' => 'newbot2',
//        'TYPE' => 'B',
//        'EVENT_HANDLER' => 'http://www.hazz/chatApi/event.php',
//        'OPENLINE' => 'Y',
//        'CLIENT_ID' => '',
//        'PROPERTIES' => Array(
//            'NAME' => 'NewBot',
//            'LAST_NAME' => '',
//            'COLOR' => 'GREEN',
//            'EMAIL' => 'test@test.ru',
//            'PERSONAL_BIRTHDAY' => '2016-03-11',
//            'WORK_POSITION' => 'Лучший сотрудник',
//            'PERSONAL_WWW' => 'http://test.ru',
//            'PERSONAL_GENDER' => 'F',
//            'PERSONAL_PHOTO' => $base64Image,
//        )
//    ));
//pr($register, '');
//function isValidBase64($base64) {
//    // Проверяем, соответствует ли строка формату base64
//    if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $base64)) {
//        // Пытаемся декодировать строку
//        $decodedData = base64_decode($base64, true);
//        if ($decodedData !== false) {
//            // Проверяем, является ли декодированная строка допустимым изображением
//            $imageInfo = getimagesizefromstring($decodedData);
//            return $imageInfo !== false;
//        }
//    }
//    return false;
//}
//function imageToBase64($imageUrl) {
//    // Получаем содержимое изображения по URL
//    $imageData = file_get_contents($imageUrl);
//
//    // Проверяем, удалось ли получить данные
//    if ($imageData === false) {
//        return false;
//    }
//
//    // Кодируем данные изображения в base64
//    $base64 = base64_encode($imageData);
//    // Получаем MIME-тип изображения
//    $finfo = new finfo(FILEINFO_MIME_TYPE);
//
//    $mimeType = $finfo->buffer($imageData);
//    // Формируем строку base64 с префиксом
//    return 'data:' . $mimeType . ';base64,' . $base64;
//}
//$connectorAdd = $auth->CScore->call(
//    "imconnector.register",
//    [
//        "ID" => "CS_cian_connector",
//        "NAME" => "CS ЦИАН",
//        "ICON" => [
//            "DATA_IMAGE" => 'data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" version="1.0" width="640.000000pt" height="640.000000pt" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet"%3e%3cg transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="%230468ff" stroke="none"%3e%3cpath d="M3070 6394 c-444 -29 -775 -105 -1147 -264 -378 -162 -816 -472 -1067 -755 -491 -555 -762 -1156 -837 -1855 -15 -148 -15 -492 0 -640 46 -430 184 -870 385 -1228 164 -292 414 -612 621 -796 360 -318 711 -530 1114 -670 359 -125 680 -179 1061 -179 226 0 366 12 570 49 340 62 691 186 977 347 320 180 622 420 827 657 306 354 503 687 640 1079 125 360 179 678 179 1061 0 383 -54 701 -179 1061 -140 403 -352 754 -670 1114 -184 207 -504 457 -796 621 -357 201 -817 345 -1223 383 -128 12 -376 20 -455 15z m728 -1500 c69 -34 155 -128 181 -199 26 -68 28 -182 6 -246 -9 -24 -41 -89 -72 -144 -183 -322 -269 -469 -278 -471 -10 -3 -307 512 -346 601 -33 74 -31 190 4 268 33 73 118 163 182 193 43 21 66 24 161 24 98 0 116 -3 162 -26z m-448 -1249 l135 -135 5 122 5 123 140 0 140 0 3 -271 2 -270 110 -109 c61 -59 110 -113 110 -119 0 -14 -271 -286 -285 -286 -6 0 -122 111 -258 248 l-247 247 -247 -247 c-137 -137 -253 -248 -258 -248 -6 0 -75 65 -155 145 l-145 145 395 395 c217 217 399 395 405 395 6 0 71 -61 145 -135z m-172 -1752 l-3 -298 -80 0 -80 0 -5 149 -5 149 -65 -59 c-163 -147 -280 -244 -294 -244 -14 0 -16 33 -16 295 l0 295 85 0 85 0 0 -146 0 -145 108 92 c59 52 136 119 171 151 106 96 102 107 99 -239z m477 190 c176 -314 258 -474 247 -485 -5 -5 -44 -8 -88 -6 l-79 3 -23 43 -23 42 -109 0 -108 0 -26 -45 -27 -45 -79 0 c-43 0 -82 4 -85 10 -5 7 151 305 246 470 11 19 30 54 41 78 12 23 29 42 38 42 9 0 39 -42 75 -107z m-1597 -128 l2 -220 101 1 100 1 2 219 2 219 85 0 85 0 3 -222 2 -222 29 5 c16 3 38 1 50 -5 20 -11 21 -20 21 -134 0 -82 -4 -127 -12 -135 -12 -12 -114 -17 -142 -6 -12 5 -16 21 -16 70 l0 64 -237 2 -238 3 -3 293 -2 293 82 -3 83 -3 3 -220z m2097 115 l0 -105 95 -5 c52 -3 98 -4 103 -2 4 2 7 52 7 113 l0 109 85 0 85 0 0 -295 0 -296 -82 3 -83 3 -3 108 -3 107 -99 0 -100 0 0 -98 c0 -63 -4 -102 -12 -110 -7 -7 -41 -12 -84 -12 -56 0 -73 3 -78 16 -8 21 -8 538 0 559 5 13 20 15 88 13 l81 -3 0 -105z"/%3e%3cpath d="M3575 4701 c-34 -14 -74 -62 -85 -100 -38 -144 133 -248 245 -150 57 50 67 142 22 200 -37 48 -126 72 -182 50z"/%3e%3cpath d="M3552 1865 c-12 -25 -22 -48 -22 -50 0 -3 23 -5 51 -5 28 0 49 3 47 8 -3 4 -13 26 -23 50 -10 23 -21 42 -25 42 -3 0 -16 -20 -28 -45z"/%3e%3c/g%3e%3c/svg%3e',
//            "COLOR" => "#fff",
//            "SIZE" => "100%",
//            "POSITION" => "center",
//        ],
//        "ICON_DISABLED" => [
//            "DATA_IMAGE" => 'data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" version="1.0" width="640.000000pt" height="640.000000pt" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet"%3e%3cg transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="%230468ff" stroke="none"%3e%3cpath d="M3070 6394 c-444 -29 -775 -105 -1147 -264 -378 -162 -816 -472 -1067 -755 -491 -555 -762 -1156 -837 -1855 -15 -148 -15 -492 0 -640 46 -430 184 -870 385 -1228 164 -292 414 -612 621 -796 360 -318 711 -530 1114 -670 359 -125 680 -179 1061 -179 226 0 366 12 570 49 340 62 691 186 977 347 320 180 622 420 827 657 306 354 503 687 640 1079 125 360 179 678 179 1061 0 383 -54 701 -179 1061 -140 403 -352 754 -670 1114 -184 207 -504 457 -796 621 -357 201 -817 345 -1223 383 -128 12 -376 20 -455 15z m728 -1500 c69 -34 155 -128 181 -199 26 -68 28 -182 6 -246 -9 -24 -41 -89 -72 -144 -183 -322 -269 -469 -278 -471 -10 -3 -307 512 -346 601 -33 74 -31 190 4 268 33 73 118 163 182 193 43 21 66 24 161 24 98 0 116 -3 162 -26z m-448 -1249 l135 -135 5 122 5 123 140 0 140 0 3 -271 2 -270 110 -109 c61 -59 110 -113 110 -119 0 -14 -271 -286 -285 -286 -6 0 -122 111 -258 248 l-247 247 -247 -247 c-137 -137 -253 -248 -258 -248 -6 0 -75 65 -155 145 l-145 145 395 395 c217 217 399 395 405 395 6 0 71 -61 145 -135z m-172 -1752 l-3 -298 -80 0 -80 0 -5 149 -5 149 -65 -59 c-163 -147 -280 -244 -294 -244 -14 0 -16 33 -16 295 l0 295 85 0 85 0 0 -146 0 -145 108 92 c59 52 136 119 171 151 106 96 102 107 99 -239z m477 190 c176 -314 258 -474 247 -485 -5 -5 -44 -8 -88 -6 l-79 3 -23 43 -23 42 -109 0 -108 0 -26 -45 -27 -45 -79 0 c-43 0 -82 4 -85 10 -5 7 151 305 246 470 11 19 30 54 41 78 12 23 29 42 38 42 9 0 39 -42 75 -107z m-1597 -128 l2 -220 101 1 100 1 2 219 2 219 85 0 85 0 3 -222 2 -222 29 5 c16 3 38 1 50 -5 20 -11 21 -20 21 -134 0 -82 -4 -127 -12 -135 -12 -12 -114 -17 -142 -6 -12 5 -16 21 -16 70 l0 64 -237 2 -238 3 -3 293 -2 293 82 -3 83 -3 3 -220z m2097 115 l0 -105 95 -5 c52 -3 98 -4 103 -2 4 2 7 52 7 113 l0 109 85 0 85 0 0 -295 0 -296 -82 3 -83 3 -3 108 -3 107 -99 0 -100 0 0 -98 c0 -63 -4 -102 -12 -110 -7 -7 -41 -12 -84 -12 -56 0 -73 3 -78 16 -8 21 -8 538 0 559 5 13 20 15 88 13 l81 -3 0 -105z"/%3e%3cpath d="M3575 4701 c-34 -14 -74 -62 -85 -100 -38 -144 133 -248 245 -150 57 50 67 142 22 200 -37 48 -126 72 -182 50z"/%3e%3cpath d="M3552 1865 c-12 -25 -22 -48 -22 -50 0 -3 23 -5 51 -5 28 0 49 3 47 8 -3 4 -13 26 -23 50 -10 23 -21 42 -25 42 -3 0 -16 -20 -28 -45z"/%3e%3c/g%3e%3c/svg%3e',
//            "SIZE" => "100%",
//            "POSITION" => "center",
//            "COLOR" => "#ffb3a3",
//        ],
//        "PLACEMENT_HANDLER" => "https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/handler.php",
//    ]
//);
//pr($connectorAdd, "");

//$externalId = '224';
//$resExternalId = explode('_', $externalId);
//echo count($resExternalId);
//echo $resExternalId[0];
//$resultEvent = $auth->CScore->call('imbot.bot.list',);
//pr($resultEvent, "");
$paramsConnector=[
    'CONNECTOR' => 'cs_cian_connector',
    'LINE' => 24,
    'ACTIVE' => 1,
];
pr($paramsConnector , "paramsConnector");
//$activate = $auth->CScore->call(
//    'imconnector.activate',$paramsConnector
//);
//pr($activate, '');
//$imconnector = $auth->CScore->call('imconnector.status', [
//        'CONNECTOR'=>'cs_cian_connector',
//    'LINE' => 24,
//]);
//pr($imconnector);
//$imconnectorList = $auth->CScore->call('imconnector.list', );
//pr($imconnectorList);
//$resultEvent = $auth->CScore->call(
//  "event.bind",
// //"event.get",
// //"event.unbind",
//   [
////////"event" => "ONIMBOTMESSAGEADD",
////////"handler" => "https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/cianBot.php"
//      // "event" => "OnImConnectorMessageAdd",
//       "event" => "ONIMCONNECTORMESSAGEADD",
//       "handler" => "https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/OnImConnectorMessageAdd.php",
// ]
//);
pr($resultEvent, "");
$resultEvent = $auth->CScore->call(
//////  "event.bind",
 "event.get",
// "event.unbind",
//   [
////////"event" => "ONIMBOTMESSAGEADD",
////////"handler" => "https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/cianBot.php"
//       "event" => "OnImConnectorMessageAdd",
//       "handler" => "https://app.cassoft.ru/cassoftApp/market/cianMessager/ajax/OnImConnectorMessageAdd.php",
// ]
);
pr($resultEvent, "");
?>
<script>
    // window.location.href = "https://brokci.bitrix24.site";
</script>
