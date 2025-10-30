<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logDeactive.txt";
p($_POST, "start", $log);




    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);
        $options = json_decode($_POST['options'], true);
p($options , "options", $log);
       $profileDell = sendPostWappi('/api/profile/delete?profile_id='.$_POST['profileId'], []);
        p($profileDell, "profileDell", $log);
if($profileDell["status"] === "done") {

        $activate = $auth->CScore->call(
            'imconnector.activate',
            [
                'CONNECTOR' => $options['CONNECTOR'],
                'LINE' => intVal($options['LINE']),
                'ACTIVE' => 0,
            ]
        );
        p($activate , "activate", $log);

    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
    $params = [
        'UF_LINE_B24' =>false,
        'UF_PROFILE_ID' => false,
        'UF_STATUS_B24' => false,
        'UF_ACTIVE' => 'N',

    ];

    $elID = profileIdCsMcm($_POST['auth']['member_id'], $_POST['profileId']);
    p($elID , "elID", $log);
    $elUp= $profileHb->elementUpdate($elID['ID'], $params);
p($elUp , "elUp", $log);
    $paramsGet = [
        'ENTITY' => 'setup_messager',
        'FILTER'=>[
            'PROPERTY_CS_PROFILE_ID'=> $_POST['profileId'],
        ]
    ];

    $resSetupGet = $auth->CScore->call('entity.item.get', $paramsGet);
    p($resSetupGet , "resSetupGet", $log);

    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $resSetupGet[0]['ID'],
        'PROPERTY_VALUES'=>[
            'CS_LINE' =>false,
            'CS_CONNECTOR' => false,
            'CS_STATUS' => false,
            'CS_PROFILE_ID'=>false
        ]
    ];

    p($paramsUp , "paramsUp", $log);
    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
    p($resSetupUp , "resSetupUp", $log);
    if ($elUp[0] == 1){
        echo 'Y';
    }
}


}