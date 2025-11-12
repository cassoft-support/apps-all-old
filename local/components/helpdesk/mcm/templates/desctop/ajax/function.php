<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logFunction.txt";
p($_POST, "start", $log);

function profileCassoftWappi($memberId){
    $profileHb = new \CSlibs\B24\HL\HlService('profile_cassoft_wappi');
  $resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$memberId]);
  


  return $resListProfile;
}