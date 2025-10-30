<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Install/tools.php");
function smartGetId($memberId, $smartId, $smartElId){

$app = "logistics_pro";
    $clientAppAdm = getAuthHl($app, $memberId);
    $auth = new Auth($app, $clientAppAdm, __DIR__ . '/');

    $paramsType =[ 'select'=>['*'], 'order'=>[], 'filter'=>[ 'id' => $smartId], 'start'=> 0 ];
//$entityTypeId = $auth->core->call("crm.type.list", $paramsType)->getResponseData()->getResult()->getResultData()['types'][0]['entityTypeId'];
    $entityType = $auth->core->call("crm.type.list", $paramsType)->getResponseData()->getResult()->getResultData()['types'][0];
    $entityTypeId = $entityType['entityTypeId'];
//d($entityTypeId);
//[title] => Проверка контрагентов
    $arResult=[];
//CRM_18
    $arResult["smartElId"] = $smartElId;
    $arResult["smartId"] = $smartId;
    $arResult["entityTypeId"] = $entityTypeId;
    $arResult["memberId"] = $memberId;

    $arResult["control"] = "N";
    if($entityType['title'] === 'Проверка контрагентов'){
        $arResult["control"] = "Y";
    }


    $smart=$auth->core->call( "crm.item.get", array('entityTypeId'=>$entityTypeId, 'id' => $smartElId))->getResponseData()->getResult()->getResultData()['item'];
//d($smart);
    $idProd = $auth->core->call("crm.product.list", ['order' => [], 'filter' => ["NAME" => "Перевозка грузов"], 'select' => ["ID", "NAME"]])->getResponseData()->getResult()->getResultData()[0]['ID'];
    $smartOwnerType = "T" . dechex($smart['entityTypeId']);
    $smartProdPrice = $auth->core->call("crm.item.productrow.list",['order'=> [], 'filter'=>["=ownerType"=> $smartOwnerType, "=ownerId"=> $smart['id'], "=productId"=> $idProd],'start'=> 0])->getResponseData()->getResult()->getResultData()['productRows'][0]['price'];
//$arResult['test'] = $smart;


    if($smartProdPrice>0) {
        $arResult["opportunityLabel"] = number_format($smartProdPrice, 0, ",", " ") . " ₽.";
    }else{
        $arResult["opportunityLabel"] = "<span class='warning'>0</span> ₽.";
    }
//UF_CRM_18_CS_DATE_PLAN_LOADING

    $arResult["companyId"] =$smart['companyId'];
    $arResult["contactId"] =$smart['contactId'];
    $arResult["opportunity"] =$smartProdPrice;
    $arResult["DatePlanLoading"] =$smart['ufCrm'.$smartId.'CsDatePlanLoading'];
    $arResult["DatePlanLoadingTo"] =$smart['ufCrm'.$smartId.'CsDatePlanLoadingTo'];
    $arResult["DatePlanUnloading"] =$smart['ufCrm'.$smartId.'CsDatePlanUnloading'];
    $arResult["DatePlanUnloadingTo"] =$smart['ufCrm'.$smartId.'CsDatePlanUnloadingTo'];
    $arResult["VAT"] =$smart['ufCrm'.$smartId.'CsVat'];
    $arResult["ConditionsPay"] =$smart['ufCrm'.$smartId.'CsConditionsPay'];
    $arResult["CardNumber"] =$smart['ufCrm'.$smartId.'CsCardNumber'];
    $arResult["CardOwner"] =$smart['ufCrm'.$smartId.'CsCardOwner'];
    $arResult["TypeDT"] =$smart['ufCrm'.$smartId.'CsTypeDt'];
    $arResult["TypeDoc"] =$smart['ufCrm'.$smartId.'CsTypeDoc'];
    $arResult["TypeDog"] =$smart['ufCrm'.$smartId.'CsTypeDog'];
    $arResult["TypePay"] =$smart['ufCrm'.$smartId.'CsTypePay'];
    $arResult["Prepayment"] =$smart['ufCrm'.$smartId.'CsPrepayment'];
    $arResult["Postponement"] =$smart['ufCrm'.$smartId.'CsPostponement'];
    $arResult["Comment"] =$smart['ufCrm'.$smartId.'CsComment'];
    $arResult["Workday"] =$smart['ufCrm'.$smartId.'CsWorkday'];
    $arResult["ProviderContact"] =$smart['ufCrm'.$smartId.'CsProviderContact'];
    $arResult["ProviderType"] =$smart['ufCrm'.$smartId.'CsProviderType'];
    $arResult["TemplateClient"] =$smart['ufCrm'.$smartId.'CsTemplateClient'];
    $arResult["TemplateInternational"] =$smart['ufCrm'.$smartId.'CsTemplateInternational'];
    $arResult["Active"] =$smart['ufCrm'.$smartId.'CsActive'];
    $arResult["Copy"] =$smart['ufCrm'.$smartId.'CsCopy'];

    if($arResult["Prepayment"]>0) {
        $arResult["PrepaymentLabel"] = number_format($arResult["Prepayment"], 0, ",", " ") . " ₽.";
    }else{
        $arResult["PrepaymentLabel"] = "<span class='warning'>0</span> ₽.";
    }

    $driverId = $smart['ufCrm'.$smartId.'CsDrivers'];
    $arResult["Drivers"] =$driverId;
    $arResult["CarId"] =$smart['ufCrm'.$smartId.'CsCar'];

    if($arResult["companyId"] > 0) {

        $contactAll = $auth->core->call("crm.company.contact.items.get", ['id' => $arResult["companyId"]])->getResponseData()->getResult()->getResultData();
        foreach ($contactAll as $kContact) {
            $contactId[] = $kContact['CONTACT_ID'];
            //  d($kContact);
        }
        $filter = ['ID' => $contactId,];
        $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
        foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
            // d($smart['contactId']);
            $contacts[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . $arContact["SECOND_NAME"];
            if ($arContact['UF_CRM_CS_DRIVER'] == 1) {
                $driversAll[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
            }
            if ($arContact['ID'] == $smart['ufCrm' . $smartId . 'CsProviderContact']) {
                $contactFIO['fio'] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
                $contactFIO['phone'] = $arContact["PHONE"][0]["VALUE"];
            }
        }
        $companyReq = $auth->core->call("crm.requisite.list", ['order' => [], 'filter' => ['ENTITY_TYPE_ID' => 4, 'ENTITY_ID' => $smart['companyId']], 'select' => ['ID', 'RQ_COMPANY_NAME', 'RQ_INN', ],])->getResponseData()->getResult()->getResultData();

        if($smart['ufCrm'.$smartId.'CsProviderPay']){
            foreach ($companyReq as $kR => $vR){
                if($smart['ufCrm'.$smartId.'CsProviderPay'] === $vR['ID']){
                    $arResult["ProviderPay"]["NAME"] = $vR['RQ_COMPANY_NAME'];
                    $arResult["ProviderPay"]["INN"] = $vR['RQ_INN'];
                    $arResult["ProviderPay"]["ID"] = $vR['ID'];

                }
            }
        }

        if ($arResult["ProviderType"] === 'company'){
            if($arResult["companyId"]) {
                $company = $auth->core->call("crm.company.get", ['id' => $arResult["companyId"]])->getResponseData()->getResult()->getResultData();
                $arResult["providerName"] ="Компания перевозчик - ".$company['TITLE'];
            }
        }

    }
    if ($arResult["contactId"] > 0){

        $filter = ['ID' => $arResult["contactId"],];
        $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
        foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
            // d($smart['contactId']);
            $contacts[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . $arContact["SECOND_NAME"];
            if ($arContact['UF_CRM_CS_DRIVER'] == 1) {
                $driversAll[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
            }
            if ($arContact['ID'] == $smart['ufCrm' . $smartId . 'CsProviderContact']) {
                $contactFIO['fio'] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
                $contactFIO['phone'] = $arContact["PHONE"][0]["VALUE"];
            }
        }
        $companyReq = $auth->core->call("crm.requisite.list", ['order' => [], 'filter' => ['ENTITY_TYPE_ID' => 4, 'ENTITY_ID' => $smart['contactId']], 'select' => ['ID', 'RQ_COMPANY_NAME', 'RQ_INN', ],])->getResponseData()->getResult()->getResultData();

        if($smart['ufCrm'.$smartId.'CsProviderPay']){
            foreach ($companyReq as $kR => $vR){
                if($smart['ufCrm'.$smartId.'CsProviderPay'] === $vR['ID']){
                    $arResult["ProviderPay"]["NAME"] = $vR['RQ_COMPANY_NAME'];
                    $arResult["ProviderPay"]["INN"] = $vR['RQ_INN'];
                    $arResult["ProviderPay"]["ID"] = $vR['ID'];

                }
            }
        }


        if ($arResult["ProviderType"] === 'individual'){
            if($arResult["contactId"]) {
                $resContact = $auth->core->call("crm.contact.get", ['id' => $arResult["contactId"]])->getResponseData()->getResult()->getResultData();
//            d($resContact);
                $arResult["providerName"] = "Перевозчик: физ.лицо - " . $resContact['LAST_NAME']." ". $resContact['NAME']." ". $resContact['SECOND_NAME'];
            }
        }

    }


    if($driverId) {
        $filter = ['ID' => $driverId];
        $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "UF_CRM_CS_DATE_CHECK"];
        foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arDriver) {
            // d($arContact);
            $drivers[$arDriver["ID"]]['FIO'] = $arDriver["LAST_NAME"] . " " . $arDriver["NAME"] . " " . $arDriver["SECOND_NAME"];
            if($arDriver["UF_CRM_CS_DATE_CHECK"]){
                $drivers[$arDriver["ID"]]['CHECK'] = $arDriver["UF_CRM_CS_DATE_CHECK"];
            }else{
                $drivers[$arDriver["ID"]]['CHECK'] =  'контроль не пройден <span class="warning">*</span>';
            }

        }
    }

    if($arResult["ProviderContact"]) {

        $filter = ['ID' => $arResult["ProviderContact"],];
        $select = ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "COMPANY_ID", "UF_CRM_CS_DRIVER", "PHONE"];
        foreach ($auth->batch->getTraversableList('crm.contact.list', [], $filter, $select, 6000) as $arContact) {
            // d($smart['contactId']);
            $contacts[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . $arContact["SECOND_NAME"];
            if ($arContact['UF_CRM_CS_DRIVER'] == 1) {
                $driversAll[$arContact["ID"]] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
            }
            if ($arContact['ID'] == $smart['ufCrm' . $smartId . 'CsProviderContact']) {
                $contactFIO['fio'] = $arContact["LAST_NAME"] . " " . $arContact["NAME"] . " " . $arContact["SECOND_NAME"];
                $contactFIO['phone'] = $arContact["PHONE"][0]["VALUE"];
            }
        }
    }


    $arResult["COMPANY"]=$arResult["companyId"];
    $arResult["DRIVERS"]=$drivers;
    $arResult["contactFIO"]=$contactFIO;

//d($arResult);
    return $arResult;
}