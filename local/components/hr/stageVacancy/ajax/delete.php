<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . '/pub/cassoftApp/brokci/tools/bitrix24call.php');
$file_log = "/home/bitrix/www/local/components/brokci_settings/favouritesStage/ajax/logDel.txt";

file_put_contents($file_log, print_r("delete",true));
$UserA=json_decode($_POST['UserAutPlan'], true);
file_put_contents($file_log, print_r($_POST,true) , FILE_APPEND);
 $CRest = new CSRest();
 $CRest::setCRestData($UserA);
$User = CSRest::call('user.current');
$UserID =$User['result']["ID"];    
$methodDel="entity.item.delete";
$paramsDel=array(
  'ENTITY'=>"stage_fav",
  'ID'=>$_POST['id'],
  
);

 $method="entity.item.update";
 $params=array(
   'ENTITY'=>"stage_fav",
   'ID'=>$_POST['id'],
   'MODIFIED_BY' => $UserID,
   'ACTIVE' => 'N'
 );
 
 
 $planType = CSRest::call($method, $params);
 
 if($planType['result']==1)
 {
          $arParam['$UserA']=$UserA;
          $APPLICATION->IncludeComponent(
            "brokci_settings:favouritesStage",
                    "b4",
                    $arParam,
                    false
          );
 }
?>
