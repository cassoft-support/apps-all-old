<?php
//d($_POST);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logSave.txt";
file_put_contents($log, print_r($date . "\n", true));
file_put_contents($log, print_r($_POST, true), FILE_APPEND);
//   d($_POST);

if($_POST['member_id']) {
    $memberId = $_POST['member_id'];
}
//file_put_contents($log, print_r('тест значения - ' . $_POST), FILE_APPEND);
//echo "<pre>";
//var_dump($_POST['formData']);
$arrToAdd = [];
foreach ($_POST['formData'] as $value){
    $arrToAdd[$value['name']] = $value['value'];
}

if ($_POST['app'] && $memberId) {

    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $memberId);
    if ($_REQUEST['PLACEMENT'] === 'REST_APP_URI') {
        $response['restAppUri'] = 'Y';
    }

    $paramsAdd = [
        'ENTITY' => 'messages',
        'NAME' => $_POST['name'],
        'PROPERTY_VALUES' => [
    'message' => json_encode($_POST['desc']),
    'messager_type' => $_POST['messager_type'],
        ]];
    file_put_contents($log, print_r($paramsAdd, true), FILE_APPEND);

    if ($_POST['id'] > 0) {
        $paramsAdd['ID'] = $_POST['id'];
        $entityAdd = $auth->CScore->call('entity.item.update', $paramsAdd);
        $response['ID'] = $_POST['id'];
    } else {
        $entityAdd = $auth->CScore->call('entity.item.add', $paramsAdd)[0];
        $response['ID'] = $entityAdd;
        file_put_contents($log, print_r($entityAdd, true), FILE_APPEND);
    }

    echo json_encode($response);
}
