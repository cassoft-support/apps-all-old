<?php

$lead= json_decode($arResult['PROP']['CS_LEAD'], true);
$deal= json_decode($arResult['PROP']['CS_DEAL'], true);
$contact= json_decode($arResult['PROP']['CS_CONTACT'], true);
$company= json_decode($arResult['PROP']['CS_COMPANY'], true);
$quote= json_decode($arResult['PROP']['CS_QUOTE'], true);
//pr($arResult, '');
?>
<head>
    <meta charset="UTF-8">
    <title>CAS-soft</title>
    <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/local/lib/bootstrap/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">

</head>

<style>
    .container-block {
        background: var(--bg-form);!important;
    }
.cs-form-block-label {
    font-size: 12px;
}
    .add-label-check {
        margin-left: 10px;
    }
</style>

<body>
<input id="id" type="text" style="display: none" value="<?=$arResult['ID'] ?>">
<div id="app" class="container container-block" style="">


        <div style=" margin-bottom: 30px; font-size: 26px; font-weight: 400;">Общие настройки </div>
        <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
            <label class="cs-form-block-label" style="margin-left: 20px;">При смене ответственного в Лиде</label>
            <div class="block-flex-column">
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input  name="contact" type="checkbox" class="cs-switch lead" <?php echo ($lead['contact'] == 1) ? 'checked' : '';?> >
                    <span for="contact" class="add-label-check">Контакт</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input name="company" type="checkbox" class="cs-switch lead" <?php echo ($lead['company'] == 1) ? 'checked' : '';?> >
                    <span for="company" class="add-label-check">Компания</span>
                </div>
<!--                <div class="block-flex-row --nowrap --align-center --mr30 ">-->
<!--                    <input  name="invoice" type="checkbox" class="cs-switch lead" --><?php //echo ($lead['invoice'] == 1) ? 'checked' : '';?><!-- >-->
<!--                    <span for="invoice" class="add-label-check">Счет</span>-->
<!--                </div>-->
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input  name="quote" type="checkbox" class="cs-switch lead" <?php echo ($lead['quote'] == 1) ? 'checked' : '';?> >
                    <span for="quote" class="add-label-check">Предложение</span>
                </div>
            </div>
        </div>
    <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
            <label class="cs-form-block-label" style="margin-left: 20px;">При смене ответственного в Сделке</label>
            <div class="block-flex-column">
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input  name="contact" type="checkbox" class="cs-switch deal" <?php echo ($deal['contact'] == 1) ? 'checked' : '';?> >
                    <span for="contact" class="add-label-check">Контакт</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input  name="company" type="checkbox" class="cs-switch deal" <?php echo ($deal['company'] == 1) ? 'checked' : '';?> >
                    <span for="company" class="add-label-check">Компания</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input  name="invoice" type="checkbox" class="cs-switch deal" <?php echo ($deal['invoice'] == 1) ? 'checked' : '';?> >
                    <span for="invoice" class="add-label-check">Счет</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input  name="quote" type="checkbox" class="cs-switch deal" <?php echo ($deal['quote'] == 1) ? 'checked' : '';?> >
                    <span for="quote" class="add-label-check">Предложение</span>
                </div>
            </div>
        </div>
        <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
            <label class="cs-form-block-label" style="margin-left: 20px;">При смене ответственного в Контакте</label>
            <div class="block-flex-column">
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="lead" name="lead" type="checkbox" class="cs-switch contact" <?php echo ($contact['lead'] == 1) ? 'checked' : '';?> >
                    <span for="lead" class="add-label-check">Лид</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input id="deal" name="deal" type="checkbox" class="cs-switch contact"  <?php echo ($contact['deal'] == 1) ? 'checked' : '';?> >
                    <span for="deal" class="add-label-check">Сделка</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input id="company" name="company" type="checkbox" class="cs-switch contact" <?php echo ($contact['company'] == 1) ? 'checked' : '';?> >
                    <span for="company" class="add-label-check">Компания</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input id="invoice" name="invoice" type="checkbox" class="cs-switch contact" <?php echo ($contact['invoice'] == 1) ? 'checked' : '';?> >
                    <span for="invoice" class="add-label-check">Счет</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30">
                    <input id="quote" name="quote" type="checkbox" class="cs-switch contact" <?php echo ($contact['quote'] == 1) ? 'checked' : '';?> >
                    <span for="quote" class="add-label-check">Предложение</span>
                </div>
            </div>
        </div>
        <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
            <label class="cs-form-block-label" style="margin-left: 20px;">При смене ответственного в Компании</label>
            <div class="block-flex-column">
                <div class="block-flex-row --nowrap --align-center --mr30  ">
                    <input id="lead" name="lead" type="checkbox" class="cs-switch company" <?php echo ($company['lead'] == 1) ? 'checked' : '';?> >
                    <span for="lead" class="add-label-check">Лид</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="deal" name="deal" type="checkbox" class="cs-switch company" <?php echo ($company['deal'] == 1) ? 'checked' : '';?> >
                    <span for="deal" class="add-label-check">Сделка</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="contact" name="contact" type="checkbox" class="cs-switch company" <?php echo ($company['contact'] == 1) ? 'checked' : '';?> >
                    <span for="contact" class="add-label-check">Контакт</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="invoice" name="invoice" type="checkbox" class="cs-switch company" <?php echo ($company['invoice'] == 1) ? 'checked' : '';?> >
                    <span for="invoice" class="add-label-check">Счет</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="quote" name="quote" type="checkbox" class="cs-switch company" <?php echo ($company['quote'] == 1) ? 'checked' : '';?> >
                    <span for="quote" class="add-label-check">Предложение</span>
                </div>
            </div>
        </div>
        <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
            <label class="cs-form-block-label" style="margin-left: 20px;">При смене ответственного в Предложении</label>
            <div class="block-flex-column">
                <div class="block-flex-row --nowrap --align-center --mr30  ">
                    <input id="lead" name="lead" type="checkbox" class="cs-switch quote" <?php echo ($quote['lead'] == 1) ? 'checked' : '';?> >
                    <span for="lead" class="add-label-check">Лид</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="deal" name="deal" type="checkbox" class="cs-switch quote" <?php echo ($quote['deal'] == 1) ? 'checked' : '';?> >
                    <span for="deal" class="add-label-check">Сделка</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr15  ">
                    <input id="contact" name="contact" type="checkbox" class="cs-switch quote" <?php echo ($quote['contact'] == 1) ? 'checked' : '';?> >
                    <span for="contact" class="add-label-check">Контакт</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="company" name="company" type="checkbox" class="cs-switch quote" <?php echo ($quote['company'] == 1) ? 'checked' : '';?> >
                    <span for="company" class="add-label-check">Компания</span>
                </div>
                <div class="block-flex-row --nowrap --align-center --mr30 ">
                    <input id="invoice" name="invoice" type="checkbox" class="cs-switch quote" <?php echo ($quote['invoice'] == 1) ? 'checked' : '';?> >
                    <span for="invoice" class="add-label-check">Счет</span>
                </div>
            </div>
        </div>
    <div class="block-flex-columns block-info --mb30 --p10" id="stageBlock" style=" ">
        <label class="cs-form-block-label" style="margin-left: 20px;">Публикация об изменении в Таймлайн</label>
        <div class="block-flex-column">
            <div class="block-flex-row --nowrap --align-center --mr30  ">
                <input id="commentAdd" name="commentAdd" type="checkbox" class="cs-switch commentAdd" <?php echo ($arResult['PROP']['CS_COMMENT_ADD'] == 1) ? 'checked' : '';?> >
                <span for="commentAdd" class="add-label-check">Публиковать</span>
            </div>
        </div>
    </div>

    <div class="btn_block --mb30" id="saveButton">
        <button class="form-small-button form-small-button-blue"  type="submit" onclick="entityUpdate(<?=$arResult['ID'] ?>);">Сохранить</button>
    </div>


</div>


<script defer src="//api.bitrix24.com/api/v1/"></script>
<script src="/local/lib/js/clipboard.min.js"></script>
<script src="/local/components/settings/base/templates/change_assigned/script.js">
</script>
</body>

