<?php


function searchNewBuilding($filter){

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


    $url = 'https://dev.brokci.ru/cassoft/handler.php';
    $comp = 'buildings';
    $resource = 'BuildingsSearch';
    $template = 'brokci';
    //$templateList = 'brokci';
    $token = '876ef885733d24f5bc449f1611d2d1739a6ef56ca8a760f4bfa3610374101e58';
    

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, sprintf(
        "%s/%s/%s/%s", $url, $comp, $resource, $template
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filter));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);

   // d(json_decode($response, true));
    return json_decode($response, true);
}
function searchNewBuildingFlats($id){

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


    $url = 'https://dev.brokci.ru/cassoft/handler.php';
    $comp = 'buildings';
    $resource = 'BuildingsSearch';
    $template = 'brokciDetail';
    //$templateList = 'brokciDetail';
    $token = '876ef885733d24f5bc449f1611d2d1739a6ef56ca8a760f4bfa3610374101e58';


    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, sprintf(
        "%s/%s/%s/%s", $url, $comp, $resource, $template
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $id);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);

   // d(json_decode($response, true));
    return json_decode($response, true);
}