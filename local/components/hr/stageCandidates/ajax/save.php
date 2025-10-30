<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . '/pub/cassoftApp/brokci/tools/bitrix24call.php');
$file_log = "/home/bitrix/www/local/components/dev_settings/objectStage/ajax/logSave.txt";

file_put_contents($file_log, print_r("start",true));
file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);   
$UserA=json_decode($_POST['UserAutStage'], true);

 $CRest = new CSRest();
 $CRest::setCRestData($UserA);
 $User = CSRest::call('user.current');
$UserID =$User['result']["ID"];    
//file_put_contents($file_log, print_r($User,true), FILE_APPEND);   
$code=translit($_POST['NAME']);
 $method="entity.item.update";
 $params=array(
  'ENTITY'=>"stage_object",
  'ID'=>$_POST['ID'],
  'MODIFIED_BY' => $UserID,
 'NAME' =>$_POST['NAME'],
  'PROPERTY_VALUES'=> [
         'CS_TYPE_STAGE' => $_POST['CS_TYPE_STAGE'],
         'CS_COLOR' => $_POST['CS_COLOR'],
         'CS_CODE' => $code,
  ],
 );
 $entityUp = CSRest::call($method, $params);
 file_put_contents($file_log, print_r($entityUp,true), FILE_APPEND);  
 $arParam['UserAut']=$UserA;
 if($entityUp['result']>0)
 {
          $APPLICATION->IncludeComponent(
            "dev_settings:objectStage",
                    "b4",
                    $arParam,
                    false
          );
 }

 function translit($value)
{
	$converter = array(
		'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
		'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
		'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
		'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
		'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
		'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
		'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
 
		'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
		'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
		'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
		'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
		'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
		'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
		'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
	);
 
	$value = strtr($value, $converter);
	return $value;
}
?>
