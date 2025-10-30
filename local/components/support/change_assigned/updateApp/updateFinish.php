<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/SelfProg/Hbk.php';
require_once($_SERVER["DOCUMENT_ROOT"] . "/pub/cassoftApp/realEstateObject/PropertyCatalog.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
//require_once ($_SERVER["DOCUMENT_ROOT"] . '/pub/test/blockSite/installBlock.php');
$file_log= "/home/bitrix/www/local/components/brokci_settings/update/updateApp/logUpdateFinish.txt";
file_put_contents($file_log, print_r("instal-finish",true));

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpClient\HttpClient;


if ($_POST) {
  /*
    $result = [];
    $authParams = $_POST['authParams'];
    $userParams = $_POST['user'];
    $HlAuthParmas = new \Cassoft\Services\HlService('app_brokci_accesses');
    $data = array(
        'UF_CS_CLIENT_PORTAL_MEMBER_ID'     => $authParams['member_id'],
        'UF_CS_CLIENT_PORTAL_DOMEN'         => $authParams['domain'],
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'  => $authParams['access_token'],
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $authParams['refresh_token']
    );
    $checkClient = $HlAuthParmas->hl::getList(array(
        'select' => array('*'),
        'filter' => array("UF_CS_CLIENT_PORTAL_MEMBER_ID" => $authParams['member_id']),
        'order' => array("ID" => "DESC"),
        'limit' => 1,
    ));
    $resultCheck = $checkClient->fetch();
    if ($resultCheck['ID']) {
        $update = $HlAuthParmas->hl::update($resultCheck["ID"], $data);
    } else {
        $add = $HlAuthParmas->hl::add($data);
    }

    $HlUserParmas = new \Cassoft\Services\HlService('client_app_cassoft');
    $userData = [
        'UF_CS_CLIENT_PORTAL_DOMEN'=>$authParams['domain'],
        'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $authParams['member_id'],
        'UF_CS_APP_NAME' =>'brokci-pro',
        'UF_CS_CLIENT_FIO' => "{$userParams['LAST_NAME']} {$userParams['NAME']} {$userParams['SECOND_NAME']}",
        'UF_CS_CLIENT_PHOTO' => $userParams['PERSONAL_PHOTO'],
        'UF_CS_CLIENT_TEL' => $userParams['WORK_PHONE'],
        'UF_CS_CLIENT_TEL2' =>$userParams['PERSONAL_PHONE'],
        'UF_CS_CLIENT_MAIL' => $userParams['EMAIL'],
        'UF_CS_BROKCI_PRO' => 'true'
    ];
    $checkUser = $HlUserParmas->hl::getList([
        'select' => ['*'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $authParams['member_id']],
        'order' => ['ID' => 'DESC'],
        'limit' => 1,
    ]);
    $resultCheck = $checkUser->fetch();

    if ($resultCheck['ID']) {
        if($resultCheck['UF_CS_FOLDER']){
$csCode =$resultCheck['UF_CS_FOLDER'];
        }else{
            $folder = crypt($resultCheck['ID'], 'ucre');
            $userData['UF_CS_FOLDER']=$folder; 
        }
        $update = $HlUserParmas->hl::update($resultCheck['ID'], $userData);
    } else {

        $add = $HlUserParmas->hl::add($userData);
        $element=$add->getId();
        file_put_contents($file_log, print_r($element,true), FILE_APPEND);
        if($element){
       $folder = crypt($element, 'ucre');
        $csCode=$folder;
       $userData['UF_CS_FOLDER']=$folder; 
        $update = $HlUserParmas->hl::update($element, $userData);
        }
    }

    
if($csCode){
    file_put_contents($file_log, print_r("code\n",true), FILE_APPEND);
    file_put_contents($file_log, print_r($csCode,true), FILE_APPEND);
    $CloudApp='brokci_2';
    $appAccess ='app_brokci_accesses';
    $CloudApplication = new \Cloud\App\CloudApplication($CloudApp);
    $HlClientAppCASSOFT = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientAppCASSOFT->hl::getList([
      'select' => ['*'],
      'order' => ['ID' => 'ASC'],
      'filter' => [ 'UF_CS_CLIENT_PORTAL_MEMBER_ID' =>$authParams['member_id']]
    ])->fetchAll();
    $hlKeys = [
      'UF_CS_CLIENT_PORTAL_MEMBER_ID',
      'UF_CS_CLIENT_PORTAL_DOMEN',
      'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
      'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
    ];
    file_put_contents($file_log, print_r($clientsApp,true), FILE_APPEND);
    $clientApp = $clientsApp['0'];
    
    $auth = new Auth($CloudApplication, $clientApp, 'log.log', '/home/bitrix/www/pub/cassoftApp/brokci/entityInstall/');
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
    
  //   file_put_contents($file_log, print_r($auth,true), FILE_APPEND);

  //  $arObject =blockObject($csCode);
    
           $resBlock[]=$arObject;
           file_put_contents($file_log, print_r($resBlock,true), FILE_APPEND);
/*          
        foreach($resBlock as $params){
            file_put_contents($file_log, print_r($params,true), FILE_APPEND);
        $registerBlock = $auth->core->call(
            'landing.repo.register',  $params
        );
       
        }
        */
 //   }        





    $result['finish'] = 'success';
    $result['check'] = $resultCheck;
    $result['auth'] = $authParams;
    $result['POST'] = $_POST;
    echo json_encode($result);
}
