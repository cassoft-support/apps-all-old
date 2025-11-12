<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


function changeUserCompany($params)
{
    $log = __DIR__ . "/logChangeN.txt";
    p($params, "start", $log);
    if ($params['data']['FIELDS']['ID']) {
      // p($params['data']['FIELDS']['ID'], "ID", $log);
        $idCompany = $params['data']['FIELDS']['ID'];
        $user_id = $params['auth']['user_is'];
        $auth = new \CSlibs\B24\Auth\Auth($params['app'], [], $params['auth']['member_id']);
        $resCompany = $auth->CScore->call("crm.company.get", ['ID' => $idCompany,]);
     //  p($resCompany , "resCompany", $log);
        $leadID = $resCompany['LEAD_ID'];
        $UserAs = $resCompany['ASSIGNED_BY_ID'];
      //  p($UserAs, "UserAs", $log);
        foreach ($auth->batch->getTraversableList('crm.contact.list', [], ["COMPANY_ID" => $idCompany], ["*"], 6000) as $arContact) {
           // p($arContact, "arContact", $log);
            if ($arContact['ASSIGNED_BY_ID'] !== $UserAs) {
            //    p($arContact['ASSIGNED_BY_ID'], "change", $log);
                $paramsCont = [
                    "ID" => $arContact['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $UserAs
                    ]
                ];
                $contactUp = $auth->CScore->call("crm.contact.update", $paramsCont);
            }
                }
        $filterDeal =[
            "COMPANY_ID" => $idCompany,
            "CLOSED" => 'N'
        ];
        p($filterDeal , "filterDeal", $log);
        foreach ($auth->batch->getTraversableList('crm.deal.list', [], $filterDeal,  ["*"], 6000) as $arContact) {
                   // p($arContact, "deal", $log);
                    if ($arContact['ASSIGNED_BY_ID'] !== $UserAs) {
                        p($arContact['ASSIGNED_BY_ID'], "change", $log);
                        $paramsCont = [
                            "ID" => $arContact['ID'],
                            'fields' => [
                                'ASSIGNED_BY_ID' => $UserAs
                            ]
                        ];
                       $dealUp = $auth->CScore->call("crm.deal.update", $paramsCont);
                    }
                }
        $filterLead =[
            "COMPANY_ID" => $idCompany,
            "CLOSED" => 'N'
        ];
foreach ($auth->batch->getTraversableList('crm.lead.list', [], $filterLead,  ["*"], 6000) as $arContact) {
                    p($arContact, "lead", $log);
                    if ($arContact['ASSIGNED_BY_ID'] !== $UserAs) {
                        p($arContact['ASSIGNED_BY_ID'], "change", $log);
                        $paramsCont = [
                            "ID" => $arContact['ID'],
                            'fields' => [
                                'ASSIGNED_BY_ID' => $UserAs
                            ]
                        ];
                       $dealUp = $auth->CScore->call("crm.lead.update", $paramsCont);
                    }
                }

    }
}

?>