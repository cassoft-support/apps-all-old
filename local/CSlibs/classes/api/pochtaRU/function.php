<?php
/*
define(NOT_CHECK_PERMISSIONS, true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
echo "test";
*/
function getTracHistory($barcode){
    $wsdlurl = 'https://tracking.russianpost.ru/rtm34?wsdl';
    $login = "ZNkvFVfJdUXtOZ";
    $password = "j9zwTwfJqL71";
    $client = new SoapClient($wsdlurl, array('trace' => 1, 'soap_version' => SOAP_1_2));
    $params3 = array ('OperationHistoryRequest' => array ('Barcode' => $barcode, 'MessageType' => '0','Language' => 'RUS'),
        'AuthorizationHeader' => array ('login'=>$login,'password'=>$password));
    $result = $client->getOperationHistory(new SoapParam($params3,'OperationHistoryRequest'));
    return $result;
}
/*

d($result);
foreach ($result->OperationHistoryData->historyRecord as $record) {
    printf("<p>%s </br>  %s, %s</p>",
        $record->OperationParameters->OperDate,
        $record->AddressParameters->OperationAddress->Description,
        $record->OperationParameters->OperAttr->Name);
};
$barcode = "30500186038305";

*/
?>