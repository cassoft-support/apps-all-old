<?php

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    //d($_REQUEST);
?>

<head>
    <link rel="stylesheet" href="/pub/cassoftApp/brokci/css/style.css"/>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css">
    <link rel="stylesheet" href="/local/components/install/logistics_pro/css/install.css">
</head>


<body>
<div class="cassoft-install-header">
    <input id="reg" value='<?= json_encode($arResult['req']) ?>' style="display:none;">
    <input id="cassoftApp" value="" style="display:none;">
    <div class="cassoft-install-header-logo">
        <img src="/pub/images/app/logistics-pro/logistics_logo.png" alt="brokci-logo">
    </div>
    <div>
        <h4 id="title" class="cassoft-install-text">Обновление приложения "Logistics-pro+"</h4>
    </div>
</div>
<div class="forma-install">
    <div class="">
        <h3>Процесс установки может занимает значительное время. </h3>
    </div>

    <div class="">Дождитесь окончания процесса.</div>

        <br>
        <br>
        <div class="btn_block">
            <button class="form-small-button-blue" id="updateApp">Обновить</button>
        </div>


        <div class="progress" hidden>
            <div class="circle done">
                <span class="label">1</span>
                <span class="title">События</span>
            </div>
            <span class="bar done"></span>
            <div class="circle done">
                <span class="label">2</span>
                <span class="title">Пользовательские поля</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">3</span>
                <span class="title">Хранилище</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">4</span>
                <span class="title">Настройки</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">5</span>
                <span class="title">Встройки</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">6</span>
                <span class="title">Блоки для сайта</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">7</span>
                <span class="title">Завершение</span>
            </div>
        </div>
    </div>
    <div class="notification_block">
        <div id="notification" class="finishInstallBlock" hidden></div>

    </div>
    <div class="result">
        <div id="setupRes" class="" hidden style="text-align: left;"></div>

    </div>
</body>
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="/local/lib/js/jquery-ui.min.js"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script src="/local/components/install/logistics_pro/js/update.js"></script>


</html>