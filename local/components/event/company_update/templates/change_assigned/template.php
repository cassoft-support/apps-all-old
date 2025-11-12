<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/event/company_update/ajax/changeUser.php");

$log = __DIR__ . "/logCompanyUpdate.txt";
$logT = __DIR__ . "/logTimeUpdate.txt";
////p("start", "-company_update", $log);
////p($_REQUEST, "REQUEST", $log);
//p($arParams['auth']['member_id'], "start", $log);
//p($arParams['app'], "arResult", $log);

//$changeUserCompany= changeUserCompany($arParams);
$idCompany = $arParams['data']['FIELDS']['ID'];
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);

$resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0]['PROPERTY_VALUES'];
p($resSetup , 'start', $log);
$resSetup = json_decode($resSetup['CS_COMPANY'], true);
$commentAdd = $resSetup['CS_COMMENT_ADD'];
p($resSetup, "start", $log);
$resElement = $auth->CScore->call("crm.company.get", ['ID' => $idCompany, ]);
p($resElement , "resElement", $log);
$userId = $arParams['auth']['user_id'];
p($resElement , "company", $log);
$timeCreate = strtotime($resElement['DATE_MODIFY']);
$date = strtotime(date('c'));
//p($resElement['id'] , "id", $logT);
//p($date , "date", $logT);
//p($timeCreate , "timeCreate", $logT);
$dateMin = $date - $timeCreate;
//p($dateMin, "dateMin", $logT);
if($dateMin < 10)
{
//    p($dateMin, "dateMin+", $logT);
    sleep(2); // Пауза в 1 секунду
}
$user = $auth->CScore->call('user.get',['filter'=>['ID'=>$arParams['auth']['user_id']]])[0];
//p($user , "user", $log);
$assigned = $resElement['ASSIGNED_BY_ID'];
$changeAssigned = new \CSlibs\App\Assigned\changeAssigned($auth, $assigned);
//p($user , "user", $log);
$name = "[B]компании[/B] ID".$resElement['ID'].": [URL=/crm/company/details/".$resElement['ID']."/]".$resElement['TITLE']."[/URL] пользователем ".$user['NAME']." ".$user['LAST_NAME'];

p($name , "user", $log);
$filterStart["companyId"] = $idCompany;

if (!empty($resSetup['deal'])) {
      $filterDeal = $filterStart;
      $filterDeal["closed"] = 'N';
    $deal = $changeAssigned->changeAssigned($filterDeal, 'deal', 2, $user['ID'], $name, $commentAdd);
    p($deal, "deal", $log);
}
//$status = $auth->CScore->call('crm.status.list',['filter' => ['ENTITY_ID' => 'STATUS', 'SEMANTICS'=> 'F']]);
foreach ($auth->CScore->call('crm.status.list') as $status){
   // p($status , "status", $log);
    if ($status['ENTITY_ID'] === 'STATUS' && $status['SEMANTICS'] !== 'F' && $status['SEMANTICS'] !== 'S'){
        $leadStatus[]= $status['STATUS_ID'];
    }
    if ($status['ENTITY_ID'] === 'SMART_INVOICE_STAGE_2'&& $status['SEMANTICS'] !== 'F' && $status['SEMANTICS'] !== 'S' ){
        $statusInvoice[]= $status['STATUS_ID'];
        p($status , "status", $log);
        p($statusInvoice , "status", $log);
    }

};
//p($filter , "start", $log);
$t=$auth->CScore->batch('crm.item.list', ['entityTypeId' => 31, 'filter'=>$filterStart]);
p($t, "t", $log);
if (!empty($resSetup['lead'])) {
    $filterLead = $filterStart;
    $filterLead["stageId"] = $leadStatus;
    $lead = $changeAssigned->changeAssigned($filterLead, 'lead', 1, $user['ID'], $name, $commentAdd);
    p($lead, "lead", $log);
}
if (!empty($resSetup['invoice'])) {
    $filterInvoice = $filterStart;
    $filterInvoice["stageId"] = $statusInvoice;
    $lead = $changeAssigned->changeAssigned($filterInvoice, 'invoice', 31, $user['ID'], $name, $commentAdd);
    p($lead, "lead", $log);
}
if (!empty($resSetup['quote'])) {
    $filterQuote = $filterStart;
    $filterQuote["closed"] = 'N';;
    $lead = $changeAssigned->changeAssigned($filterQuote, 'quote', 7, $user['ID'], $name, $commentAdd);
    p($lead, "quote", $log);
}
if (!empty($resSetup['contact'])) {
    // $filter["COMPANY_ID"] = $idCompany;
    $contact = $changeAssigned->changeAssigned($filterStart, 'contact', 3, $user['ID'], $name, $commentAdd);
    p($contact, "contact", $log);
}
//SMART_INVOICE_STAGE
//QUOTE_STATUS crm.status.entity.items

?>

