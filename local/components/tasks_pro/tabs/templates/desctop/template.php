<?php

?>

<head>
    <link rel="stylesheet" href="/local/lib/chosen/chosen.min.css">
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
    <style>
        :root {
            --main-font-color:#444;
            --main-font-family: "Roboto", Helvetica, Arial, sans-serif;
        }


    </style>
</head>


<body>

<div id="cs_tasksForm"  class="form-filter-edit" >
    <div class="block-flex-columns --w100p --mb50" style="overflow: hidden;">
        <div style="display:none">
            <input id="member_id" value='<?= $arResult['member_id'] ?>' style="display:none;">
            <input id="app" value="<?= $arResult['app'] ?>" style="display:none;">

        </div>
        <div class="form-section ">
            <div class="form-section-header">
                <div class="form-section-header-title">
                    <span class="form-section-header-title-text" style="">Основные данные</span>
                </div>
                <div class="form-section-header-actions" style="display: none">
                    <span class="form-section-header-edit-lnk" data-target="blockMain">Скрыть</span>
                </div>
            </div>

            <div id="blockMain" class="">
                <div  class="block-flex-columns cs-form-block --mb30 --p15">
                    <div class="block-flex-row --wrap ">
                        <div class="cs-input-container">
                            <select class="js-chosen-tasks cs-input-block cs-input-save" name="country" id="country-quide">
                                <option value="" disabled selected class="option-dis">(Не установлена)</option>
                                <?php foreach ($countryQuide as $key => $value) {
                                    if ($value['ID'] === $country) {
                                        ?>
                                        <option value="<?= $value['ID'] ?>" selected><?= $value['UF_NAME'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $value['ID'] ?>"><?= $value['UF_NAME'] ?></option>
                                        <?php
                                    }
                                } ?>
                            </select>
                            <label for="country-quidey" class="cs-input-label">Страна</label>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
    </div>
</body>
<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script  src="/local/lib/js/jquery-ui.min.js"></script>
<script src="/local/lib/chosen/chosen.jquery.min.js"></script>
<!--<script src="/local/components/tasks_pro/tabs/templates/desctop/js/script.js"></script>-->

<script>
    $(document).ready(function () {
        $('.js-chosen-tasks').chosen({
            console.log('chosen')
            width: '100%',
            no_results_text: 'Совпадений не найдено',
            placeholder_text_single: ' ',
            Placeholder_text_multiple: 'Выберите значения',
            max_selected_options: 5,
            disable_search_threshold: 5
        });
       
        });