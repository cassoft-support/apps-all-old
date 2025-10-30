<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CAS-soft</title>
    <link rel="stylesheet" href="/local/components/marketing/cloud.install.dev/templates/.default/style_my.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/style.css">
    <link rel="stylesheet" href="/local/lib/css/select2.css">
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma.css">
    <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
</head>

<style>
    .container-block {
        background: #ffff !important;
    }


</style>

<body>
<div id="app" class="container" style="">

    <div class="row row-settigns">
        <div class="item item-settings">

            <div style="background-color: #ffff; margin-bottom: 50px; font-size: 26px;
    font-weight: 600;">Общие настройки
            </div>
            <div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="UF_CS_KEY_ATI" type="text"
                       placeholder=" " value="<?= $arResult['PROP']['UF_CS_KEY_ATI'] ?>">
                <label for="UF_CS_KEY_ATI" class="cs-input-label">Ключ ATI<span
                            class="warning">*</span></label>
            </div>


            <div class="acor-container">
                <input class="acor-btn" type="checkbox" style="display:none;" name="chacor" id="chacor2"/>
                <label class="acor-container-label  subtitle-block" for="chacor2">Настройки доступа</label>
                <div class="acor-body">
                    <div class="row-cards">

                        <div class="cs-input-container">
                            <select class="cassoft-select select2 cs-select-block" placeholder=" " multiple
                                    id="UF_CS_ADMIN" cassoft-data='<?php
                                echo $arResult['PROP']['UF_CS_ADMIN'] ?>'>
                                <option value="">(Не выбрано)</option>
                            </select>
                            <label id="label_UF_CS_ADMIN" class="cs-input-label">Администратор<span
                                        class="warning">*</span></label>
                        </div>
                        <div class="cs-input-container">
                            <select class="cassoft-select select2 cs-select-block" placeholder=" " multiple
                                    id="UF_CS_ACCOUNTANT" cassoft-data='<?php
                                echo $arResult['PROP']['UF_CS_ACCOUNTANT'] ?>'>
                                <option value="">(Не выбрано)</option>
                            </select>
                            <label id="label_UF_CS_ACCOUNTANT" class="cs-input-label">Бухгалтерия<span
                                        class="warning">*</span></label>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="result"></div>
    <div class="slider-btn-group-top">
        <a href="javascript:entityUpdate('<?= $arResult['ID'] ?>')" class="btn-link-fa-blue"><i
                    class="bi bi-check2-circle"></i>Сохранить</a>
    </div>


</div>
</div>

<script src="/local/lib/js/lightbox.min.js"></script>
<script src="/local/lib/js/select2.js"></script>
<script src="/local/lib/js/clipboard.min.js"></script>
<script src="/local/components/settings/base/templates/logistics_pro/script_my.js">
</script>
</body>

</html>