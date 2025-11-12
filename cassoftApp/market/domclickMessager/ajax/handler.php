<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandler.txt";
p($_REQUEST, "start", $log);

$memberId = $_REQUEST['member_id'];
$fieldName = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
if ($memberId) {
    $CloudApp = "domclick_messager";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        if (!empty($_REQUEST['PLACEMENT_OPTIONS']) && $_REQUEST['PLACEMENT'] == 'SETTING_CONNECTOR') {
            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            $APPLICATION->IncludeComponent(
                "settings:domclick_messager",
                'desctop',
                $arParams,
                false
            );
        }
    }
}







//
//    $filename = 'example.txt';
//    $content = "Это пример текста, который будет записан в файл.";
//
//// Проверяем, существует ли файл
//    if (!file_exists($filename)) {
//        // Если файл не существует, создаем его и записываем информацию
//        if (file_put_contents($filename, $content) !== false) {
//            echo "Файл '$filename' успешно создан и в него записана информация.";
//        } else {
//            echo "Ошибка при создании файла '$filename'.";
//        }
//    } else {
//        echo "Файл '$filename' уже существует.";
//    }
//
//
//    //activate connector
//    $options = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
//    $result = CRest::call(
//        'imconnector.activate',
//        [
//            'CONNECTOR' => $connector_id,
//            'LINE' => intVal($options['LINE']),
//            'ACTIVE' => intVal($options['ACTIVE_STATUS']),
//        ]
//    );
//    if (!empty($result['result']))
//    {
//        //add data widget
//        if(!empty($widgetUri) && !empty($widgetName))
//        {
//            $resultWidgetData = CRest::call(
//                'imconnector.connector.data.set',
//                [
//                    'CONNECTOR' => $connector_id,
//                    'LINE' => intVal($options['LINE']),
//                    'DATA' => [
//                        'id' => $connector_id.'line'.intVal($options['LINE']),//
//                        'url_im' => $widgetUri,
//                        'name' => $widgetName
//                    ],
//                ]
//            );
//            if(!empty($resultWidgetData['result']))
//            {
//                setLine($options['LINE']);
//                echo 'successfully';
//            }
//        }
//        else
//        {
//            setLine($options['LINE']);
//            echo 'successfully';
//        }
//    }
/*
}
if(
    $_REQUEST['event'] == 'ONIMCONNECTORMESSAGEADD'
    && !empty($_REQUEST['data']['CONNECTOR'])
    && $_REQUEST['data']['CONNECTOR'] == $connector_id
    && !empty($_REQUEST['data']['MESSAGES'])
)
{
    foreach ($_REQUEST['data']['MESSAGES'] as $arMessage)
    {
        $idMess = saveMessage($arMessage['chat']['id'], $arMessage);
        $resultDelivery = CRest::call(
            'imconnector.send.status.delivery',
            [
                'CONNECTOR' => $connector_id,
                'LINE' => getLine(),
                'MESSAGES' => [
                    [
                        'im' => $arMessage['im'],
                        'message' => [
                            'id' => [$idMess]
                        ],
                        'chat' => [
                            'id' => $arMessage['chat']['id']
                        ],
                    ],
                ]
            ]
        );
    }
}
*/