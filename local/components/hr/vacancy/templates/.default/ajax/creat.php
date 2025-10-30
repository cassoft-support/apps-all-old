<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/item.php");
$log = __DIR__ . "/logCreat.txt";
p($_POST,'start', $log);


//$Result=$_POST['value'];
$auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

//$smartClass = new \CSlibs\B24\Smarts\SmartProcess($auth, "");
//$resSmartGuide= $smartClass->smartGuide();
//$Result['stage']= $smartClass->smartCategoryStageName($resSmartGuide['Найм персонала']['entityTypeId']);

if($_POST["PLACEMENT_OPTIONS"]) {
    $id = json_decode(htmlspecialchars_decode($_POST["PLACEMENT_OPTIONS"], true));
    $ID = json_decode($id, true)['id'];
}
$item= itemData($auth, $_POST['value']);
//pr($item, '');
if($contactID){
   $header = "РЕДАКТИРОВАНИЕ ВАКАНСИИ";
}

if($arAppID !== "driver" && !$contactID){
    $header = "СОЗДАНИЕ ВАКАНСИИ";
}


    $arPhoto=$contact['UF_CRM_CS_SCAN_DOC'];
    $arResult["scanDoc"]=json_encode([]);
    if($arPhoto) {
        foreach ($arPhoto as $photo) {

            //   d($photo);
            $photoInfo = explode('|', $photo);
            $resScanDoc[] = [
                'photo_id' => $photoInfo['0'],
                'photo_link' => $photoInfo['1'],
            ];
            $resLink[] = $photoInfo['1'];
        }
        $arResult["scanDoc"] = json_encode($resScanDoc);
        $arResult["link"] = $resLink;

}
//d($arResult);
//d($TypeIdTest);

?>
<!--<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet">-->
<link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
<link href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" rel="stylesheet"/>
<!--<link rel="stylesheet" href="/local/lib/css/cassoft/dropzone.css">-->
<!--<link rel="stylesheet" href="/local/lib/gallery/fancybox/jquery.fancybox.min.css"/>-->
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
<link rel="stylesheet" href="/local/lib/css/new/select.css"/>
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css">
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css">
<!--<link href="/local/lib/css/cassoft/style.css" rel="stylesheet">-->
<!--<link rel="stylesheet" href="/local/lib/css/new/forma.css"/>-->
<link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css"/>
<link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">

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
    .scan-doc-img{
        width: 100px;
        height: 100px;
        overflow: hidden;
        border-radius: 10px;
        margin: 5px;
    }
    .scan-doc-img img{
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
<!-- НАЧАЛО ФОРМЫ -->
<div id="slider-card" class="slider-card " >
    <div class=" slider-card-header">
        <div>
            <!--   <img class="cassoft-install-image" src="/pub/images/brokci.png" alt="brokci-logo">-->
        </div>

            <div id ="header"> <?= $header?> </div>

    </div>
    <div id="panel-close" class="side-panel-label" style="display: none;">
        <div class="side-panel-label-icon-box" title="Закрыть">
            <div class="side-panel-label-icon side-panel-label-icon-close"></div>
        </div><span class="side-panel-label-text"></span>
    </div>
    <form id="add_contact">
    <div class="block-flex-columns-center-center">
        <input hidden type="text"  value="<?=$contactID?>" id="id" name="ID">
        <div class="block-flex-columns cs-form-block --mb30 --p10"  >
            <label class="cs-form-block-label" style="margin-left: 20px;">Данные вакансии</label>
            <div class="block-flex-row --wrap ">

                <div class="cs-input-container">
                    <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " value="" id="fullname" name="fullname"  >
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
              <!--  <input class="cs-input-block__text cs-input-block"  type="text" style="display: none;" value="Y" id="UF_CRM_CS_DRIVER" name="UF_CRM_CS_DRIVER">
-->
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
            <div class="block-flex-row --wrap ">
                <div class="add-item-check">
                    <input id="UF_CRM_CS_DRIVER" name="UF_CRM_CS_DRIVER" type="checkbox" class="cs-switch">
                    <span for="UF_CRM_CS_DRIVER" class="add-label-check">Создать контакт в CRM</span>
                </div>
                <div class="block-flex-columns " id="dl" style="width: auto;" >
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
            </div>
        </div>

        <div id="workarea" class="block-flex-columns cs-form-block --mb30 --p10"  >
            <label class="cs-form-block-label" style="margin-left: 20px;">Копии документов</label>
            <div class="block-flex-row --wrap ">
<!--                <div class="cs-input-container" >-->
<!---->
<!--                    <div id="gal" hidden>   [] </div>-->
<!--                    <div class="dropzone mt20 sortable" id="dropzone">-->
<!--                        <div style="display: inline; width: 40px;"></div>-->
<!--                    </div>-->
<!--                    <span for="dropzone" class="cs-input-label cs-input-label-select">Добавьте скан документа</span>-->
<!--                    <div class="msg-error" data-name="gallery"></div>-->
<!--                </div>-->


                <div class="block-info-container" id="galCard" style="display:flex;">
                    <div class="block-flex-columns --w100p">
                        <div class="block-flex-row --justify-between --wrap" style="display: none;">
                            <div class="block-info-title"> Копии документов</div>
                        </div>
                        <div id="scanGal" class="block-flex-row --justify-start --wrap">
                                                        <?php if($arResult["link"]){
                                                            foreach ($arResult["link"] as $link){?>
                                                                <a class="scan-doc" data-caption="" data-fancybox="gallery" href="<?= $link?>">
                                                                    <div  class="scan-doc-img">
                                                                        <img src="<?= $link?>">
                                                                    </div>
                                                                </a>
                                                            <?php } }?>
                        </div>
                        <div class="btn_block" id="editCopy">
                            <div class="form-small-button form-small-button-blue" style="display: flex; align-items: center;" onclick="scanEdit();"><div>Изменить</div></div>
                        </div>
                    </div>
                </div>

                <div class="block-info-container" id="galForm" style="display:flex;" >
                    <div class="block-flex-columns --w100p">
                        <form id="scanForm">
                            <div class="block-flex-row --justify-start --wrap">
                                <div class="cs-input-container" >
<!--                                    <div id="gal" hidden> --><?php //$arResult['scanDoc'] ?? ''?><!--</div>-->
                                    <div id="gal" hidden> <?php $arResult['scanDoc'] ?? ''?></div>
                                    <div class="dropzone mt20 sortable" id="dropzone">
                                        <div style="display: inline; width: 40px;"></div>
                                    </div>
                                    <span for="dropzone" class="cs-input-label cs-input-label-select">Добавьте скан документа</span>
                                    <div class="msg-error" data-name="gallery"></div>
                                </div>
                            </div>
                        </form>
                    </div>
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
    <script src="//api.bitrix24.com/api/v1/"></script>
    <script  src="/local/lib/js/jquery-ui.min.js"></script>
    <script defer type="text/javascript" src="/local/lib/chosen/chosen.jquery.js"></script>
<!--    <script src="/local/lib/Api/dadata-php-master/jquery.suggestions.min.js"></script>-->
<!--    <script  src="/local/lib/gallery/fancybox/jquery.fancybox.min.js"></script>-->
<!--    <script src="/local/lib/js/dropzone.min.js"></script>-->
    <script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<!--<script defer src="/local/lib/classes/validate/validateReq.js"></script>-->
    <script defer type="text/javascript" src="/local/components/hr/vacancy/templates/.default/ajax/creat.js?1207"></script>


