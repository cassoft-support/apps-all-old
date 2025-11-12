<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    
    <title><?php echo $ULang->t("Вход в личный кабинет"); ?></title>
    
    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body  data-prefix="<?php echo $config["urlPrefix"]; ?>" style="background-color: white!important;" >

   <div class="container-fluid no-padding h-100" >

     <div class="row no-gutters" >
        <div class="col-xl-5 col-lg-5 col-md-5 d-none d-md-block" >

           <div class="auth-block-left" >

                <div  class="auth-block-left-box" >

                  <h3 class="auth-left-box-title" ><?php echo $settings["title"]; ?></h3>
                  <p class="auth-left-box-desc mb40" ><?php echo $ULang->t("Удобный сервис, который позволяет быстро и безопасно продавать и покупать товары онлайн."); ?></p>
                  
                  <ul>
                    <li>
                      <div class="ul-list-icon" > <span style="background-color: <?php echo randomColor(); ?>" ><i class="las la-comment-alt"></i></span> </div>
                      <div class="ul-list-title" >
                        <h5><?php echo $ULang->t("Общайтесь"); ?></h5>
                        <p><?php echo $ULang->t("по объявлениям в чатах"); ?></p>
                      </div>
                    </li>
                    <li>
                      <div class="ul-list-icon" > <span style="background-color: <?php echo randomColor(); ?>" ><i class="las la-plus"></i></span> </div>
                      <div class="ul-list-title" >
                        <h5><?php echo $ULang->t("Размещайте"); ?></h5>
                        <p><?php echo $ULang->t("объявления бесплатно"); ?></p>
                      </div>                      
                    </li>
                    <li>
                      <div class="ul-list-icon" > <span style="background-color: <?php echo randomColor(); ?>" ><i class="las la-percent"></i></span> </div>
                      <div class="ul-list-title" >
                        <h5><?php echo $ULang->t("Покупайте со скидкой"); ?></h5>
                        <p><?php echo $ULang->t("по безопасной сделке"); ?></p>
                      </div>                      
                    </li> 
                    <li>
                      <div class="ul-list-icon" > <span style="background-color: <?php echo randomColor(); ?>" ><i class="las la-store-alt"></i></span> </div>
                      <div class="ul-list-title" >
                        <h5><?php echo $ULang->t("Продавайте товары"); ?></h5>
                        <p><?php echo $ULang->t("Просто и безопасно"); ?></p>
                      </div>                      
                    </li>                                                                                                   
                  </ul>

                </div>

           </div>
          
        </div>
        <div class="col-xl-7 col-lg-7 col-md-7" >
          
           <div class="auth-block-right" >
               
               <div class="auth-block-right-box" >

                  <?php include $config["basePath"] . "/templates/include/auth.php"; ?>

               </div>

           </div>

        </div>
     </div>
     
   </div>



   <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>