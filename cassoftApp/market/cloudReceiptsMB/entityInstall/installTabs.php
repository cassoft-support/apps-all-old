<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
function d($print){echo "<pre>"; print_r($print); echo "</pre>";}

$file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/entityInstall/logTabs.txt";
file_put_contents($file_log, print_r("Tabs",true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
$CSRest = new CSRest("cloud_receipts_mb");

if ($_POST) {
 // $authParams = json_decode($_POST['request'], true);
  //$member = $authParams['member_id'];

    $result = [];
    

//---------------- вкладка в сделке

    $isPlacementExist = false;
    $placementParams = [
        'PLACEMENT' => 'CRM_DEAL_DETAIL_TAB',
        'HANDLER' => "https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/index.php",
        'TITLE' => 'МодульБанк',
        'DESCRIPTION' => 'cassoft.ru'
    ];

    
    $getPlacement = $CSRest->call('placement.get');
    if (!empty($getPlacement['0'])) {
        foreach ($getPlacement as $placement) {
            if (($placement['handler'] == $placementParams['HANDLER']) && ($placement['placement'] == $placementParams['PLACEMENT'])) {
                $isPlacementExist = true;
            }
           
        }
    }
    if (!$isPlacementExist) {
        $addPlacement = $CSRest->call('placement.bind', $placementParams);
    }

   

$result['Tabs'] = 'success';
echo json_encode($result);
}
