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

        $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');

        $params = [
            'UF_LINE_B24' =>intVal($options['LINE']),
                'UF_CONNECTOR' => $options['CONNECTOR'],
                    'UF_STATUS_B24' => intVal($options['ACTIVE_STATUS']),

        ];
        $el= $profileHb->getByFilterList(['UF_PROFILE_ID' => $_POST['profile']]);
        $elID = profileIdCsMcm($_POST['auth']['member_id'], $_POST['profile']);
        p($elID , "elID", $log);
       $elUp= $profileHb->elementUpdate($el[0]['ID'], $params);
       p($elUp , "elUp", $log);
        $paramsGet = [
            'ENTITY' => 'setup_messager',
            'FILTER'=>[
                'PROPERTY_CS_PROFILE_ID'=> $_POST['profile'],
            ]
        ];

        $resSetupGet = $auth->CScore->call('entity.item.get', $paramsGet);
        p($resSetupGet , "resSetupGet", $log);

    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $resSetupGet[0]['ID'],
        'PROPERTY_VALUES'=>[
            'CS_LINE' =>intVal($options['LINE']),
            'CS_CONNECTOR' => $options['CONNECTOR'],
            'CS_STATUS' => intVal($options['ACTIVE_STATUS']),
        ]
    ];

p($paramsUp , "paramsUp", $log);
    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
  p($resSetupUp , "resSetupUp", $log);
    if ($resSetupUp[0] == 1){
        p($resSetupUp[0] , "resSetupUp[0]", $log);
        echo 'Y';
    }
}

}