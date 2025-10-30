<?php
//d($arParams);
// Ваши данные приложения
$clientId = 'G0CFT2QTMUDTRFKBL72N13SBFRTN1Q3GI916F91SDV5VMM503Q5PDNQ6QCLV54NU';
$redirectUri = 'https://app.cassoft.ru/cassoftApp/market/hr/ajax/handlerHh.php';

$keySetup = json_decode($arResult['PROP']['CS_HH_KEY'], true);
//pr($arResult, '');
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
$params =[
        'ACTIVE'=>'Y',
    'USER_TYPE' => 'employee'
];
foreach ($auth->CScore->batch('user.search', $params) as $resUser){
    $users[$resUser['ID']]= $resUser['LAST_NAME']." ".$resUser['NAME'];
}
//pr($users);
$btnDisplay = "";

if(!empty($keySetup) && array_key_exists($arParams['user_id'], $keySetup)){
    $btnDisplay = "display:none;";
}
// URL для авторизации
$authUrl = 'https://hh.ru/oauth/authorize?' . http_build_query([
        'response_type' => 'code',
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'state' => $arParams['auth']['member_id']."|".$arParams['user_id'],
    ]);
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
    .form-small-button {
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .form-block-title{
        font-weight: 700;
        margin-bottom: 30px;
    }
</style>

<body>
<div id="app" class="container" style="">

    <div class="row row-settigns">
        <div class="item item-settings">

            <div style="background-color: #ffff; margin-bottom: 50px; font-size: 26px; font-weight: 600;">Общие настройки </div>
                <div class="block-flex-row" style="margin-bottom: 50px">
                    <div class="block-flex-columns">
                        <div class="form-block-title">Подключенные пользователи</div>
                        <?php
                        foreach ($keySetup as $key =>$value) {?>
<div class="block-flex-row --justify-start --align-center">
    <div class="--mr15">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
                            <div class=""> <?= $users[$key] ?></div>

</div>
                       <?php } ?>


                    <div class="btn_block" style="<?= $btnDisplay?>">
                        <a class="form-small-button form-small-button-blue " target="_blank" href="<?= htmlspecialchars($authUrl) ?>" ><span>Подключить HH</span> </a>
                    </div>
                    </div>
                    </div>

                    <div class="block-flex-row" style="display: none">
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
<script src="/local/components/settings/base/templates/hr_pro/script_my.js?1907">
</script>
</body>

</html>