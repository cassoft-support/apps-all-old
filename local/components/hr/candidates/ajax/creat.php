<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


$date = date("d.m.YTH:i");
$log = __DIR__ . "/logEdit.txt";

if($_POST["PLACEMENT_OPTIONS"]){
    $options = json_decode(htmlspecialchars_decode($_POST["PLACEMENT_OPTIONS"], true));
    $arDealID = json_decode($options, true)['DealId'];
    $arAppID = json_decode($options, true)['AppId'];
    $arSmartID = json_decode($options, true)['SmartId'];
    $companyID = json_decode($options, true)['company'];
}
//d("REQ");
//d($options);
//d("POST");
//d($arResult);
?>

<link rel="stylesheet" href="/local/lib/css/cassoft/dropzone.css">
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
<link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
<link href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" rel="stylesheet"/>
<link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<!--    форма добавления груза     -->
<link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css?1111">
<link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
<link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css?1111">
<link rel="stylesheet" href="/local/lib/css/new/forma.css"/>
<link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/select.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css">
<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet">
<style>
    .block-flex-columns{
        width: 100%;
    }
    .slider-card {
        margin-right: 0;
        border: 0;
        width:100%;
        background: var(--bg-form);
    }
    .slider-card-header {
        width: fit-content;
        padding: 10px 100px 10px 20px;
        margin: -20px 0 30px -30px;
    }

</style>
<!-- НАЧАЛО ФОРМЫ -->
<div id="slider-card" class="slider-card " >
    <div class=" slider-card-header">
        <div>
            <!--   <img class="cassoft-install-image" src="/pub/images/brokci.png" alt="brokci-logo">-->
        </div>
        <?php if ($_POST['id']) : ?>
            <div> РЕДАКТИРОВАНИЕ СОИСКАТЕЛЯ </div>
        <?php else : ?>
            <div> СОЗДАНИЕ СОИСКАТЕЛЯ </div>
        <?php endif; ?>
    </div>
    <div id="panel-close" class="side-panel-label" style="display: none;">
        <div class="side-panel-label-icon-box" title="Закрыть">
            <div class="side-panel-label-icon side-panel-label-icon-close"></div>
        </div><span class="side-panel-label-text"></span>
    </div>
    <form id="add_contact">
        <div class="block-flex-columns-center-center">
            <input hidden type="text"  value="<?=$_POST['id']?>" id="id" name="ID">
            <input id="reg" value='<?= $arResult['req'] ?>' style="display:none;" name="reg">
            <input id="app" value='<?= $arResult['app'] ?>' style="display:none;" name="app">
            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <label class="cs-form-block-label" style="margin-left: 20px;">Данные клиента</label>
                <div class="block-flex-row --wrap ">

                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " value="" id="fullname" name="fullname"  require='true'>
                        <label for="fullname" class="cs-input-label">
                            ФИО<span class="warning">*</span>
                        </label>
                    </div>
                    <input hidden id="NAME" type="text" value='' name="NAME">
                    <input hidden id="LAST_NAME" type="text" value='' name="LAST_NAME">
                    <input hidden id="SECOND_NAME" type="text" value='' name="SECOND_NAME">

                    <div  class=" cs-input-container">

                        <input type="text" id="FULLADDRESS" name="FULLADDRESS" class="cs-input-block__text cs-input-block" value=""
                               placeholder=" " autocomplete="off" require='true'>
                        <label for="FULLADDRESS" class="cs-input-label">
                            Адрес регистрации<span class="warning">*</span>
                        </label>
                    </div>
                    <input hidden id="ADDRESS_ONLY" type="text" value='' name="ADDRESS_ONLY">

                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block" type="date" placeholder=" " value="" id="BIRTHDATE" name="BIRTHDATE" require='true'>

                        <label for="BIRTHDATE" class="cs-input-label">
                            Дата рождения<span class="warning">*</span>
                        </label>
                    </div>
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block mask-phone"  type="text" placeholder=" " value="" id="phone" name="PHONE" require='true'>
                        <label for="phone" class="cs-input-label">
                            Телефон<span class="warning">*</span>
                        </label>
                    </div>
                    <input class="cs-input-block__text cs-input-block"  type="text" style="display: none;" value="<?=$companyID?>" id="COMPANY_ID" name="COMPANY_ID">
                    <input class="cs-input-block__text cs-input-block"  type="text" style="display: none;" value="Y" id="UF_CRM_CS_DRIVER" name="UF_CRM_CS_DRIVER">
                    <input class="cs-input-block__text cs-input-block"  type="text" style="display: none;" value="CS_PROVIDER_STAFF" id="TYPE_ID" name="TYPE_ID">
                </div>
            </div>

            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <label class="cs-form-block-label" style="margin-left: 20px;">Паспортные данные</label>
                <div class="block-flex-row --wrap ">
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block mask-pasport-series"  type="text" placeholder=" " value="" id="RQ_IDENT_DOC_SER" name="RQ_IDENT_DOC_SER" require='true'>
                        <label for="RQ_IDENT_DOC_SER" class="cs-input-label">
                            Серия паспорта<span class="warning">*</span>
                        </label>
                    </div>
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block mask-pasport-number"  type="text" placeholder=" " value="" id="RQ_IDENT_DOC_NUM" name="RQ_IDENT_DOC_NUM" require='true'>
                        <label for="RQ_IDENT_DOC_NUM" class="cs-input-label">
                            Номер паспорта<span class="warning">*</span>
                        </label>
                    </div>
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " value="" id="fms_unit" name="RQ_IDENT_DOC_ISSUED_BY" require='true'>
                        <label for="fms_unit" class="cs-input-label">
                            Кем выдан<span class="warning">*</span>
                        </label>
                    </div>
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block"  type="date" placeholder=" " value="" id="RQ_IDENT_DOC_DATE" name="RQ_IDENT_DOC_DATE" require='true'>
                        <label for="RQ_IDENT_DOC_DATE" class="cs-input-label">
                            Когда выдан<span class="warning">*</span>
                        </label>
                    </div>

                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block mask-pasport-division"  type="text" placeholder=" " value="" id="RQ_IDENT_DOC_DEP_CODE" name="RQ_IDENT_DOC_DEP_CODE" require='true'>
                        <label for="RQ_IDENT_DOC_DEP_CODE" class="cs-input-label">
                            Код подразделения<span class="warning">*</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <label class="cs-form-block-label" style="margin-left: 20px;">Водительское удостоверение</label>
                <div class="block-flex-row --wrap ">
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block mask-driver"  type="text" placeholder=" " value="" id="UF_CRM_CS_SERIES_NUMBER_DL" name="UF_CRM_CS_SERIES_NUMBER_DL" require='true'>
                        <label for="UF_CRM_CS_SERIES_NUMBER_DL" class="cs-input-label">
                            Номер и серия ВУ<span class="warning">*</span>
                        </label>
                    </div>
                    <div class="cs-input-container">
                        <input class="cs-input-block__text cs-input-block" type="date" placeholder=" " value="" id="UF_CRM_CS_VALIDITY_PERIOD_DL" name="UF_CRM_CS_VALIDITY_PERIOD_DL" require='true'>
                        <label for="UF_CRM_CS_VALIDITY_PERIOD_DL" class="cs-input-label">
                            Срок действия ВУ<span class="warning">*</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <label class="cs-form-block-label" style="margin-left: 20px;">Копии документов</label>
                <div class="block-flex-row --wrap ">
                    <div class="cs-input-container" >

                        <div id="gal" hidden>   [] </div>
                        <div class="dropzone mt20 sortable" id="dropzone">
                            <div style="display: inline; width: 40px;"></div>
                        </div>
                        <span for="dropzone" class="cs-input-label cs-input-label-select">Добавьте скан документа</span>
                        <div class="msg-error" data-name="gallery"></div>
                    </div>
                </div>
            </div>


            <?php // КНОПКА ?>


            <div id="notification" class="block-flex-row --wrap "></div>
            <br>

            <div class="btn-help-row">
                <div class="btn-blue">
                    <div><i class="fa fa-check-circle fa-2x "></i></div>
                    <div><button id="save">Сохранить</button></div>
                </div>
            </div>

    </form>
</div>
<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>-->
<script  src="/local/lib/js/jquery-ui.min.js"></script>
<script defer type="text/javascript" src="/local/lib/chosen/chosen.jquery.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
<script defer src="/local/lib/js/dropzone.min.js"></script>
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script defer type="text/javascript" src="/local/components/event/contact_add/templates/driverAdd/script.js"></script>
