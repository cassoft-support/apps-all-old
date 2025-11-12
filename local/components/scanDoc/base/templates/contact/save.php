<?php
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/scanDoc/base/ajax/savePhoto.php");

$log  = __DIR__ . "/logSave.txt";

p($_POST, "start", $log);
$paramAuth = json_decode($_POST['authParams'], true);
file_put_contents($log , print_r($paramAuth, true), FILE_APPEND);
if($paramAuth){
    $clientApp = [
        'DOMAIN' => $paramAuth['domain'],
        'member_id' => $paramAuth['member_id'],
        'AUTH_ID' => $paramAuth['access_token'],
        'REFRESH_ID' => $paramAuth['refresh_token'],
    ];
}
if(!empty($clientApp) && $_POST['app']) {
    file_put_contents($log , print_r($_POST['app'], true), FILE_APPEND);
    file_put_contents($log , print_r($clientApp, true), FILE_APPEND);
    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], $clientApp, "");
    if(!empty($_FILES['files'])){
        $photo = savePhoto($auth, $paramAuth['member_id'], $_POST, $_FILES, 'photo');
    }else{
        $photo = savePhoto($auth, $paramAuth['member_id'], $_POST, $_FILES, 'sort_photo');
    }

        if (empty($photo['result'])) {
            $paramsUp["UF_CRM_CS_SCAN_DOC"] = '';
        } else {
            $paramsUp["UF_CRM_CS_SCAN_DOC"] =$photo['result'];
        }

    file_put_contents($log, print_r($paramsUp, true), FILE_APPEND);
    $contactUp = $auth->CScore->call("crm.contact.update", [ 'id' => $_POST['contact_id'], 'fields' => $paramsUp]);
    file_put_contents($log, print_r($contactUp, true), FILE_APPEND);

echo json_encode($contactUp);
    }
?>
