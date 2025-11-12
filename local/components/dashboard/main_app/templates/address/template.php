<?

    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $colorScheme = "Dark";
?>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>

<!--     Fonts and icons     -->
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link href="/local/lib/css/cassoft/cs-root-blue.css" rel="stylesheet">
<link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/logistics_pro/menuMob.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/panel.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css?020223"/>

<style>

    .mobile {
        display: none;
    }

    @media (max-width: 400px) {
        .mobile {
            display: block;
        }

        .menu-mob {
            display: block !important;
        }

        .menu-box {
            display: none !important;
        }

        .dashboard-element {
            /*   margin: 10px 0px!important;*/
            min-width: 100% !important;
            max-width: 500px;
        }

        /* CSS для ширины до 575px (включительно) */
    }

    .main-block {
        min-height: 1200px;
    }
</style>

<div class="main-block">
    <div class="input-block">
        <input type="text" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
        <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
        <input type="text" id="UserAut" name="user_id" hidden value='<?= $arResult['UserAut'] ?>'>
        <input type="text" id="placement" name="user_id" hidden value='<?= $arResult['PLACEMENT'] ?>'>
        <input type="text" id="apps" name="apps" hidden value='address'>

    </div>



    <div class="menu-box">
        <div class="menu-img-box" >
            <img class="menu-img" src=" /local/images/brokci55x55.png"/>
        </div>
        <div class="" id="user-menu" >
            <ul id="menu">
                <li><a href="#" class="click" value="">Главная</a></li>
                <li><a href="#">Настройки</a>
                    <ul>

                        <?php
                        if ($arResult['userFIO'] === 'Черкасов') {
                            //  echo($arResult['userFIO']);
                            ?>
                            <li><a href="#" class="click" value="support">Support</a></li>
                            <li><a href="#" class="click" value="general_settings">Общие настройки</a></li>
                            <?php
                        } ?>

                    </ul></li>
            </ul>
        </div>

    </div>



    <div id='main' class="container-block"></div>
</div>
<script type="text/javascript" src="/local/lib/js/jquery-3.6.0.min.js"></script>
<!--<script src="/local/lib/js/js_brokci_shop/jquery-1.11.1.min.js"></script>-->

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/components/dashboard/main_app/templates/address/script.js"></script>


</body>