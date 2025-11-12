<?

    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $colorScheme = "Light";
?>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css"/>
<!--     Fonts and icons     -->
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link href="/local/lib/css/cassoft/cs-root-blue.css" rel="stylesheet">
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/callControler/menu<?= $colorScheme ?>.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/callControler/menuMob.css"/>
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

    }
</style>

<div class="main-block">
    <div class="input-block">
        <input type="text" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
        <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
        <input type="text" id="apps" name="apps" hidden value='callControler'>

    </div>
    <div class="menu-box">
        <div class="menu-img-box" style=display:none;">
            <img class="menu-img" src=""/>
        </div>
        <div class="" id="user-menu">
            <ul id="menu">
                <li><a href="#" class="click" value="charts">Главная</a></li>
                <li><a href="#" class="click" value="setting">Настройки</a>

            </ul>
        </div>

    </div>

    <nav>
        <div class="navbar mobile" style=" width: 90%!important;">
            <div class="container nav-container">
                <input class="checkbox" type="checkbox" name="" id=""/>
                <div class="hamburger-lines">
                    <span class="line line1"></span>
                    <span class="line line2"></span>
                    <span class="line line3"></span>
                </div>
                <div class="logo">
                    <span>Меню</span>
                </div>
                <div class="menu-items">
                    <li><a class="click menu-items-name" style="color: #ffff!important;" value="charts">Главная</a></li>

                </div>
            </div>
        </div>
    </nav>


    <div id='main' class="container-block"></div>
</div>
<!--<script src="/local/lib/js/js_brokci_shop/jquery-1.11.1.min.js"></script>-->
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/components/dashboard/main_app/templates/callControler/script.js"></script>


</body>