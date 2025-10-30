<?

    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $colorScheme = "Dark";
?>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css"/>
<!--     Fonts and icons     -->
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cs-root-blue.css">
<link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/accountant/menuMob.css"/>
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
    .container-block{
        padding: 10px;
    }
</style>

<div class="main-block" id="workarea">
    <div class="input-block">
        <input type="text" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
        <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
       <!-- <input type="text" id="UserAut" name="UserAut" hidden value='<?= htmlspecialchars($arResult['UserAut']) ?>'>-->
        <input type="text" id="apps" name="apps" hidden value='accountant'>

    </div>
    <div class="menu-box">
        <div class="menu-img-box" style=display:none;">
            <img class="menu-img" src=" /local/images/brokci55x55.png"/>
        </div>
        <div class="" id="user-menu">
            <ul id="menu">
                <li><a href="#" class="click" value="charts">Главная</a></li>
                <?php
                  //  if ($arResult['admin'] === 'Y') { ?>

                        <li><a href="#" class="click" value="patents">Патенты</a></li>

                    <?php
                  //  } ?>
                <li><a href="#">Настройки</a>
                    <ul>

                        <?php
                          //  if ($arResult['userFIO'] === 'Черкасов') {
                                //  echo($arResult['userFIO']);
                                ?>
                                <li><a href="#" class="click" value="update">Update</a></li>
                                <li><a href="#" class="click" value="support">Support</a></li>
                                <li><a href="#" class="click" value="general_settings">Общие настройки</a></li>
                                <li><a href="#" class="click" value="stageApps">Стадии патента</a></li>

                                <?php
                           // } ?>

                    </ul>
                    <!--     <li><a href="" class="click" value="help">База знаний</a> </li>
                       <li><a href="" class="click" value="galleryUpdate">galleryUpdate</a> </li>-->

                    <!-- <li><a href="https://f.cassoft.company/brokci/" target="_blank">Ваши предложения</a> </li>-->
            </ul>
        </div>

    </div>

    <!--
       <div class="menu-mob" id="" style="display:none">
       <div class="">мобильное style="width: auto!important;"
       <a href="#"class="click" value="mob">Моб</a>
       <body>-->
    <nav>
        <div class="navbar mobile" style=" width: 90%!important; display:none;">
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
    <!--</body>

     </div>
     </div>

    -->


    <div id='main' class="container-block"></div>
</div>
<!--<script src="/local/lib/js/js_brokci_shop/jquery-1.11.1.min.js"></script>-->
<!--<script type="text/javascript" src="/local/lib/js/jquery-3.6.0.min.js"></script>-->

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/js/moment.min.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="/local/components/logistics/applications/js/bootstrap-table-1.22.1.min.js"></script>
<script defer src="/local/components/dashboard/main_app/templates/accountant/script.js?1238"></script>

<script>
    // console.log('test_log_952');
    function resizeFrame() {
        var currentSize = BX24.getScrollSize();
        console.log(currentSize)
        minHeight = currentSize.scrollHeight;
        var FrameWidth = document.getElementById("workarea").offsetWidth;
        console.log(FrameWidth)
        if (minHeight < 300){
            frameHeight = 300;
        } else{
            frameHeight = minHeight + 100;
        }
        console.log(frameHeight)
        BX24.resizeWindow(FrameWidth, frameHeight);
    }


    $(document).ready(function () {
        BX24.init(function(){
            resizeFrame();
        });
    });
</script>
</body>