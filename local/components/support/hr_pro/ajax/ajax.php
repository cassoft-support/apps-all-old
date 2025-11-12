<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/ajaxUpdate.txt";
//pr($_POST, '');
//file_put_contents($file_log, print_r($date . "\n", true));
$arParams = json_decode($_POST['request'], true);
//$_REQUEST = json_decode($arParams['UserAut'], true);
$member= $arParams['member_id'];
$app = $arParams['app'];
$authParams = [];
$user = 1;
$auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
$hhClass = new \CSlibs\App\HR\hhClass($auth, $app, 1);
$hhManager=$hhClass->curlGet('me'); // информация о пользователе
$idcompanyHh = $hhManager['employer']['id'];
pr($idcompanyHh, '');
$url="employers/".$idcompanyHh."/vacancies/active";
pr($url, '');
//$hhVacancy=$hhClass->curlGet($url); // информация о пользователе
//pr($hhVacancy, '');

$url="employers/".$idcompanyHh."/vacancies/archived";
pr($url, '');
//$hhVacancyArchive=$hhClass->curlGet($url); // список архивных вакансий
//$res =$hhClass->curlPostAuthUp();
//pr($hhVacancyArchive, '');
//if (!empty($hhVacancyArchive['items'])){
//    foreach ($hhVacancyArchive['items'] as $item){
//
//    }
//}
$id= '48455985';

$url="vacancies/".$id;
//$url="vacancy/".$id."/visitors";
//$url="negotiations/response?vacancy_id=";

pr($url, '');
$hhVacancy=$hhClass->curlGet($url); // информация по вакансии
foreach ($hhVacancy['key_skills'] as $slill){
    $skills=$slill['name'];
}
foreach ($hhVacancy['working_hours'] as $el){
    $working_hours =$el['id'];
}
foreach ($hhVacancy['work_schedule_by_days'] as $el){
    $work_schedule_by_days =$el['id'];
}
foreach ($hhVacancy['professional_roles'] as $el){
    $professional_roles =$el['id'];
}
//pr($hhVacancy, '');
$params = [];
$params = [
    'ENTITY' => 'vacancy',
    'NAME' => $hhVacancy['name'],
    'ACTIVE'=>$active,
    'PROPERTY_VALUES'=> [
    'desc' => json_encode($hhVacancy['description']),//Описание
    'smart_id' => '',//smart ID
    'stage' => '',//Стадия вакансии
    'category' => '',//Категория
    'requirement' => '',//Требование к кандидату
    'conditions' => '',//Условия
    'company_info' => '',//О компании
    'company_id' => '',//Привязка к компании
    'assigned' => $user,//Ответственный
    'number_staff' => '',//Количество мест
    'code' => '',//Код
    'education' => '',//Образование
    'specialization' => json_encode($professional_roles),//Специализация professional_roles
    'experience' => $hhVacancy['experience']['id'],//Опыт работы
    'employment' => $hhVacancy['employment_form']['id'],//Тип занятости
    'internship' => $hhVacancy['internship'],//Стажировка
    'part_time_job' => $hhVacancy['accept_temporary'],//Совместительство
    'format_work' => $hhVacancy['work_format']['id'],//Формат работы
     'chart_work' => json_encode($work_schedule_by_days),//График работы
     'clock_work' => json_encode($working_hours),//Часы работы
     'address_work' => $hhVacancy['address']['id'],//Адрес работы
     'city_publish' => $hhVacancy['']['id'],//Город публикации
     'salary' => json_encode($hhVacancy['salary_range']),//Оплата
    // 'period_payments' => $hhVacancy['']['id'],//Периодичность выплат
     'skills' => json_encode($skills),//Навыки
     'preview' => '',//Краткое описание
     'fits' => '',//Вакансия подходит
     'type' => $hhVacancy['type']['id'],//тип вакансии
     'night_shifts' => $hhVacancy['night_shifts'],//тип вакансии
     'language_level' => json_encode($hhVacancy['languages']),//тип вакансии
     'disabled_pensioner' => $hhVacancy['accept_handicapped'],//тип вакансии
     'kids' => $hhVacancy['accept_kids'],//тип вакансии

//skills компетенции
//оплата
//условия оплаты
//графк работы
//регион публикации
        // languages языки
//working_hours рабочих часов в день
//            Требуемый опыт работы: не требуется
//Полная занятость, удаленная работа
//Возможно временное оформление: договор услуг, подряда, ГПХ, самозанятые, ИП
//Возможна подработка: сменами по 4-6 часов или по вечерам
]
    ];
//$vacancyAdd = $auth->CScore->call('entity.item.add',$params);

$params =[
        'ENTITY'=> 'vacancy',
        'ID'=> 5791
    ];
//$vacancyAdd = $auth->CScore->call('entity.item.delete', $params);
//[0] => 5791
pr($vacancyAdd);
$paramsReport =[

//    'counters' => Array
//(
//    'responses' => 70,
//            'views' => 657,
//            'invitations' => 32,
//            'unread_responses' => 0,
//            'resumes_in_progress' => 31,
//            'invitations_and_responses' => 71,


//        'status' => '',//Статус
//        'tl_color' => '',//Цвет статуса
//        'errors' => '',//Ошибка
        'date_end' => $hhVacancy['expires_at'],//Дата окончания
        'date_open' => $hhVacancy['published_at'],//Дата размещения
       // 'up' => '',//Тип поднятия
        'site' => 'hh',//Площадка
        'link' => $hhVacancy['alternate_url'],//Ссылка
     //   'discount' => '',//Скидка
      //  'discount_info' => '',//Инф-ция о скидке
     //   'deal_id' => '',//ID сделки
        'responses' => $hhVacancy['counters']['responses'],//Отклики
        'views' => $hhVacancy['counters']['views'],//Просмотры
        'invitations' => $hhVacancy['counters']['invitations'],//Приглашение
        'unread_responses' => $hhVacancy['counters']['unread_responses'],//Непрочитанных ответов
        'resumes_in_progress' => $hhVacancy['counters']['resumes_in_progress'],//Приглашения и отклики
        'invitations_and_responses' => $hhVacancy['counters']['invitations_and_responses'],//Приглашения и отклики
        'id' => '',//ID элемента
        'site_code' => $hhVacancy['id'],//Код на площадке

];
/*
 * СПРАВОЧНИКИ
$hlClass = new \CSlibs\B24\HL\HlService('guide_hr_pro');

$url="dictionaries";
pr($url, '');
$hhProp=$hhClass->curlGet($url); // справочники полей
//pr($hhProp);
foreach ($hhProp as $key =>$val){
    foreach ($val as $prop){
        $data=[
            'UF_CATEGORY_CODE' =>	$key,
'UF_HH_CODE' =>$prop['id'],
'UF_CS_CODE' =>$prop['id'],
'UF_NAME' =>$prop['name'],
        ];
     //   $el = $hlClass->elementAdd($data);
        pr($el, '');
        $guide[$key][$prop['id']]=$prop['name'];
    }
}

*/
//$jsonString = json_encode($guide, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/local/CSlibs/classes/app/hr_pro/guideHH.json', $jsonString);
//pr($guide, '');
foreach ( $auth->CScore->call('entity.item.property.get', ['ENTITY' => 'vacancy',]) as $prop){
   $property[$prop['PROPERTY']]="'',//".$prop['NAME'];

}
//pr($property, '');
foreach ( $auth->CScore->call('entity.item.property.get', ['ENTITY' => 'ads_report',]) as $prop){
   $propertyRep[$prop['PROPERTY']]="'',//".$prop['NAME'];

}
//pr($propertyRep, '');

$setupKeyData = $setupKey['data'];

$responseData =Array(
    'access_token' => 'USERHBMFLIU6FC4G6GVSMKEP4JL885798COBF6PFFAL96CUPCBU7M6UUMEAUEP8N',
'refresh_token' => 'USERU5UF3L24KT5HNPQJ3L30NTA0USOKK4A9O4ESL62A1H3ALVORSQ7GO3N06A08',
'expires_in' => 1209599
);
    $dateClose = strtotime(date('c'))+$responseData['expires_in'];
$setupKeyData[$user]=
    [
    'access_token'=>   $responseData['access_token'],
    'refresh_token' => $responseData['refresh_token'],
    'date_close' => $dateClose,
];

//   $setupKey[$_POST['user_id']] = $_POST['keyHH'];
pr($setupKey );
$paramsUp = [
    'ENTITY' => 'setup',
    'ID'=> $setupKey['ID'],
    'PROPERTY_VALUES'=>[
        'CS_HH_KEY' => json_encode($setupKeyData)
    ]
];
pr($paramsUp, );
//$resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
//$hKey = $hhClass->hhKey($app);
//pr($hKey, '');
//$hCode = $hhClass->hhCode(1);
pr($hKey, '');
//$hh=$hhClass->curlPost('me');
//https://api.hh.ru/skills
$hlClass = new \CSlibs\B24\HL\HlService('');
$name = 'HrGuideSkills';
$tableName= 'hr_guide_skills';
$langRu= 'Справочник ключевых навыков';
$fields = [
       '0'=>[
                'FIELD_NAME' => 'UF_CODE',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'CODE',
                'LABEL' => 'Код элемента'
           ],
    '1'=>[
                'FIELD_NAME' => 'UF_NAME',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'NAME',
                'LABEL' => 'Название'
           ],
    '2'=>[
                'FIELD_NAME' => 'UF_CODE_HH',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'CODE_HH',
                'LABEL' => 'Код элемента HH'
           ],
    '3'=>[
                'FIELD_NAME' => 'UF_CODE_RR',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'CODE_RR',
                'LABEL' => 'Код элемента Работа.ру'
           ],
    '4'=>[
                'FIELD_NAME' => 'UF_CODE_SJ',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'CODE_SJ',
                'LABEL' => 'Код элемента SJ'
           ],

    ];

//$hbAdd= $hlClass->hbAdd($name, $tableName, $langRu, $fields);
//pr($hbAdd, '');

/* ------------------ Наполнение справочников компетенциями
$hlClass = new \CSlibs\B24\HL\HlService($tableName);
$url = 'suggests/skill_set'; // черновики вакансий компании
$params = [
    'text' => $_POST['smart'],
];

// Формируем строку запроса из массива параметров
$queryString = http_build_query($params);

// Добавляем строку запроса к URL
$urlWithParams = $url . '?' . $queryString;
//$hhVacancy=$hhClass->curlPost($urlWithParams); // список закрытых вакансий

pr($hhVacancy);
foreach ($hhVacancy['items'] as $item){

    $getByFilterList = $hlClass->getByFilterList(['UF_CODE'=>$item['id']]);
    pr($getByFilterList, '');
    if(empty($getByFilterList)){
        $data=[
            'UF_NAME'=>$item['text'],
            'UF_CODE'=>$item['id'],
            'UF_CODE_HH'=>$item['id'],

        ];
//$el = $hlClass->elementAdd($data);
pr($el, '');
    }
}
*/
//----------------------------------


////d($arParams);
//$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
//$params=[
//    'name' => 'Ivanov GPT',
//        'code' => 'ivanov_gpt',
//        'category' => 'text',
//        'completions_url' => 'https://app.cassoft.ru/cassoftApp/test/ia.php',
//        'settings' => [
//    'code_alias' => 'ChatGPT',
//            'model_context_type' => 'token',
//            'model_context_limit' => 16*1024,
//        ],
//];

//$ai=$auth->CScore->call('ai.engine.register', $params);
//$ai=$auth->CScore->call('ai.engine.list',);
