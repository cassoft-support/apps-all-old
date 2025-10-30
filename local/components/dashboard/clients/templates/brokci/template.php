<?php

    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    if ($arResult['userFIO'] === 'Черкасов') {
        $colorScheme = "Blur";
        //$colorScheme = "Dark";
    } else {
        $colorScheme = "Blur";
    }


?>
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/brokci/menu<?= $colorScheme ?>.css">
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/brokci/menuMob.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-card-object.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/panel.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-panel.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/cs-root-blue.css">
<link rel="stylesheet" href="/local/lib/chosen/chosen.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-filter.css"/>

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
    }
</style>

<div class="modal-detail"></div>

<div class="main-block">
    <input type="text" id="UserAut" name="UserAut" hidden value='<?php echo $arResult['UserAut'] ?>'>
    <input type="text" id="apps" name="UserAut" hidden value='brokci'>
    <input type="text" id="country" name="country" hidden value='<?php echo $arResult['country'] ?>'>
    <input type="text" id="region_id" name="region_id" hidden value='<?php echo $arResult['region'] ?>'>
    <input type="text" id="city_id" name="city_id" hidden value='<?php echo $arResult['city'] ?>'>
    <input type="text" id="user_id" name="user_id" hidden value='<?php echo $arResult['user_id'] ?>'>
</div>
<div class="menu-box">
    <div class="menu-img-box">
        <img class="menu-img" src=" https://city.brokci.ru/local/images/brokci55x55.png"/>
    </div>
    <div class="" id="user-menu">
        <ul id="menu">
            <?php  if ($arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>
            <li><a href="#" class="click" value="charts">Главная</a></li>
            <?php } ?>
            <li><a href="#">Подбор</a>
                <ul>
                    <li><a href="#" class="click" value="favourites">Избранное</a></li>
                    <li><a href="#" class="click" value="object">База компании</a></li>
<!--                    <li><a href="#" class="click" value="objectPars">Парсер</a></li>-->
                    <li><a href="#" class="click" value="selection">Подборки</a></li>
                    <?php if ($arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>
                            <li><a href="#" class="click" value="favouritesNew">Избранное NEW</a></li>
                            <li><a href="#" class="click" value="selectionNEW">Подборки NEW</a></li>
                            <li><a href="#" class="click" value="objectNEW">База компании NEW</a></li>
                            <li><a href="#" class="click" value="objectParsNEW">Парсер NEW</a></li>
                            <li><a href="#" class="click" value="objectParsNEW2">Парсер NEW2</a></li>

                            <?php } ?>
                </ul>
            </li>

                <?php
                    if ($arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>

                        <li><a href="#" class="click" value="marketing">Выгрузка в рекламу</a></li>

                        <li><a href="#">Отчеты (скоро).</a></li>
                        <li><a href="#" class="click" value="objectEdit">Работа с базой</a>
                        <li><a href="#">Настройки разработчика</a>
                <ul>
                    <li><a href="#" class="click" value="objectMob">Моб</a>

                    <li><a href="#" class="click" value="block_site">Блоки</a>
                    <li><a href="#" class="click" value="test">test запросы</a></li>
                    <li><a href="#" class="click" value="support">Support</a></li>
                    <li><a href="#" class="click" value="transfer">Transfer</a></li>
                    <li><a href="#" class="click" value="update">Обновление</a></li>
                    <li><a href="#" class="click" value="mUp">Обновление маркетинг</a></li>
                    <li><a href="#" class="click" value="setupSite">Настроки сайтов</a></li>
                </ul>
            </li>
            <?php
                } ?>

            <li><a href="#">Настройки</a>
                <ul>
                    <?php
                        if ($arResult['plans'] || $arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>
                            <li><a href="#" class="click" value="plan_type">Создание типа плана</a></li>
                            <li><a href="#" class="click" value="plan_edit_b4">Установка плана</a></li>
                            <li><a href="#" class="click" value="plan_exec">Выполнение плана</a></li>
                            <li><a href="#" class="click" value="favouritesStage">Стадии избранных</a></li>
                            <?php
                        } ?>
                    <?php
                        if (!$arResult['marketing'] || $arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>
                            <li><a href="#" class="click" value="marketing_settings">Настройка рекламы</a></li>
                            <li><a href="#" class="click" value="adsSchedule">Изменение даты выгрузки</a></li>
                            <?php
                        } ?>
                    <?php
                        if ($arResult['admin'] || $arResult['userFIO'] === 'Черкасов' || $arResult['userFIO'] ==='CAS-soft'|| $arResult['userFIO'] ==='Тех.поддержка') { ?>
                            <li><a href="#" class="click" value="general_settings">Общие настройки</a></li>

                            <?php
                        } ?>


                </ul>
                <!--     <li><a href="" class="click" value="help">База знаний</a> </li>
               <li><a href="" class="click" value="galleryUpdate">galleryUpdate</a> </li>-->

                <!-- <li><a href="https://f.cassoft.company/brokci/" target="_blank">Ваши предложения</a> </li>-->
        </ul>
    </div>

</div>

   <div class="menu-mob" id="" style="display:none">
   <div class="">мобильное style="width: auto!important;"
   <a href="#"class="click" value="mob">Моб</a>
   <body>
<nav>
    <div class="navbar mobile" style=" width: 90%!important;">
        <div class="container-mob nav-container">
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
                <li><a class="click menu-items-name" style="color: #ffff!important;" value="objectMob">База компании</a>
                </li>
                <li><a class="click menu-items-name" style="color: #ffff!important;" value="">Парсинг (скоро)</a></li>

            </div>
        </div>
    </div>
</nav>
</body>

</div>

</div>

<div class="obj_cm_head plr" style="display:none">
    <div class="obj_cm_head_l">
        <!--    <a class="add-selection" ><i class="fa fa-question-circle fa-2x"></i>Создать подбор	</a>  -->

    </div>
</div>


<div id='main' class="container-block"></div>
<!--<div id='panel' class="container-block" ></div>-->
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/components/dashboard/main_app/templates/brokci/script.js"></script>
<script src="/local/lib/chosen/chosen.jquery.min.js"></script>


