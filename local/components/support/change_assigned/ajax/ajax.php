<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/ajaxUpdate.txt";
//pr($_POST, '');
//file_put_contents($file_log, print_r($date . "\n", true));
$arParams = json_decode($_POST['request'], true);
//pr($arParams, '');
//$_REQUEST = json_decode($arParams['UserAut'], true);
$memberId = $arParams['member_id'];
$app = $arParams['app'];
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
//pr($auth, '');
//$fields = $auth->CScore->call('userfieldtype.list');
//$fields = $auth->CScore->call('user.current');
$filter=[
   'contactIds' => 112,
   'closed' => 'N'
   // 'id'=>400
];
$resElement = $auth->CScore->call('crm.item.list', ['entityTypeId' => 2, 'filter'=>$filter]);
pr($resElement, '');
