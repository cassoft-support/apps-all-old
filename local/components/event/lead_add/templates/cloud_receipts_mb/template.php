<?

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css">
    <link rel="stylesheet" href="css/style.css"/>
</head>
<style>
    .forma-install {
        display: flex;
        flex-direction: column;
        align-content: center;
        background: #eef2f4;
        text-align: center;
    }
</style>

<body>
<div id="request" style="display:none;"><?= json_encode($_REQUEST) ?></div>
<div class="cassoft-install-header">
    <div class="cassoft-install-image">
        <img src="/pub/images/app/cloud_receipts_mb/logo_mb_v.png" alt="">
    </div>
    <div>
        <h4 id="title" class="cassoft-install-text">Установка приложения "Облачные чеки Mодульбанк"</h4>
    </div>
</div>
<div class="forma-install">
    <div class="">
        <h3>Процесс установки занимает некоторое время.
    </div>
    </h3>
    <div class="">Дождитесь окончания процесса.</h3>

        <br>
        <br>
        <div class="btn_block">
            <button class="form-small-button-blue" id="install">Установить</button>
        </div>

        <div class="progress" hidden>
            <div class="circle done">
                <span class="label">1</span>
                <span class="title" style="display: block; width: 200px;">Платежная система</span>
            </div>

            <span class="bar done"></span>
            <div class="circle done">
                <span class="label">2</span>
                <span class="title">Настройки</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">3</span>
                <span class="title">Вкладки</span>
            </div>
            <span class="bar half"></span>
            <div class="circle active">
                <span class="label">4</span>
                <span class="title">Завершение</span>
            </div>
        </div>
    </div>
    <div class="notification_block">
        <div id="notification" class="finishInstallBlock" hidden></div>

    </div>
</body>
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="/local/lib/js/jquery-ui.min.js"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script src="/local/components/install/base/templates/cloud_receipts_mb/script.js"></script>

</html>