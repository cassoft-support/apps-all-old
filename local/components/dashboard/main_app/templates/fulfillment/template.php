<?

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$colorScheme = "Dark";
$log= __DIR__."/logTemp.txt";
p("start", "start", $log);
p($arResult, "arResult", $log);
?>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>

<!--     Fonts and icons     -->
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link href="/local/lib/css/cassoft/cs-root-blue.css" rel="stylesheet">
<link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/fulfillment/menuMob.css"/>
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
    #workarea{
        padding: 5px
    }
</style>

<div class="main-block">
    <div class="input-block">
        <input type="text" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
        <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
        <input type="text" id="client_id" name="client_id" hidden value='<?= $arResult['client_id'] ?>'>
        <input type="text" id="UserAut" name="UserAut" hidden value='<?= $arResult['UserAut'] ?>'>
        <input type="text" id="placement" name="placement" hidden value='<?= $arResult['PLACEMENT'] ?>'>
        <input type="text" id="apps" name="apps" hidden value='fulfillment'>

    </div>


    <div class="" id="workareaApp">
        <div class="menu-box">
            <div class="menu-img-box" >
                <img class="menu-img" src=" /pub/images/app/fulfillment/logo.png"/>
            </div>
            <div class="" id="user-menu" >
                <ul id="menu">
                    <li><a href="#" class="click" value="">Главная</a></li>
                    <li><a href="#" class="click" value="product">Товары</a></li>
                    <li><a href="#" class="click" value="applications">Заявки</a></li>
                    <li><a href="#">Настройки</a>
                        <ul>

                            <li><a href="#" class="click" value="stage">Стадии заявки</a></li>
                            <li><a href="#" class="click" value="support">Support</a></li>
                            <li><a href="#" class="click" value="general_settings">Общие настройки</a></li>


                        </ul>
                    </li>
                </ul>
            </div>

        </div>



        <div id='main' class="container-block"></div>
    </div>
</div>
<script type="text/javascript" src="/local/lib/js/jquery-3.6.0.min.js"></script>
<!--<script src="/local/lib/js/js_brokci_shop/jquery-1.11.1.min.js"></script>-->

<!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--<script defer src="//api.bitrix24.com/api/v1/"></script>-->
<script defer src="/local/components/dashboard/main_app/templates/fulfillment/script.js?1249"></script>


</body>