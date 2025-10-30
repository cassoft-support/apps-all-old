<?php

    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
   // require($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
    require($_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/guide/financier.php');

    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/logTemp.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    //file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
//\CBitrixComponent::includeComponentClass('logistics:applications');
//$arCarBodyTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_car_body_type', [], [], ['UF_TYPE_ID', 'UF_NAME']); // типы кузова
//$arCarBodyTypesID = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_car_body_type', [], [], ['ID', 'UF_NAME']); // типы кузова
//$arCarBodyLoadingTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_loading_types', [], [], ['UF_ID_ATI', 'UF_NAME']); // варианты загрузки
//$arCarBodyUnloadingTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_unloading_types', [], [], ['UF_ID_ATI', 'UF_NAME']); // варианты разгрузки
//$arCurrencyTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_currency_types', [], [], ['UF_ID_ATI', 'UF_NAME']); // типы валюты
//$arTruckBrands = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_truckbrands', [], [], ['ID', 'UF_NAME']); // типы валюты
//$arTruckModels = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_truckmodels', [], [], ['ID', 'UF_MODEL']); // типы валюты
//$arTruckTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_type_auto', [], [], ['ID', 'UF_NAME']); // типы валюты

foreach ($arCarBodyTypesID as $key){
    $carBodyTypesID[$key['ID']] =  $key['UF_NAME'];
}
//d($carBodyTypesID);
foreach ($arCarBodyTypes as $key){
    $carBodyTypes[$key['UF_TYPE_ID']] =  $key['UF_NAME'];
}
foreach ($arTruckBrands as $key){
    $truckBrands[$key['ID']] =  $key['UF_NAME'];
}
foreach ($arTruckModels as $key){
    $truckModels[$key['ID']] =  $key['UF_MODEL'];
}
foreach ($arTruckTypes as $key){
    $truckTypes[$key['ID']] =  $key['UF_NAME'];
}

foreach ($arCarBodyLoadingTypes as $key){

    $carBodyLoadingTypes[$key['UF_ID_ATI']] =  $key['UF_NAME'];
}

foreach ($arCarBodyUnloadingTypes as $key){
    $carBodyUnloadingTypes[$key['UF_ID_ATI']] =  $key['UF_NAME'];
}
$status = "background: #9999998c; border: 1px solid #999;" ;
$statusTempCl = "background: #9999998c; border: 1px solid #999;" ;
//$blockTempCl ="display:none;";
if($arResult['TemplateClient'] == "Y"){
    $statusTempCl = "background: green; border: 1px solid green;" ;
   // $blockTempCl ="display:flex;";
}
$statusTempIn = "background: #9999998c; border: 1px solid #999;" ;
//$blockTempIn ="display:none;";
if($arResult['TemplateInternational'] == "Y"){
    $statusTempIn = "background: red; border: 1px solid red;" ;
   // $blockTempIn ="display:flex;";
}
//d($arResult);
?>
<style>
   body{
       background:#f9fafb ;
   }
   .cs-input-block__text {
       width: 100%;
   }
.fancybox-desktop{
    top: 120px!important;
    left: 1px!important;
}
</style>

<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-panel.css"/>
<link rel="stylesheet" href="/local/lib/css/new/preloader.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css">
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
<!--<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-filter-tr.css"/>-->
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css"/>
<link rel="stylesheet" href="/local/lib/css/new/select.css"/>
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css">

<div class="" id="workarea">
  <!--  <div class="block-info">-->
        <div class="preloader preloader-remove">
            <div class="preloader-2">
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
        </div>


    <input type="text" id ="smartElId" value="<?= $arResult['smartElId']?>" style="display: none;">
    <input type="text" id ="smartId" value="<?= $arResult['smartId']?>" style="display: none;">
    <input type="text" id ="memberId" value="<?= $arResult['member_id']?>" style="display: none;">
    <input type="text" id ="app" value="<?= $arResult['app']?>" style="display: none;">
    <input type="text" id ="candidateId" value="<?= $arResult['item']['id']?>" style="display: none;">

    <div class="block-info" id="requestCard">
<div class="block-flex-row --justify-between --nowrap --w100p">
        <div class="block-info-title" > Карточка соискателя</div>
        <div class="form-button-circle form-button-circle-24 "  style="margin-right: 5px; display: none;">
            <i class="fa fa-play-circle form-button-circle-play-red" aria-hidden="true" id="startBP"></i>
        </div>
    </div>
        <div class="block-info-request" id="requestInfo">

            <?php if($arResult['Copy'] && $arResult['Active'] !== 'Y'){?>
                <div class=" cs-input-container" style=" align-items: center; margin-bottom: 15px; flex-flow: row nowrap; display: none;" id="blockActive">
                    <input id="Active"  type="checkbox" class="cs-switch">
                    <span for="Active" class="cs-input-label-checkbox" style="color:red;">Активировать процессы</span>
                </div>
            <?php }?>
            <div class="block-info-container-content" style="align-items: flex-start; max-width: 100%;">
                <div class="block-info-container-content-block-title">
                        <span class="block-info-container-subtitle-text">ФИО</span>
                </div>
                    <div class="block-flex-row --justify-between --nowrap --w100p">
                            <div class="block-info-container-client-box-name" id="NAME"><?= $arResult['item']['fio']?></div>
                            <div class="form-button-circle form-button-circle-24 form-button-circle-gray" id="copyApp" style="display: none;">
                                <i class="fa fa-files-o form-button-circle__copy-gray" aria-hidden="true"></i>
                            </div>
                    </div>
                </div>


            <div class="block-info-container block-flex-columns" >
                <div class="block-info-container-title"> Контактные данные </div>
                <div class="block-flex-row --justify-start --wrap">
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text">Телефон</span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_RATE_WITH_VAT"></div>
                    </div>
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text">email</span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_RATE_WITHOUT_VAT"></div>
                    </div>

                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_RATE_CASH"></div>
                    </div>
                </div>
            </div>


            <div class="block-info-container block-flex-columns" >
                <div class="block-info-container-title"> Резюме </div>
                <div class="block-flex-row --justify-start --wrap">
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_NAME"></div>
                    </div>
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_START_DATE"></div>
                    </div>

                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_WEIGHT"></div>
                    </div>

                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_VOLUME"></div>
                    </div>
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_BODY"></div>
                    </div>
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_TYPE_LOADING"></div>
                    </div>
                    <div class="block-info-container-content">
                        <div class="block-info-container-content-block-title">
                            <span class="block-info-container-subtitle-text"></span>
                        </div>
                        <div class="block-info-container-client-box-name" id="UF_CS_TYPE_UNLOAD"></div>
                    </div>

                </div>
            </div>

            <div class="btn_block">
                <button class="form-small-button form-small-button-blue"  type="submit"
                        onclick="candidatesEdit();">Изменить</button>
            </div>
        </div>
    </div>

</div>
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="/local/lib/js/jquery-ui.min.js"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script defer src="/local/lib/js/ckeditor.js"></script>
<script  src="/pub/assets/js/jquery.fancybox.js"></script>
<script  src="/local/lib/js/jquery.maskedinput.js"></script>
<script defer src="/local/components/hr/candidatesForm/templates/smart/script.js?1309"></script>

<script>


</script>

</html>