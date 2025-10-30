<?php //класс получает id сделок, выгружаемых в рекламу
namespace Cassoft\SelfProg;

class GetDealsIdsFromXml
{
    public function getDeals($filePath)
    {
        $xml = simplexml_load_file($_SERVER["DOCUMENT_ROOT"] . $filePath);

        $arDealIds = array();
        foreach ($xml->Ad as $ad){
            $test = $ad->Id;
            $arDealIds[] = (string) $ad->Id[0];
        }

        return $arDealIds;
    }
}