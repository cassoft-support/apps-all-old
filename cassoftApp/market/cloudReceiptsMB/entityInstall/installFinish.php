<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/SelfProg/Hbk.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
function d($print){echo "<pre>"; print_r($print); echo "</pre>";}

$file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/entityInstall/logFinish.txt";
file_put_contents($file_log, print_r("Finish",true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
$CSRest = new CSRest("cloud_receipts_mb");
if ($_POST) {
  $authParams = json_decode($_POST['request'], true);
  $member = $authParams['member_id'];
    $result = [];
   // $authParams = $_POST['authParams'];
    $userParams = $_POST['user'];
    $HlAuthParmas = new \Cassoft\Services\HlService('app_cloud_receipts_access');
    $data = array(
        'UF_CS_CLIENT_PORTAL_MEMBER_ID'     => $authParams['member_id'],
        'UF_CS_CLIENT_PORTAL_DOMEN'         => $authParams['DOMAIN'],
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'  => $authParams['AUTH_ID'],
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $authParams['REFRESH_ID']
    );
    $checkClient = $HlAuthParmas->hl::getList(array(
        'select' => array('*'),
        'filter' => array("UF_CS_CLIENT_PORTAL_MEMBER_ID" => $authParams['member_id']),
        'order' => array("ID" => "DESC"),
        'limit' => 1,
    ));
    $resultCheck = $checkClient->fetch();
    if ($resultCheck['ID']) {
        $update = $HlAuthParmas->hl::update($resultCheck["ID"], $data);
    } else {
        $add = $HlAuthParmas->hl::add($data);
    }

    $HlUserParmas = new \Cassoft\Services\HlService('client_app_cassoft');
    $userData = [
        'UF_CS_CLIENT_PORTAL_DOMEN'=>$authParams['DOMAIN'],
        'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $authParams['member_id'],
        'UF_CS_APP_NAME' =>'cloud_receipts_MB',
        'UF_CS_CLIENT_FIO' => "{$userParams['LAST_NAME']} {$userParams['NAME']} {$userParams['SECOND_NAME']}",
        'UF_CS_CLIENT_PHOTO' => $userParams['PERSONAL_PHOTO'],
        'UF_CS_CLIENT_TEL' => $userParams['WORK_PHONE'],
        'UF_CS_CLIENT_TEL2' =>$userParams['PERSONAL_PHONE'],
        'UF_CS_CLIENT_MAIL' => $userParams['EMAIL'],
        'UF_CS_CLOUD_RECEIPTS_MB' => 'true'
    ];
    $checkUser = $HlUserParmas->hl::getList([
      'select' => ['*'],
      'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $authParams['member_id']],
      'order' => ['ID' => 'DESC'],
      'limit' => 1,
  ]);
  $resultCheck = $checkUser->fetch();

  if ($resultCheck['ID']) {
      $update = $HlUserParmas->hl::update($resultCheck['ID'], $userData);
  } else {
      $add = $HlUserParmas->hl::add($userData);
  }


    $result['finish'] = 'success';
    $result['check'] = $resultCheck;
    $result['auth'] = $authParams;
    $result['POST'] = $_POST;
    echo json_encode($result);
}
