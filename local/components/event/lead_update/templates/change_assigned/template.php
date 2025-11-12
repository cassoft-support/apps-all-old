<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/event/company_update/ajax/changeUser.php");

$log = __DIR__ . "/logUpdate.txt";
$logT = __DIR__ . "/logUpdateTime.txt";
$log2 = __DIR__ . "/logUpdate2.txt";
////p("start", "-company_update", $log);
////p($_REQUEST, "REQUEST", $log);
//p($arParams['auth']['member_id'], "start", $log);
//p($arParams['app'], "arResult", $log);

//$changeUserCompany= changeUserCompany($arParams);
$id = $arParams['data']['FIELDS']['ID'];
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);

$resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0]['PROPERTY_VALUES'];
$commentAdd = $resSetup['CS_COMMENT_ADD'];
p($resSetup , 'start', $log);
p($resSetup , date('d.m.Y H:i:s'), $log);
$setup = json_decode($resSetup['CS_LEAD'], true);
p($setup, "resCompanySetup", $log);
//$resElement = $auth->CScore->call("crm.contact.get", ['ID' => $id, ]);
$resElement = $auth->CScore->call( 'crm.item.get', [ 'entityTypeId' => 1, 'id' => $id ])['item'];
$timeCreate = strtotime($resElement['updatedTime']);
$date = strtotime(date('c'));
p($resElement['id'] , "start", $logT);
if($arParams['auth']['domain'] === 'dagaz-24.bitrix24.ru'){
    p($resElement , data('c'), $log2);  
}
p($date , "date", $logT);
p($timeCreate , "timeCreate", $logT);
$dateMin = $date - $timeCreate;
p($dateMin, "dateMin", $logT);
if($dateMin < 10)
{
    p($dateMin, "dateMin+", $logT);
    sleep(2); // Пауза в 1 секунду
}
//p($resElement , "element", $log);
$user = $auth->CScore->call('user.get',['filter'=>['ID'=>$arParams['auth']['user_id']]])[0];
//p($user , "user", $log);
$assigned = $resElement['assignedById'];
$changeAssigned = new \CSlibs\App\Assigned\changeAssigned($auth, $assigned, $arParams['auth']['domain']);
//p($user , "user", $log);
$name = "[B]лида[/B] ID".$resElement['id'].": [URL=/crm/lead/details/".$resElement['id']."/]".$resElement['title']."[/URL] пользователем ".$user['NAME']." ".$user['LAST_NAME'];
p($name , "name", $log);
//$filterStart["dealId"] = $id;
//$filterStart2["id"] = $resElement['companyIds'];
//////$filterStart2=[];
////
//p($filterStart2 , "filterStart", $log);
//$t=$auth->CScore->call('crm.item.list', ['entityTypeId' => 31, 'filter'=>[]]);
//p($t, "t", $log);

//if (!empty($setup['deal'])) {
//      $filterDeal = $filterStart;
//      $filterDeal["closed"] = 'N';
//    $deal = $changeAssigned->changeAssigned($filterDeal, 'deal', 2, $user['ID'], $name);
//    p($deal, "deal", $log);
//}
//$status = $auth->CScore->call('crm.status.list',['filter' => ['ENTITY_ID' => 'STATUS', 'SEMANTICS'=> 'F']]);
foreach ($auth->CScore->call('crm.status.list') as $status){
   // p($status , "status", $log);
//    if ($status['ENTITY_ID'] === 'STATUS' && $status['SEMANTICS'] !== 'F' && $status['SEMANTICS'] !== 'S'){
//        $leadStatus[]= $status['STATUS_ID'];
//    }
    if ($status['ENTITY_ID'] === 'SMART_INVOICE_STAGE_2'&& $status['SEMANTICS'] !== 'F' && $status['SEMANTICS'] !== 'S' ){
        $statusInvoice[]= $status['STATUS_ID'];
      //  p($status , "status", $log);
        p($statusInvoice , "statusInvoice", $log);
    }
};
//p($filter , "start", $log);
//$t=$auth->CScore->batch('crm.item.list', ['entityTypeId' => 31, 'filter'=>$filterStart]);
//p($t, "t", $log);
//if (!empty($setup['lead'])) {
//    $filterLead = $filterStart;
//    $filterLead["stageId"] = $leadStatus;
//    $lead = $changeAssigned->changeAssigned($filterLead, 'lead', 1, $user['ID'], $name);
//    p($lead, "lead", $log);
//}
//if (!empty($setup['invoice'])) {
//    $filterInvoice['parentId2'] = $id;
//    $filterInvoice["stageId"] = $statusInvoice;
//    $invoice = $changeAssigned->changeAssigned($filterInvoice, 'invoice', 31, $user['ID'], $name);
//    p($invoice, "invoice", $log);
//}
if (!empty($setup['quote'])  && $resElement['quoteId']>0) {
    $filterQuote['id'] = $resElement['quoteId'];
    $filterQuote["closed"] = 'N';
    $quote = $changeAssigned->changeAssigned($filterQuote, 'quote', 7, $user['ID'], $name, $commentAdd);
    p($quote, "quote", $log);
}
if (!empty($setup['contact']) && !empty($resElement['contactId'])) {
    $filterContact["id"] = $resElement['contactId'];
    $contact = $changeAssigned->changeAssigned($filterContact, 'contact', 3, $user['ID'], $name, $commentAdd);
    p($contact, "contact", $log);
}
if (!empty($setup['company']) && !empty($resElement['companyId'])) {
    $filterCompany["id"] = $resElement['companyId'];
    $company = $changeAssigned->changeAssigned($filterCompany, 'company', 4, $user['ID'], $name, $commentAdd);
    p($company, "company", $log);
}
?>

