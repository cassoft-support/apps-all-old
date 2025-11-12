<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logEdit.txt";
p($_POST, "start", $log);
if($_POST['member_id']) {
    $memberId = $_POST['member_id'];
}
if($_POST['app']) {
    $app = $_POST['app'];
}
if (!empty($memberId) && $app) {
// $auth = new \CSlibs\B24\Auth\Auth($app, $_POST['authParams'], "");
$auth = new \CSlibs\B24\Auth\Auth($app, [], $memberId);

    if ($_POST['id']) {
        $elId = $_POST['id'];
    }
    if ($elId) {
        $paramsApp = ['ENTITY' => 'messages','filter' => ['id' => $elId ]];
        $resElement = $auth->CScore->call('entity.item.get', $paramsApp)[0];
        if (!empty($resElement['ID'])) {
            $arElement = $resElement['PROPERTY_VALUES'];
            $arElement['id'] = $resElement['ID'];
            $arElement['NAME'] = $resElement['NAME'];
        }
$desc = json_decode($arElement['message']);
    }

// d($arElement['message']);
//    d($desc);
//    d($arElement);
    $button = "Создать";
    $header = "СОЗДАНИЕ РАССЫЛКИ";
    if ($arElement["id"]) {
        $button = "Сохранить";
        $header = "РЕДАКТИРОВАНИЕ РАССЫЛКИ №" . $arElement['id'];
    }

}
$messager_type=[
        'chatApp'=>"ChatApp",
        'wazzup'=>"Wazzup"
];


?>

<!-- НАЧАЛО ФОРМА ДОБАВЛЕНИЯ ЗАЯВКИ  -->
<div class="slider-card " style="width:100%; background: var(--bg-form);">
    <div class=" slider-card-header">
        <div>
            <!--   <img class="cassoft-install-image" src="/pub/images/brokci.png" alt="brokci-logo">-->
        </div>

            <div id="header"> <?php echo $header?></div>

    </div>
    <div id="panel-close" class="side-panel-label" style="display: none;">
        <div class="side-panel-label-icon-box" title="Закрыть">
            <div class="side-panel-label-icon side-panel-label-icon-close"></div>
        </div><span class="side-panel-label-text"></span>
    </div>



        <? if ($error) : ?>
            <span style="color: red"><?=$error?></span><br>
        <? endif; ?>
        <form class="algorithm__form" id="addAlgorithm">
        <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " value="<?=$arElement["id"]?>" name="ID" id="id" style="display:none;">

            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <label class="cs-form-block-label" style="margin-left: 20px;">Общая информация</label>
                <div class="block-flex-row --wrap ">
                    <div class="cs-input-container ">
                        <? $value = $arElement['NAME'] ?? ''; ?>
                        <input class="cs-input-block__text cs-input-block"  type="text" placeholder=" " value='<?=htmlspecialchars($value)?>' name="NAME" id="NAME">
                        <label for="NAME" class="cs-input-label">Название Рассылки</label>
                    </div>

                    <div class="cs-input-container" >
                        <? $value = $arElement['messager_type'] ?? ''; ?>
                        <select class="js-chosen cs-input-block" id="messager_type" name="messager_type">
                            <?php foreach ($messager_type as $key => $val){?>
                                <option <?=$value == $key ? 'selected' : ''?> value="<?= $key?>"><?= $val?></option>
                            <? }?>
                        </select>
                        <label for="weight-quantity" class="cs-input-label">
                            Канал отправки
                        </label>
                    </div>
                </div>
               <div class="description_block" id="descBlock" style=" min-width: 680px; max-width: 920px ">
                           <span for="CS_DESCRIPTION" class="add-label" style="display:none;" >Описание</span>
                           <div class="editable" id="editorGroup" name="CS_DESCRIPTION">
<?=$desc?>
                           </div>
                       </div>
            </div>


            <?// Заявка ?>
            <div class="block-flex-columns cs-form-block --mb30 --p10"  >
                <div class="" style="">
                    <div class="cs-form-block-label">Условия</div>
                </div>
                </div>


            <div id="notification"></div>

            <!--// КНОПКА -->
            <div class="block-flex-columns-center-center">
                <div class="btn_block">
                    <div class="form-small-button-blue" id="uploadMessage" style="line-height: 34px;"><?= $button?></div>
                </div>
            </div>

        </form>
    </div>



<style>

.cs-sortable-list {
max-width: 300px;
}

.cs-sortable-list ul {
        list-style-type: none;
        padding: 0;
    }
.cs-sortable-list li {
        padding: 10px;
        margin: 5px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
    border-radius: 10px;
        cursor: move;
    }
</style>


<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="/local/lib/js/jquery-ui.min.js"></script>
<script src="/local/lib/gallery/fancybox/jquery.fancybox.min.js"></script>
<!--<script src="/local/lib/js/dropzone.min.js"></script>-->
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script defer src="/local/lib/js/ckeditor.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="/local/components/massenger/messager/js/addEl.js"></script>

