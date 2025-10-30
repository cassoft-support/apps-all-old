<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);


    $options = json_decode($_POST['options'], true);
    p($options, "options", $log);
        $activate = $auth->CScore->call(
            'imconnector.activate',
            [
                'CONNECTOR' => $options['CONNECTOR'],
                'LINE' => intVal($options['LINE']),
                'ACTIVE' => intVal($options['ACTIVE_STATUS']),
            ]
        );
        p($activate , "activate", $log);
        $line = json_encode([
            $_POST['profile'] =>   [
            'CONNECTOR' => $options['CONNECTOR'],
            'LINE' => intVal($options['LINE']),
            'ACTIVE' => intVal($options['ACTIVE_STATUS']),
        ]]);

    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $_POST['id'],
        'PROPERTY_VALUES'=>[
            'CS_MCM_CONNECT'=> $line,
        ]
    ];


    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
    p($resSetupUp , "resSetupUp", $log);
    if ($resSetupUp[0] == 1){
        echo 'Y';
    }
}

}