<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Install/tools.php");

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Install/guide/financier.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");
$debug = new \debug('debug');
$date = date("d.m.YTH:i");

    $file_log = __DIR__ . "/logProvider.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    file_put_contents($file_log, print_r($_POST, true), FILE_APPEND);
// $debug->console("testProvider");

    if ($_POST['UserAut']['member_id']) {
        $memberId = $_POST['UserAut']['member_id'];
    }
    if ($_POST['memberId']) {
        $memberId = $_POST['memberId'];
    }

    if ($_POST['app'] && $memberId) {
        file_put_contents($file_log, print_r("\npost-" . $_POST['type'] . "\n", true), FILE_APPEND);
        $clientAppAdm = getAuthHl($_POST['app'], $memberId);
        $auth = new Auth($_POST['app'], $clientAppAdm, __DIR__ . '/');

        if ($_POST['type'] === "company") {
            $order = ['ID' => 'ASC'];
            $select = ['ID', 'TITLE', 'COMPANY_TYPE'];
            $filter = [

            ];

//    file_put_contents($file_log, print_r($_POST['provider'] , true), FILE_APPEND);
            foreach ($auth->batch->getTraversableList('crm.company.list', $order, $filter, $select, 6000) as $value) {
                $i++;
            //  file_put_contents($file_log, print_r($value, true), FILE_APPEND);
                if ($value['COMPANY_TYPE'] === 'CS_PROVIDER' || $value['COMPANY_TYPE'] === 'CS_PROVIDER_CLIENT') {
                    $selectList[$value['ID']] = $value['TITLE'];
                }
            }
        }
        file_put_contents($file_log, print_r("\n".$i, true), FILE_APPEND);
        if ($_POST['type'] === "individual") {
            $order = ['ID' => 'ASC'];
            $select = ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'TYPE_ID'];
            $filter = [
            ];

            foreach ($auth->batch->getTraversableList('crm.contact.list', $order, $filter, $select, 6000) as $value) {
                //  d($value);
                if ($value['TYPE_ID'] === 'CS_PROVIDER') {
                    $selectList[$value['ID']] = $value['LAST_NAME'] . " " . $value['NAME'] . " " . $value['SECOND_NAME'];
                }
//            $selectList[$value['ID']] = $value['TYPE_ID'];
            }
        }

        if ($_POST['type'] === "carList") {
            $HlTruckBrands = new \Cassoft\Services\HlService("ati_su_truckbrands"); //Бранды авто
            $arTruckBrands = $HlTruckBrands->hl::getList(['select' => ['ID', 'UF_ENG_NAME', 'UF_NAME'], 'order' => ['ID' => 'ASC'], 'filter' => []])->fetchAll();
            foreach ($arTruckBrands as $ketB => $vB) {
                $truckBrands[$vB['ID']] = $vB['UF_ENG_NAME'] . "-" . $vB['UF_NAME'];
            }
            $HlTruckModels = new \Cassoft\Services\HlService("ati_su_truckmodels"); //модель авто
            $arTruckModels = $HlTruckModels->hl::getList(['select' => ['ID', 'UF_MODEL'], 'order' => ['ID' => 'ASC'], 'filter' => []])->fetchAll();
            foreach ($arTruckModels as $key => $vM) {
                $truckModels[$vM['ID']] = $vM['UF_MODEL'];
            }
            $HlTruckType = new \Cassoft\Services\HlService("ati_su_type_auto"); //тип авто
            $arTruckType = $HlTruckType->hl::getList(['select' => ['ID', 'UF_NAME'], 'order' => ['ID' => 'ASC'], 'filter' => []])->fetchAll();
            foreach ($arTruckType as $key => $val) {
                $truckType[$val['ID']] = $val['UF_NAME'];
            }
            $HlTruckType = new \Cassoft\Services\HlService("ati_su_car_body_type"); //тип кузова
            $arTruckTypeBody = $HlTruckType->hl::getList(['select' => ['ID', 'UF_NAME'], 'order' => ['ID' => 'ASC'], 'filter' => []])->fetchAll();
            foreach ($arTruckTypeBody as $key => $val) {
                $truckTypeBody[$val['ID']] = $val['UF_NAME'];
            }
            $HlOwnership = new \Cassoft\Services\HlService("ati_su_ownership_car"); //тип кузова
            $arOwnership = $HlOwnership->hl::getList(['select' => ['ID', 'UF_NAME'], 'order' => ['ID' => 'ASC'], 'filter' => []])->fetchAll();
            foreach ($arOwnership as $key => $val) {
                $ownership[$val['ID']] = $val['UF_NAME'];
            }
            $CarBase = [];
            $filter = ["ACTIVE" => 'Y'];
            if ($_POST['providerType'] === "company") {
                $filter['PROPERTY_company'] = $_POST["provider"];
            }
            if ($_POST['providerType'] === "individual") {
                $filter['PROPERTY_owner'] = $_POST["provider"];
            }
            file_put_contents($file_log, print_r($filter, true), FILE_APPEND);
            foreach (
                $auth->batch->getTraversableListEntity(
                    'entity.item.get',
                    'car_base',
                    ['ID' => 'ASC'],
                    $filter,
                    6000
                ) as $arCars
            ) {

                $value = $arCars['PROPERTY_VALUES'];
                $CarBase[$arCars['ID']] = $truckBrands[$value["truck_brands"]] . " - " . $truckModels[$value["truck_models"]];

                /*
                 $CarBase[$arCars['ID']]['truck_models'] = $truckModels[$value["truck_models"]];
                 $CarBase[$arCars['ID']]['truck_type'] = $truckType[$value["truck_type"]];
                 $CarBase[$arCars['ID']]['truck_body_type'] = $truckTypeBody[$value["truck_body_type"]];
                 $CarBase[$arCars['ID']]['reg_number'] = $value["reg_number"];
                 $CarBase[$arCars['ID']]['reg_number_semitrailer'] = $value["reg_number_semitrailer"];
                 $CarBase[$arCars['ID']]['company'] = $value["company"];
                 $CarBase[$arCars['ID']]['contacts'] = $value["contacts"];
                 $CarBase[$arCars['ID']]['control'] = $value["control"];
                 $CarBase[$arCars['ID']]['documents'] = $value["documents"];
                 $CarBase[$arCars['ID']]['load_capacity'] = $value["load_capacity"];
                 $CarBase[$arCars['ID']]['body_volume'] = $value["body_volume"];
                 $CarBase[$arCars['ID']]['ownership'] = $ownership[$value["ownership"]];
     */
            }
            $selectList = $CarBase;
        }
        file_put_contents($file_log, print_r($selectList, true), FILE_APPEND);
        if ($_POST['type'] === 'driverList') {

            if ($_POST['providerType'] === "company") {
                $filter = ['ID' => $_POST['provider']];
                $contactAll = $auth->core->call("crm.company.contact.items.get", ['id' => $_POST['provider']])->getResponseData()->getResult()->getResultData();
                foreach ($contactAll as $kContact) {
                    $contactId[] = $kContact['CONTACT_ID'];
                }
                $filter = ['ID' => $contactId, 'UF_CRM_CS_DRIVER' => 1];
                $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
                foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
                    $selectList[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
                }
            }
            if ($_POST['providerType'] === "individual") {
                $filter = ['UF_CRM_CS_MAIN_CONTACT' => $_POST['provider'], 'UF_CRM_CS_DRIVER' => 1];
                $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
                foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
                    // d($smart['contactId']);
                    $selectList[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];

                }


            }
        }

        if ($_POST['type'] === 'contactList') {

            if ($_POST['providerType'] === "company") {
                $filter = ['ID' => $_POST['provider']];
                $contactAll = $auth->core->call("crm.company.contact.items.get", ['id' => $_POST['provider']])->getResponseData()->getResult()->getResultData();
                foreach ($contactAll as $kContact) {
                    $contactId[] = $kContact['CONTACT_ID'];
                }
                $filter = ['ID' => $contactId,];
                $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "PHONE"];
                foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
                    $selectList[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
                }
            }
            if ($_POST['providerType'] === "individual") {
                $filter = ['UF_CRM_CS_MAIN_CONTACT' => $_POST['provider'],];
                $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
                foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
                    $selectList[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];

                }

            }
        }
        if ($_POST['type'] === 'req') {

            if ($_POST['providerType'] === "company") {
                $selectList = $auth->core->call("crm.requisite.list", ['order' => [], 'filter' => ['ENTITY_TYPE_ID' => 4, 'ENTITY_ID' => $_POST['provider']], 'select' => ["*",]])->getResponseData()->getResult()->getResultData();
                file_put_contents($file_log, print_r($selectList, true), FILE_APPEND);
            }
            if ($_POST['providerType'] === "individual") {

            }

        }

      //  file_put_contents($file_log, print_r($selectList, true), FILE_APPEND);
        // d($selectList);
        echo json_encode($selectList);
    }
