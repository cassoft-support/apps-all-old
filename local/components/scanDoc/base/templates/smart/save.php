<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/scanDoc/base/ajax/savePhoto.php");

$log  = __DIR__ . "/logSave.txt";
p($_POST, "start", $log);
$paramAuth = json_decode($_POST['auth'], true);
p($paramAuth , "paramAuth", $log);
if($paramAuth['member_id'] && $_POST['app']) {

    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $paramAuth['member_id']);
    if (!empty($_FILES['files'])) {
        $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'photo');
        p($photo , "photo", $log);
    } else {
        $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'sort_photo');
    }
    if (empty($photo['result'])) {
        $paramsUp['ufCrm' . $_POST['smartId'] . 'CsScanDoc'] = '';
    } else {
        $paramsUp['ufCrm' . $_POST['smartId'] . 'CsScanDoc'] = $photo['result'];
    }

    p($paramsUp, "paramsUp", $log);

    $smartUp = $auth->CScore->call("crm.item.update", ['entityTypeId' => $_POST['entityTypeId'], 'id' => $_POST['smartElId'], 'fields' => $paramsUp]);
p($smartUp , "smartUp", $log);

    $res['result'] = 'close';

    echo json_encode($res);
}
?>
