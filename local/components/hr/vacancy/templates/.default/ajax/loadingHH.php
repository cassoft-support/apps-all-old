<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logLoadingHH.txt";
p($_POST, "start", $log);
if(!empty($_POST['app']) && !empty($_POST['auth']['member_id'])) {
    $app = $_POST['app'];
    $authParams = [];
    $member = $_POST['auth']['member_id'];
    $auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
    $hhClass = new \CSlibs\App\HR\hhClass($auth, $app, $_POST['user_id']);
    $hhManager=$hhClass->curlGet('me'); // информация о пользователе
    $idcompanyHh = $hhManager['employer']['id'];
  //  pr($idcompanyHh, '');
    $url="employers/".$idcompanyHh."/vacancies/active";
  //  pr($url, '');
$hhVacancyActive=$hhClass->curlGet($url); // информация о пользователе
//pr($hhVacancy, '');
if(!empty($hhVacancyActive['items'])){
  $vacancyAdd=  vacancyAddHh($auth,  $hhClass, $hhVacancyActive['items'], 'Y');
}
    $url="employers/".$idcompanyHh."/vacancies/archived";
   // pr($url, '');
$hhVacancyArchive=$hhClass->curlGet($url); // список архивных вакансий
    if(!empty($hhVacancyArchive['items'])){
        $vacancyAdd=  vacancyAddHh($auth,  $hhClass, $hhVacancyArchive['items'], 'N');
    }


}

function vacancyAddHh($auth, $hhClass, $vacancyAll, $active){
  //  pr($vacancyAll, $active);
   // console($vacancyAll, $active);
    $user=1;
    $i=0;
    foreach ($vacancyAll as $vacancy){

   $paramsSearch= [
        'ENTITY'=> 'ads_report',
        'FILTER' => [
        'PROPERTY_site_code'=> $vacancy['id'],
        ]
    ];
$item = $auth->CScore->call('entity.item.get',$paramsSearch)[0];
//pr($item, 'item');
if(empty($item)) {
    $url="vacancies/".$vacancy['id'];
   // pr($url, '');
    $hhVacancy=$hhClass->curlGet($url); // информация по вакансии
    foreach ($hhVacancy['key_skills'] as $slill) {
        $skills = $slill['name'];
    }
    foreach ($hhVacancy['working_hours'] as $el) {
        $working_hours = $el['id'];
    }
    foreach ($hhVacancy['work_schedule_by_days'] as $el) {
        $work_schedule_by_days = $el['id'];
    }
    foreach ($hhVacancy['professional_roles'] as $el) {
        $professional_roles = $el['id'];
    }
//pr($hhVacancy, '');
    $params = [];
    $params = [
        'ENTITY' => 'vacancy',
        'NAME' => $hhVacancy['name'],
        'ACTIVE' => $active,
        'PROPERTY_VALUES' => [
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
        ]
    ];

   // pr($params);
    $vacancyAdd = $auth->CScore->call('entity.item.add',$params);
if($vacancyAdd[0]>0) {
    $paramsReport = [
        'ENTITY' => 'ads_report',
        'NAME' => $hhVacancy['name'],
        'ACTIVE' => $active,
        'PROPERTY_VALUES' => [
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
            'id' => $vacancyAdd[0],//ID элемента
            'site_code' => $hhVacancy['id'],//Код на площадке
        ]
    ];
    $vacancyReportAdd = $auth->CScore->call('entity.item.add',$paramsReport);
   // pr($paramsReport, '');
    $i++;
}
}
    }
    pr($i, 'создано');
    return $i;
}