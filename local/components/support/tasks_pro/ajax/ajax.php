<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/ajaxUpdate.txt";
pr($_POST, '');
//file_put_contents($file_log, print_r($date . "\n", true));
$arParams = json_decode($_POST['request'], true);
//$_REQUEST = json_decode($arParams['UserAut'], true);
$member= $arParams['member_id'];
$app = $arParams['app'];
$authParams = [];

$auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);
//$user =$auth->core->call('user.current')->getResponseData()->getResult();
//pr($user, '');
//foreach ($auth->CScore->call('entity.item.property.get',['ENTITY' =>'guide']) as $prop){
//    $propAll[$prop['PROPERTY']]=$prop['NAME'];
//};
$paramsAdd=[
  // 'PARAMS'=>
   'params'=>
        [
           // 'USER_TYPE_ID' => 'enumeration', // Тип поля "список"
            'user_type_id' => 'enumeration', // Тип поля "список"
           //  'USER_TYPE_ID' => 'string', // Тип поля "список"
            'field_name' => 'CS_LIST2',
         //   'FIELD_NAME' => 'CS_LIST2',
'XML_ID' => 'Cat',
'SORT' => '500',
'MULTIPLE' => 'N',
'MANDATORY' => 'N',
'SHOW_FILTER' => 'Y',
'SHOW_IN_LIST' => 'Y',
'EDIT_IN_LIST' => 'Y',
'IS_SEARCHABLE' => 'Y',
'EDIT_FORM_LABEL' => [
    'ru'=> 'категории'
],
'LABEL' => 'Категория',
'SETTINGS' => [
    'DISPLAY' => 'LIST',
//    'LIST_HEIGHT' => '1',
//            'CAPTION_NO_VALUE' => '',
//            'SHOW_NO_VALUE' => 'Y',
        ],
    'LIST' => [
        ['VALUE' => 'Option 1', 'SORT' => 100],
        ['VALUE' => 'ИТ направление', 'SORT' => 100],
    ]

       ]
];
$params=[
    "PARAMS"=> [
    "USER_TYPE_ID"=> "string",
            "FIELD_NAME"=> "NEW_TASKS_FIELD",
            "XML_ID"=> "MY_TASK_FIELD",
            "EDIT_FORM_LABEL"=> [
        "en"=> "New task field",
                "ru"=> "Новое поле задач"
            ],
            "LABEL"=> "New task field"
        ]
] ;

//$TaskPropAdd= $auth->core->call('task.item.userfield.add',$params)->getResponseData()->getResult();
$params=[
    'PARAMS'=>
        [
            'USER_TYPE_ID' => 'enumeration', // Тип поля "список"
            // 'USER_TYPE_ID' => 'enum', // Тип поля "список"
            'FIELD_NAME' => 'CS_TASKS_LIST3',
            'XML_ID' => 'CS_TASK_LIST3',
            'SHOW_FILTER' => 'Y',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'Y',
            'LIST' => [
                ['VALUE' => 'Option 1', 'SORT' => 100],
                ['VALUE' => 'Option 2', 'SORT' => 200],
                ['VALUE' => 'Option 3', 'SORT' => 300],
            ],
            'EDIT_FORM_LABEL' => [
                'en'=>'CAS task field3',
                'ru'=>'CS список3'
            ],

            'LABEL' => 'CAS список3'
        ]
] ;
pr($params, '');
$taskAdd=$auth->core->call('task.item.userfield.add', $params)->getResponseData()->getResult();
pr($taskAdd, '');
//$TaskPropAdd= $auth->CScore->call('task.item.userfield.getlist');
pr($TaskPropAdd, '');

foreach ($auth->CScore->call('task.item.userfield.getlist',) as $prop){
    pr($prop, '');
    $propAll[$prop['PROPERTY']]=$prop['NAME'];
};
pr($propAll, '');
$guide=[
    '0'=>[
        'name'=> 'Категории',
        'code' => 'categories',
    ]
];




$params=[
        'ENTITY' =>'guide',
        'NAME' =>'Связанное поле',
        'PROPERTY_VALUES' => [
'name' => '',//'Название
'code' => '',//'Код
'parent' => '',//'список значений
'accounting' => '',//'Код для бух.учета
'category' => '',//'Категория
'group' => '',//'Группа
'fields' => '',//'Связанное поле

        ]
    
];
$paramsGuideEl=[
        'ENTITY' =>'guide',
        'NAME' =>'Связанное поле',
        'PROPERTY_VALUES' => [
'name' => '',//'Название
'code' => '',//'Код
'parent_value' => '',//'список значений
'accounting' => '',//'Код для бух.учета

        ]

];
//$items = $auth->CScore->call('entity.item.add',$params);
pr($items, '');
////d($arParams);

//$tasks = $auth->CScore->call('tasks.task.get',['taskId'=>13090, 'select'=>['*']]);
//pr($tasks, '');
//$propAdd = $auth->CScore->call('entity.item.property.add',
  //  [
  //      'ENTITY' =>'guide',
//        'PROPERTY' => 'parent',
//        'NAME' => 'Связанный справочник',
//        'TYPE' => 'S'
  //  ]);
//$ai=$auth->CScore->call('ai.engine.register', $params);
//$ai=$auth->CScore->call('ai.engine.list',);
