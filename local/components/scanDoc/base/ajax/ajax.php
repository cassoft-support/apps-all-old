<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/Api/pochtaRU/function.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/trac/base/ajax/mailRussia.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/trac/base/ajax/cdek.php';

$date = date("d.m.YTH:i");
$file_log = __DIR__ . "/logAjax.txt";
file_put_contents($file_log, print_r($date . "\n", true));
file_put_contents($file_log, print_r($_POST, true), FILE_APPEND);
$params = json_decode($_POST['reg'], true);
$_REQUEST = $params;
//file_put_contents($file_log, print_r($params, true), FILE_APPEND);

if ($params['app']) {
    $CloudApp = $params['app'];
    $appAccess = 'app_' . $CloudApp . '_access';

    $CloudApplication = new \Cloud\App\CloudApplication($CloudApp);
    $HlClientAppCASSOFT = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientAppCASSOFT->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $params['member_id']]
    ])->fetchAll();
    $hlKeys = [
        'UF_CS_CLIENT_PORTAL_MEMBER_ID',
        'UF_CS_CLIENT_PORTAL_DOMEN',
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
    ];

    $clientApp = $clientsApp['0'];
//d($clientApp);
//  $debug->printR($clientApp, "clientApp");

    $auth = new Auth($CloudApplication, $clientApp, 'log.log', __DIR__ . '/');
    try {
        $startAuth = $auth->startAuth();

        if ($needUpdate = $auth->needUpdateAuth()) {
            $HlClientAppCASSOFT->hl::update(
                $clientApp['ID'],
                [
                    'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $needUpdate['access_token_new'],
                    'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $needUpdate['refresh_token_new']
                ]
            );
        }
    } catch (\Exception $e) {
        d($e->getMessage());
    }
}
$setup = $auth->core->call('entity.item.get', ['ENTITY' => "setup",])->getResponseData()->getResult()->getResultData();
file_put_contents($file_log, print_r($setup, true), FILE_APPEND);


pd($setup, "start");
/*
 * [CsTracer] => 30500186038305
    [CsTracCompany] => Почта России
Account/Идентификатор	Secure password/Пароль	
2lRAfpMc5dWaRUWRcNgD8uHZiUEs8IP1	y7WerTGCFzvHzYKAf7EktBEaYwKEzYeB
 */
$i=0;
if($_POST['CsTracCompany'] === 'CS_PR') {

  $mailRusHistory =  mailRusHistory($auth, $_POST);
    file_put_contents($file_log, print_r($mailRusHistory, true), FILE_APPEND);

}
if($_POST['CsTracCompany'] === 'CS_CDEK') {

    $cdekHistory =  cdekHistory($auth, $_POST, $setup[0]['PROPERTY_VALUES']['UF_CS_ACCOUNT_CDEK'], $setup[0]['PROPERTY_VALUES']['UF_CS_KEY_CDEK']);
    file_put_contents($file_log, print_r($cdekHistory, true), FILE_APPEND);

}

 $res['result'] = 'close';

echo json_encode($res);