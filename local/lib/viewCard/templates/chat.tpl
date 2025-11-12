<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    
    <title><?php echo $ULang->t("Чат"); ?></title>
    
    <?php include $config["basePath"] . "/templates/head.tpl"; ?>


  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>">

    <?php include $config["basePath"] . "/templates/header.tpl"; ?>

    <div class="container" >
       
       <nav aria-label="breadcrumb" class="mt15" >
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="/">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
              <meta itemprop="position" content="1">
            </li>
            
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo _link( "user/" . $user["clients_id_hash"] ); ?>">
              <span itemprop="name"><?php echo $Profile->name($user); ?></span></a>
              <meta itemprop="position" content="2">
            </li>
            
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php echo $ULang->t("Чат"); ?></span>
              <meta itemprop="position" content="3">
            </li>
                             
          </ol>

        </nav>
        
        <div class="mt40" ></div>

        <div class="row" >
           <div class="col-lg-3 d-none d-lg-block" >

               <div class="user-sidebar" >

                  <div class="user-avatar" >
                     <div class="user-avatar-img" >
                        <img src="<?php echo $Profile->userAvatar($user["clients_avatar"]); ?>" />
                     </div>  
                     <h4> <?php echo $Profile->name($user); ?> </h4>  
                     <p><?php echo $ULang->t("На"); ?> <?php echo $settings["site_name"]; ?> <?php echo $ULang->t("с"); ?> <?php echo date("d.m.Y", strtotime($user["clients_datetime_add"])); ?></p>  

                     <div class="board-view-stars">
                         
                       <?php echo $data["ratings"]; ?>
                       <div class="clr"></div>   

                     </div>

                  </div>
                  
                  <div class="user-menu d-none d-lg-block" >
                       <?php
                        foreach ($data["menu_links"] as $key => $value) {
                           
                           if($key == "balance"){
                              ?>
                              <a href="<?php echo $value["link"]; ?>" ><?php echo $value["icon"]; ?> <?php echo $value["name"]; ?> <strong><?php echo $Main->price($user["clients_balance"]); ?></strong></a>
                              <?php
                           }elseif($key == "chat"){
                              ?>
                              <a href="<?php echo $value["link"]; ?>" class="active" > <?php echo $value["icon"]; ?> <?php echo $value["name"]; ?></a>
                              <?php
                           }else{
                              ?>
                              <a href="<?php echo $value["link"]; ?>" ><?php echo $value["icon"]; ?> <?php echo $value["name"]; ?></a>
                              <?php
                           }
                           
                        }                       
                       ?>
                  </div>

               </div>
             
           </div>
           <div class="col-lg-9 col-12" >

               <h3 class="mb35 user-title d-block d-lg-none"> <strong><?php echo $ULang->t("Чат"); ?></strong> </h3>

               <div class="module-chat d-none d-lg-block" >

                <?php if($list_chat_users){ ?>

                  <div class="row no-gutters" >
                     <div class="col-lg-4" >
                        <div class="module-chat-users" >

                          <?php echo $list_chat_users; ?>
                          
                        </div>
                     </div>
                     <div class="col-lg-8" >
                        <div class="module-chat-dialog" >

                            <div class="chat-dialog-empty" >
                                <i class="las la-comment"></i>
                                <p><?php echo $ULang->t("Выберите чат для общения"); ?></p>
                            </div>
                          
                        </div>                         
                     </div>
                  </div>

                  <?php }else{ ?>

                    <div class="module-chat-dialog" >

                        <div class="chat-dialog-empty" >
                            <i class="las la-comment"></i>
                            <p><?php echo $ULang->t("У вас пока нет диалогов"); ?></p>
                        </div>
                      
                    </div>                    

                  <?php } ?>


               </div>
               
               <div class="module-chat module-chat-mobile d-block d-lg-none" >

               <div class="module-chat-dialog-prev" >
                 <span> <i class="las la-arrow-left"></i> <?php echo $ULang->t("Назад"); ?> </span>
               </div>

                <?php if($list_chat_users){ ?>

                    <div class="module-chat-users" >

                      <?php echo $list_chat_users; ?>
                      
                    </div>

                    <div class="module-chat-dialog" >

                        <div class="chat-dialog-empty" >
                            <i class="las la-comment"></i>
                            <p><?php echo $ULang->t("Выберите чат для общения"); ?></p>
                        </div>
                      
                    </div>                         

                  <?php }else{ ?>

                    <div class="module-chat-dialog" >

                        <div class="chat-dialog-empty" >
                            <i class="las la-comment"></i>
                            <p><?php echo $ULang->t("У вас пока нет диалогов"); ?></p>
                        </div>
                      
                    </div>                    

                  <?php } ?>


               </div>

               
           </div>
        </div>



    </div>
    
    <div class="mt35" ></div>
 
    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>
    

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-delete" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить диалог?"); ?></h4>            
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue chat-user-delete schema-color-button" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-block" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Внести пользователя в чёрный список?"); ?></h4>    
              <p class="mt15" ><?php echo $ULang->t("Пользователь не сможет писать вам в чатах и оставлять комментарии к объявлениям."); ?></p>        
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue chat-user-block schema-color-button" ><?php echo $ULang->t("Внести"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>
    

  </body>
</html>