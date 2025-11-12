<?php
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/scanDoc/base/ajax/savePhoto.php");

$log  = __DIR__ . "/logSave.txt";
p($_POST, "start", $log);
    $paramAuth = json_decode($_POST['authParams'], true);
p($paramAuth , "paramAuth", $log);
if($paramAuth['member_id'] && $_POST['app']) {

    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $paramAuth['member_id']);
    if(!empty($_FILES['files'])){
        $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'photo');
    }else {
        $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'sort_photo');
    }
    if($_POST['type'] === 'smart'){
        if (empty($photo['result'])) {
            $paramsUp['ufCrm' . $_POST['smartId'] . 'CsScanDoc'] = '';
        } else {
            $paramsUp['ufCrm' . $_POST['smartId'] . 'CsScanDoc'] = $photo['result'];
        }
        p($paramsUp, "paramsUp", $log);
        $elUp = $auth->CScore->call("crm.item.update", ['entityTypeId' => $_POST['entityTypeId'], 'id' => $_POST['id'], 'fields' => $paramsUp]);
        p($elUp , "smartUp", $log);
    }else {

        if (empty($photo['result'])) {
            $paramsUp["UF_CRM_CS_SCAN_DOC"] = '';
        } else {
            $paramsUp["UF_CRM_CS_SCAN_DOC"] = $photo['result'];
        }
        p($paramsUp, "paramsUp", $log);
        $elUp = $auth->CScore->call("crm." . $_POST['type'] . ".update", ['id' => $_POST['id'], 'fields' => $paramsUp]);
        p($elUp, "elUp", $log);
    }
if($elUp[0]>0){
   // echo 'Y';
}else{
   // echo 'N';
}
//echo json_encode($elUp);
    }
?>
