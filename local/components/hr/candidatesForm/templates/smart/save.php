<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Install/guide/forma.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");
$debug = new \debug('debug');
$date = date("d.m.YTH:i");
$file_log = __DIR__ . "/logSave.txt";
file_put_contents($file_log, print_r($date . "\n", true));
file_put_contents($file_log, print_r($_POST , true), FILE_APPEND);


if($_POST['UserAut']['member_id']){
    $memberId = $_POST['UserAut']['member_id'];
}

if ($_POST['app'] && $memberId) {
    $CloudApp = $_POST['app'];
    $appAccess = 'app_' . $CloudApp . '_access';
//  $debug->console($_REQUEST, "app");
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


$drivers = $_POST['drivers'];
sort($drivers);
    $params=[
        'entityTypeId'=>$_POST['entityTypeId'],
        'id'=> $_POST['smartElId'],
        'fields'=>[
            "companyId" => null,
            "contactId" => null,
            "ufCrm".$_POST['smartId']."CsProviderContact"=> $_POST['contact'],
            "ufCrm".$_POST['smartId']."CsCar"=> $_POST['car'],
            "ufCrm".$_POST['smartId']."CsProviderPay"=> $_POST['ProviderPay'],
            //    "ufCrm".$_POST['smartId']."CsDrivers"=> json_encode($_POST['drivers']),
            "ufCrm".$_POST['smartId']."CsDrivers"=> $drivers,
            "ufCrm".$_POST['smartId']."CsComment"=> $_POST['Comment'],
            "ufCrm".$_POST['smartId']."CsProviderType"=> $_POST['ProviderType'],
        ]
    ];
    if($_POST['ProviderType'] === 'company'){
        $params['fields']["companyId"]= $_POST['ProviderID'];
        $params['fields']["ufCrm".$_POST['smartId']."CsProvider"] = "CO_".$_POST['ProviderID'];
    }
    if($_POST['ProviderType'] === 'individual'){
        $params['fields']["contactId"]= $_POST['ProviderID'];
        $params['fields']["ufCrm".$_POST['smartId']."CsProvider"] = "C_".$_POST['ProviderID'];
    }



    $smart = $auth->core->call("crm.item.get", ['entityTypeId'=> $_POST['entityTypeId'], 'id'=> $_POST['smartElId']])->getResponseData()->getResult()->getResultData()['item'];
    file_put_contents($file_log, print_r("\nsmart\n" , true), FILE_APPEND);
    file_put_contents($file_log, print_r($smart , true), FILE_APPEND);


    //финансовая информация
    /*
   if($smart["ufCrm".$_POST['smartId']."CsProviderPay"] !==$_POST['ProviderPay']){
        $paramReq=[
            'order'=> [],
            'filter'=> ['ENTITY_TYPE_ID'=> 4, 'ENTITY_ID'=> $_POST['ProviderID'], 'ID'=> $_POST['ProviderPay']],
            'select'=> ["ID", "RQ_COMPANY_NAME",  "UF_CRM_CS_CARD_NUM", "UF_CRM_CS_TYPE_PAY",  "UF_CRM_CS_VAT", "UF_CRM_CS_CARD_OWNER", "UF_CRM_CS_TYPE_DT", "UF_CRM_CS_TYPE_DOC", "UF_CRM_CS_TYPE_DOG", ]
        ];
        $req = $auth->core->call("crm.requisite.list", $paramReq)->getResponseData()->getResult()->getResultData()[0];
     //   file_put_contents($file_log, print_r($req , true), FILE_APPEND);
        $order =['ID' => 'DESC'];
        $select =['*'];
        $filter = [];

            foreach ($auth->batch->getTraversableList('crm.requisite.userfield.list', $order, $filter, $select, 6000) as $arProperty) {
    if($arProperty['XML_ID'] ==='CS_VAT'){
        foreach ($arProperty['LIST'] as $kv){
            $arVat[$kv['ID']]=$kv['VALUE'];
        }
    }
            if($arProperty['XML_ID'] ==='CS_TYPE_PAY'){
                foreach ($arProperty['LIST'] as $kv){
                    $arTypePay[$kv['ID']]=$kv['VALUE'];
                }
            }
            if($arProperty['XML_ID'] ==='CS_TYPE_DT'){
                foreach ($arProperty['LIST'] as $kv){
                    $arTypeDT[$kv['ID']]=$kv['VALUE'];
                }
            }
            if($arProperty['XML_ID'] ==='CS_TYPE_DOC'){
                foreach ($arProperty['LIST'] as $kv){
                    $arTypeDoc[$kv['ID']]=$kv['VALUE'];
                }
            }
            if($arProperty['XML_ID'] ==='CS_TYPE_DOG'){
                foreach ($arProperty['LIST'] as $kv){
                    $arTypeDog[$kv['ID']]=$kv['VALUE'];
                }
            }
        }
    $resTypePay = $TypePayName[$arTypePay[$req['UF_CRM_CS_TYPE_PAY']]];
    $resVat = $vatName[$arVat[$req['UF_CRM_CS_VAT']]];
    $resTypeDT = $TypeDTName[$arTypeDT[$req['UF_CRM_CS_TYPE_DT']]];
    $resTypeDoc = $TypeDocName[$arTypeDoc[$req['UF_CRM_CS_TYPE_DOC']]];
    $resTypeDog = $TypeDogName[$arTypeDog[$req['UF_CRM_CS_TYPE_DOG']]];

        $params['fields']["ufCrm".$_POST['smartId']."CsCardNumber"]= $req['UF_CRM_CS_CARD_NUM'];
        $params['fields']["ufCrm".$_POST['smartId']."CsCardOwner"]= $req['UF_CRM_CS_CARD_OWNER'];
        $params['fields']["ufCrm".$_POST['smartId']."CsVat"]= $resVat;
        $params['fields']["ufCrm".$_POST['smartId']."CsTypeDt"]= $resTypeDT;
        $params['fields']["ufCrm".$_POST['smartId']."CsTypeDoc"]= $resTypeDoc;
        $params['fields']["ufCrm".$_POST['smartId']."CsTypeDog"]= $resTypeDog;
        $params['fields']["ufCrm".$_POST['smartId']."CsTypePay"]= $resTypePay;

            file_put_contents($file_log, print_r($params , true), FILE_APPEND);
        }
    */

    $smartUp = $auth->core->call("crm.item.update", $params)->getResponseData()->getResult()->getResultData();
    file_put_contents($file_log, print_r($params , true), FILE_APPEND);
    file_put_contents($file_log, print_r($smartUp , true), FILE_APPEND);
   // $debug->console($params, 'post');
echo "Y";
}