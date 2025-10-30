<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/ajaxUpdate.txt";
//pr($_POST, '');
//file_put_contents($file_log, print_r($date . "\n", true));
$arParams = json_decode($_POST['request'], true);
//$_REQUEST = json_decode($arParams['UserAut'], true);

$memberId = '350f96d46411bd6439bd5372cd90fec3';
$CloudApp = 'mcm';
////d($arParams);
$auth = new \CSlibs\B24\Auth\Auth($CloudApp, [], $memberId);

$profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');

//$resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$arParams['member_id'], 'UF_PROFILE_ID' => '954597cd-cd99' ])[0];
//pr($resListProfile, '');
//$params = [
//    'UF_LINE_B24' =>4,
//    'UF_CONNECTOR' => "cs_mcm_whatsapp",
//    'UF_STATUS_B24' => 1,
//
//];
//
//$el= $profileHb->getByFilterList(['UF_PROFILE_ID' => '954597cd-cd99']);
//pr($el, '');
//$elUp= $profileHb->elementUpdate($el[0]['ID'], $params);

//$postData=[
//    'name'=>"eww",
//    'webhook_url'=>'rwe'
//];
//$res=  http_build_query($postData);
//pr($res);
//$profile = '9f66aa43-6e64';
//$token = '785026ea43c1bb0b1b842189cbca9197c05f424e';
////$profileAll = sendGetWappi('/api/profile/all/get?profile_id'.$profile);
//$profileAll = sendGetWappi('/api/sync/get/status?profile_id=954597cd-cd99');
//pr($profileAll, '');
//$data=["authorization_status", "incoming_message", "delivery_status", "outgoing_message_phone", "outgoing_message_api", "application_status"];
//$profileWebHukWappi =  sendPostWappi('/api/webhook/types/set?profile_id='.$profile, json_encode($data));
//pr($profileWebHukWappi, '');
//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    CURLOPT_URL => 'https://wappi.pro/api/sync/get/status?profile_id='.$profile,
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => '',
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => 'GET',
//    CURLOPT_HTTPHEADER => array(
//        'Authorization: '.$token
//    ),
//));
//
//$response = curl_exec($curl);
//
//curl_close($curl);
//$profileAll = json_decode($response, true);
pr($profileAll, '');
//
//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    //CURLOPT_URL => 'https://wappi.pro/api/sync/get/status?profile_id='.$profile,
//    CURLOPT_URL => 'https://wappi.pro/api/sync/qr/get?profile_id='.$profile,
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => '',
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 0,
//    CURLOPT_FOLLOWLOCATION => true,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => 'GET',
//    CURLOPT_HTTPHEADER => array(
//        'Authorization: '.$token
//    ),
//));
//
//$response = curl_exec($curl);
//pr($response, '');
//$res = json_decode($response, true);
//pr($res , "resAddWebhook", );
//curl_close($curl);
//$base64String = preg_replace('#^data:image/\w+;base64,#i', '', $res['qrCode']);
////data:image/png;base64,
//$imageData = base64_decode($base64String);
//if($res['detail'] === 'You are already authorized'){
//    echo 'You are already authorized';
//}else{
//    ?>
<!---->
<!--    <img src="--><?php //=$res['qrCode']?><!--">-->
<!---->
<!--    --><?php
//}
//


$setup = $auth->CScore->call('entity.item.property.get',
   [
       'ENTITY' =>  'setup_messager',
//        'PROPERTY' =>  'CS_MCM_CONNECT',
//        'NAME' =>  'Подключение MCM',
//        'TYPE' => 'S'
    ]);
//
//$setup = $auth->CScore->call('entity.item.get', ['ENTITY'=> 'setup_messager'])[0];
//
pr($setup);
