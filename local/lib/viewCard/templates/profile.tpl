<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    
    <title><?php echo $data["page_name"]; ?></title>
    
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
            
            <?php if($data["advanced"]){ ?>
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php echo $data["page_name"]; ?></span>
              <meta itemprop="position" content="3">
            </li>
            <?php } ?>
                             
          </ol>

        </nav>
        
        <div class="mt40" ></div>

        <div class="row" >
           <div class="col-lg-3" >

               <div class="user-sidebar mb30" >

                  <div class="user-avatar" >
                     <div class="user-avatar-img" >
                        <?php if($data["advanced"]){ ?>
                        <span class="user-avatar-replace" > <i class="las la-sync-alt"></i> </span>
                        <?php } ?>
                        <img src="<?php echo $Profile->userAvatar($user["clients_avatar"]); ?>" />
                     </div>  
                     <h4> <?php echo $Profile->name($user); ?> </h4>  
                     <p><?php echo $ULang->t("На"); ?> <?php echo $ULang->t($settings["site_name"]); ?> <?php echo $ULang->t("с"); ?> <?php echo date("d.m.Y", strtotime($user["clients_datetime_add"])); ?></p>  

                     <div class="board-view-stars">
                         
                       <?php echo $data["ratings"]; ?>
                       <div class="clr"></div>   

                     </div>

                  </div>
                  
                  <?php if($data["advanced"]){ ?>

                  <div class="user-menu d-none d-lg-block" >

                       <?php
                        foreach ($data["menu_links"] as $key => $value) {
                           
                           if($key == "balance"){
                              ?>
                              <a href="<?php echo $value["link"]; ?>" <?php if($action == $key){ echo 'class="active"'; } ?> ><?php echo $value["icon"]; ?> <?php echo $value["name"]; ?> <strong><?php echo $Main->price($user["clients_balance"]); ?></strong></a>
                              <?php
                           }elseif($key == "chat"){
                              ?>
                              <a href="<?php echo $value["link"]; ?>" <?php if($action == $key){ echo 'class="active"'; } ?> > <?php echo $value["icon"]; ?> <?php echo $value["name"]; ?></a>
                              <?php
                           }else{
                              ?>
                              <a href="<?php echo $value["link"]; ?>" <?php if($action == $key){ echo 'class="active"'; } ?> ><?php echo $value["icon"]; ?> <?php echo $value["name"]; ?></a>
                              <?php
                           }
                           
                        }                       
                       ?>

                  </div>

                  <div class="d-none d-lg-block" >
                  <hr>
                  <p class="small-title mt0" ><?php echo $ULang->t("Поделиться профилем"); ?></p>
                  <?php echo $data["share"]; ?>
                  </div>

                  <form id="user-form-avatar" ><input type="file" name="image" /></form>
                  <?php }else{

                    ?>
                    <div class="mt15" ></div>
                    <?php
                    
                    if( $action != "reviews" ){
                        ?>
                        <a class="button-style-custom schema-color-button color-blue mb5" href="<?php echo _link("user/".$user["clients_id_hash"]."/reviews"); ?>"> <span><?php echo $ULang->t("Отзывы"); ?>(<?php echo count($data["reviews"]); ?>)</span> </a>
                        <?php
                    }else{
                      if(!$data["status_publication_review"]){
                        ?>
                        <span <?php echo $Main->modalAuth( ["attr"=>'class="button-style-custom color-green open-modal mt15 mb5" data-id-modal="modal-user-add-review"', "class"=>"button-style-custom color-green mt15 mb5"] ); ?> ><?php echo $ULang->t("Добавить отзыв"); ?></span>
                        <?php
                      }
                    }

                    if(!$data["locked"]){ ?>
                    <div <?php echo $Main->modalAuth( ["attr"=>'class="open-modal button-style-custom color-light" data-id-modal="modal-confirm-block"', "class"=>"button-style-custom color-light"] ); ?> ><?php echo $ULang->t("Заблокировать"); ?></div>
                    <?php }else{ ?>
                    <div <?php echo $Main->modalAuth( ["attr"=>'class="profile-user-block button-style-custom color-light" data-id="'.$user["clients_id"].'" ', "class"=>"profile-user-block button-style-custom color-light"] ); ?> ><?php echo $ULang->t("Разблокировать"); ?></div>
                    <?php }

                  } ?>

               </div>
             
           </div>
           <div class="col-lg-9 min-height-600" >
               
               <?php if($data["advanced"]){ ?>
               <div class="user-warning-seller-safety" >
                   <span class="warning-seller-safety-close" ><i class="las la-times"></i></span>
                   <div class="row no-gutters" >
                       <div class="col-lg-8 col-md-8 col-sm-8 col-12" >
                          <div class="seller-safety-text" >
                             <h4><strong><?php echo $ULang->t("Не дайте себя обмануть!"); ?></strong></h4>
                             <p><?php echo $ULang->t("Узнайте, как уберечь свой кошелёк от злоумышленников"); ?></p>
                             <a href="#" class="open-modal" data-id-modal="modal-seller-safety" ><?php echo $ULang->t("Советы по безопасности"); ?></a>
                          </div>
                       </div>
                       <div class="col-lg-4 col-md-4 col-sm-4 d-none d-md-block" >
                          <div class="seller-safety-img" ></div>
                       </div>
                   </div>
               </div>
               <?php } ?>

               <?php if($action == "ad" || !$action){ ?>

               <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>
               
               <div class="user-menu-tab" >
                 <div data-id-tab="ad" <?php if($action == "ad" || !$action){ echo 'class="active"'; } ?> > <?php if($data["ad"]["count"]){ echo $data["ad"]["count"] . " " . $ULang->t('в продаже'); }else{ echo $ULang->t('В продаже'); } ?></div>
                 <div data-id-tab="sold" > <?php if($data["sold"]["count"]){ echo $data["sold"]["count"] . " " . $ULang->t('продано'); }else{ echo $ULang->t('Продано'); } ?> </div>
                 <?php if($data["advanced"]){ ?>
                 <div data-id-tab="archive" > <?php if($data["archive"]["count"]){ echo $data["archive"]["count"] . " " . $ULang->t('в архиве'); }else{ echo $ULang->t('В архиве'); } ?> </div>
                 <?php } ?>
               </div>

               <div class="user-menu-tab-content <?php if($action == "ad" || !$action){ echo 'active'; } ?>" data-id-tab="ad" >
                  
                  <div class="row no-gutters gutters10" >
                  <?php
                    if($data["ad"]["all"]){

                        foreach ($data["ad"]["all"] as $key => $value) {
                           include $config["basePath"] . "/templates/include/user_ad_grid.php";
                        }

                        ?>
                        
                        <div class="col-lg-12" >
                          <ul class="pagination justify-content-center mt15">  
                           <?php echo out_navigation( array("count"=>$data["ad"]["count"], "output" => $settings["catalog_out_content"], "url"=>"", "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
                          </ul>
                        </div>

                        <?php

                    }else{
                       ?>
                       <div class="user-block-no-result" >

                          <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                          <p><?php echo $ULang->t("Все созданные объявления будут отображаться на этой странице."); ?></p>
                         
                       </div>
                       <?php
                    }
                  ?>
                  </div>
                 
               </div>

               <div class="user-menu-tab-content <?php if($action == "sold"){ echo 'active'; } ?>" data-id-tab="sold" >
                  
                  <div class="row no-gutters gutters10" >
                  <?php
                    if($data["sold"]["all"]){

                        foreach ($data["sold"]["all"] as $key => $value) {
                           include $config["basePath"] . "/templates/include/user_ad_grid.php";
                        }

                        ?>
                        
                        <div class="col-lg-12" >
                          <ul class="pagination justify-content-center mt15">  
                           <?php echo out_navigation( array("count"=>$data["sold"]["count"], "output" => $settings["catalog_out_content"], "url"=>"", "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
                          </ul>
                        </div>

                        <?php

                    }else{
                       ?>
                       <div class="user-block-no-result" >

                          <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                          <p><?php echo $ULang->t("Все проданные товары будут отображаться на этой странице."); ?></p>
                         
                       </div>
                       <?php
                    }
                  ?>
                  </div>
                 
               </div>
               
               <?php if($data["advanced"]){ ?>
               <div class="user-menu-tab-content <?php if($action == "archive"){ echo 'active'; } ?>" data-id-tab="archive" >
                  
                  <div class="row no-gutters gutters10" >
                  <?php
                    if($data["archive"]["all"]){

                        foreach ($data["archive"]["all"] as $key => $value) {
                           include $config["basePath"] . "/templates/include/user_ad_grid.php";
                        }

                        ?>
                        
                        <div class="col-lg-12" >
                          <ul class="pagination justify-content-center mt15">  
                           <?php echo out_navigation( array("count"=>$data["archive"]["count"], "output" => $settings["catalog_out_content"], "url"=>"", "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
                          </ul>
                        </div>

                        <?php

                    }else{
                       ?>
                       <div class="user-block-no-result" >

                          <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                          <p><?php echo $ULang->t("Все объявления помещенные в архив будут отображаться на этой странице."); ?></p>
                         
                       </div>
                       <?php
                    }
                  ?>
                  </div>
                 
               </div>
               <?php } ?>
            

               <?php }elseif($action == "balance"){ ?>

               <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>

               <div class="user-menu-tab" >
                 <div data-id-tab="balance" <?php if($action == "balance"){ echo 'class="active"'; } ?> > <?php echo $ULang->t("Пополнение баланса"); ?> </div>
                 <div data-id-tab="history" > <?php echo $ULang->t("История платежей"); ?> </div>
               </div>

               <div class="user-menu-tab-content <?php if($action == "balance"){ echo 'active'; } ?>" data-id-tab="balance" >
                
                <form method="POST" class="form-balance" >

                <div class="module-balance" >
                   
                   <h5> <span class="label-count-number" >1</span> <?php echo $ULang->t("Выберите способ оплаты"); ?></h5>

                   <div class="user-balance-payment" >

                      <?php
                        if(count($data["payments"])){
                           foreach ($data["payments"] as $key => $value) {
                               ?>
                               <div title="<?php echo $value["name"]; ?>" class="user-change-pay" >
                                <span><img src="<?php echo Exists($config["media"]["other"], $value["logo"], $config["media"]["no_image"]); ?>" ></span>
                                <input type="radio" name="payment" value="<?php echo $value["code"]; ?>" >
                               </div>
                               <?php
                           }
                        }else{
                           ?>
                           <p><?php echo $ULang->t("У вас нет ни одной платежной системы"); ?></p>
                           <?php
                        }
                      ?>

                   </div> 

                   <h5 class="mt35" > <span class="label-count-number" >2</span> <?php echo $ULang->t("Сумма пополнения"); ?></h5>

                   <div class="user-balance-summa" >

                        <div>
                          <div>
                            <p><?php echo $Main->price(100); ?></p>
                            <?php if($settings["bonus_program"]["balance"]["status"]){ ?>
                            <span>+ <?php echo $Main->price( $Profile->calcBonus(100) ); ?> <?php echo $ULang->t("бонус"); ?></span>
                            <?php } ?>
                            <input type="radio" name="amount" value="100" >
                          </div>
                        </div>

                        <div>
                          <div>
                             <p><?php echo $Main->price(300); ?></p>
                             <?php if($settings["bonus_program"]["balance"]["status"]){ ?>
                             <span>+ <?php echo $Main->price( $Profile->calcBonus(300) ); ?> <?php echo $ULang->t("бонус"); ?></span>
                             <?php } ?>    
                             <input type="radio" name="amount" value="300" >                        
                          </div>
                        </div>

                        <div>
                          <div>
                             <p><?php echo $Main->price(500); ?></p>
                             <?php if($settings["bonus_program"]["balance"]["status"]){ ?>
                             <span>+ <?php echo $Main->price( $Profile->calcBonus(500) ); ?> <?php echo $ULang->t("бонус"); ?></span>
                             <?php } ?> 
                             <input type="radio" name="amount" value="500" >
                          </div>
                        </div>

                        <div>
                          <span><?php echo $ULang->t("Популярный выбор"); ?></span>
                          <div>
                             <p><?php echo $Main->price(1000); ?></p>
                             <?php if($settings["bonus_program"]["balance"]["status"]){ ?>
                             <span>+ <?php echo $Main->price( $Profile->calcBonus(1000) ); ?> <?php echo $ULang->t("бонус"); ?></span>
                             <?php } ?> 
                             <input type="radio" name="amount" value="1000" >
                          </div>
                        </div>

                        <div>
                          <div>
                             <p style="font-size: 17px" ><?php echo $ULang->t("Произвольная сумма"); ?></p>
                             <input type="radio" name="amount" value="" >
                          </div>
                        </div>

                        <span class="clr" ></span>

                   </div>

                   <div class="mt15" ></div>
                   
                   <div class="bg-container balance-input-amount" >
                     
                     <div>
                       <h6> <strong><?php echo $ULang->t("Укажите сумму пополнения"); ?></strong> </h6>
                       <input type="number" step="any" name="change_amount" min="<?php echo $settings["min_deposit_balance"]; ?>" max="<?php echo $settings["max_deposit_balance"]; ?>" class="form-control" >
                     </div>

                   </div>

                   <div class="mt35" ></div>

                   <div class="row" >
                     <div class="col-lg-4" ></div>
                     <div class="col-lg-4" >
                       <button class="btn-custom-big btn-color-blue mb5" ><?php echo $ULang->t("Перейти к оплате"); ?></button>
                     </div>
                     <div class="col-lg-4" ></div>
                   </div>


                </div>

                </form>

                <div class="redirect-form-pay" ></div>
                  
                 
               </div>

               <div class="user-menu-tab-content <?php if($action == "history"){ echo 'active'; } ?>" data-id-tab="history" >

                    <div class="bg-container" >
                      
                      <div class="table-responsive">

                           <?php
                              $get = getAll("SELECT * FROM uni_history_balance where id_user=? order by id desc", [$_SESSION["profile"]["id"]]);     

                               if(count($get)){   

                               ?>
                               <table class="table table-borderless">
                                  <thead>
                                     <tr>
                                      <th><?php echo $ULang->t("Назначение"); ?></th>
                                      <th><?php echo $ULang->t("Сумма"); ?></th>
                                      <th><?php echo $ULang->t("Дата"); ?></th>
                                     </tr>
                                  </thead>
                                  <tbody class="sort-container" >                     
                               <?php

                                  foreach($get AS $value){
           
                                  ?>

                                   <tr id="item<?php echo $value["id"]; ?>" >
                                       <td style="max-width: 350px;" ><?php echo $ULang->t($value["name"]); ?></td>
                                       <td>

                                         <?php
                                          if($value["action"] == "+"){
                                              echo '<span style="color: green;" >+ '.$Main->price($value["summa"]).'</span>';
                                          }else{
                                              echo '<span style="color: red;" >- '.$Main->price($value["summa"]).'</span>';
                                          }
                                         ?>                                   

                                       </td>
                                       <td><?php echo datetime_format($value["datetime"]); ?></td>                          
                                   </tr> 
                           
                                 
                                   <?php                                         
                                  } 

                                  ?>

                                     </tbody>
                                  </table>

                                  <?php               
                               }else{
                                  ?>

                                   <div class="user-block-no-result" >

                                      <img src="<?php echo $settings["path_tpl_image"]; ?>/zdun-icon.png">
                                      <p><?php echo $ULang->t("Вы еще не делали никаких платежей"); ?></p>
                                     
                                   </div>

                                  <?php
                               }                  
                            ?>

                      </div> 

                    </div>
                  
               </div>

               <?php }elseif($action == "favorites"){

               ?>
               <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>
               <?php
                   
                   if(count($data["favorites"])){

                   ?>
                   <div class="row no-gutters gutters10" >
                   <?php

                   foreach ($data["favorites"] as $key => $value) {
                       $value = $Ads->get("ads_id=?", [$value["favorites_id_ad"]]);
                       if( $value ){
                           include $config["basePath"] . "/templates/include/user_ad_grid.php";
                       }
                   }

                   ?>
                   </div>
                   <?php

                   }else{
                      ?>
                       <div class="user-block-no-result" >

                          <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                          <p><?php echo $ULang->t("Все избранные товары будут отображаться на этой странице."); ?></p>
                         
                       </div>                      
                      <?php
                   }


               }elseif($action == "settings"){

                   ?>
                   <form class="user-form-settings" >

                   <div class="user-bg-container" >

                     <h4 class="mb35" > <strong><?php echo $ULang->t("Личные данные"); ?></strong> </h4>
                     
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Я"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="status" <?php if($user["clients_type_person"] == "user"){ echo 'checked=""'; } ?> id="status1" value="user" >
                                <label class="custom-control-label" for="status1"><?php echo $ULang->t("Частное лицо"); ?></label>
                            </div>                        

                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="status" <?php if($user["clients_type_person"] == "company"){ echo 'checked=""'; } ?> id="status2" value="company" >
                                <label class="custom-control-label" for="status2"><?php echo $ULang->t("Компания"); ?></label>
                            </div>

                            <div class="msg-error" data-name="status" ></div>

                        </div>
                     </div>
                     </div>

                     <div class="user-data-item user-name-company" <?php if($user["clients_type_person"] == "company"){ echo 'style="display: block;"'; } ?> >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Название компании"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          <input type="text" name="name_company" class="form-control" value="<?php echo $user["clients_name_company"]; ?>" >
                          <div class="msg-error" data-name="name_company" ></div>
                        </div>
                     </div>
                     </div>

                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Имя"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          <input type="text" name="user_name" class="form-control" value="<?php echo $user["clients_name"]; ?>" >
                          <div class="msg-error" data-name="user_name" ></div>
                        </div>
                     </div>
                     </div>
       
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Фамилия"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          <input type="text" name="user_surname" class="form-control" value="<?php echo $user["clients_surname"]; ?>" >
                          <div class="msg-error" data-name="user_surname" ></div>
                        </div>
                     </div>
                     </div>
                     
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Номер телефона"); ?></label>
                        </div>
                        <div class="col-lg-6" >
                          <?php if($user["clients_phone"]){ ?>
                            <span><?php echo $user["clients_phone"]; ?></span>
                          <?php }else{ ?>
                            <span><?php echo $ULang->t("Укажите номер телефона, чтобы покупатели смогли с вами связываться"); ?></span>
                          <?php } ?>
                        </div>
                        <div class="col-lg-3 j-right v-middle" > <span class="user-list-change open-modal" data-id-modal="modal-edit-phone" ><?php echo $ULang->t("Изменить"); ?></span> </div>
                     </div>
                     </div>
                     
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("E-mail"); ?></label>
                        </div>
                        <div class="col-lg-6" >
                          <?php if($user["clients_email"]){ ?>
                            <span><?php echo $user["clients_email"]; ?></span>
                          <?php }elseif($settings["bonus_program"]["email"]["status"]){ ?>
                            <span><?php echo $ULang->t("Укажите e-mail и получите"); ?> <?php echo $Main->price($settings["bonus_program"]["email"]["price"]); ?> <?php echo $ULang->t("на свой бонусный счет."); ?></span>
                          <?php }else{ ?>
                            <span><?php echo $ULang->t("Укажите e-mail, чтобы не пропустить актуальные новости и акции сервиса"); ?></span>
                          <?php } ?>
                        </div>
                        <div class="col-lg-3 j-right v-middle" > <span class="user-list-change open-modal" data-id-modal="modal-edit-email" ><?php echo $ULang->t("Изменить"); ?></span> </div>
                     </div>
                     </div>
                     
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Пароль"); ?></label>
                        </div>
                        <div class="col-lg-6" >
                          <span class="user-list-change open-modal" data-id-modal="modal-edit-pass" ><?php echo $ULang->t("Изменить"); ?></span>
                        </div>
                     </div>
                     </div>

                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3 v-middle" >
                          <label><?php echo $ULang->t("Город"); ?></label>
                        </div>
                        <div class="col-lg-7" >

                          <div class="container-custom-search" >
                            <input type="text" autocomplete="nope" class="form-control action-input-search-city" value="<?php echo $user["city_name"]; ?>" >
                            <div class="custom-results SearchCityResults" ></div>
                          </div>

                          <input type="hidden" name="city_id" value="<?php echo $user["clients_city_id"]; ?>" >

                        </div>
                     </div>
                     </div>
                  
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Короткое имя"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          <input type="text" name="id_hash" class="form-control" value="<?php echo $user["clients_id_hash"]; ?>" >
                          <div class="msg-error" data-name="id_hash" ></div>
                          <span class="user-info" ><?php echo $ULang->t("Укажите короткий адрес вашей страницы, чтобы он стал более удобным и запоминающимся"); ?></span>
                        </div>
                     </div>
                     </div>

                   </div>

                   <div class="user-bg-container mt15" >

                     <h4 class="mb35" > <strong><?php echo $ULang->t("Общие настройки"); ?></strong> </h4>
                     
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Показывать мой телефон в объявлениях"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                            <label class="checkbox">
                              <input type="checkbox" name="view_phone" value="1" <?php if($user["clients_view_phone"]){ echo 'checked=""'; } ?> >
                              <span></span>
                            </label>                          
                        </div>
                     </div>
                     </div>
                     
                     <?php if( $settings["secure_status"] ){ ?>
                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Безопасные сделки"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                            <label class="checkbox">
                              <input type="checkbox" name="secure" value="1" <?php if($user["clients_secure"]){ echo 'checked=""'; } ?> >
                              <span></span>
                            </label>  
                            <span class="user-info mt10"  >
                              <?php echo $ULang->t("Активируйте тумблер, чтобы ваши товары были доступны для продажи по безопасной сделке с онлайн оплатой."); ?> <a href="<?php echo _link("promo/secure"); ?>"><?php echo $ULang->t("Подробнее"); ?></a>
                            </span>                        
                        </div>
                     </div>
                     </div>
                     <?php } ?>

                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Комментарии в объявлениях"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                            <label class="checkbox">
                              <input type="checkbox" name="comments" value="1" <?php if($user["clients_comments"]){ echo 'checked=""'; } ?> >
                              <span></span>
                            </label>                          
                        </div>
                     </div>
                     </div>

                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Банковская карта"); ?></label>
                        </div>
                        <?php if( $user["clients_bank_card"] ){ ?>
                        <div class="col-lg-7" >
                          <span><?php echo $Main->getCardType($user["clients_bank_card"]); ?> <strong><?php echo "xxxx" . substr($user["clients_bank_card"], strlen($user["clients_bank_card"])-4, strlen($user["clients_bank_card"]) ); ?></strong></span>
                        </div>
                        <div class="col-lg-2 j-right v-middle" > <span class="user-list-change open-modal" data-id-modal="modal-bank-card" ><?php echo $ULang->t("Изменить"); ?></span> </div>
                        <?php }else{ ?>
                        <div class="col-lg-7" >
                          <span class="user-list-change open-modal" data-id-modal="modal-bank-card" ><?php echo $ULang->t("Добавить"); ?></span>
                          <span class="user-info" ><?php echo $ULang->t("Добавьте карту для приема оплаты по безопасной сделке"); ?></span>
                        </div>                       
                        <?php } ?>
                     </div>
                     </div>

                     <div class="user-data-item" >
                     <div class="row" >
                        <div class="col-lg-3" >
                          <label><?php echo $ULang->t("Уведомления"); ?></label>
                        </div>
                        <div class="col-lg-7" >
                          <span class="user-list-change open-modal" data-id-modal="modal-edit-notifications" ><?php echo $ULang->t("Изменить"); ?></span>
                        </div>
                     </div>
                     </div>       

                   </div>

                   <div class="row" >
                     <div class="col-lg-4" ></div>
                     <div class="col-lg-4" >
                       <button class="btn-custom-big btn-color-blue mb5 mt25 schema-color-button" ><?php echo $ULang->t("Сохранить"); ?></button>
                     </div>
                     <div class="col-lg-4" ></div>
                   </div>

                   </form>

                   <?php

               }elseif($action == "orders"){
                   ?>
                   <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>

                   <div class="user-menu-tab" >
                     <div data-id-tab="buy" class="active" > <?php echo $ULang->t("Покупки"); ?> (<?php echo count($data["orders"]["buy"]); ?>)</div>
                     <div data-id-tab="sell" > <?php echo $ULang->t("Продажи"); ?> (<?php echo count($data["orders"]["sell"]); ?>)</div>
                   </div>

                   <div class="user-menu-tab-content active" data-id-tab="buy" >
                       <?php
                          if(count($data["orders"]["buy"])){
                            ?>
                            <div class="row" >
                            <?php
                              foreach ($data["orders"]["buy"] as $key => $value) {
                                 include $config["basePath"] . "/templates/include/order_list.php";
                              }
                            ?>
                            </div>
                            <?php
                          }else{
                            ?>
                               <div class="user-block-no-result" >

                                  <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                                  <p><?php echo $ULang->t("Заказы по купленным товарам будут отображаться на этой странице."); ?></p>
                                 
                               </div>                            
                            <?php
                          }
                       ?>
                   </div>
                   <div class="user-menu-tab-content" data-id-tab="sell" >
                       <?php
                          if(count($data["orders"]["sell"])){
                            ?>
                            <div class="row" >
                            <?php
                              foreach ($data["orders"]["sell"] as $key => $value) {
                                 include $config["basePath"] . "/templates/include/order_list.php";
                              }
                            ?>
                            </div>
                            <?php
                          }else{
                            ?>
                               <div class="user-block-no-result" >

                                  <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                                  <p><?php echo $ULang->t("Все проданные товары будут отображаться на этой странице."); ?></p>
                                 
                               </div>                            
                            <?php
                          }
                       ?>
                   </div>

                   <?php
               }elseif($action == "reviews"){

                   ?>
                   <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> <span style="font-size: 23px;" ><?php echo count($data["reviews"]); ?></span> </h3>
                   <?php

                   if( count($data["reviews"]) ){

                      foreach ($data["reviews"] as $key => $value) {
                         include $config["basePath"] . "/templates/include/reviews_user.php";
                      }

                   }else{
                      ?>
                       <div class="user-block-no-result" >

                          <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                          <p><?php echo $ULang->t("Все отзывы пользователя будут отображаться на этой странице."); ?></p>
                         
                       </div>                       
                      <?php
                   }

               }elseif($action == "subscriptions"){

                   ?>
                   <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>

                   <div class="user-menu-tab" >
                     <div data-id-tab="search" class="active" > <?php echo $ULang->t("Поиски"); ?> </div>
                     <div data-id-tab="shops" > <?php echo $ULang->t("Магазины"); ?> </div>
                   </div>

                   <div class="user-menu-tab-content active" data-id-tab="search" >
                     
                     <?php
                       if( count($data["subscriptions_search"]) ){

                          ?>
                          <div class="bg-container" >
                          <?php

                          foreach($data["subscriptions_search"] AS $value){
                          
                          ?>
                          <div class="profile-subscriptions-list" >
                             <div class="row">
                                 <div class="col-lg-6" >
                                   <a class="profile-subscriptions-name" target="_blank" href="<?php echo _link($value["ads_subscriptions_params"]); ?>"><?php echo $Ads->buildNameSubscribe($value["ads_subscriptions_params"]); ?></a>
                                   <div>
                                     <?php echo datetime_format($value["ads_subscriptions_date"], false); ?>
                                   </div>
                                 </div>

                                 <div class="col-lg-6" >
                                   <div class="profile-subscriptions-config" >

                                      <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-light profile-subscriptions-ad-period <?php if($value["ads_subscriptions_period"] == 2){ echo 'active'; } ?> " data-period="2" data-id="<?php echo $value["ads_subscriptions_id"]; ?>" ><?php echo $ULang->t("Сразу при публикации"); ?></button>
                                        <button type="button" class="btn btn-light profile-subscriptions-ad-period <?php if($value["ads_subscriptions_period"] == 1){ echo 'active'; } ?>" data-period="1" data-id="<?php echo $value["ads_subscriptions_id"]; ?>" ><?php echo $ULang->t("Раз в день"); ?></button>
                                        <button type="button" class="btn btn-danger profile-subscriptions-ad-delete" data-id="<?php echo $value["ads_subscriptions_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
                                      </div>

                                   </div>                               
                                 </div>
                              </div>
                           </div>
                           <?php                                         
                          }

                          ?>
                          </div>
                          <?php 
                          
                       }else{
                          ?>
                           <div class="user-block-no-result" >

                              <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                              <p><?php echo $ULang->t("Все подписки на поиск будут отображаться на этой странице."); ?></p>
                             
                           </div>                       
                          <?php
                       }
                     ?>

                   </div>

                   <div class="user-menu-tab-content <?php if($action == "shops"){ echo 'active'; } ?>" data-id-tab="shops" >
                     
                     <?php
                       if( count($data["subscriptions_shops"]) ){

                          ?>
                          <div class="bg-container" >
                          <?php

                          foreach($data["subscriptions_shops"] AS $value){
                          
                          ?>
                          <div class="profile-subscriptions-list" >
                             <div class="row">
                                 <div class="col-lg-6" >
                                   <a class="profile-subscriptions-name" target="_blank" href="<?php echo $Shop->link( $value["clients_shops_id_hash"] ); ?>"><?php echo $value["clients_shops_title"]; ?></a>
                                   <div>
                                     <?php echo datetime_format($value["clients_subscriptions_date_add"], false); ?>
                                   </div>
                                 </div>

                                 <div class="col-lg-6" >
                                   <div class="profile-subscriptions-config" >

                                      <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-danger profile-subscriptions-shop-delete" data-id="<?php echo $value["clients_subscriptions_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
                                      </div>

                                   </div>                               
                                 </div>
                              </div>
                           </div>
                           <?php                                         
                          }

                          ?>
                          </div>
                          <?php 
                          
                       }else{
                          ?>
                           <div class="user-block-no-result" >

                              <img src="<?php echo $settings["path_tpl_image"]; ?>/card-placeholder.svg">
                              <p><?php echo $ULang->t("Все подписки на магазины будут отображаться на этой странице."); ?></p>
                             
                           </div>                       
                          <?php
                       }
                     ?>

                   </div>

                   <?php

               }elseif($action == "shop"){

                   if(!$data["shop"]){

                   ?>
                   <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>

                   <div class="user-block-shop-promo" >
                     
                     <div class="row no-gutters" >
                        <div class="col-lg-8" >
                          <div class="user-block-shop-promo-content" >
                            <p>
                               <?php echo $ULang->t("Превратите свой профиль в полноценный онлайн-магазин с рекламной обложкой, удобными фильтрами, персональными страницами и поиском"); ?>
                            </p>
                            <span class="btn-custom btn-color-blue schema-color-button mt15 display-inline open-modal" data-id-modal="modal-open-shop" ><?php echo $ULang->t("Подключить магазин"); ?></span>
                          </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block" style="text-align: center;" >
                            <img src="<?php echo $settings["path_tpl_image"]; ?>/shop_3345733.png" height="230px" >
                        </div>
                     </div>

                   </div>

                   <div class="user-bg-container mt15" >
                       
                       <div class="user-list-shop-promo" >

                         <h4 class="mb35" ><strong><?php echo $ULang->t("Теперь на"); ?> <?php echo $settings["site_name"]; ?> <?php echo $ULang->t("можно открыть полноценный магазин"); ?> </strong></h4>


                         <div style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Рекламная обложка, привлекающая внимание"); ?></strong></h6>
                            <p><?php echo $ULang->t("Добавляйте красочные обложки и рассказывайте на них о преимуществах магазина, распродажах и спецпредложениях"); ?></p>
                         </div>
                         <div class="mt30" style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Удобные фильтры и поиск по товарам"); ?></strong></h6>
                            <p><?php echo $ULang->t("Ваши покупатели смогут находить товары через поисковую строку, по категориям и фильтровать по разным критериям"); ?></p>
                         </div>
                         <div class="mt30" style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Уведомления о новых товарах"); ?></strong></h6>
                            <p><?php echo $ULang->t("Покупатели смогут подписаться на магазин, получать уведомления о новых товарах"); ?></p>
                         </div>
                         <div class="mt30" style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Простое управление магазином"); ?></strong></h6>
                            <p><?php echo $ULang->t("Для управления не нужны специальные знания, затраты на программистов, домены и прочее. Ничто не будет отвлекать вас от бизнеса"); ?></p>
                         </div>                                                                                       
                         <div class="mt30" style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Каталог магазинов"); ?></strong></h6>
                            <p><?php echo $ULang->t("Ваш магазин будет отображаться в каталоге магазинов"); ?></p>
                         </div>
                         <div class="mt30" style="text-align: center;" >
                            <h6><strong><?php echo $ULang->t("Персональные страницы"); ?></strong></h6>
                            <p><?php echo $ULang->t("Создавайте страницы для своего магазина, например: о нас, контакты и т.п"); ?></p>
                         </div>

                         <div class="row mt30" >
                            <div class="col-lg-4" ></div>
                            <div class="col-lg-4" ><span class="btn-custom btn-color-blue schema-color-button mt15 open-modal" data-id-modal="modal-open-shop" ><?php echo $ULang->t("Подключить магазин"); ?></span></div>
                            <div class="col-lg-4" ></div>
                         </div>

                       </div>

                   </div>

                   <?php
                   }else{
                      ?>

                      <h3 class="mb35 user-title" > <strong><?php echo $data["page_name"]; ?></strong> </h3>

                      <div class="user-block-shop-promo" >
                       
                         <div class="row" >
                            <div class="col-lg-12" >

                              <div class="user-block-shop-promo-content" >

                               <div class="row" >
                                  <div class="col-lg-3 col-4" >
                                    <label><?php echo $ULang->t("Активен до"); ?></label>
                                  </div>
                                  <div class="col-lg-8 col-8" >
                                    
                                    <?php if( strtotime($data["shop"]["clients_shops_time_validity"]) > time() ){ ?>
                                    <strong><?php echo date( "d.m.Y, H:i", strtotime($data["shop"]["clients_shops_time_validity"]) ); ?></strong>
                                    <?php }else{ ?>
                                    <strong style="color: red;" ><?php echo $ULang->t("Магазин заблокирован"); ?></strong>
                                    <?php } ?>
                                    <span class="icon-title-question open-modal" data-id-modal="modal-extend-shop"><?php echo $ULang->t("Продлить"); ?></span>
                                  </div>
                               </div>

                               <div class="row" >
                                  <div class="col-lg-3 col-4" >
                                    <label><?php echo $ULang->t("Ваш магазин"); ?></label>
                                  </div>
                                  <div class="col-lg-8 col-8" >
                                    <a href="<?php echo $Shop->link($data["shop"]["clients_shops_id_hash"]); ?>" target="_blank" ><?php echo $data["shop"]["clients_shops_id_hash"]; ?> <i class="las la-external-link-alt"></i></a>
                                  </div>
                               </div>

                               <div class="row" >
                                  <div class="col-lg-3 col-4" >
                                    <label><?php echo $ULang->t("Подписчиков"); ?></label>
                                  </div>
                                  <div class="col-lg-8 col-8" >
                                    <strong><?php echo $data["shop_count_subscriptions"]; ?></strong>
                                  </div>
                               </div>

                               <div class="row" >
                                  <div class="col-lg-3 col-4" >
                                    <label style="margin-bottom: 0px;" ><?php echo $ULang->t("Просмотров"); ?></label>
                                  </div>
                                  <div class="col-lg-8 col-8" >
                                    <strong><?php echo intval($data["shop"]["clients_shops_count_view"]); ?></strong>
                                  </div>
                               </div>


                              </div>

                            </div>
                         </div>

                      </div>
                      
                      <form class="user-form-shop" >

                      <div class="user-bg-container mt15" >
                        
                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3 v-middle" >
                                <label><?php echo $ULang->t("Логотип"); ?></label>
                              </div>
                              <div class="col-lg-7 user-shop-logo-container" >
                                
                                <?php if($data["shop"]["clients_shops_logo"]){ ?>
                                <div>
                                  <img src="<?php echo Exists($config["media"]["other"],$data["shop"]["clients_shops_logo"],$config["media"]["no_image"]); ?>" height="64px" >
                                </div>
                                <span class="user-shop-logo-delete user-list-change" ><?php echo $ULang->t("Удалить"); ?></span> 
                                <input type="hidden" name="image_status" value="1" >
                                <?php }else{ ?> 
                                <input type="file" name="image" class="input-user-shop-logo" >
                                <?php } ?>

                                <div class="msg-error" data-name="image" ></div>

                              </div>
                           </div>
                           </div>

                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3 v-middle" >
                                <label><?php echo $ULang->t("Название магазина"); ?></label>
                              </div>
                              <div class="col-lg-7" >
                                 
                                <input type="text" name="shop_title" class="form-control" value="<?php echo $data["shop"]["clients_shops_title"]; ?>" >
                                <div class="msg-error" data-name="shop_title" ></div>

                              </div>
                           </div>
                           </div>

                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3 v-middle" >
                                <label><?php echo $ULang->t("Тематика магазина"); ?></label>
                              </div>
                              <div class="col-lg-7" >

                                <select name="shop_theme_category" class="form-control" >
                                    <option value="0"><?php echo $ULang->t("Общая тематика"); ?></option>
                                    <?php
                                       if( $getCategoryBoard["category_board_id_parent"][0] ){
                                           foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
                                              ?>
                                              <option <?php if( $data["shop"]["clients_shops_id_theme_category"] == $value["category_board_id"] ){ echo 'selected=""'; } ?> value="<?php echo $value["category_board_id"]; ?>"> <?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?> </option>
                                              <?php
                                           }
                                       }
                                    ?>
                                </select>
                                 
                              </div>
                           </div>
                           </div>

                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3 v-middle" >
                                <label><?php echo $ULang->t("Краткое описание магазина"); ?></label>
                              </div>
                              <div class="col-lg-7" >
                                 
                                <textarea style="min-height: 160px;" name="shop_desc" class="form-control" ><?php echo $data["shop"]["clients_shops_desc"]; ?></textarea>

                                <span class="user-info"><?php echo $ULang->t("Укажите краткое описание вашего магазина, данное описание будет отображаться в каталоге магазинов"); ?></span>
                                
                              </div>
                           </div>
                           </div>

                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3 v-middle" >
                                <label><?php echo $ULang->t("Идентификатор магазина"); ?></label>
                              </div>
                              <div class="col-lg-7" >
                                 
                                <input type="text" name="shop_id" class="form-control" value="<?php echo $data["shop"]["clients_shops_id_hash"]; ?>" >
                                <div class="msg-error" data-name="shop_id" ></div>

                                <span class="user-info"><?php echo $ULang->t("Укажите короткий адрес вашего магазина, чтобы он стал более удобным и запоминающимся"); ?></span>

                              </div>
                           </div>
                           </div>

                           <hr>

                           <div class="user-data-item" >
                           <div class="row" >
                              <div class="col-lg-3" >

                              </div>
                              <div class="col-lg-7" >
                                 
                                <span class="btn-custom btn-color-light schema-color-button open-modal display-inline" data-id-modal="modal-confirm-delete-shop" ><?php echo $ULang->t("Удалить магазин"); ?></span>

                                <span class="user-info"><?php echo $ULang->t("Вы можете полностью удалить свой магазин, объявления не удалятся. Внимание! Деньги за оплаченный период не возвращаются!"); ?></span>

                              </div>
                           </div>
                           </div>

                      </div>

                       <div class="row">
                         <div class="col-lg-4"></div>
                         <div class="col-lg-4">
                           <button class="btn-custom-big btn-color-blue mb5 mt25 schema-color-button"><?php echo $ULang->t("Сохранить"); ?></button>
                         </div>
                         <div class="col-lg-4"></div>
                       </div>


                      </form>

                      <?php
                   }

               }

               ?>

             
           </div>
        </div>



    </div>
    
    <div class="mt35" ></div>
 
    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>
    
    <?php 

    if($settings["bonus_program"]["email"]["status"] && $data["advanced"] && !$user["clients_email"] && !$_SESSION["modal"]["bonus_program"]["email"]){ 

    ?>
    <script type="text/javascript">
       $(document).ready(function () {

          setTimeout( function(){

          $("#modal-notification-email").show();
          $("body").css("overflow", "hidden");

          } , 5000);
 
       })
    </script>
    <?php 

    $_SESSION["modal"]["bonus_program"]["email"] = 1;

    } 

    ?>
    
    <script type="text/javascript">
    $(document).ready(function () {

      <?php 
      if($_GET["modal"] == "notifications" && $data["advanced"]){ ?>
      $(window).load(function() { 
         $( "#modal-edit-notifications" ).show();
         $("body").css("overflow", "hidden");
      });
      <?php 
      }
      ?>


    });
    </script>

    <div class="modal-custom-bg" style="display: none;" id="modal-edit-pass" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Смена пароля"); ?></h4>    
              <input type="text" name="user_current_pass" class="form-control mt25" placeholder="<?php echo $ULang->t("Текущий пароль"); ?>" >
              <input type="text" name="user_new_pass" class="form-control mt10" placeholder="<?php echo $ULang->t("Новый пароль"); ?>" >                    
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue schema-color-button user-edit-pass" ><?php echo $ULang->t("Изменить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg" style="display: none;" id="modal-edit-email" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("E-mail"); ?></h4>   
              <p class="mt15 confirm-edit-email" ></p> 
              <input type="text" name="email" class="form-control mt25" placeholder="<?php echo $ULang->t("Укажите e-mail"); ?>" >                    
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue schema-color-button user-edit-email" ><?php echo $ULang->t("Продолжить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg" style="display: none;" id="modal-edit-phone" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Смена телефона"); ?></h4>   
              <input type="text" name="phone" class="form-control mt25 phone-mask" placeholder="<?php echo $ULang->t("Номер телефона"); ?>" maxlength="30" data-format="<?php echo $settings["format_phone"]; ?>" > 
              <input type="text" name="code" class="form-control mt25" placeholder="<?php echo $ULang->t("Укажите код из смс"); ?>" >
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <?php if($settings["confirmation_phone"]){ ?>
               <button class="button-style-custom color-blue schema-color-button user-edit-phone-send" ><?php echo $ULang->t("Продолжить"); ?></button>
               <button class="button-style-custom color-blue schema-color-button user-edit-phone-save" ><?php echo $ULang->t("Сохранить"); ?></button>
               <?php }else{ ?>
               <button class="button-style-custom color-blue schema-color-button user-edit-phone-save" style="display: block;" ><?php echo $ULang->t("Сохранить"); ?></button>
               <?php } ?>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-notification-email" style="display: none;"  >
        <div class="modal-custom animation-modal no-padding" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-notification-email-content" >

             <div class="modal-notification-email-content-icon" >
             </div>

             <div class="modal-notification-email-content-title" >
               <h4><?php echo $ULang->t("Укажите e-mail и получите"); ?> <?php echo $Main->price($settings["bonus_program"]["email"]["price"]); ?> <?php echo $ULang->t("на свой бонусный счет."); ?></h4>
             </div>
            
            <div class="modal-custom-button" >
               <div>
                 <button class="button-style-custom color-green mb25 schema-color-button open-modal" data-id-modal="modal-edit-email" ><?php echo $ULang->t("Указать e-mail"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
               </div>                                       
            </div>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-edit-notifications" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-edit-notifications-content" >
            
            <form class="form-edit-notifications" >
            <h4 class="mb25" > <strong><?php echo $ULang->t("Уведомления"); ?></strong> </h4>

              <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" name="notifications[messages]" id="notifications1" <?php if($data["notifications_param"]["messages"]){ echo 'checked=""'; } ?> value="1" >
                  <label class="custom-control-label" for="notifications1"><?php echo $ULang->t("Сообщения"); ?></label>
                  <p><?php echo $ULang->t("Уведомлять меня о получении новых сообщений"); ?></p>
              </div>              

              <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" name="notifications[answer_comments]" id="notifications2" <?php if($data["notifications_param"]["answer_comments"]){ echo 'checked=""'; } ?> value="1" >
                  <label class="custom-control-label" for="notifications2"><?php echo $ULang->t("Ответы на комментарии"); ?></label>
                  <p><?php echo $ULang->t("Уведомлять меня о получении новых ответов на мои комментарии"); ?></p>
              </div> 

              <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" name="notifications[services]" id="notifications3" <?php if($data["notifications_param"]["services"]){ echo 'checked=""'; } ?> value="1" >
                  <label class="custom-control-label" for="notifications3"><?php echo $ULang->t("Окончание услуг"); ?></label>
                  <p><?php echo $ULang->t("Уведомлять меня о завершении платных услуг"); ?></p>
              </div>

              <small><?php echo $settings["site_name"]; ?> <?php echo $ULang->t("оставляет за собой право отправлять пользователям информационные сообщения"); ?></small>
              </form>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-bank-card" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-confirm-content" >
            
            <h4 class="mb25" > <?php echo $ULang->t("Банковская карта"); ?> </h4>

            <input type="text" name="bank_card" class="form-control mt25" value="<?php echo $user["clients_bank_card"]; ?>" >

          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue user-edit-bank-card schema-color-button" ><?php echo $ULang->t("Сохранить"); ?></button>
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
               <button class="button-style-custom color-blue profile-user-block schema-color-button" data-id="<?php echo $user["clients_id"]; ?>" ><?php echo $ULang->t("Внести"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-delete-review" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить отзыв?"); ?></h4>            
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue user-delete-review schema-color-button" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-delete-shop" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить магазин?"); ?></h4> 
              <p><?php echo $ULang->t("Восстановление магазина будет невозможным!"); ?></p>           
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue user-delete-shop schema-color-button" data-id="<?php echo $data["shop"]["clients_shops_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-open-shop" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 530px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-open-shop-content" >
             
             <h4 class="mb25" > <strong><?php echo $ULang->t("Подключение магазина"); ?></strong> </h4>

             <p><?php echo $ULang->t("Выберите срок оплаты"); ?></p>

              <input type="text" class="js-range-slider" name="open-shop-month" value=""
                  data-min="1"
                  data-max="12"
                  data-from="<?php echo $settings["user_shop_discount_month"]; ?>"
                  data-grid="true"
                  data-postfix=" <?php echo $ULang->t("мес"); ?>"
              />

             <div class="mt25 user-shop-price js-shop-price-calc" ></div>

             <div class="mt30" ></div>

             <div class="row" >
                 <div class="col-lg-3" ></div>
                 <div class="col-lg-6" >
                   <a class="button-style-custom schema-color-button color-blue user-open-shop" ><?php echo $ULang->t("Подключить"); ?></a>
                 </div>
                 <div class="col-lg-3" ></div>            
             </div>

          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-extend-shop" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 530px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-open-shop-content" >
             
             <h4 class="mb25" > <strong><?php echo $ULang->t("Продление магазина"); ?></strong> </h4>

             <p><?php echo $ULang->t("Выберите срок оплаты"); ?></p>

              <input type="text" class="js-range-slider" name="extend-shop-month" value=""
                  data-min="1"
                  data-max="12"
                  data-from="<?php echo $settings["user_shop_discount_month"]; ?>"
                  data-grid="true"
                  data-postfix=" <?php echo $ULang->t("мес"); ?>"
              />

             <div class="mt25 user-shop-price js-shop-price-calc" ></div>

             <div class="mt30" ></div>

             <div class="row" >
                 <div class="col-lg-3" ></div>
                 <div class="col-lg-6" >
                   <a class="button-style-custom schema-color-button color-blue user-extend-shop" ><?php echo $ULang->t("Продлить"); ?></a>
                 </div>
                 <div class="col-lg-3" ></div>            
             </div>

          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-seller-safety" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 480px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <h4 class="mb25" > <strong><?php echo $ULang->t("Берегитесь мошенников"); ?></strong> </h4>

          <p><?php echo $ULang->t("1. Не переходите в другие мессенджеры, общайтесь только на"); ?> <?php echo $settings["site_name"]; ?>.</p>
          <p><?php echo $ULang->t("2. Не делитесь своими данными с другими людьми. Ваши почта, паспорт и трёхзначный код с карты нужны только злоумышленникам."); ?></p>
          <p><?php echo $ULang->t("3. Не переходите по ссылкам собеседника."); ?></p>

          <button class="button-style-custom color-blue button-click-close mt25" ><?php echo $ULang->t("Я все понял!"); ?></button>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-user-add-review" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 600px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <form class="form-user-add-review" >
          <div class="user-add-review-tab-1 user-add-review-tab" >
             
             <h4 class="mb25" > <strong><?php echo $ULang->t("Что вы обсуждали?"); ?></strong> </h4>

             <div class="user-add-review-list-ads" >
                <?php 
                if( $data["ad_list_reviews"]["count"] ){

                   foreach ($data["ad_list_reviews"]["all"] as $key => $value) {
                       $image = $Ads->getImages($value["ads_images"]);
                       ?>
                       <div class="mini-list-ads" data-id="<?php echo $value["ads_id"]; ?>" >
                          <div class="mini-list-ads-img" > <img class="image-autofocus" src="<?php echo Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]); ?>" > </div>
                          <div class="mini-list-ads-content" >
                            <div>
                            <?php echo $value["ads_title"]; ?>
                            <div class="mini-list-ads-price" >
                             <?php
                                   echo $Ads->outPrice( ["data"=>$value,"class_price"=>"mini-list-ads-price-now","class_price_old"=>"mini-list-ads-price-old"] );
                             ?>        
                            </div>  
                            </div>                            
                          </div>
                          <div class="clr" ></div>
                       </div>
                       <?php
                   }

                }else{ 
                  ?>
                     <p><?php echo $ULang->t("У продавца объявлений нет"); ?></p>
                  <?php 
                } 
                ?>
             </div>

          </div>

          <div class="user-add-review-tab-2 user-add-review-tab" >

            <h4 class="mb25" > <strong><?php echo $ULang->t("Чем всё закончилось?"); ?></strong> </h4>

            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status_result" value="1" id="status_result1">
                <label class="custom-control-label" for="status_result1"><strong><?php echo $ULang->t("Сделка состоялась"); ?></strong> <br> <?php echo $ULang->t("Продавец получил деньги"); ?> </label>
            </div>

            <div class="custom-control custom-radio mt10">
                <input type="radio" class="custom-control-input" name="status_result" value="2" id="status_result2">
                <label class="custom-control-label" for="status_result2"><strong><?php echo $ULang->t("Сделка сорвалась"); ?></strong> <br> <?php echo $ULang->t("При встрече, осмотре товара"); ?> </label>
            </div>

            <div class="custom-control custom-radio mt10">
                <input type="radio" class="custom-control-input" name="status_result" value="3" id="status_result3">
                <label class="custom-control-label" for="status_result3"><strong><?php echo $ULang->t("Не договорились"); ?></strong> <br> <?php echo $ULang->t("По телефону или в переписке"); ?> </label>
            </div>

            <div class="custom-control custom-radio mt10">
                <input type="radio" class="custom-control-input" name="status_result" value="4" id="status_result4">
                <label class="custom-control-label" for="status_result4"><strong><?php echo $ULang->t("Не общались"); ?></strong> <br> <?php echo $ULang->t("Не удалось связаться"); ?> </label>
            </div>

            <div class="button-style-custom color-light user-add-review-tab-prev mt25" ><?php echo $ULang->t("Назад"); ?></div>
            
          </div>

          <div class="user-add-review-tab-3 user-add-review-tab" >
          
            <h4 class="mb15" > <strong><?php echo $ULang->t("Оценка и детали"); ?></strong> </h4>

            <div class="star-rating star-rating-js">
              <span class="ion-ios-star" data-rating="1"></span>
              <span class="ion-ios-star-outline" data-rating="2"></span>
              <span class="ion-ios-star-outline" data-rating="3"></span>
              <span class="ion-ios-star-outline" data-rating="4"></span>
              <span class="ion-ios-star-outline" data-rating="5"></span>
              <input type="hidden" name="rating" value="1">
            </div>

            <textarea class="mt10 form-control" rows="6" name="text" placeholder="<?php echo $ULang->t("Поделитесь впечатлениями: что понравилось, а что — не очень. В отзыве не должно быть оскорблений и мата."); ?>" ></textarea>

            <div class="user-add-review-attach" >
               
               <span class="user-add-review-attach-change" ><?php echo $ULang->t("Прикрепить фото"); ?></span>

               <div class="user-add-review-attach-files" ></div>

            </div>

            <div class="row" >
               <div class="col-lg-6" >
                 <div class="button-style-custom color-light user-add-review-tab-prev mt25" ><?php echo $ULang->t("Назад"); ?></div>
               </div>
               <div class="col-lg-6" >
                 <button class="button-style-custom color-green mt25" ><?php echo $ULang->t("Отправить отзыв"); ?></button>
               </div>
            </div>

          </div>

          <input type="hidden" name="id_ad" value="0" >

          </form>

          <input type="file" accept=".jpg,.jpeg,.png" multiple="true" style="display: none;" class="input_attach_files" />


        </div>
    </div>

  </body>
</html>