
<?php
 if( $_SESSION["geo"]["data"] ){
     $country_alias = $_SESSION["geo"]["data"]["country_alias"];
 }else{
     $country_alias = $settings["country_default"];
 }
?>

<noindex>

<?php 
if( $_SESSION["profile"]["id"] ){ 
?>
<div class="floating-menu" >
   
   <?php if( $route_name != "profile" && $route_name != "chat" && $route_name != "shops" && $route_name != "shop" ){ ?>

   <?php if( $route_name != "ad_create" && $route_name != "auth" ){ ?>
   <a href="<?php echo _link( "ad/create" ); ?>" class="floating-menu-add-ad" style="display: none;" data-tippy-placement="top" title="<?php echo $ULang->t("Разместить объявление"); ?>" >
      <i class="las la-plus"></i>
   </a>
   <?php } ?>

   <a href="<?php echo _link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/favorites" ); ?>" class="floating-menu-favorite" data-tippy-placement="top" title="<?php echo $ULang->t("Избранное"); ?>" >
      <i class="las la-heart-broken"></i>
   </a>

   <?php } ?>
   
   <?php if( $route_name == "shops" ){ ?>
   <a href="#" class="floating-menu-shops open-modal" style="display: none;" data-id-modal="modal-shops-category"  >
      <i class="las la-bars"></i>
   </a>
   <?php } ?>

   <?php if( $route_name == "shop" ){ ?>
   <a href="#" class="floating-menu-shops toggle-shop-filter-menu" style="display: none;"  >
      <i class="las la-bars"></i>
   </a>
   <a href="#" class="floating-menu-shops user-subscribe" data-shop="<?php echo $data["shop"]["clients_shops_id"]; ?>" data-id="<?php echo $data["shop"]["clients_shops_id_user"]; ?>" data-tippy-placement="top" title="<?php if( !$data["user_status_subscribe"] ){ echo $ULang->t("Подписаться"); }else{ echo $ULang->t("Отписаться"); } ?>" style="display: none;"  >
      <?php if( !$data["user_status_subscribe"] ){ echo '<i class="las la-user-plus"></i>'; }else{ echo '<i class="las la-user-minus"></i>'; } ?>
   </a>   
   <?php } ?>

   <?php if( $route_name != "chat" ){ ?>
   <a href="<?php echo _link( "chat" ); ?>" class="floating-menu-message" >
      <div class="floating-menu-message-fragment" ></div>
      <div class="floating-menu-message-count" ><span></span></div>
      <i class="las la-comment-alt"></i>
   </a>
   <?php } ?>

</div>
<?php 

}else{
  ?>

    <div class="floating-menu" >
      
      <?php if( $route_name == "shops" ){ ?>
      <a href="#" class="floating-menu-shops open-modal" style="display: none;" data-id-modal="modal-shops-category"  >
        <i class="las la-bars"></i>
      </a>
      <?php } ?>

      <?php if( $route_name == "shop" ){ ?>
      <a href="#" class="floating-menu-shops toggle-shop-filter-menu" style="display: none;"  >
        <i class="las la-bars"></i>
      </a>  
      <?php } ?>

      <?php if( $route_name != "ad_create" && $route_name != "auth" && $route_name != "shops" && $route_name != "shop" ){ ?>
      <a href="<?php echo _link( "ad/create" ); ?>" class="floating-menu-add-ad" style="display: none;" data-tippy-placement="top" title="<?php echo $ULang->t("Разместить объявление"); ?>" >
          <i class="las la-plus"></i>
      </a>
      <?php } ?>

    </div>

  <?php
}
?>

<div class="modal-custom-bg" id="modal-auth" style="display: none;" >
    <div class="modal-custom" style="max-width: 450px" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <div class="modal-auth-content" >

         <?php include $config["basePath"] . "/templates/include/auth.php"; ?>

      </div>


    </div>
</div>

<div class="modal-custom-bg" id="modal-geo-options" style="display: none;" >
    <div class="modal-custom" style="max-width: 850px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <div class="modal-geo-options-tab" >
          <?php if($data["city_areas"] && $data["city_metro"]){ ?>
                  <div data-id="1" class="active" > <?php echo $ULang->t("Метро"); ?> </div>
                  <div data-id="2" > <?php echo $ULang->t("Районы"); ?> </div>
          <?php }elseif($data["city_areas"]){ ?> 
                  <div data-id="2" class="active" > <?php echo $ULang->t("Районы"); ?> </div>
          <?php }elseif($data["city_metro"]){ ?>
                  <div data-id="1" class="active" > <?php echo $ULang->t("Метро"); ?> </div>
          <?php } ?>        
      </div>
      
      <form class="modal-geo-options-form" >
      <div class="modal-geo-options-tab-content" >
          <div data-tab="1" <?php if($data["city_metro"]){ echo 'class="active"'; } ?> >

           <div class="geo-options-metro" >
           <div class="row" >
           <?php
             echo $Geo->outOptionsMetro( $data );
           ?>
           </div>
           </div>

          </div>
          <div data-tab="2" <?php if(!$data["city_metro"] && $data["city_areas"]){ echo 'class="active"'; } ?> >
             <div class="geo-options-areas" >
             <div class="row" >
             <?php
             echo $Geo->outOptionsArea( $data );
             ?>
             </div>
             </div>
          </div>
      </div>
      </form>

      <div class="row mt30" >
         <div class="col-lg-6" ></div>
         <div class="col-lg-3" > <button class="btn-custom btn-color-light width100 button-click-close mb5" ><?php echo $ULang->t("Закрыть"); ?></button> </div>
         <div class="col-lg-3" > <button class="btn-custom btn-color-blue width100 filter-accept mb5" ><?php echo $ULang->t("Применить"); ?></button> </div>
      </div>

    </div>
</div>

<div class="modal-custom-bg" id="modal-geo" style="display: none;" >
    <div class="modal-custom" style="max-width: 800px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <div class="modal-geo-list" >
          <h4><i class="las la-map-marker"></i> <?php echo $ULang->t("Город или регион"); ?></h4>

          <div class="container-custom-search" >
            <input type="text" autocomplete="nope" class="form-control modal-geo-search" value="<?php echo $Geo->change()["name"]; ?>" >
            <div class="custom-results modal-geo-change-city" ></div>
          </div>

          <div class="geo-list-popular modal-geo-change-city" >

               <?php 
               echo $Geo->cityDefault($country_alias,30);
               ?>                              

          </div>
      </div>

    </div>
</div>


<div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-notification" >
    <div class="modal-custom animation-modal" style="max-width: 400px" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>
      
      <div class="modal-notification-content" >
          <i class="las la-check"></i>

          <h4 class="modal-notification-text" ></h4>            
      </div>

    </div>
</div>

<div class="modal-custom-bg"  id="modal-auth-recovery" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 450px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <h4> <strong><?php echo $ULang->t("Восстановление пароля"); ?></strong> </h4>

      <div class="mt30" ></div>

      <input type="text" class="form-control input-style-custom auth-forgot-login" placeholder="<?php echo $ULang->t("Телефон или электронная почта"); ?>" >
      <div class="msg-error mb10" data-name="user_recovery_login" ></div>

      <div class="mt30" ></div>

      <div class="row" >
         <div class="col-lg-7" >
           <button class="button-style-custom schema-color-button color-green mb10 auth-forgot" ><span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> <?php echo $ULang->t("Восстановить пароль"); ?></button>
         </div>            
      </div>

    </div>
</div>

<div class="modal-custom-bg bg-click-close"  id="modal-auth-recovery-success" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 450px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <h4> <strong><?php echo $ULang->t("Восстановление пароля"); ?></strong> </h4>

      <div class="mt30" ></div>

      <p></p>

      <div class="mt30" ></div>

    </div>
</div>

<div class="modal-custom-bg bg-click-close"  id="modal-auth-block" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 450px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <h4 style="color: red;" > <strong><?php echo $ULang->t("Ваш аккаунт заблокирован!"); ?></strong> </h4>

      <div class="mt30" ></div>

      <p><?php echo $ULang->t("Если вы не согласны с нашим решением — напишите в службу поддержки"); ?></p>

      <div class="mt30" ></div>

      <div class="row" >
         <div class="col-lg-7" >
           <a class="button-style-custom schema-color-button color-green mb10" href="<?php echo _link("feedback"); ?>" ><?php echo $ULang->t("Написать в поддержку"); ?></a>
         </div>            
      </div>

    </div>
</div>

<div class="modal-custom-bg bg-click-close"  id="modal-auth-delete" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 450px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <h4 style="color: red;" > <strong><?php echo $ULang->t("Ваш аккаунт удален!"); ?></strong> </h4>

      <div class="mt30" ></div>

      <div class="row" >
         <div class="col-lg-7" >
           <a class="button-style-custom schema-color-button color-green mb10" href="<?php echo _link("feedback"); ?>" ><?php echo $ULang->t("Написать в поддержку"); ?></a>
         </div>            
      </div>

    </div>
</div>

<div class="modal-custom-bg bg-click-close"  id="modal-balance" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 500px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <div class="text-center" >
        <span class="circle-icon" > <i class="las la-wallet"></i> </span>
      </div>

      <h4 class="text-center" > <strong><?php echo $ULang->t("Недостаточно средств для оплаты!"); ?></strong> </h4>

      <div class="mt30" ></div>

      <h5 class="text-center" ><?php echo $ULang->t("Ваш баланс"); ?> <strong class="modal-balance-summa" ></strong> </h5>

      <div class="mt30" ></div> 

      <div class="row" >
         <div class="col-lg-3" ></div>
         <div class="col-lg-6" >
           <a class="button-style-custom schema-color-button color-green mb10" href="<?php echo _link("user/".$_SESSION["profile"]["data"]["clients_id_hash"]."/balance"); ?>" ><?php echo $ULang->t("Пополнить"); ?></a>
         </div>
         <div class="col-lg-3" ></div>            
      </div>

    </div>
</div>

<div class="modal-custom-bg bg-click-close"  id="modal-services-access" style="display: none;" >
    <div class="modal-custom animation-modal" style="max-width: 500px;" >

      <span class="modal-custom-close" ><i class="las la-times"></i></span>

      <div class="text-center" >
        <span class="circle-icon" > <i class="las la-check"></i> </span>
      </div>

      <h4 class="text-center" > <strong><?php echo $ULang->t("Услуга успешно подключена!"); ?></strong> </h4>

      <div class="mt30" ></div> 

      <div class="row" >
         <div class="col-lg-3" ></div>
         <div class="col-lg-6" >
            <button class="button-style-custom schema-color-button width100 button-click-close color-light mb10" ><?php echo $ULang->t("Закрыть"); ?></button>
         </div>
         <div class="col-lg-3" ></div>            
      </div>

    </div>
</div>
<div class="block-cookies" >
   <p>
     <?php echo $ULang->t("Этот Сайт использует файлы cookies для более удобной работы пользователей с ним. Продолжая любое дальнейшее использование Сайта, Вы соглашаетесь с этим. Более подробная информация доступна в"); ?> <a href="<?php echo _link("politika-cookie"); ?>"><?php echo $ULang->t("Политики использования cookie"); ?></a>
   </p>
   <span class="btn-custom btn-color-blue" ><?php echo $ULang->t("Понятно"); ?></span>
</div>

<div class="lang-js-1 lang-js" ><?php echo $ULang->t("Аукцион завершен"); ?></div>
<div class="lang-js-2 lang-js" ><?php echo $ULang->t("минут"); ?></div>
<div class="lang-js-3 lang-js" ><?php echo $ULang->t("секунд"); ?></div>
<div class="lang-js-4 lang-js" ><?php echo $ULang->t("Выбрано"); ?></div>
<div class="lang-js-5 lang-js" ><?php echo $ULang->t("Добавить"); ?></div>
<div class="lang-js-6 lang-js" ><?php echo $ULang->t("Аукцион завершен"); ?></div>
<div class="lang-js-7 lang-js" ><?php echo $ULang->t("Скрыть параметры"); ?></div>
<div class="lang-js-8 lang-js" ><?php echo $ULang->t("Все параметры"); ?></div>
<div class="lang-js-9 lang-js" ><?php echo $ULang->t("Вы действительно хотите удалить страницу?"); ?></div>

</noindex>

<?php if( $visible_footer == true ){ ?>


<div class="footer-bg" >

<?php if( $visible_cities == true && !$settings["city_id"] ){ ?>
<div class="footer-list-cities" >
<div class="container" >
<?php echo $Geo->cityDefaultFooter($country_alias); ?>
</div>
</div>
<?php } ?>

<footer>
   
   <div class="container" >
   <div class="row" >

     <div class="col-lg-9 col-12" >
      
        <p class="footer-list-text" >
          © <?php echo date("Y"); ?> <?php echo $settings["title"]; ?>
        </p>
      
        <div class="footer-list-link" >
          <a href="<?php echo _link("rules"); ?>"><?php echo $ULang->t("Правила сервиса"); ?></a>
          <a href="<?php echo _link("polzovatelskoe-soglashenie"); ?>"><?php echo $ULang->t("Пользовательское соглашение"); ?></a>
          <a href="<?php echo _link("feedback"); ?>"><?php echo $ULang->t("Служба поддержки"); ?></a>
        </div>

     </div>
     <div class="col-lg-3 col-12 text-right" >
      
        <a href="<?php echo _link("ad/create"); ?>" class="btn-custom btn-footer-icon-gradient" ><i class="las la-plus"></i>  <?php echo $ULang->t("Разместить объявление"); ?> </a>  

     </div>



   </div>
   </div>
  
</footer>


</div>


<?php

  }
  
  echo $Main->assets($config["js_plugins"]);
  echo $Main->pwa();
  echo $settings["code_script"];
  
?>