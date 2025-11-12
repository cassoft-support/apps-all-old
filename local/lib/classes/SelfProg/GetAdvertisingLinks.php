<?
namespace Cassoft\SelfProg;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/lib/classes/SelfProg/Hbk.php");

//подключение /local/cassoftApp/adverFeedback
require_once($_SERVER["DOCUMENT_ROOT"].'/local/cassoftApp/adverFeedback/vendor/autoload.php');
use \Cassoft\Api\Avito\AvitoApi as AvitoApi;
use \Cassoft\Api\Avito\AvitoApiHandler as AvitoApiHandler;
use \Cassoft\Api\Cian\CianApi as CianApi;
use \Cassoft\Api\Cian\CianApiHandler as CianApiHandler;
use \Cassoft\Api\Domclick\DomclickApi as DomclickApi;
use \Cassoft\Api\Domclick\DomclickApiHandler as DomclickApiHandler;
use \Cassoft\Api\Yandex\YandexApi as YandexApi;
use \Cassoft\Api\Yandex\YandexApiHandler as YandexApiHandler;

//подключение модуля хайблоков
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");

class GetAdvertisingLinks
{
    //получение ссылок от авито
    public static function getLinksAvito($memberId, $arIds)
    {
        //поиск настроек доступа на рекламные площадки для текущего портала в хайблоке
        $hl = \Cassoft\SelfProg\Hbk::getHlbk('client_advertising_accesses');
        $arSettings = self::getSettings($memberId, $hl);

        /*----------- поиск ссылок на объявления -------------*/
        $accessParams = array(
            'client_id' => $arSettings["UF_CS_AVITO_CLIENT_ID"],
            'client_secret' => $arSettings["UF_CS_AVITO_CLIENT_SECRET"]
        );

        $client = new \GuzzleHttp\Client();
        $avito = new AvitoApi($accessParams, $client);
        $avitoHandler = new AvitoApiHandler($avito);

        // $res = $avito->autorizate();
        // $res3 = $avito->getLastReport();

        $res = $avitoHandler->getLink($arIds);

        return $res;
    }

    //получение ссылок от Циана
    public static function getLinksCian($memberId, $arIds)
    {
        //поиск настроек доступа на рекламные площадки для текущего портала в хайблоке
        $hl = \Cassoft\SelfProg\Hbk::getHlbk('client_advertising_accesses');
        $arSettings = self::getSettings($memberId, $hl);

        /*----------- поиск ссылок на объявления -------------*/
        $token = $arSettings["UF_CS_CIAN_TOKEN"];

        $client = new \GuzzleHttp\Client();
        $cian = new CianApi($token, $client);
        $cianHandler = new CianApiHandler($cian);

        $res = $cianHandler->getLink($arIds);

        return $res;
    }

    //получение ссылок от ДомКлика
    public static function getLinksDomсlick($memberId, $arIds)
    {
        //поиск настроек доступа на рекламные площадки для текущего портала в хайблоке
        $hl = \Cassoft\SelfProg\Hbk::getHlbk('client_advertising_accesses');
        $arSettings = self::getSettings($memberId, $hl);

        /*----------- поиск ссылок на объявления -------------*/
        $tokenDom = $arSettings["UF_CS_DOMCLICK_TOKEN"];
        $linkDom = $arSettings["UF_CS_DOMCLICK_LINK"];

        $client = new \GuzzleHttp\Client();
        $domclick = new DomclickApi($tokenDom, $linkDom, $client);
        $domclickHadler = new DomclickApiHandler($domclick);

        $res = $domclickHadler->getLink($arIds);

        return $res;
    }

    //получение ссылок от Яндекса
    public static function getLinksYandex($memberId, $arIds)
    {
        //поиск настроек доступа на рекламные площадки для текущего портала в хайблоке
        $hl = \Cassoft\SelfProg\Hbk::getHlbk('client_advertising_accesses');
        $arSettings = self::getSettings($memberId, $hl);

        /*----------- поиск ссылок на объявления -------------*/
        $tokenYandex = $arSettings["UF_CS_YANDEX_TOKEN"];
        $xTokenYandex = $arSettings["UF_CS_YANDEX_X_TOKEN"];

        $client = new \GuzzleHttp\Client();
        $yandex = new YandexApi($tokenYandex, $Xtoken, $client);
        $yandexHandler = new YandexApiHandler($yandex);

        $res = $YandexApiHandler->getLink($arIds);

        return $res;
    }


    //функция возвращает содержимое файла по указанному url
    private static function getXmlFromUrl($url){
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    
        $xmlstr = curl_exec($ch);
        curl_close($ch);
    
        return $xmlstr;
    }
    



    //функция получает ссылку на xml-файл выгрузки на Авито
    //функция находит в xml-файле и возвращает массив id сделок
    public static function getDealsIdsFromAvitoXml($xmlLink)
    {
        $response_xml_data = self::getXmlFromUrl($xmlLink);
        $xml = simplexml_load_string($response_xml_data);
        $arDealIds = array();
        foreach ($xml->Ad as $ad){
            $arDealIds[] = (string) $ad->Id[0];
        }
        return $arDealIds;
    }

    //функция получает ссылку на xml-файл выгрузки на Циан
    //функция находит в xml-файле и возвращает массив id сделок
    public static function getDealsIdsFromCianXml($xmlLink)
    {
        $response_xml_data = self::getXmlFromUrl($xmlLink);
        $xml = simplexml_load_string($response_xml_data);
        $arDealIds = array();
        foreach ($xml->object as $object){
            $arDealIds[] = (string) $object->ExternalId[0];
        }
        return $arDealIds;
    }

    //функция получает ссылку на xml-файл выгрузки на ДомКлик
    //функция находит в xml-файле и возвращает массив id сделок
    public static function getDealsIdsFromDomclickXml($xmlLink)
    {
        $response_xml_data = self::getXmlFromUrl($xmlLink);
        $xml = simplexml_load_string($response_xml_data);
        $arDealIds = array();
        foreach ($xml->offer as $offer){
            $arDealIds[] = (string) $offer["internal-id"];
        }
        return $arDealIds;
    }

    //функция получает ссылку на xml-файл выгрузки на Яндекс
    //функция находит в xml-файле и возвращает массив id сделок
    public static function getDealsIdsFromYandexXml($xmlLink)
    {
        $response_xml_data = self::getXmlFromUrl($xmlLink);
        $xml = simplexml_load_string($response_xml_data);
        $arDealIds = array();
        foreach ($xml->offer as $offer){
            $arDealIds[] = (string) $offer["internal-id"];
        }
        return $arDealIds;
    }


    //функция получает настройки для облака из хайблока
    private static function getSettings($memberId, $hl)
    {
        $rowsResult = $hl::getList(array(
            'select' => array('*'),
            'filter' => array("UF_CS_CLIENT_MEMBER_ID" => $memberId),
            'order' => array("ID" => "DESC"),
            'limit' => 1,
        ));
        $res = $rowsResult->fetch();
        return $res;
    }
}