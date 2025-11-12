<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$log = __DIR__."/logTemp.txt";
//p($arParams, "start", $log);
//if(!empty($arParams["app"]) && !empty($arParams["member_id"])) {

$app = 'hr_pro';
$authParams = [];
$member = '177649d0d08d8bdd5106f15a0fb6ab8d';
$auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
$hhClass = new \CSlibs\App\HR\hhClass($auth, $app, 200);
//$hhResume=$hhClass->curlGet('me'); // информация о пользователе
//$hhResume=$hhClass->curlGet('resumes/995a623100073af398001f4d2c4d573854564b/negotiations_history'); // информация о пользователе
//$hhResume=$hhClass->curlGet('resumes/995a623100073af398001f4d2c4d573854564b'); // информация о пользователе
//$hhRes=$hhClass->curlGet('resumes/995a623100073af398001f4d2c4d573854564b/download/%D0%A8%D0%B2%D0%B0%D0%BB%D1%91%D0%B2%D0%B0%20%D0%90%D0%BD%D0%B0%D1%81%D1%82%D0%B0%D1%81%D0%B8%D1%8F%20%D0%9C%D0%B8%D1%85%D0%B0%D0%B9%D0%BB%D0%BE%D0%B2%D0%BD%D0%B0%20.pdf'); // информация о пользователе
//pr($hhRes, '');
$hhResume=$hhClass->curlGet('resumes/995a623100073af398001f4d2c4d573854564b'); // информация о пользователе
//https://api.hh.ru/resumes/{resume_id}/negotiations_history
//$hhResume=$hhClass->curlGet('vacancies/120974006'); // информация о пользователе
//pr($hhResume, '');
//    if($arParams["result"]['action_type'] === 'NEW_NEGOTIATION_VACANCY'){ //новый отклик на вакансию
//        $hhResume=$hhClass->curlGet('resumes/6dd60075000ee9e2db001f4d2c35744a703670'); // информация о пользователе
//        pr($hhResume, '');
//        //https://api.hh.ru/resumes/{resume_id} получить резюме
//    }

$arParams["result"] =Array
(
    'id' => 'notification278667topic4573291499',
    'payload' => Array
    (
        'topic_id' => 4573291499,
        'resume_id' => '995a623100073af398001f4d2c4d573854564b',
        'vacancy_id' => 121219805,
        'employer_id' => 2051372,
        'negotiation_date' => '2025-06-21T07:59:01+0300',
    ),

    'subscription_id' => 278667,
    'action_type' => 'NEW_NEGOTIATION_VACANCY',
    'user_id' => 156209206
);


if(!empty($hhResume['contact'])){
    foreach ($hhResume['contact'] as $conVal){
        if($conVal['type']['id'] === 'cell'){
            $phoneAll[] = [ "VALUE"=> $conVal['value']['formatted'],  "VALUE_TYPE"=> "MOBILE" ];
        }
        if($conVal['type']['id'] === 'email'){
            $emailAll[] =  [ "VALUE"=> $conVal['value'],  "VALUE_TYPE"=> "WORK" ];
        }
    }
}
//pr($phoneAll, '');

$phoneMain = preg_replace('/[^0-9]/', '', $phoneAll[0]["VALUE"]);

$paramsSearch=[
    "ENTITY"=>'candidates',
    "FILTER"=>[
        'PROPERTY_phone' =>$phoneMain
    ]
];
$searchCandidate = $auth->CScore->call('entity.item.get', $paramsSearch)[0];
//pr($searchCandidate, '');

$paramsReport=[
    "ENTITY"=>'ads_report',
    "FILTER"=>[
        'ACTIVE'=>'Y',
        'PROPERTY_site' =>'hh',
        'PROPERTY_site_code' =>$arParams["result"]['payload']['vacancy_id']
    ]
];
$searchReport = $auth->CScore->call('entity.item.get', $paramsReport)[0];
pr($searchReport, '');
$paramsControl=[
    "ENTITY"=>'ads_control',
    "FILTER"=>[
        'ACTIVE'=>'Y',
        'ID'=> $searchReport['PROPERTY_VALUES']['id']
        // 'PROPERTY_site' =>'hh',
        // 'PROPERTY_site_code' =>$arParams["result"]['payload']['vacancy_id']
    ]
];
$searchControl = $auth->CScore->call('entity.item.get', $paramsControl)[0];
pr($searchControl, '');

//$statusAdd = $auth->CScore->call(
//    "crm.status.add",
//    [
//        'fields'=>
//        [
//            "ENTITY_ID"=> "SOURCE",
//            "STATUS_ID"=> "HH",
//            "NAME"=> "HH",
//            "SORT"=> 70
//        ]
//    ]);
//
//$status = $auth->CScore->call('crm.status.list', ['filter' =>[
//    "ENTITY_ID"=> "SOURCE", "STATUS_ID"=> "HH",
//]] );
//pr($status, '');

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
        "email" =>$emailAll[0],//Почта",
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
        "assigned_by_id" =>$searchControl['PROPERTY_VALUES']['manager_id'],,//Ответственный",
        //   "stage" =>'',//Стадия Кандидата",
//        "smart_id" =>'',//smart ID",
//        "lead_id" =>'',//Лид",
//        "company_id" =>18,//Лид",
        "vacancy" =>'',//Вакансия",
        "deal_id" =>'',//Сделка"
        "responses" => json_encode($responses)
    ]
];
pr($params, '');
if(empty($searchCandidate)){
    // $itemAdd = $auth->CScore->call('entity.item.add', $params);
  //  pr($itemAdd[0], '');
    //  if($itemAdd[0]!==0){
    $paramsLead =[
//"TITLE"=> "Отклик на вакансию ".$searchReport['NAME']." (".$hhResume['last_name']." ".$hhResume['name']." ".$hhResume['middle_name'].")",
//"NAME"=>  $hhResume['name'],
//"LAST_NAME"=>  $hhResume['last_name'],
//"SECOND_NAME"=>  $hhResume['middle_name'],
//"STATUS_ID"=> "NEW",
//"ASSIGNED_BY_ID"=> $searchControl['PROPERTY_VALUES']['manager_id'],
//                  "SOURCE_ID"=>"HH",
//                   "PHONE"=> $phoneAll,
//                    "EMAIL"=> $emailAll,
//"UF_CRM_CS_VACANCY" =>$searchReport['PROPERTY_VALUES']['id'],
//"UF_CRM_CS_VACANCY_NAME" => $searchReport['NAME'],
//"UF_CRM_CS_CANDIDATE" =>$itemAdd[0],
//"UF_CRM_CS_CANDIDATE_NAME" =>$hhResume['last_name']." ".$hhResume['name']." ".$hhResume['middle_name'],
    ];
    pr($paramsLead, '');
    // }
}
//}

?>

ytyty