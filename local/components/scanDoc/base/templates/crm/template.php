<?php
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

    $log = __DIR__ . "/logTemp.txt";
    p($arResult , "start", $log);
$styleGalCard = "display:none;";
$styleGalForm = "display:flex;";
if($arResult["link"]){
$styleGalCard = "display:flex;";
    $styleGalForm = "display:none;";
}
//$debug->console($arResult, 'arResult');
?>
<style>
   body{
       background:#f9fafb ;
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
   object-fit: cover;}
   .fancybox-thumbs__list a {
       max-width: calc(100% - 4px) !important;
   }
   .fancybox-thumbs__list {
       display: flex;
       flex-direction: column;
   }
   .fancybox-thumbs {
       width: 110px!important;
   }
   .fancybox-show-thumbs .fancybox-inner {
       right: 110px!important;
   }
</style>
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<link href="/local/lib/css/cassoft/dropzone.css" rel="stylesheet" />
<link rel="stylesheet" href="/local/lib/css/cassoft/dropzone.css">
<!--<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-panel.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/checkbox-tr.css">-->
<link rel="stylesheet" href="/local/lib/gallery/fancybox/jquery.fancybox.min.css"/>
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css"/>
<link rel="stylesheet" href="/local/lib/css/new/select.css"/>
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css">

<div class="" id="workscan">

    <input type="text" id ="id" value="<?= $arResult['id']?>" style="display: none;">
    <input type="text" id ="smartId" value="<?= $arResult['smartId']?>" style="display: none;">
    <input type="text" id ="entityTypeId" value="<?= $arResult['entityTypeId']?>" style="display: none;">
    <input type="text" id ="type" value="<?= $arResult['type']?>" style="display: none;">
    <input type="text" id ="app" value='<?= htmlspecialchars($arResult['app'])?>' style="display: none;">
    <div class="block-info">

                <div class="block-info-container" id="galCard" style="<?=$styleGalCard?>">
                    <div class="block-flex-columns --w100p">
                    <div class="block-flex-row --justify-between --wrap" style="display: none;">
                        <div class="block-info-title"> Копии документов</div>
                    </div>
                                <div class="block-flex-row --justify-start --wrap">
                                   <?php if($arResult["link"]){
                                    foreach ($arResult["link"] as $link){?>
                                        <a class="scan-doc" data-caption="" data-fancybox="gallery" href="<?= $link['photo_link']?>">
                                            <div  class="scan-doc-img">
                                                <img src="<?= $link['photo_link']?>">
                                            </div>
                                        </a>
                                    <?php } }?>

                                </div>
                    <div class="btn_block">
                        <button class="form-small-button form-small-button-blue" type="submit" onclick="scanEdit();">Изменить</button>
                    </div>
                </div>
                </div>

                <div class="" id="galForm" style="margin-top: 20px; <?=$styleGalForm?>" >
                  <form id="scanForm" class="block-flex-columns --w100p">
                                <div class="block-flex-row --justify-start --nowrap">
                                                        <div class="block-flex-row --justify-start --nowrap --w100p" >

                                                            <div id="gal" hidden> <?= $arResult['scanDoc']?>   </div>
                                                            <span for="dropzone" class="cs-input-label cs-input-label-select" style="top: auto;">Добавьте скан документа </span>
                                                            <div class="dropzone mt20 sortable" id="dropzone">
                                                                <div style="display: inline; width: 40px;"></div>
                                                            </div>

                                                            <div class="msg-error" data-name="gallery"></div>
                                                        </div>
                                </div>
                      <div class="info" style="display: none;">Начинаем сохранение файлов, процесс может занять некоторое время</div>
                                    <div class="btn_block" style="margin-top: 30px;">
                                        <button class="form-small-button form-small-button-blue" id="save" type="submit" >Сохранить</button>
                                    </div>
                    </form>
                </div>
    </div>
</div>


<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script  src="/local/lib/js/jquery-ui.min.js"></script>
<script  src="/local/lib/gallery/fancybox/jquery.fancybox.min.js"></script>
<script  src="/local/lib/js/dropzone.min.js"></script>
<script  src="/local/lib/js/jquery.maskedinput.js"></script>
<script defer type="text/javascript" src="/local/components/scanDoc/base/templates/crm/script.js?1009"></script>
