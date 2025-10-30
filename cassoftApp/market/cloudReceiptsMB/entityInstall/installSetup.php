<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/SelfProg/Hbk.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
//require_once "UserField.php";
function d($print){echo "<pre>"; print_r($print); echo "</pre>";}

$file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/entityInstall/logSetup.txt";
file_put_contents($file_log, print_r("Setup",true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
$CSRest = new CSRest("cloud_receipts_mb");

if ($_POST) {
  $authParams = json_decode($_POST['request'], true);
  $member = $authParams['member_id'];
  
    $HlEntities = new \Cassoft\Services\HlService('entity_list');
    $entities = $HlEntities->hl::getList([
        'select' => ['*'],
        'order' => [],
        'filter' => [
            'UF_CS_APP_NAME' => 'cloud_receipts'
        ]
    ])->fetchAll();

    foreach ($entities as $entity) {
        $entityParams = [
            'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
            'NAME' => $entity['UF_CS_ENTITY_NAME'],
            'ACCESS' => [
                'AU' => 'W'
            ]
        ];


        $needInstall = false;
        $entityGet = $CSRest->call('entity.get', $entityParams);
        if ($entityGet['error'] == 'ERROR_ENTITY_NOT_FOUND') {
            $needInstall = true;
        }

        if ($needInstall === true) {
            $entityInstall = $CSRest->call('entity.add', $entityParams);
        } else {
            $entityUpdate = $CSRest->call('entity.update', $entityParams);
        }

        $HlEntityProperties = new \Cassoft\Services\HlService($entity['UF_CS_TABLE_NAME']);
        $entityProperties = $HlEntityProperties->hl::getList([
            'select' => ['*'],
            'order' => [],
            'filter' => []
        ])->fetchAll();

        foreach ($entityProperties as $property) {
            $propertyParam = [
                'ENTITY' => $entityParams['ENTITY'],
                'PROPERTY' => $property['UF_CS_PROPERTY'],
                'NAME' => $property['UF_CS_NAME'],
                'TYPE' => $propertiesType[$property['UF_CS_TYPE']],
            ];
            $propertyGet = $CSRest->call('entity.item.property.get', $propertyParam);
            if ($propertyGet['error'] == 'ERROR_PROPERTY_NOT_FOUND') {
                $propertyAdd = $CSRest->call('entity.item.property.add', $propertyParam);
            } else {
                $propertyAdd = $CSRest->call('entity.item.property.update', $propertyParam);
            }
        }
    }
   /* 
    Array
    (
        [error] => ERROR_ARGUMENT
        [error_description] => Argument 'ENTITY' is null or empty
        [argument] => ENTITY
    )
*/
    $result['setup'] = 'success';
    echo json_encode($result);
}
