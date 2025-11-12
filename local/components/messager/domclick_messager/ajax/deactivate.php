<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/report/logistics_pro/function.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");

$response = [];
$response['error'] = '';

$_REQUEST = json_decode($_POST['UserAut'], true);
$memberId = $_POST['authParams']['member_id'];


if ($_POST['app']) {
    $CloudApp = $_POST['app'];
    $appAccess = 'app_' . $CloudApp . '_access';
    $CloudApplication = new \Cloud\App\CloudApplication($CloudApp);
    $HlClientAppCASSOFT = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientAppCASSOFT->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId]
    ])->fetchAll();
    $hlKeys = [
        'UF_CS_CLIENT_PORTAL_MEMBER_ID',
        'UF_CS_CLIENT_PORTAL_DOMEN',
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
    ];

    $clientApp = $clientsApp['0'];

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

    $CSRest = new  \CSRest($CloudApp);


    if ($appId = intval($_POST['id'])) {
        $filter = ["ACTIVE"=>'Y', "ID" => $appId];
        $answer = $CSRest->call('entity.item.get', [
            'ENTITY' => 'application',
            'FILTER' => $filter,
            ]);
//        d($test);
//        die();
        $arApp = $answer['result'][0] ?? [];
        if (!$arApp) {
            $response['error'] = 'Заявка не найдена';
            echo json_encode($response);
            die();
        }

        // деактивировать условие оплаты для данной заявки
        if (isset($arApp['PROPERTY_VALUES']['UF_CS_PAYMENT']) && intval($arApp['PROPERTY_VALUES']['UF_CS_PAYMENT']) > 0) {
            $updatePay = $CSRest->call('entity.item.update', [
                "ENTITY" => 'payment_terms',
                'ID' => intval($arApp['PROPERTY_VALUES']['UF_CS_PAYMENT']),
                "ACTIVE" => 'N',
            ]);
            if ($updatePay['result']) {
                $response['message'] .= 'Условие оплаты для заявки успешно деактивировано.'.PHP_EOL;
            }
        }

        // деактивировать груз для данной заявки
        if (isset($arApp['PROPERTY_VALUES']['UF_CS_LOADING']) && intval($arApp['PROPERTY_VALUES']['UF_CS_LOADING']) > 0) {
            $updateCargo = $CSRest->call('entity.item.update', [
                "ENTITY" => 'cargo',
                'ID' => intval($arApp['PROPERTY_VALUES']['UF_CS_LOADING']),
                "ACTIVE" => 'N',
            ]);
            if ($updatePay['result']) {
                $response['message'] .= 'Груз для заявки успешно деактивирован.'.PHP_EOL;
            }
        }

        // деактивировать саму заявку
        $updateApp = $CSRest->call('entity.item.update', [
            "ENTITY" => 'application',
            'ID' => $appId,
            "ACTIVE" => 'N',
        ]);
        if ($updateApp['result']) {
            $response['message'] .= 'Заявка успешно деактивирована.'.PHP_EOL;
        }

        if (!$response['error']) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }
}