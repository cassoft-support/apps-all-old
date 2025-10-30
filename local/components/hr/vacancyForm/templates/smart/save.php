<?php
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/authAll.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
    //require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/scanDoc/base/templates/smart/save_function.php");
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
    //require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");
  //  $debug = new \debug('debug');
    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/logSave.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    file_put_contents($file_log, print_r($_POST, true), FILE_APPEND);

    $paramAuth = json_decode($_POST['authParams'], true);
file_put_contents($file_log, print_r($paramAuth, true), FILE_APPEND);
if($paramAuth){
    $clientApp = [
        'DOMAIN' => $paramAuth['domain'],
        'member_id' => $paramAuth['member_id'],
        'AUTH_ID' => $paramAuth['access_token'],
        'REFRESH_ID' => $paramAuth['refresh_token'],
    ];
}
if(!empty($clientApp) && $_POST['app']) {
    file_put_contents($file_log, print_r($_POST['app'], true), FILE_APPEND);
    file_put_contents($file_log, print_r($clientApp, true), FILE_APPEND);
    $auth = new Auth($_POST['app'], $clientApp, __DIR__ . '/');
    $photo['result']=[];
        if(!empty($_FILES['files'])) {
            $photo = savePhoto($auth, $_POST['authParams']['member_id'], $_POST, $_FILES, 'photo');
        }
            if (empty($photo['result'])) {
                $paramsUp["ufCrm".$_POST['smartId']."CsScanDoc"] = '';
            } else {
                $paramsUp["ufCrm".$_POST['smartId']."CsScanDoc"] =$photo['result'];
            }

    file_put_contents($file_log, print_r($paramsUp, true), FILE_APPEND);
    $smartUp = $auth->core->call("crm.item.update", ['entityTypeId' => $_POST['entityTypeId'], 'id' => $_POST['smartElId'], 'fields' => $paramsUp])->getResponseData()->getResult()->getResultData();
    file_put_contents($file_log, print_r($smartUp, true), FILE_APPEND);

echo json_encode($smartUp);
    }
?>
