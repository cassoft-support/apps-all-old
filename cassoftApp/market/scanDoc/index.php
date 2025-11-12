<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST, "start", $log);

?>
<script>
    window.location.href = 'https://apps-doc.cassoft.ru/scandoc/app_scan_doc/';
</script>
