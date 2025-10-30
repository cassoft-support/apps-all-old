<?php
//define(NOT_CHECK_PERMISSIONS, true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/authAll.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
$date=date("d.m.YTH:i");
$file_log = __DIR__."/logAjax.txt";
file_put_contents($file_log, print_r($date."\n",true));
file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
if($_POST['app']) {
    $clientApp = [
        'DOMAIN' => $_POST['auth']['domain'],
        'member_id' => $_POST['auth']['member_id'],
        'AUTH_ID' => $_POST['auth']['access_token'],
        'REFRESH_ID' => ""
    ];
    $adminApp = getAuthHl($_POST['app'], $_POST['member_id']);
    $authAdm = new Auth($_POST['app'], $adminApp, __DIR__ . '/');
    $authCl = new Auth($_POST['app'], $clientApp, __DIR__ . '/');

}