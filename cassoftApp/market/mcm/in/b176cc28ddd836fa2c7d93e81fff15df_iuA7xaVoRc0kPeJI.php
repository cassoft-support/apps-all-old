 <?php
 header("Content-Type: application/json");
 header("HTTP/1.1 200 OK");
 echo json_encode(['messages' => [['status' => 'SUCCESS']]]);

 fastcgi_finish_request();

 // Получаем данные
 $result = json_decode(file_get_contents("php://input"), true);

$fileInfo = pathinfo(basename(__FILE__))["filename"];
 $resName = explode("_", $fileInfo);
 $memberId = $resName[0];
 $profile = $resName[1];

        $arParams["message"] = $result;
         $arParams["tempList"] = "sendIn";
         $arParams["app"] = "mcm";
         $arParams["app_code"] = "auto_ru";
         $arParams["member_id"] = $memberId;
         $arParams["profile"] = $profile;

 // Запускаем фоновый обработчик

 // Отправляем POST-запрос на внутренний обработчик
 $ch = curl_init("https://app.cassoft.ru/cassoftApp/market/mcm/in/async_processor.php");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arParams));
 curl_setopt($ch, CURLOPT_HTTPHEADER, [
     'Content-Type: application/json'
 ]);
 curl_exec($ch);
 curl_close($ch);


