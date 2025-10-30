<?php

function profileCsMcm($memberId){
    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
  $resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$memberId]);
  
  return $resListProfile;
}
function profileCsMcmAdd($data){
   $log = __DIR__."/logAdd.txt";
   p($data, "start", $log);
    $profileName = $data['profile_name'];
     $profile = translit($data['profile_name']);
      p($profile , "profile", $log);
    $fileName = '/cassoftApp/market/mcm/in/'.$data['member_id']."_".$profile.".php";
    p($fileName , "fileName", $log);
    $fileAdd = $_SERVER["DOCUMENT_ROOT"] .$fileName;
    global $content;

    //// Проверяем, существует ли файл
    if (!file_exists($fileAdd)) {
        //        // Если файл не существует, создаем его и записываем информацию
        if (file_put_contents($fileAdd, $content) !== false) {
            p("Файл ".$fileName." успешно создан и в него записана информация", "add", $log);
        } else {
            p("Ошибка при создании файла ".$fileName, "addError", $log);
        }
    } else {
        p("Файл ".$fileName." существует", "res", $log);
    }

    $postData=[
        'name'=>$profile,
        'webhook_url'=>ADDRESS_SITE.$fileName
    ];
    $urlPost = http_build_query($postData);
    if($data['type'] === 'Whatsapp'){
        $urlType = '/api';
    }else{
        $urlType = '/tapi';
    }
    $profileAddWappi =  sendPostWappi($urlType.'/profile/add?'.$urlPost, []);
    p($profileAddWappi , date('c'), $log);
    if(!empty($profileAddWappi['profile_id']) && $profileAddWappi['status'] === "done") {

        $result=[];
        $resCsCode = generateCodeCS();
        $params = [
            'UF_DATE_CREATE' => strtotime(date('c')),
            'UF_DATE_CLOSE' => strtotime('+30 days', strtotime(date('c'))),
            'UF_CS_CODE' => $resCsCode,
            'UF_PROFILE_NAME' => $profile,
            'UF_PROFILE_ID' => $profileAddWappi['profile_id'],
            'UF_MEMBER_ID' => $data['member_id'],
            'UF_DOMAIN' => $data['domain'],
            'UF_ACTIVE' => 'Y',
            'UF_RESOURCE' => 'wappi',
            'UF_TYPE' => $data['type'],
            'UF_NAME' => $profileName,

        ];
        p($params , "params", $log);
        $result=[
            'profile_id'=>$profileAddWappi['profile_id'],
            'date_creat'=>strtotime(date('c')),
            'date_close'=>strtotime('+30 days', strtotime(date('c'))),
            'type'=>$data['type'],
            'profile_name'=>$profile,
            'cs_code'=>$resCsCode,
            'name' =>$profileName

        ];
        $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');

        $profileAdd = $profileHb->elementAdd($params);
        p($profileAdd , "profileAdd", $log);
        sleep(3);
        $dataHuk=["authorization_status", "incoming_message", "delivery_status", "outgoing_message_phone", "outgoing_message_api", "incoming_call", "application_status"];
        p($dataHuk , date('c'), $log);
        $profileWebHukWappi =  sendPostWappi('/api/webhook/types/set?profile_id='.$profileAddWappi['profile_id'], $dataHuk);
        p($profileWebHukWappi , date('c'), $log);
        if ($profileAdd > 0){
            p($result , "result", $log);
            return $result;
        }

    }
}
function profileCsMcmWappiAdd($data){
    $log = __DIR__."/logAdd.txt";
    p($data, "start", $log);
    $profile = $data['profile']['profileName'];
    p($profile , "profile", $log);
    $fileName = '/cassoftApp/market/mcm/in/'.$data['member_id']."_".$profile.".php";
    p($fileName , "fileName", $log);
    $fileAdd = $_SERVER["DOCUMENT_ROOT"] .$fileName;
    global $content;
    //// Проверяем, существует ли файл
    if (!file_exists($fileAdd)) {
        //        // Если файл не существует, создаем его и записываем информацию
        if (file_put_contents($fileAdd, $content) !== false) {
            p("Файл ".$fileName." успешно создан и в него записана информация", "add", $log);
        } else {
            p("Ошибка при создании файла ".$fileName, "addError", $log);
        }
    } else {
        p("Файл ".$fileName." существует", "res", $log);
    }

    $postData=[
        'name'=>$profile,
        'webhook_url'=>ADDRESS_SITE.$fileName
    ];
    $urlPost = http_build_query($postData);
    if($data['profile']['type'] === 'Whatsapp' || $data['profile']['type'] === 'whatsapp'){
        $urlType = '/api';
    }else{
        $urlType = '/tapi';
    }
    $profileAddWappi =  sendPostWappi($urlType.'/profile/add?'.$urlPost, []);
    p($profileAddWappi , date('c'), $log);
    if(!empty($profileAddWappi['profile_id']) && $profileAddWappi['status'] === "done") {

        $result=[];

        $params = [

            'UF_DATE_CLOSE' => $data['profile']['dateClose'],
            'UF_PROFILE_ID' => $profileAddWappi['profile_id'],
            'UF_ACTIVE' => 'Y',

        ];
        p($params , "params", $log);
        $result=[
            'profile_id'=>$profileAddWappi['profile_id'],
            'date_close'=>$data['profile']['dateClose'],
        ];
        $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
        $el= $profileHb->getByFilterList(['UF_CS_CODE' => $data['profile']['csCode']]);
        $profileUp= $profileHb->elementUpdate($el[0]['ID'], $params);

        p($profileUp , "profileUp", $log);
        sleep(3);
        $dataHuk=["authorization_status", "incoming_message", "delivery_status", "outgoing_message_phone", "outgoing_message_api", "incoming_call", "application_status"];
        p($dataHuk , date('c'), $log);
        $profileWebHukWappi =  sendPostWappi('/api/webhook/types/set?profile_id='.$profileAddWappi['profile_id'], $dataHuk);
        p($profileWebHukWappi , date('c'), $log);
        if ($profileUp > 0){
            p($result , "result", $log);
            return $result;
        }

    }
}
function profileCsMcmUpdate($data){
    $log = __DIR__."/logUpdate.txt";
    p($data, "start", $log);
    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
    $params = [
        'UF_LINE_B24' =>intVal($data['LINE']),
        'UF_CONNECTOR' => $data['CONNECTOR'],
        'UF_STATUS_B24' => intVal($data['ACTIVE_STATUS']),
    ];
    $el= $profileHb->getByFilterList(['UF_PROFILE_ID' => $data['profile']]);

    $elUp= $profileHb->elementUpdate($el[0]['ID'], $params);
   p($elUp , "elUp", $log);
   if($elUp>0){
       $res = 'Y';
   }else{
       $res = 'Error';
   }
    return $res;
}
function profileIdCsMcm($memberId, $profileId){
    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
  $resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$memberId, 'UF_PROFILE_ID' => $profileId ])[0];

  return $resListProfile;
}
function profileLineCsMcm($memberId, $line, $connector){
    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
  $resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$memberId, 'UF_LINE_B24' => $line, 'UF_CONNECTOR' => $connector ]);
  return $resListProfile;
}

function tokenWappi(){
 $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $tokenWappi = $HlApp->getByFilterList(['UF_APP_NAME'=>'mcm'])[0]['UF_TOKEN_WAPPI'];
return $tokenWappi;
}

function sendGetWappi($urlGet) {
    $curl = curl_init();
    $token = tokenWappi();
    $url = 'https://wappi.pro'.$urlGet;
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

function sendPostWappi($urlPost, $postData = []) {
    $log = __DIR__."/logSendPostWappi.txt";
    p($urlPost, "start", $log);
    p($postData, "postData", $log);
    $curl = curl_init();
    $token = tokenWappi();
    p($token , "token", $log);
//    $url = 'https://wappi.pro'.$urlPost;
//    curl_setopt_array($curl, array(
//        CURLOPT_URL => $url,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => '',
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 0,
//        CURLOPT_FOLLOWLOCATION => true,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => 'POST',
//        CURLOPT_POSTFIELDS => $postData,
//        CURLOPT_HTTPHEADER => array(
//            'Authorization: ' . $token,
//            'Content-Type: application/x-www-form-urlencoded'
//        ),
//    ));
//    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wappi.pro'.$urlPost,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Authorization:' . $token,
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
p($response , "response", $log);
    return json_decode($response, true);
}
function mesSendGroupTg($message, $idGroup, $keyBot){
    $log = __DIR__."/logMesSendGoupTg.txt";
    $microtime = microtime(true);
    $timestamp = date('d-m-Y-H-i-s') . '-' . substr($microtime, -6); // Например: 12-06-2025-13-16-41-123456

    // Логируем факт отправки
    file_put_contents($log, "[$timestamp] Sending message to group $idGroup\n", FILE_APPEND);

    $data = [
        'chat_id' => $idGroup,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => 'true'
    ];

    $dataStr = http_build_query($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $keyBot . "/sendMessage");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    // Логируем ответ
    file_put_contents($log, "[$timestamp] Response: " . json_encode($responseData) . "\n", FILE_APPEND);

    if ($responseData['ok']) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $responseData['description'];
    }

    return $responseData;
}

$content = ' <?php
      define("NO_KEEP_STATISTIC", true);
      define("NOT_CHECK_PERMISSIONS", true);
      require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
      $log = __DIR__."/logHandler.txt";
      $message = file_get_contents("php://input");
      $result=json_decode($message, true);
      p($result, "start", $log);
      $fileInfo = pathinfo(basename(__FILE__))["filename"];
      $resName = explode("_", $fileInfo);
       $memberId = $resName[0];
       $ProfileId = $resName[1];

      p($memberId, "memberId", $log);
      if ($memberId) {
          $CloudApp = "mcm";
          $appAccess = "app_" . $CloudApp . "_access";
          $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
          $clientsApp = $HlClientApp->searchID($memberId);
          p($clientsApp, "rest", $log);
          if ($clientsApp["ID"] > 0) {
             $arParams["message"] = $result;
              $arParams["tempList"] = "sendIn";
              $arParams["app"] = $CloudApp;
              $arParams["member_id"] = $memberId;
              $arParams["profile_id"] = $ProfileId;
              $APPLICATION->IncludeComponent(
                  "messager:mcm",
                  "desctop",
                  $arParams,
                  false
              );
          }
      }
          ';
