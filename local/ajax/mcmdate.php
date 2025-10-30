<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logMcm.txt";

    $appAccess = 'app_mcm_profile';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $el= $HlClientApp->getVocabulary();
   // pr($el, '');
foreach ($el as $val){
    if($val['UF_PROFILE_NAME'] === 'cs_613') {
        $date = new DateTime('@' . $val['UF_DATE_CLOSE']);
        $date->modify('+30 days');
        pr($date, '3');
        if ($date < new DateTime()) {
            $date->modify('+30 days');
        }
        pr($date, '2');
        $newDate = $date->getTimestamp();
        $params = [
            'UF_DATE_CLOSE' => $newDate
        ];
        pr($params, '');
        $update = $HlClientApp->elementUpdate($val['ID'], $params);
        pr($update);
    }
}
