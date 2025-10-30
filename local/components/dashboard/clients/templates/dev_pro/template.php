<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$colorScheme = "Dark";
?>
  <link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
  <link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" />
    <!--     Fonts and icons     -->
  <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
  <link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
  <link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
  <link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
  <link href="/local/lib/css/cassoft/cs-root-blue.css" rel="stylesheet">
  <link rel="stylesheet" href="/local/components/brokci/dashboard/templates/dev_pro/menu<?= $colorScheme ?>.css"/>
  <link rel="stylesheet" href="/local/components/brokci/dashboard/templates/dev_pro/menuMob.css"/>
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
        display: block!important;
    }
    .menu-box {
        display: none!important;
    }
    .dashboard-element {
     /*   margin: 10px 0px!important;*/
        min-width: 100%!important;
        max-width: 500px;
    }
    /* CSS для ширины до 575px (включительно) */
}

</style>



   <div class="main-block">
      <input type="text" id="UserAut" name="UserAut" hidden value='<?php echo $arResult['UserAut'] ?>'></input>
      <input type="text" id="country" name="country" hidden value='<?php echo $arResult['country'] ?>'></input>
      <input type="text" id="member_id" name="member_id" hidden value='<?php echo $arResult['member_id'] ?>'></input>
      <input type="text" id="user_id" name="user_id" hidden value='<?php echo $arResult['user_id'] ?>'></input>
      <input type="text" id="apps" name="apps" hidden value='devPro'></input>
      
   </div>
   <div class="menu-box">
      <div class="menu-img-box" style=display:none;">
         <img class="menu-img" src=" https://city.brokci.ru/local/images/brokci55x55.png" />
      </div>
      <div class="" id="user-menu">
         <ul id="menu">
            <li><a href="#" class="click" value="charts">Главная</a></li>
            <?php if ($arResult['admin']) { ?>
            <li><a href="#">Новостройки</a>
               <ul>
                  <li> <a href="#" class="click" value="complex">ЖК</a> </li>
                  <li><a href="#" class="click" value="building">Здания/Блок-секция</a> </li>
                  <li> <a href='javascript:switchTemplate("section")' value="section">Подъезд(секции)</a> </li>
                  <li> <a  href='javascript:switchTemplate("object")' value="object">Объекты</a> </li>
                 
               </ul>
            </li>
            <?}?>
            <li><a href="#">Подбор</a>
               <ul>
               <li> <a href="#" class="click" value="chess">Шахматки</a> </li>
                  <li> <a href="#" class="click" value="favourites">Избранное</a> </li>
                  <li><a href="#" class="click" value="objectCom">Вторичка</a> </li>
                  <li> <a href="#" class="click" value="objectPars">Парсер</a> </li>
                  <li> <a href="#" class="click" value="selection">Подборки</a> </li>
               </ul>
            </li>
            <li> <a href="#" class="click" value="marketing">Выгрузка в рекламу</a> </li>
            <li> <a href="#">Отчеты (скоро)</a>
            <?php if ($arResult['admin']) { ?>
            <li> <a href="#"class="click" value="objectMob">Моб</a>
            <li> <a href="#" class="click" value="block_site">Блоки</a> </li>
            <li> <a href="#" class="click" value="support">Support</a> </li>
            <?php } ?>
            </li>
            <li> <a href="#">Настройки</a>
               <ul>
                  <?php if ($arResult['plans']) { ?>
                     <li> <a href="#" class="click" value="plan_type">Создание типа плана</a> </li>
                     <li><a href="#" class="click" value="plan_edit_b4">Установка плана</a> </li>
                     <li> <a href="#" class="click" value="favouritesStage">Стадии избранных</a> </li>
                    
                  <?php } ?>
                  <?php if ($arResult['marketing']) { ?>
                     <li> <a href="#" class="click" value="marketing_settings">Настройка рекламы</a> </li>
                  <?php } ?>
                  <?php if ($arResult['admin']) { ?>
                     <li> <a href="#" class="click" value="general_settings">Общие настройки</a> </li>
                     
                  <?php } ?>
                  <li> <a href="#" class="click" value="objectStage">Стадии объекта</a> </li>
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
      <div class="navbar mobile" style=" width: 90%!important;">
        <div class="container nav-container">
            <input class="checkbox" type="checkbox" name="" id="" />
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
            <li><a class="click menu-items-name"  style="color: #ffff!important;" value="objectMob">База компании</a></li>
            <li><a class="click menu-items-name"  style="color: #ffff!important;" value="">Парсинг (скоро)</a></li>
            
          </div>
        </div>
      </div>
    </nav>
  <!--</body>

   </div>
   </div>
      
-->
  
   
   <div id='main' class="container-block"></div>
   <!--<script src="/local/lib/js/js_brokci_shop/jquery-1.11.1.min.js"></script>-->
   <script src="/local/lib/js/jquery-3.6.0.min.js"></script>
   <script defer src="//api.bitrix24.com/api/v1/"></script>
   <script defer src="/local/components/brokci/dashboard/templates/dev_pro/script.js"></script>
  
        
  


</body>