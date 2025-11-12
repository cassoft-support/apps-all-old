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

$auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
$hhClass = new \CSlibs\App\HR\hhClass($auth, $app, 1);
$res =$hhClass->curlPostAuth('authorization_code');
pr($res, '');
//$hKey = $hhClass->hhKey($app);
//pr($hKey, '');
$hCode = $hhClass->hhCode(1);
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
$hlClass = new \CSlibs\B24\HL\HlService($tableName);
$url = 'suggests/skill_set'; // черновики вакансий компании
$params = [
    'text' => $_POST['smart'],
];

// Формируем строку запроса из массива параметров
$queryString = http_build_query($params);

// Добавляем строку запроса к URL
$urlWithParams = $url . '?' . $queryString;
$hhVacancy=$hhClass->curlPost($urlWithParams); // список закрытых вакансий
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
$el = $hlClass->elementAdd($data);
pr($el, '');
    }
}



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
