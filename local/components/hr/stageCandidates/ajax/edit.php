<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logEdit.txt";
p('start', 'stage', $log);
p($_POST,'post', $log);
$Result=$_POST['value'];
$auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']);
$smartClass = new \CSlibs\B24\Smarts\SmartProcess($auth, "");
$resSmartGuide= $smartClass->smartGuide();
$Result['stage']= $smartClass->smartCategoryStageName($resSmartGuide['Найм персонала']['entityTypeId']);
if($Result['ID']){
    $header = "Редактирование стадии кандидата";
}else{
    $header = "Создание стадии кандидата";
}

?>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css">
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
<link rel="stylesheet" href="/local/lib/css/new/flex.css">
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/control_element.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css">


<style>
    .cs-input-circle{
        width: 30px;
        height:30px;
        border-radius: 50%;
        border: 0;
        cursor: pointer;
        overflow: hidden;
        padding: 0;
    }
</style>
<div class="slider-card slideLeft" style="height: 400px;  min-height: auto;  margin: 0 70px; position: absolute;" >
    <div class=" slider-card-header">
        <span><?=$header?></span>
    </div>


    <div id="panel-close" class="side-panel-label" style="left:25px">
        <div class="side-panel-label-icon-box" title="Закрыть">
            <div class="side-panel-label-icon side-panel-label-icon-close"></div>
        </div><span class="side-panel-label-text"></span>
    </div>

    <div class="add-item" style="display:none;">
        <span for="ID" class="add-label">ID</span>
        <input  type="number" step="1" name="ID" id="ID" value="<?=$Result['ID']?>"  class="fields-input fields-input--add" >
    </div>

    <div class="block-flex-columns cs-form-block --mb30 --p10"  >
        <!--        <label class="cs-form-block-label" style="margin-left: 20px;">Вес товара</label>-->
        <div class="block-flex-row --wrap ">
            <div class="cs-input-container ">
                <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " name="NAME" id="NAME" value="<?=$Result['NAME']?>">
                <label for="NAME" class="cs-input-label">
                    Наименование стадии
                </label>
            </div>
        </div>
        <div class="block-flex-row --wrap ">
            <div class="cs-input-container">
                <select class="js-chosen cs-input-block"   name="CS_TYPE_STAGE" id="CS_TYPE_STAGE" require='true'>
                    <option value="new_action" <?php if($Result['CS_TYPE_STAGE']==="new_action"){echo 'selected';}?> >Начальная стадия</option>
                    <option value="action"<?php if($Result['CS_TYPE_STAGE']==="action"){echo 'selected';}?> >Активная  стадия</option>
                    <option value="end_plus" <?php if($Result['CS_TYPE_STAGE']==="end_plus"){echo 'selected';}?>>Упешная стадия</option>
                    <option value="close" <?php if($Result['CS_TYPE_STAGE']==="close"){echo 'selected';}?>>Провал</option>
                </select>
                <label for="CS_TYPE_STAGE" class="cs-input-label">
                    Тип стадии
                </label>
            </div>
        </div>


            <div class="cs-input-container --mxw50p --w45p">
                <select multiple class="js-chosen cs-input-block" id="CS_STAGE_SMART" name="CS_STAGE_SMART[]">
                    <option  value="" hidden>Не выбрано</option>
                    <?php foreach ($Result['stage'] as $category => $valStage){?>
                    <optgroup label="<?=$category?>">
                        <?php foreach ($valStage as $key =>$val){?>
                            <option value='<?=$key?>'<?php echo(in_array($key, $Result['CS_STAGE_SMART']) ? "selected='selected'" : '') ?> ><?=$val."-(<b>".$category."</b>)" ?></option>
                        <?php }}?>
                </select>
                <label for="TypePay" class="cs-input-label"> Стадии сделок для выгрузки<span class="warning">*</span> </label>
            </div>


        <div class="block-flex-row --wrap ">
            <div class="add-item" >
                <span for="CS_COLOR" class="add-label">Цвет стадии</span>
                <input  type="color" name="CS_COLOR" id="CS_COLOR" value="<?=$Result['CS_COLOR']?>" class="fields-input fields-input--add cs-input-circle" require='false' style="">
            </div>
        </div>
    </div>

    <div class="btn-help-row" style="justify-content: flex-start;">
        <div class="btn-blue">
            <div><i class="fa fa-check-circle fa-2x "></i></div>
            <div><button id="save">Сохранить</button></div>
        </div>
    </div>
    <!-- </form>-->
</div>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="/local/components/hr/stageCandidates/templates/.default/script.js"></script>
<script>
    $(document).ready(function () {

        $('.js-chosen').chosen({
            width: '100%',
            no_results_text: 'Совпадений не найдено',
            placeholder_text_single: 'Выберите значение',
            Placeholder_text_multiple: 'Выберите значения',
            max_selected_options: 5,
            disable_search_threshold: 5
        });
    });
</script>












