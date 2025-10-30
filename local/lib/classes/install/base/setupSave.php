<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
function setupSave($app, $eventApp, $CSRest){
  $date=date("d.m.YTH:i");
$file_log = __DIR__."/setupSave.txt";
file_put_contents($file_log, print_r($date."\n",true));
file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
$resUser = $CSRest->call('user.current');
file_put_contents($file_log, print_r($resUser,true), FILE_APPEND);



$HlAuthParams = new \Cassoft\Services\HlService('app_auth_params');
$authParams = $HlAuthParams->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => []
 //   'filter' => [ 'UF_APP_NAME' =>$app]
])->fetchAll();
foreach($authParams as $keyParams => $valParams){
  if($valParams['UF_APP_NAME']==$app){
    $tabAccesses =$valParams['UF_ACCESSES'];
  }
  $arAPP[$valParams['UF_APP_NAME']]=$valParams['ID'];
}
file_put_contents($file_log, print_r($arAPP,true), FILE_APPEND);
//$tabAccesses = $authParams['0']['UF_ACCESSES'];
file_put_contents($file_log, print_r($tabAccesses,true), FILE_APPEND);

    /*---------- сохранение авторизационных данных -----------*/
    //поиск авторизационных данных в хайблоке
$HlAccesses = new \Cassoft\Services\HlService($tabAccesses);
$accessesApp= $HlAccesses->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' =>$_REQUEST['member_id']]
])->fetchAll();

file_put_contents($file_log, print_r($accessesApp ,true), FILE_APPEND);
    //подготовка данных для хайблока
    $data = array(
        'UF_CS_CLIENT_PORTAL_MEMBER_ID'     => $_REQUEST['member_id'],
        'UF_CS_CLIENT_PORTAL_DOMEN'         => $_REQUEST['DOMAIN'],
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'  => $_REQUEST['AUTH_ID'],
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $_REQUEST['REFRESH_ID']
    );
    file_put_contents($file_log, print_r($data ,true), FILE_APPEND);
    if ($accessesApp["0"]["ID"]) //если данные уже существуют, обновим их
    {
        $resAccesses = $HlAccesses->hl::update($accessesApp["0"]["ID"], $data);
    }
    else //иначе добавим новую запись
    {
      file_put_contents($file_log, print_r("add\n" ,true), FILE_APPEND);
        $resAccesses = $HlAccesses->hl::add($data);  
        file_put_contents($file_log, print_r($resAccesses ,true), FILE_APPEND);
    }
   
    /*---------- сохранение настроек -----------*/
   $HlClientAppCASSOFT = new \Cassoft\Services\HlService("client_app_cassoft");
$ClientAppCASSOFT= $HlClientAppCASSOFT->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' =>$_REQUEST['member_id']]
])->fetchAll();

file_put_contents($file_log, print_r($ClientAppCASSOFT ,true), FILE_APPEND);
    //подготовка данных для хайблока
    $installAPP=[];
    $installAPP=$ClientAppCASSOFT["0"]["UF_INSTALL_APP"];
    $installAPP[]=$arAPP[$app];
    $installAPP=array_unique($installAPP, SORT_NUMERIC);

    $dataClientAppCASSOFT = array(
        "UF_CS_CLIENT_PORTAL_MEMBER_ID"  => $_REQUEST['member_id'],
        "UF_CS_CLIENT_PORTAL_DOMEN"   => $_REQUEST['DOMAIN'],
        "UF_CS_ACTIVE"=>"1",
        "UF_CS_CLIENT_FIO"=>$resUser['result']['LAST_NAME']." ".$resUser['result']['NAME']." ".$resUser['result']['SECOND_NAME'],
        "UF_CS_CLIENT_TEL2"=>$resUser['result']['WORK_PHONE'], 
        "UF_CS_CLIENT_TEL"=>$resUser['result']['PERSONAL_MOBILE'],
        "UF_CS_CLIENT_MAIL"=>$resUser['result']['EMAIL'],
        "UF_CS_CLIENT_PHOTO"=>$resUser['result']['PERSONAL_PHOTO'],
        "UF_INSTALL_APP"=>$installAPP,
  /*      "UF_CS_CLOUD_RECEIPTS_MB"=>"",
        "UF_CS_LICENSE_DEV_PRO"=>"",
        "UF_CS_LICENSE_BROKCI_PRO"=>"",
        "UF_CS_DATE_LICENSE_DEV_PRO"=>"",
        "UF_CS_DATE_LICENSE_BROKCI_PRO"=>"",
        "UF_CS_FOLDER"=>"",
        "UF_CS_PBX"=>"",
        "UF_CS_DEV_PRO"=>"",
        "UF_CS_BROKCI_PRO"=>"",
        "UF_CS_OBJECT"=>"",
        "UF_CS_BROKCI_INDUSTRY"=>"",
        "UF_CS_CALLS_CONTROLLER"=>"",
        "UF_CS_SELECT_RELEVANT"=>"",
        "UF_CS_SELECTION_MOB"=>"",
        "UF_CS_GALLERY"=>"",
        "UF_CS_MARKETING"=>"",
"UF_CS_APP_NAME"=>"",
        "UF_INSTALL_APP"=>"",
*/
  
    );
    file_put_contents($file_log, print_r($dataClientAppCASSOFT ,true), FILE_APPEND);
    
    if ($ClientAppCASSOFT["0"]["ID"]) //если данные уже существуют, обновим их
    {
        $resClientAppCASSOFT = $HlClientAppCASSOFT->hl::update($ClientAppCASSOFT["0"]["ID"], $dataClientAppCASSOFT);
    }
    else //иначе добавим новую запись
    {
      file_put_contents($file_log, print_r("add\n" ,true), FILE_APPEND);
        $resClientAppCASSOFT = $HlClientAppCASSOFT->hl::add($ClientAppCASSOFT);  
        file_put_contents($file_log, print_r($resClientAppCASSOFT ,true), FILE_APPEND);
    }
   




}