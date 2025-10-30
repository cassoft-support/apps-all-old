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
    .filter_block_title {
        font-size: 26px;
        font-weight: 300;
        padding: 10px 30px;
        color: #333;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
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
        <div class="filter_block_title">Авторизация на ДомКлик</div>

        <div class="filter-row" style="">
            <input style="display: none;" type="text" value="<?= $arResult['app']?>" id="app" >
            <input style="display: none;" type="text" value="<?= $arResult['ID']?>" id="ID" >
            <input style="display: none;" type="text" value='<?= $arResult['options']?>' id="options" >
            <div class="cs-input-container --w100p cs-messager" >
                    <input class="cs-input-block__text cs-input-block cs-messager" type="text" placeholder=" " value="<?= $arResult['CS_KEY_DC']?>" id="keyMessegar" >
                    <label for="keyMessegar" class="cs-input-label">Ключ ДомКлик<span class="warning">*</span></label>
                </div>
        </div>


        <div class="btn_block">
            <a class="form-small-button form-small-button-blue" href='javascript:activeDCMessager()'><span>Подключить к ДомКлик</span> </a>
        </div>
        <!--</form>-->
    </div>
    <div class="filter_block" id="deactive" style="<?=$deactive?>">
        <div class="filter_block_title">Коннектор подключен к ДомКлик</div>
        <div class="btn_block">
            <a class="form-small-button form-small-button-blue" href='javascript:deactiveDCMessager()'><span>Отключить от ДомКлк</span> </a>
        </div>

        </div>
    </div>
</div>


<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script  src="//api.bitrix24.com/api/v1/"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script defer type="text/javascript" src="/local/components/settings/domclick_messager/templates/desctop/script.js"></script>


