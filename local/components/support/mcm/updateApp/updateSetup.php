<?php
//function blockObjectCarusel($csCode){
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$file_log="/home/bitrix/www/local/components/brokci_settings/update/updateApp/logUpdateSetup.txt";
file_put_contents($file_log, print_r("updateSetup",true));
//echo "<pre>"; print_r($_REQUEST); echo "</pre>";
file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once($_SERVER["DOCUMENT_ROOT"] . '/local/lib/classes/Cloud/Bitrix24Api/bitrix24call.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/pub/cassoftApp/brokci/tools/bitrix24call.php');

if ($_POST) {
  $arParams=json_decode($_POST['authParams'], true);
  file_put_contents($file_log, print_r($arParams,true), FILE_APPEND);
  $memberId = $arParams['member_id'];
$HlPropertiesType = new \Cassoft\Services\HlService('product_property_type');
    $propertiesType = $HlPropertiesType->makeFieldToField('ID', 'UF_CODE');
if($_POST['cassoftApp'] == "brokciPro"){
$CloudApplication = new \Cloud\App\CloudApplication('brokci_2');
$HlClientAppCASSOFT = new \Cassoft\Services\HlService('app_brokci_accesses');
} elseif($_POST['cassoftApp'] == "devPro"){
  $CloudApplication = new \Cloud\App\CloudApplication('devPro');
$HlClientAppCASSOFT = new \Cassoft\Services\HlService('app_dev_pro_access');
}
$clientsApp = $HlClientAppCASSOFT->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => [ 'UF_CS_CLIENT_PORTAL_MEMBER_ID' =>$memberId
      ]
])->fetchAll();
$hlKeys = [
    'UF_CS_CLIENT_PORTAL_MEMBER_ID',
    'UF_CS_CLIENT_PORTAL_DOMEN',
    'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
    'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
];

$clientApp = $clientsApp['0'];
$CRest = new CSRest();
$UserA = array();
$UserA = array(
  'access_token' => $arParams['AUTH_ID'],
  'application_token' => $arParams['APP_SID']
);
$UserA['member_id'] = $clientApp['UF_CS_CLIENT_PORTAL_MEMBER_ID'];
$UserA['domen'] = $clientApp['UF_CS_CLIENT_PORTAL_DOMEN'];
$UserA['refresh_token'] = $clientApp['UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'];
$CRest::setCRestData($UserA);

  $auth = new Auth($CloudApplication, $clientApp, 'logSetup.log', "/home/bitrix/www/local/components/brokci_settings/update/updateApp/");
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

    $HlEntities = new \Cassoft\Services\HlService('entity_list');
    $entities = $HlEntities->hl::getList([
        'select' => ['*'],
        'order' => [],
        'filter' => [
            'UF_CS_APP_NAME' => 'setup'
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

        $entityGet =  CSRest::call('entity.get', $entityParams);

       if ($entityGet['error']) {
            $entityInstall = $auth->core->call('entity.add', $entityParams);
        } else {
            $entityUpdate = $auth->core->call('entity.update', $entityParams);
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
            $arPropertyParam = [
              'PROPERTY' => $property['UF_CS_PROPERTY'],
             'NAME' => $property['UF_CS_NAME'],
              'TYPE' => $propertiesType[$property['UF_CS_TYPE']],
             'SORT' => 500
          ];
          //  file_put_contents($file_log, print_r($propertyParam,true), FILE_APPEND);
         
          $itemPropertyParam = [
            'ENTITY' => $entityParams['ENTITY'],
        ];
          $propertyGet = CSRest::call('entity.item.property.get', $propertyParam);
    
           if ($propertyGet['error'] =="ERROR_PROPERTY_NOT_FOUND") {
            file_put_contents($file_log, print_r("Создаем\n",true), FILE_APPEND);
                file_put_contents($file_log, print_r($propertyParam,true), FILE_APPEND);
                $propertyAdd = $auth->core->call('entity.item.property.add', $propertyParam)->getResponseData()->getResult()->getResultData();
            } else {
              $comparison=array_diff($resPropertyGet['result'], $arPropertyParam);
              if (count($comparison) !== 0 ) {
               $propertyAdd = $auth->core->call('entity.item.property.update', $propertyParam)->getResponseData()->getResult()->getResultData();
            }else{
              file_put_contents($file_log, print_r("Пропускаем\n",true), FILE_APPEND);
            }
          }
        }
        
    }


    $result['setup'] = 'success';
    //$result['addDealFields'] = $addDealFields;
    $result['add'] = $add;
    
    echo json_encode($result);
}
