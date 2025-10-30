<?php
define(NOT_CHECK_PERMISSIONS, true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/logTemp.txt";
p($arParams, date('c'), $log);
pr($arParams, '');
$authParams = [];
if(!empty($arParams['app']) && !empty($arParams['member_id']) && !empty($arParams['app_code'])){
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], $authParams, $arParams['member_id'], $arParams['app_code']);
$params=[
    'ENTITY'=> 'events',
        'NAME'=> $arParams['result']['id'],
        'PROPERTY_VALUES'=>$arParams['result']
];
//$itemAdd = $auth->CScore->call( 'entity.item.add', $params);
$itemGet = $auth->CScore->call( 'entity.item.get', ['ENTITY'=> 'events']);
pr($itemGet, '');
p($itemAdd, "itemAdd", $log);
if(!empty($arParams['result'])){

if($arParams['member_id'] === 'b176cc28ddd836fa2c7d93e81fff15df'){
    
if($arParams['result']['EventType'] === 'ApplicationSubmitted'){
    $comment="";

    if(!empty($arParams['result']['CarMake'])){
        $comment = $comment." "."Марка - ".$arParams['result']['CarMake'].", ";
    }

    if(!empty($arParams['result']['CarModel'])){
        $comment = $comment." "."Модель - ".$arParams['result']['CarModel'].", ";
    }
    if(!empty($arParams['result']['YearRange'])){
        $comment = $comment." "."Год выпуска - ".$arParams['result']['YearRange'].", ";
    }
    if(!empty($arParams['result']['FuelType'])){
        $comment = $comment." "."Тип двигателя - ".$arParams['result']['FuelType'].", ";
    }
    if(!empty($arParams['result']['BodyType'])){
        $comment = $comment." "."Кузов - ".$arParams['result']['BodyType'].", ";
    }
    if(!empty($arParams['result']['Transmission'])){
        $comment = $comment." "."Тип трансмиссии - ".$arParams['result']['Transmission'].", ";
    }
    if(!empty($arParams['result']['Equipment'])){
        $comment = $comment." "."Комплектация - ".$arParams['result']['Equipment'].", ";
    }
    if(!empty($arParams['result']['Budget'])){
        $comment = $comment." "."Бюджет на покупку - ".$arParams['result']['Budget'].", ";
    }

    $applicationType =[
        'Credit' =>'Заявка на кредит',
        'Call' =>'Заявка на звонок'
    ];
    
    // --- Обработка имени, фамилии, отчества и телефона ---
    $firstName = ($arParams['result']['FirstName']=== 'None' || $arParams['result']['FirstName'] === null)? "": $arParams['result']['FirstName'];
    $lastName = ($arParams['result']['LastName'] === 'None' || $arParams['result']['LastName'] === null)? "": $arParams['result']['LastName'];
    $Username = ($arParams['result']['Username']=== 'None' || $arParams['result']['Username'] === null)? "": $arParams['result']['Username'];
    $middleName = isset($arParams['result']['MiddleName']) ? $arParams['result']['MiddleName'] : null;
    $phone = $arParams['result']['Phone'];
    if ($phone === 'None' || $phone === null) {
        $phone = '';
    }
    // Для создания лида: если имя None, подставляем 'Имя не указано'
    $leadFirstName = ($firstName === 'None' || $firstName === null) ? 'Имя не указано' : $firstName;
    // Для фамилии и отчества: если None, пропускаем (делаем пустым)
    $leadLastName = ($lastName === 'None' || $lastName === null) ? '' : $lastName;
    $leadMiddleName = ($middleName === 'None' || $middleName === null) ? '' : $middleName;
$fullName ='';
    $fullName = $firstName." ".$lastName." ".$Username;
    $paramsLead= [
        'fields' => [
        'TITLE' => "Бот расчета ".$fullName." ".$applicationType[$arParams['result']['ApplicationType']],
        'NAME' => $fullName,
        'SECOND_NAME' => $leadMiddleName,
        'LAST_NAME' => $leadLastName,
        'STATUS_ID' => 'NEW',
        'ASSIGNED_BY_ID' => 15,
            "IM"=> [
                [ "VALUE"=>$arParams['result']['Username'],
                    "VALUE_TYPE"=> "TELEGRAM"
                ]  ,
                [
                "VALUE"=>$arParams['result']['TelegramID'],
                "VALUE_TYPE"=> "TELEGRAM" ]
            ] ,
             "COMMENTS"=> $comment,
            "SOURCE_ID"=>'UC_GPJU6S',
            'PHONE' => $phone,
    ],
        'params' => [
        'REGISTER_SONET_EVENT' => 'Y',
    ]
        ];
    $leadAdd = $auth->CScore->call( 'crm.lead.add', $paramsLead);
    p($leadAdd, "leadAdd", $log);
    $crm ='';
    if(!empty($leadAdd)){
        $crm ='lead|'.$leadAdd[0];
    }
}
//EventType=CarSearch

    $data = [
        'ENTITY'=> 'events',
        'NAME'=>$arParams['result']['TelegramID'],
        'PROPERTY_VALUES'=>[
        'action'=>$arParams['result']['EventType'],
        'crm'=>$crm,
       'subscription_date' => $arParams['result']['SubscriptionDate'],
        'subscription_time' => $arParams['result']['SubscriptionTime'],
        'telegram_id' => $arParams['result']['TelegramID'],
       'first_name' => ($firstName === 'None' || $firstName === null ? '' : $firstName),
        'last_name' => ($lastName === 'None' || $lastName === null ? '' : $lastName),
        'username' => $arParams['result']['Username'],
       'subscription_source' => $arParams['result']['SubscriptionSource'],
      'unsubscribe_date' => $arParams['result']['Unsubscribe_Date'],
        'unsubscribe_time' => $arParams['result']['UnsubscribeTime'],
       'days_in_channel' => $arParams['result']['DaysInChannel'],
        'activation_date' => $arParams['result']['ActivationDate'],
        'activation_time' => $arParams['result']['ActivationTime'],
        'activation_source' => $arParams['result']['ActivationSource'],
        'search_date' => $arParams['result']['SearchDate'],
        'search_time' => $arParams['result']['SearchTime'],
        'car_make' => $arParams['result']['CarMake'],
        'car_model' => $arParams['result']['CarModel'],
        'year_range' => $arParams['result']['YearRange'],
        'fuel_type' => $arParams['result']['FuelType'],
        'transmission' => $arParams['result']['Transmission'],
        'equipment' => $arParams['result']['Equipment'],
        'budget' => $arParams['result']['Budget'],
       'body_type' => $arParams['result']['BodyType'],
        'search_result_count' => $arParams['result']['SearchResultCount'],
       'track_date' => $arParams['result']['TrackDate'],
       'track_time' => $arParams['result']['TrackTime'],
        'year' => $arParams['result']['Year'],
      'application_date' => $arParams['result']['ApplicationDate'],
        'application_time' => $arParams['result']['ApplicationTime'],
      'phone' => $phone,
       'request_type' => $arParams['result']['RequestType'],
        'car_link' => $arParams['result']['Carlink'],
        'source' => $arParams['result']['Source'],
        'application_type' => $arParams['result']['ApplicationType'],
    ]
    ];

    $itemAdd = $auth->CScore->call( 'entity.item.add', $data);
    p($itemAdd , "itemAdd", $log);
} else{
    if(!empty($arParams['result'])){
        foreach ($arParams['result'] as $item){
            $utm = [];
            $data = [];
            $utm = [
                "utm_link"=> $item["utm_link"],
                "utm_source"=> $item["utm_source"],
                "utm_medium"=> $item["utm_medium"],
                "utm_campaign"=> $item["utm_campaign"],
                "utm_content"=> $item["utm_content"],
                "utm_term"=> $item["utm_term"],
            ];
            $data = [
                'ENTITY'=> 'events',
                'NAME'=>$item['telegram_id'],
                'PROPERTY_VALUES'=>[
            'telegram_id' => $item["telegram_id"],
            'first_name' => $item["first_name"],
            'last_name' => $item["last_name"],
            'username' => $item["username"],
            'is_bot' => $item["is_bot"],
            'is_premium' => $item["is_premium"],
            'user_link' => $item["user_link"],
            'subscribed' => $item["subscribed"],
            'subscribe_date' => $item["subscribe_date"],
            'unsubscribe_date' => $item["unsubscribe_date"],
            'days_in_channel' => $item["days_in_channel"],
            'utm'=>json_encode($utm)
            ]];
            $itemAdd = $auth->CScore->call( 'entity.item.add', $data);
            p($itemAdd , "itemAdd", $log);
        }
    }
    
}
}
}