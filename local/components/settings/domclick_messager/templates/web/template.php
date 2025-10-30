<!doctype html>
<head>
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
    <link href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" rel="stylesheet"/>
    <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/local/lib/css/new/select.css"/>


    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
    <link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet" />

    <!--    форма добавления груза     -->
   <!-- <link rel="stylesheet" href="/local/components/fulfillment/applications/templates/desctop/css/add_app.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css?1111">-->
    <link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css?1111">
    <link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/forma.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/select.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
</head>
<style>

    .cs_filter_item_label {
        position: unset;
        margin-top: 15px;
        background: 0;
    }

    .container-block {
        background: #fff !important;
    }

    .filter_conteiner {
        padding: 5px;
    }

    .filter_block {
        border: 1px solid #999;
        background: #eef2f4 !important;
        border-radius: 15px;
    }

    .filter_block_title {
        font-size: 26px;
        font-weight: 300;
        padding: 10px 30px;
        color: #333;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    }

    .filter_column {
        display: flex;
        flex-direction: column;
        width: 30%;
        margin: 20px;
        max-width: 320px;
    }

    .filter-input__clear {
        margin-left: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .filter-input__clear svg {
        cursor: pointer;
        fill: #c4c7cc;;
        border: 1px solid #c4c7cc;;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        padding: 2px;
        -webkit-transition: all .3s;
        -moz-transition: all .3s;
        -ms-transition: all .3s;
        -o-transition: all .3s;
        transition: all .3s;
    }

    .filter-input__clear svg:hover {
        -webkit-transform: scale(1.2);
        -moz-transform: scale(1.2);
        -ms-transform: scale(1.2);
        -o-transform: scale(1.2);
        transform: scale(1.2);
        background: rgba(224, 46, 46, 0.82);
        border-color: rgba(224, 46, 46, 0.82);
        fill: #ffff;
    }

</style>
<?php
//$res =json_decode(htmlspecialchars($_REQUEST[UserAut]), true);
$res =json_decode($_REQUEST['UserAut'], true);
//echo "<pre>";print_r($res);echo "</pre>";
if($res['PLACEMENT'] !== 'CRM_COMPANY_DETAIL_TAB'){
?>
<!-- форма фильтр -->
<div class="filter_conteiner">
    <div class="filter_block">
        <div class="filter_block_title"> Заявки web</div>

        <div class="filter-row" style="display: none;">
            <div class="filter_column">
                <div class="filter-item filter-input__switcher" data-id="CS_ASSIGNED_BY" id="userObjects"
                     data-order="8">
                    <div class="input-radio__item">
                        <input class="input-radio" name="CS_ASSIGNED_BY" type="checkbox" id="CS_ASSIGNED_BY"
                               data-value="Да" checked="">
                        <label class="block-title-text" for="CS_ASSIGNED_BY">Активные заявки</label>
                    </div>
                </div>
            </div>
                <div class="filter_column">
<!--                <div class="filter-item filter-input__switcher" data-id="CS_ASSIGNED_BY" id="userObjects"-->
<!--                     data-order="8">-->
<!--                    <div class="input-radio__item">-->
<!--                        <input class="input-radio" name="CS_ASSIGNED_BY" type="checkbox" id="CS_ASSIGNED_BY"-->
<!--                               data-value="Да" checked="">-->
<!--                        <label class="block-title-text" for="CS_ASSIGNED_BY">Мои запросы</label>-->
<!--                    </div>-->
<!--                </div>-->

            </div>


        </div>

        <div class="slider-btn-group-top" style="display: none;">
            <div class="btn-link-fa-blue"><a href='javascript:searchApp()'><i class="fa fa-search"></i>Искать</a>
            </div>

        </div>

        <!--</form>-->
    </div>
</div>
<?php
} else{
?>

<?php
}
?>
<div id="slider_card" class="slider-card-row" style="display: none;"></div>
<div id="report" style="background: #fff!important; padding: 10px;">

</div>
<!--<script defer src="//api.bitrix24.com/api/v1/"></script>-->
<script src="/local/lib/js/moment.min.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="/local/components/fulfillment/applications/js/bootstrap-table-1.22.1.min.js"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>

<script src="/local/components/fulfillment/applications/templates/web/table.js"></script>
<script defer type="text/javascript" src="/local/components/fulfillment/applications/templates/web/script.js?0806"></script>


