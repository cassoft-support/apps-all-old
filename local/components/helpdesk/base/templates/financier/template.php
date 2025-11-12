<?php
//d($arResult);
?>
<head>
    <meta charset="UTF-8">
    <title>CAS-soft</title>
    <link rel="stylesheet" href="/local/lib/bootstrap/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/local/components/marketing/cloud.install.dev/templates/.default/style_my.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/style.css">
    <link rel="stylesheet" href="/local/lib/css/select2.css">
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">
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

            <div style="background-color: #ffff; margin-bottom: 50px; font-size: 26px; font-weight: 600;">Общие настройки </div>
                <div class="block-flex-row">
            <div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="CS_HH_KEY" type="text"  placeholder=" " value="<?= $arResult['PROP']['CS_HH_KEY'] ?>">
                <label for="CS_HH_KEY" class="cs-input-label">Ключ HR<span class="warning">*</span></label>
            </div>
<div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="CS_HH_ID" type="text" placeholder=" " value="<?= $arResult['PROP']['CS_HH_ID'] ?>">
                <label for="CS_HH_ID" class="cs-input-label">ID HR<span class="warning">*</span></label>
            </div>
<div class="cs-input-container">
                <input class="cs-input-block__text cs-input-block" id="CS_RR_KEY" type="text" placeholder=" " value="<?= $arResult['PROP']['CS_RR_KEY'] ?>">
                <label for="CS_RR_KEY" class="cs-input-label">Ключ Работа ру<span  class="warning">*</span></label>
            </div>
                </div>

            <div class="acor-container">
                <input class="acor-btn" type="checkbox" style="display:none;" name="chacor" id="chacor2"/>
                <label class="acor-container-label  subtitle-block" for="chacor2">Настройки доступа</label>
                <div class="acor-body">
                    <div class="row-cards">

                        <div class="cs-input-container">
                            <select class="cassoft-select select2 cs-select-block" placeholder=" " multiple
                                    id="CS_ADMIN" cassoft-data='<?php echo $arResult['PROP']['CS_ADMIN'] ?>'>
                                <option value="">(Не выбрано)</option>
                            </select>
                            <label id="label_CS_ADMIN" class="cs-input-label">Администратор<span
                                        class="warning">*</span></label>
                        </div>
                        <div class="cs-input-container">
                            <select class="cassoft-select select2 cs-select-block" placeholder=" " multiple
                                    id="CS_RECRUITER" cassoft-data='<?php echo $arResult['PROP']['CS_RECRUITER'] ?>'>
                                <option value="">(Не выбрано)</option>
                            </select>
                            <label id="label_CS_RECRUITER" class="cs-input-label">Отдел HR<span
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
<script src="/local/components/settings/base/templates/financier/script_my.js?1907">
</script>
</body>

</html>