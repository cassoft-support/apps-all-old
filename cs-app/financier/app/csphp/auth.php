<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logAuth.txt";
p($_POST, "start", $log);
$memberId = $_POST['member_id'];
if ($memberId) {
  $arParams['app'] = 'mcm';
  $appAccess = 'app_' . $arParams['app'] . '_access';
  $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
  $clientsApp = $HlClientApp->searchID($memberId);
  p($clientsApp , "clientsApp", $log);
  if ($clientsApp["ID"] > 0) {
  echo $clientsApp["ID"];
  }
}
