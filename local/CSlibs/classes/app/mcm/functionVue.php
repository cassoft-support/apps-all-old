<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once("/var/www/bitirx-brokci/data/www/app.cassoft.ru/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logFunctionVue.txt";

$input = file_get_contents('php://input');
p($input, "start", $log);
$data = json_decode($input, true);
p($data , "data", $log);
if(!empty($data)) {
    p($data['fn'], "fn", $log);
    if ($data['fn'] === 'profileCsMcm') {
        p($data, "data", $log);
        $res = profileCsMcm($data['member_id']);
        p($res, "res", $log);
        echo json_encode($res);
        exit;
    } elseif ($data['fn'] === 'profileAdd') {
        p($data, "profileAdd1", $log);
        $res = profileCsMcmAdd($data);
        p($res, "res-profileAddW", $log);
        echo json_encode($res);
        exit;
    } elseif ($data['fn'] === 'profileWappiAdd') {
        p($data, "profileAddWappi", $log);
        $res = profileCsMcmWappiAdd($data);
        p($res, "res-profileAddW", $log);
        echo json_encode($res);
        exit;
    } elseif ($data['fn'] === 'profileUpdate') {
        p($data, "profileAdd1", $log);
        $res = profileCsMcmUpdate($data);
        p($res, "res-profileAddW", $log);
        echo $res;
        exit;
    }

}