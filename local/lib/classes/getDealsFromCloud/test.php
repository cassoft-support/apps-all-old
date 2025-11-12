<?
require_once($_SERVER["DOCUMENT_ROOT"]."/local/lib/classes/getDealsFromCloud/GetDealsFromCloud123.php");

$app = new \Cassoft\SelfProg\GetDealsFromCloud;

$domen = 'test.brokci.ru';
// $domen = 'cassoft.bitrix24.ru';


//авторизация в облаке
$auth = $app->getAuthFromDB($domen);

if (!$auth)
{
    $error = $app->getError();
    echo "ошибка авторизации:<br>";
    echo "<pre>";
    print_r($error);
    echo "</pre><br>";
}
else
{
    $arDeals = $app->getDeals();
    echo "сделки:<br>";
    echo "<pre>";
    print_r($arDeals);
    echo "</pre><br>";
}