<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST, "start", $log);

//$auth = new \CSlibs\B24\Auth\Auth("cian_messager", [], $_REQUEST["member_id"]);
//$property = $auth->CScore->call('crm.product.property.list');
//pr(count($property), '');


?>
<script>
    window.location.href = "https://cassoft.ru/app_cian_messager/";
</script>
