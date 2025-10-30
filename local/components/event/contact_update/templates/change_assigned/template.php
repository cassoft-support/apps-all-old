<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/event/company_update/ajax/changeUser.php");

$log = __DIR__ . "/logContactUpdate.txt";
$logT = __DIR__ . "/logtimeUpdate.txt";
////p("start", "-company_update", $log);
////p($_REQUEST, "REQUEST", $log);
//p($arParams['auth']['member_id'], "start", $log);
//p($arParams['app'], "arResult", $log);

//$changeUserCompany= changeUserCompany($arParams);
$id = $arParams['data']['FIELDS']['ID'];
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);

$resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0]['PROPERTY_VALUES'];
$commentAdd = $resSetup['CS_COMMENT_ADD'];
p($resSetup , "start", $log);
$setup = json_decode($resSetup['CS_CONTACT'], true);
p($setup, "resCompanySetup", $log);
//$resElement = $auth->CScore->call("crm.contact.get", ['ID' => $id, ]);
$resElement = $auth->CScore->call( 'crm.item.get', [ 'entityTypeId' => 3, 'id' => $id ])['item'];
$timeCreate = strtotime($resElement['updatedTime']);
$date = strtotime(date('c'));
p($resElement['id'] , "start", $logT);
p($date , "date", $logT);
p($timeCreate , "timeCreate", $logT);
$dateMin = $date - $timeCreate;
p($dateMin, "dateMin", $logT);
if($dateMin < 10)
{
    p($dateMin, "dateMin+", $logT);
    sleep(2); // Пауза в 1 секунду
}
p($resElement , "resElement", $log);
$user = $auth->CScore->call('user.get',['filter'=>['ID'=>$arParams['auth']['user_id']]])[0];
//p($user , "user", $log);
$assigned = $resElement['assignedById'];
$changeAssigned = new \CSlibs\App\Assigned\changeAssigned($auth, $assigned);
//p($user , "user", $log);
$name = "[B]контакта[/B] ID".$resElement['id'].": [URL=/crm/contact/details/".$resElement['id']."/] ".$resElement['name']." ".$resElement['lastName']."[/URL], пользователем ".$user['NAME']." ".$user['LAST_NAME'];
p($name , "name", $log);
$filterStart["contactId"] = $id;
$filterStart2["id"] = $resElement['companyIds'];
////$filterStart2=[];
//
p($filterStart2 , "filterStart", $log);
$t=$auth->CScore->call('crm.item.list', ['entityTypeId' => 4, 'filter'=>$filterStart2]);
p($t, "t", $log);
if (!empty($setup['company']) && !empty($resElement['companyIds'])) {
    p($setup['company'] , "company", $log);
   $filterCompany["id"] = $resElement['companyIds'];
    $company = $changeAssigned->changeAssigned($filterCompany, 'company', 4, $user['ID'], $name, $commentAdd);
    p($company, "company", $log);
}
if (!empty($setup['deal'])) {
      $filterDeal["contactIds"] = $id;
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
//$t=$auth->CScore->batch('crm.item.list', ['entityTypeId' => 31, 'filter'=>$filterStart]);
//p($t, "t", $log);
if (!empty($setup['lead'])) {
    $filterLead = $filterStart;
    $filterLead["stageId"] = $leadStatus;
    $lead = $changeAssigned->changeAssigned($filterLead, 'lead', 1, $user['ID'], $name, $commentAdd);
    p($lead, "lead", $log);
}
if (!empty($setup['invoice'])) {
    $filterInvoice = $filterStart;
    $filterInvoice["stageId"] = $statusInvoice;
    $lead = $changeAssigned->changeAssigned($filterInvoice, 'invoice', 31, $user['ID'], $name, $commentAdd);
    p($lead, "lead", $log);
}
if (!empty($setup['quote'])) {
    $filterQuote = $filterStart;
    $filterQuote["closed"] = 'N';;
    $lead = $changeAssigned->changeAssigned($filterQuote, 'quote', 7, $user['ID'], $name, $commentAdd);
    p($lead, "quote", $log);
}


?>

