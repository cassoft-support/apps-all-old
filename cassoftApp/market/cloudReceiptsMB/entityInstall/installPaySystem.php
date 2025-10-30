<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    require_once('paySystemHandler.php');
    require_once('paySystem.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/icons/iconBase64.php');
    function d($print)
    {
        echo "<pre>";
        print_r($print);
        echo "</pre>";
    }

    $file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/entityInstall/logPaySystem.txt";
    file_put_contents($file_log, print_r("PaySystem", true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
    $CSRest = new CSRest("cloud_receipts_mb");

    if ($_POST) {
        $result = [];
        $authParams = json_decode($_POST['request'], true);
        //$dom = $authParams['DOMAIN'];
        $member = $authParams['member_id'];
        $key = "";


        $resHandlerList = $CSRest->call("sale.paysystem.handler.list");
        file_put_contents($file_log, print_r($resHandlerList, true), FILE_APPEND);
        if (empty($resHandlerList['result'])) {
            file_put_contents($file_log, print_r("установка обработчика\n", true), FILE_APPEND);
            $installHandler = paySystemHandler($member, $key);
            foreach ($installHandler as $key => $value) {
                file_put_contents($file_log, print_r($value, true), FILE_APPEND);
                $resHandler = $CSRest->call("sale.paysystem.handler.add", $value);
                file_put_contents($file_log, print_r($resHandler, true), FILE_APPEND);
                if ($resHandler['error'] == 'ERROR_HANDLER_ALREADY_EXIST') {
                    file_put_contents($file_log, print_r("update\n", true), FILE_APPEND);
                }
            }
        } else {
            file_put_contents($file_log, print_r("обновление обработчика\n", true), FILE_APPEND);
            $installHandler = paySystemHandler($member, $key);
            foreach ($resHandlerList['result'] as $key => $value) {
                $resHandler = $CSRest->call("sale.paysystem.handler.update", [
                    'id' => $value['ID'],
                    'fields' => $installHandler[$value['CODE']]
                ]);
                file_put_contents($file_log, print_r($resHandler, true), FILE_APPEND);
            }
        }

        $resHandlerList = $CSRest->call("sale.paysystem.handler.list");
        file_put_contents($file_log, print_r($resHandlerList, true), FILE_APPEND);

        $paramPaySystem = [
            'select' => [],
            'filter' => [
                'ACTION_FILE' => 'cs_modulbank_order'
            ],
            'order' => [
                'id' => 'ASC'
            ],
            //  'navigation'=> 1
        ];
        $paySystemList = $CSRest->call("sale.paysystem.list", $paramPaySystem);
        file_put_contents($file_log, print_r($paySystemList, true), FILE_APPEND);
        if (empty($paySystemList['result'])) {
            file_put_contents($file_log, print_r("установка payOrder\n", true), FILE_APPEND);
            $persontypeOrder = $CSRest->call("sale.persontype.list");
            file_put_contents($file_log, print_r($persontypeOrder, true), FILE_APPEND);
            foreach ($persontypeOrder['result']['personTypes'] as $key => $value) {
                $nameTypePerson = $value['name'];
                $idTypePerson = $value['id'];
                $handler = "cs_modulbank_order";
                $registryType = "ORDER";
                $paySystemOrder = paySystemArray(
                    $nameTypePerson,
                    $idTypePerson,
                    $handler,
                    $registryType,
                    $modulbankPay
                );
                file_put_contents($file_log, print_r($paySystemOrder, true), FILE_APPEND);

                $paySystemAdd = $CSRest->call("sale.paysystem.add", $paySystemOrder);
                file_put_contents($file_log, print_r($paySystemAdd, true), FILE_APPEND);
            }
        }

        $paramPaySystem['filter']['ACTION_FILE'] = 'cs_modulbank_invoice';
        $paySystemList = $CSRest->call("sale.paysystem.list", $paramPaySystem);
        file_put_contents($file_log, print_r($paySystemList, true), FILE_APPEND);
        if (empty($paySystemList['result'])) {
            file_put_contents($file_log, print_r("установка payInvoice\n", true), FILE_APPEND);
            $persontypeCRM = $CSRest->call("crm.persontype.list");
            file_put_contents($file_log, print_r($persontypeCRM, true), FILE_APPEND);
            foreach ($persontypeCRM['result'] as $key => $value) {
                $nameTypePerson = $value['NAME'];
                $idTypePerson = $value['ID'];
                $handler = "cs_modulbank_invoice";
                $registryType = "CRM_INVOICE";
                $paySystemInvoice = paySystemArray(
                    $nameTypePerson,
                    $idTypePerson,
                    $handler,
                    $registryType,
                    $modulbankPay
                );
                file_put_contents($file_log, print_r($paySystemInvoice, true), FILE_APPEND);
                $paySystemAdd = $CSRest->call("sale.paysystem.add", $paySystemInvoice);
                file_put_contents($file_log, print_r($paySystemAdd, true), FILE_APPEND);
            }
        }


        $result['paySystem'] = 'success';
        echo json_encode($result);
    }
