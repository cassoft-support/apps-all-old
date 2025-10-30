<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once("tools/tools.php");
require_once('tools/HlbkForRealEst.php');

$b24_error = '';
$isTokenRefreshed = false; //ключ обновления кода доступа
$arAccessParams = prepareFromRequest($_REQUEST); //параметры для авторизации
//создание специального класса для выполнения запросов (arB24App)
$arB24App = getBitrix24($arAccessParams, $isTokenRefreshed, $b24_error);
if ($b24_error) exit('Ошибка авторизации');



//если не грузятся скрипты, убрать эпилог
// require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>