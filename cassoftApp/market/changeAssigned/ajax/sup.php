<?php define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logSup.txt";
$app = "change_assigned";

$HlClientApp = new \CSlibs\B24\HL\HlService('app_' . $app . '_access');
$clientsApp = $HlClientApp->getByFilterList(['UF_ACTIVE' => 1]);
//p($clientsApp, "clientsAppAll", $log);
//pr()
foreach ($clientsApp as $clientApp) {
//p($clientApp, "clientApp", $log);
    $i = 0;
    $domain = $clientApp['UF_CS_CLIENT_PORTAL_DOMEN'];
    if (
        $clientApp['UF_CS_CLIENT_PORTAL_DOMEN'] !== 'voessen.bitrix24.ru'
    ) {
        continue;
    }
    $authParams = [];
    $member = $clientApp['UF_CS_CLIENT_PORTAL_MEMBER_ID'];

    $auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
    $info = $auth->CScore->call('app.info');
    p($info , "info", $log);
    $options = $auth->CScore->call('user.option.get');
    p($options , "options", $log);
}