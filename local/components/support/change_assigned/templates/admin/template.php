<?php

  //  require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    //d($_REQUEST);
?>

<head>
<!--    <link rel="stylesheet" href="/pub/cassoftApp/brokci/css/style.css"/>-->
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">-->
<!---->
<!--    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>-->
<!--    <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">-->
<!--    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css">-->
<!--    <link rel="stylesheet" href="/local/components/install/logistics_pro/css/install.css">-->
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
</head>


<body>
<div class="cassoft-install-header">
    <input id="reg" value='<?= json_encode($arResult['req']) ?>' style="display:none;">
    <input id="cassoftApp" value="" style="display:none;">

    <div>
        <h4 id="title" class="cassoft-install-text">Приложения "Смана ответственного"</h4>
    </div>
</div>
<div class="forma-install">


        <div class="" style="display: none;">
            <div class="">протокол</div>
            <input type="text" id="event" value="" style="">
            <br>
            <div class="btn_block">

                <button class="form-small-button-blue" id="updateApp">Запустить </button>
            </div>
        </div>
        <br>
        <div class="block-flex-columns-center-center " style="display: none;">
            <div class="">Загрузка заявок</div>
            <div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="eventLoad" value="/v1.0/loads" type="text"
                       placeholder=" ">
                <label for="UF_CS_KEY_ATI" class="cs-input-label">Действие<span
                            class="warning">*</span></label>
            </div>
            <div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="eventype" type="text"
                       placeholder=" " value="">
                <label for="UF_CS_KEY_ATI" class="cs-input-label">Тип<span
                            class="warning">*</span></label>
            </div>

        <div class="btn_block" >
            <button class="form-small-button-blue" id="uploadApplication">Загрузить</button>
        </div>
        <br>
        <br>
        <br>
    </div>
        <div class="block-flex-columns-center-center " >
        <div class="btn_block" id="blockButton">
            <button class="form-small-button-blue" id="updateApp">Старт  </button>
        </div>
        <br>
        <div class="block-flex-columns-center-center ">
            <div class="cs-input-container" style="">
                <input type="text" class="cs-input-block__text cs-input-block" id="smart" value="" placeholder=" ">
                <label for="UF_CS_KEY_ATI" class="cs-input-label">Номер смарта<span
                            class="warning">*</span></label>
            </div>
            <div class="btn_block" style="display: none;">
                <button class="form-small-button-blue" id="upSmart">Обновить</button>
            </div>

        </div>
        <div class="notification_block">
            <div id="notification" class="finishInstallBlock"  style="width: 1200px; text-align: left;"></div>

        </div>
        <div class="result">
            <div id="setupRes" class="" hidden style="text-align: left;"></div>

        </div>
</body>
<!--<script src="/local/lib/js/jquery-3.6.0.min.js"></script>-->
<!--<script src="/local/lib/js/jquery-ui.min.js"></script>-->
<!--<script src="//api.bitrix24.com/api/v1/"></script>-->
<script src="/local/components/support/change_assigned/js/admin.js"></script>


</html>