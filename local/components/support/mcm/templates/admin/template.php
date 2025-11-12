<?php

  //  require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    //d($_REQUEST);
?>

<head>
    <link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
    <link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css"/>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
    <link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
    <link rel="stylesheet" href="/local/lib/css/cassoft/cs-root-blue.css">
    <link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
    <link rel="stylesheet" href="/local/components/dashboard/main_app/templates/accountant/menuMob.css"/>
    <link rel="stylesheet" href="/local/lib/css/cassoft/panel.css"/>
    <link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css?020223"/>
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
</head>


<body>
<div class="cassoft-install-header">
    <input id="reg" value='<?= json_encode($arResult['req']) ?>' style="display:none;">
    <input id="cassoftApp" value="" style="display:none;">

    <div>
        <h4 id="title" class="cassoft-install-text">Приложения "MCM"</h4>
    </div>
</div>
<div class="forma-install">


    </div>
        <div class="block-flex-columns-center-center " >
        <div class="btn_block" id="blockButton">
            <button class="form-small-button-blue" id="updateApp">Старт  </button>
        </div>
        <br>


        </div>
        <div class="notification_block">
            <div id="notification" class="finishInstallBlock"  style="width: 1200px; text-align: left;"></div>

        </div>
        <div class="result">
            <div id="setupRes" class="" hidden style="text-align: left;"></div>

        </div>
</body>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/js/moment.min.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="/local/components/logistics/applications/js/bootstrap-table-1.22.1.min.js"></script>
<script src="/local/components/support/mcm/js/admin.js?2007"></script>


</html>