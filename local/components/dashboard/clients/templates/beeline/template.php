<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$colorScheme = "Light";

?>

   <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
   
   <link rel="stylesheet" href="/local/components/brokci/dashboard/templates/dev_pro/menu<?= $colorScheme ?>.css">
   <link rel="stylesheet" href="/local/components/brokci/dashboard/templates/dev_pro/menuMob.css">
   <link rel="stylesheet" href="/local/lib/css/cassoft/panel.css">
   <link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css">

<style>

.mobile {
    display: none;
}
.menu-img-box {
    margin-left: 20px;
    height: 35px;
    width: 35px;
    background: #ffff;
    -moz-border-radius: 50px;
    border-radius: 50px;
    overflow: hidden;
    padding: 0;
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
     //   margin: 10px 0px!important;
        min-width: 100%!important;
        max-width: 500px;
    }
    /* CSS для ширины до 575px (включительно) */
}

</style>



   <div class="main-block">
      <input type="text" id="app" name="app" hidden value='beeline'></input>
       <input type="text" id="member_id" name="member_id" hidden value='<?= $arResult['member_id'] ?>'>
       <input type="text" id="user_id" name="user_id" hidden value='<?= $arResult['user_id'] ?>'>
       <input type="text" id="UserAut" name="user_id" hidden value='<?= htmlspecialchars($arResult['UserAut']) ?>'>

   </div>
   <div class="menu-box">
      <div class="menu-img-box" style="">
         <img class="menu-img" src="/pub/images/app/beeline/beeline.jpg" />
      </div>
      <div class="" id="user-menu">
         <ul id="menu">
            <li><a href="#" class="click" value="dashboard">Главная</a></li>
            
           
            <li> <a href="#">Настройки</a>
               <ul>
                  
                  <?php// if ($arResult['admin']) { ?>
                     <li> <a href="#" class="click" value="authSettings">Токен</a> </li>
                     <li> <a href="#" class="click" value="settings">Общие настройки</a> </li>
                     <li> <a href="#" class="click" value="support">Support</a> </li>
                  <?php// } ?>
                 
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
   <body>
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
  </body>

   </div>
   </div>
      
-->
  
   
   <div id='main' class="container-block"></div>
   <div id='panel' class="container-block"></div>
  
   <script src="/local/lib/js/jquery-3.6.0.min.js"></script>
   <script defer src="//api.bitrix24.com/api/v1/"></script>
   <script defer src="/local/components/dashboard/main_app/templates/beeline/script.js"></script>
  
         
     <!--     <script>
          $(document).ready(function() {
                    BX24.init(function() {
                              app.saveFrameWidth();
                    });
                  })
                    </script>-->
</body>