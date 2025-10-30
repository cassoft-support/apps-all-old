<!doctype html>
<head>
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">
    <link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
</head>
<style>

    .filter_conteiner {
        padding: 0;
    }

    .filter_block {
        border: 0!important;
        background: #fff !important;

    }
    .filter_block_title,.filter_block_subtitle {
        font-size: 26px;
        font-weight: 300;
        padding: 10px 30px;
        color: #333;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    }
    .filter_block_subtitle{
        margin: 0 20px;
        font-size: 14px;
        margin-bottom: 30px;
    }

.cs-messager{
    max-width: 100%;
    /*padding: 0 15px;*/
}
.form-small-button{
    padding: 10px 17px;
    height: 41px;
}

</style>
<?php
//pr($arResult, '');
if (empty($arResult['CS_CIAN_CONNECT'])){
    $deactive = 'display:none;';
    $active = '';
}else{
    $deactive = '';
    $active = 'display:none;';
}

?>
<!-- форма фильтр -->
<div class="filter_conteiner" id="mainApp">
    <div class="filter_block" id="formaApp" style="<?=$active?>">
        <div class="filter_block_title">Импорт с HH</div>
<div class="filter_block_subtitle">Для работы Коннектора необходимо получить ключ авторизации. Для получения ключа нужно написать письмо на import@cian.ru с темой "ACCESS KEY" и названием агентства, которое вы представляете. Менеджер уточнит подробности и пришлет ACCESS KEY. Для каждой учетной записи на ЦИАН выдается отдельный ключ авторизации.</div>
        <div class="filter-row" style="">
            <input style="display: none;" type="text" value="<?= $arResult['app']?>" id="app" >
            <input style="display: none;" type="text" value="<?= $arResult['ID']?>" id="ID" >
            <input style="display: none;" type="text" value='<?= $arResult['options']?>' id="options" >
            <div class="cs-input-container --w100p cs-messager" >
                    <input class="cs-input-block__text cs-input-block cs-messager" type="text" placeholder=" " value="<?= $arResult['CS_KEY_CIAN']?>" id="keyCian" >
                    <label for="CS_KEY_CIAN" class="cs-input-label">Ключ ЦИАН<span class="warning">*</span></label>
                </div>
        </div>


        <div class="btn_block">
            <a class="form-small-button form-small-button-blue" href='javascript:loadingHH("vacancy")'><span>Импорт вакансий</span> </a>
        </div>
        <!--</form>-->
    </div>
    <div class="filter_block" id="deactive" style="<?=$deactive?>">
        <div class="filter_block_title">Коннектор подключен к ЦИАН</div>
        <div class="btn_block">
            <a class="form-small-button form-small-button-blue" href='javascript:loadingHH()'><span>Отключить от ЦИАН</span> </a>
        </div>

        </div>
    </div>

<div class="" id="resHH"></div>

<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script  src="//api.bitrix24.com/api/v1/"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script defer type="text/javascript" src="/local/components/settings/hr_pro/templates/hh/script.js"></script>


