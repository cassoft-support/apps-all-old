<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/event/company_update/ajax/changeUser.php");

//$log = __DIR__ . "/logCompany_update.txt";
//p("start", "-company_update", $log);
//p($_REQUEST, "REQUEST", $log);
//p($arResult, "arResult", $log);
$_REQUEST['app']='gas_mix';
$changeUserCompany= changeUserCompany($_REQUEST);

?>

