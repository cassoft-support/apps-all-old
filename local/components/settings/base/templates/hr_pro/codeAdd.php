<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logCodeAdd.txt";
p($arResult, "start", $log);
p($arParams, "params", $log);


?>
<input type="text" id="member_id" hidden value="<?= $arParams['auth']['member_id']?>">
<input type="text" id="user_id" hidden value="<?= $arParams['user_id']?>">
<input type="text" id="appCode" hidden value="<?= $arParams['app']?>">
<input type="text" id="keyHH" hidden value="<?= $arParams['code']?>">
<input type="text" id="setupKeyHH" hidden value='<?= $arResult["PROP"]["CS_HH_KEY"]?>'>
<input type="text" id="ID" hidden value="<?= $arResult['ID']?>">
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script defer src="/local/components/settings/base/templates/hr_pro/script_add.js"></script>
