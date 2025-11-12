<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$log = __DIR__."/logTemp.txt";
p($arParams, "start", $log);
//$arParams = Array
//(
//    "app" =>'hr_pro',
//    "result" =>Array
//(
//    "id" =>'notification278667topic4574397199',
//    "payload" =>Array
//(
//
//                    "resume_id" =>'eb9450fd0006320527001f4d2c4d3855337478',
//"vacancy_id" =>121678070,
//                    "negotiation_date" =>'2025-06-22T07:52:29+0300',
//                ),
//            "action_type" =>'NEW_NEGOTIATION_VACANCY',
//        ),
//
//    "member_id" =>'177649d0d08d8bdd5106f15a0fb6ab8d',
//"userId" =>200
//);
if(!empty($arParams["app"]) && !empty($arParams["member_id"])) {

$app = $arParams['app'];
$authParams = [];
$member = $arParams['member_id'];
$auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
$hhClass = new \CSlibs\App\HR\hhClass($auth, $app, $arParams['userId']);

$hhResume=$hhClass->curlGet('resumes/'.$arParams["result"]['payload']['resume_id']); // информация о пользователе

p($hhResume, "hhResume", $log);
if(!empty($hhResume['contact'])){
foreach ($hhResume['contact'] as $conVal){
if($conVal['type']['id'] === 'cell'){
$phoneAll[] = [ "VALUE"=> $conVal['value']['formatted'],  "VALUE_TYPE"=> "MOBILE" ];
}
if($conVal['type']['id'] === 'email'){
$emailAll[] =  [ "VALUE"=> $conVal['value'],  "VALUE_TYPE"=> "WORK" ];
}
}
if(!empty($phoneAll)){
    $phoneMain = preg_replace('/[^0-9]/', '', $phoneAll[0]["VALUE"]);
}
}




$paramsSearch=[
"ENTITY"=>'candidates',
"FILTER"=>[
'PROPERTY_phone' =>$phoneMain
]
];
$searchCandidate = $auth->CScore->call('entity.item.get', $paramsSearch)[0];
p($searchCandidate , "searchCandidate", $log);

$paramsReport=[
"ENTITY"=>'ads_report',
"FILTER"=>[
'ACTIVE'=>'Y',
'PROPERTY_site' =>'hh',
'PROPERTY_site_code' =>$arParams["result"]['payload']['vacancy_id']
]
];
$searchReport = $auth->CScore->call('entity.item.get', $paramsReport)[0];
p($searchReport , "searchReport", $log);
$paramsControl=[
"ENTITY"=>'ads_control',
"FILTER"=>[
'ACTIVE'=>'Y',
'ID'=> $searchReport['PROPERTY_VALUES']['id']
]
];
$searchControl = $auth->CScore->call('entity.item.get', $paramsControl)[0];
p($searchControl , "searchControl", $log);

$resume['hh'][$hhResume['id']]=$hhResume;
$responses['hh'][$arParams["result"]['id']] =$arParams["result"];

$params = [
"ENTITY"=>'candidates',
'DATA_CREATE'=> $arParams["result"]['payload']['negotiation_date'],
'NAME' => $hhResume['title']." ".$hhResume['last_name']." ".$hhResume['name']." ".$hhResume['middle_name'],
"PROPERTY_VALUES"=> [
"name" =>$hhResume['first_name'],//Имя",
"last_name" =>$hhResume['last_name'],//Фамилия",
"second_name" =>$hhResume['middle_name'],//Отчество",
//        "contact_id" =>'',//Контакт в CRM",
//        "password" =>'',//Пароль",
//        "code_auth" =>'',//Код авторизации",
"phone" =>$phoneMain,//Телефон",
"email" =>$emailAll[0]["VALUE"],//Почта",
//        "phone_check" =>'',//Телефон подтвержден",
//        "email_check" =>'',//Почта подтверждена",
//        "code_email_auth" =>'',//Код подтверждения почты",
//        "date_auth" =>'',//Дата послезней авторизации",
"birthdate" =>$hhResume['birth_date'],//Дата рождения",
"photo" =>$hhResume['photo'],//Аватар",
"personal_check" =>'',//Согласие на использование персональных данных",
// "inn" =>'',//ИНН",
"resume" =>json_encode($hhResume),//Резюме",
"cover_letter" =>'',//Cопроводительное письмо",
"assigned_by_id" =>$searchControl['PROPERTY_VALUES']['manager_id'],//Ответственный",
//   "stage" =>'',//Стадия Кандидата",
//        "smart_id" =>'',//smart ID",
//        "lead_id" =>'',//Лид",
//        "company_id" =>18,//Лид",
"vacancy" =>'',//Вакансия",
"deal_id" =>'',//Сделка"
"responses" => json_encode($responses)
]
];
p($params , "params", $log);
//pr($params, '');
if(empty($searchCandidate)){
 $itemAdd = $auth->CScore->call('entity.item.add', $params);
p($itemAdd , "itemAdd", $log);
 if($itemAdd[0]>0){
     $searchDuplicate = $auth->CScore->call("crm.duplicate.findbycomm",
         [
             'entity_type'=> "LEAD",
             'type'=> "PHONE",
             'values'=> [ $phoneMain ],
         ]);
     if(!empty($searchDuplicate['LEAD'])){
         foreach ($searchDuplicate['LEAD'] as $lead){
             $searchLead = $auth->CScore->call('crm.lead.get', ['ID' => $lead ]);

             if($searchLead['STATUS_SEMANTIC_ID'] === 'P' && !$searchLead['UF_CRM_CS_VACANCY'] &&
                 !$searchLead['UF_CRM_CS_CANDIDATE']){
                 p($searchLead , "$searchLead", $log);
                 $leadId = $searchLead['ID'];
             }
         }
     }
     $paramsLead['fields'] = [
         "TITLE" => "Отклик на вакансию " . $searchReport['NAME'] . " (" . $hhResume['last_name'] . " " . $hhResume['first_name'] . " " . $hhResume['middle_name'] . ")",
         "NAME" => $hhResume['first_name'],
         "LAST_NAME" => $hhResume['last_name'],
         "SECOND_NAME" => $hhResume['middle_name'],
         "STATUS_ID" => "NEW",
         "ASSIGNED_BY_ID" => $searchControl['PROPERTY_VALUES']['manager_id'],
         "SOURCE_ID" => "HH",
         "PHONE" => $phoneAll,
         "EMAIL" => $emailAll,
         "UF_CRM_CS_VACANCY" => $searchReport['PROPERTY_VALUES']['id'],
         "UF_CRM_CS_VACANCY_NAME" => $searchReport['NAME'],
         "UF_CRM_CS_CANDIDATE" => $itemAdd[0],//,
         "UF_CRM_CS_CANDIDATE_NAME" => $hhResume['last_name'] . " " . $hhResume['name'] . " " . $hhResume['middle_name'],
     ];
     p($paramsLead , "paramsLead", $log);
     if(empty($leadId)) {
         $leadAdd = $auth->CScore->call('crm.lead.add', $paramsLead);
         $leadId = $leadAdd[0];
         p($leadAdd , "leadAdd", $log);
     }else{
         $paramsLead['ID'] =$leadId;
         $leadUp = $auth->CScore->call('crm.lead.update', $paramsLead);
         p($leadUp , "leadUp", $log);
     }
  if(!empty($leadId)){
$paramsUp = [
"ENTITY"=>'candidates',
'ID'=>130,
"PROPERTY_VALUES"=> [
"lead_id" =>$leadId,// Лид",
]];
 $itemUp = $auth->CScore->call('entity.item.update', $paramsUp);
 p($itemUp , "itemUp", $log);
 }
 }
}
}

?>