<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logContactUpdate.txt";
p($_REQUEST, date('c'), $log);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = "change_assigned";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        $arParams = $_REQUEST;
        $arParams['app'] = $CloudApp;
        $arParams['auth']['member_id']= $memberId;
        $APPLICATION->IncludeComponent(
            "dashboard:main_app",
            "change_assigned",
            $arParams,
            false
        );
    }else{?>
        
        <style>
            .no-app{
            width: 100%;
            height: 500px;
                font-size: 24px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .no-app-subtitle{
            margin-top: 50px;
                font-size: 18px;
            }

        </style>
        <div class="no-app" style="">
           <div class="no-app-title">Приложение надо переустановить, обратитесь к вашему Администратору.</div>
            <div class="no-app-subtitle">или напишите нам в чат в этом окне</div>
            </div>
        <script>
        (function(w,d,u){
            var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
            var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn-ru.bitrix24.ru/b9950371/crm/site_button/loader_5_9bynjt.js');
        </script>
<?php
    }
}
