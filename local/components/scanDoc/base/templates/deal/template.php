<?php
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/logTemp.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
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
   object-fit: cover;
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

    <input type="text" id ="deal_id" value="<?= $arResult['deal_id']?>" style="display: none;">
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
                  <form id="scanForm" class="block-flex-columns">
                                <div class="block-flex-row --justify-start --nowrap">
                                                        <div class="cs-input-container" >

                                                            <div id="gal" hidden> <?= $arResult['scanDoc']?>   </div>
                                                            <div class="dropzone mt20 sortable" id="dropzone">
                                                                <div style="display: inline; width: 40px;"></div>
                                                            </div>
                                                            <span for="dropzone" class="cs-input-label cs-input-label-select">Добавьте скан документа</span>
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
<script defer type="text/javascript" src="/local/components/scanDoc/base/templates/deal/script.js?1009"></script>
