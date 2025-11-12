<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);
        $hhClass = new \CSlibs\App\HR\hhClass($auth, $_POST['app'], $_POST['user_id']);
//$hhMe=$hhClass->curlPost('me'); //о пользователе
    //    pr($hhMe, '');
//$hh=$hhClass->curlPost('vacancies/blacklisted'); // список закрытых вакансий
   //     $url = 'vacancies?employer_id='.$hhMe['employer']['id']; вакансии компании
     //   $url = 'vacancies/drafts/50162787'; // черновики вакансий компании
      //  $url = 'skills?id='; // черновики вакансий компании

        $url = 'suggests/skill_set'; // черновики вакансий компании
        $params = [
            'text' => 'продаж',
//            'another_param' => 'another_value',
//            'more_params' => 'more_values'
        ];

// Формируем строку запроса из массива параметров
        $queryString = http_build_query($params);

// Добавляем строку запроса к URL
        $urlWithParams = $url . '?' . $queryString;
$hhVacancy=$hhClass->curlPost($urlWithParams); // список закрытых вакансий
pr($hhVacancy, '');

    }
}