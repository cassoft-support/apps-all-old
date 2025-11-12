<header class="header-wow d-none d-lg-block" >
   
   
   <div class="header-wow-top" >
       
       <div class="container" >

       <div class="row" >
           <div class="col-lg-2" >
             
              <a class="h-logo" href="<?php echo _link(); ?>" title="<?php echo $ULang->t($settings["title"]); ?>" >
                  <img src="<?php echo $settings["logotip"]; ?>" data-inv="<?php echo $settings["logo_color_inversion"]; ?>" alt="<?php echo $ULang->t($settings["title"]); ?>">
              </a>

           </div>
           <div class="col-lg-3" >
              
              <div class="header-wow-top-geo" >
                  <span <?php if(!$settings["city_id"]){ ?> class="open-modal" data-id-modal="modal-geo" <?php } ?> ><i class="las la-map-marker-alt icon-link-middle"></i> <?php if($_SESSION["geo"]["data"]){ echo $ULang->t($Geo->change()["name"], [ "table"=>"geo", "field"=>"geo_name" ] ); }else{ echo $ULang->t('Выберите город'); } ?></span>
              </div>

           </div>
           <div class="<?php if($settings["visible_lang_site"]){ echo 'col-lg-4'; }else{ echo 'col-lg-5'; } ?> text-right" >
             
              <div class="header-wow-top-list <?php if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_tpl"] ){ echo 'header-wow-top-list-admin'; } ?>" >

                <?php if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_tpl"] ){ echo '<span class="header-wow-top-list-admin-edit open-modal" data-id-modal="modal-edit-site-menu"  >'.$ULang->t("Изменить").'</span>'; } ?>

                 <?php
                    if( count($settings["frontend_menu"]) ){
                        foreach ($settings["frontend_menu"] as $key => $value) {
                           $link = strpos($value["link"], "http") !== false ? $value["link"] : _link($value["link"]);
                           $target = strpos($value["link"], "http") !== false ? 'target="_blank"' : '';
                           ?>
                           <a href="<?php echo $link; ?>" <?php echo $target; ?> ><?php echo $ULang->t($value["name"]); ?></a>
                           <?php
                        }
                    }
                 ?>

              </div>

           </div>
           <div class="<?php if($settings["visible_lang_site"]){ echo 'col-lg-3'; }else{ echo 'col-lg-2'; } ?> text-right header-wow-sticky-list" >
               
               <div class="toolbar-link" >
                   <div class="toolbar-theme-toggle" >
                      <span class="site-color-change <?php if( $_SESSION["schema-color"] == "white" || !$_SESSION["schema-color"] ){ echo 'toolbar-theme-toggle-active'; } ?>" data-color="white" ><i class="las la-sun"></i></span>
                      <span class="site-color-change <?php if( $_SESSION["schema-color"] == "dark" ){ echo 'toolbar-theme-toggle-active-dark'; } ?>" data-color="dark" ><i class="las la-moon"></i></span>
                   </div>                     
               </div>
               
               <?php if($settings["visible_lang_site"]){ ?>
                <div class="toolbar-dropdown dropdown-click">
                  <span class="toolbar-dropdown-image-circle" ><img class="image-autofocus" src="<?php echo Exists( $config["media"]["other"],$_SESSION["langSite"]["image"],$config["media"]["no_image"] ); ?>"></span>
                  <div class="toolbar-dropdown-box width-180 right-0 no-padding toolbar-dropdown-js">

                       <div class="dropdown-box-list-link dropdown-lang-list">

                          <?php
                            $getLang = getAll("select * from uni_languages where status=?", [1]);
                            if(count($getLang)){
                               foreach ($getLang as $key => $value) {
                                  ?>
                                  <a href="<?php echo trim($config["urlPath"] . "/" . $value["iso"] . "/" . REQUEST_URI, "/"); ?>"> <img src="<?php echo Exists( $config["media"]["other"],$value["image"],$config["media"]["no_image"] ); ?>"> <span><?php echo $value["name"]; ?></span> </a>
                                  <?php
                               }
                            }
                          ?>

                       </div>

                  </div>
                </div>
                <?php } ?>

               <div class="toolbar-link toolbar-link-profile" >
                  <?php echo $Profile->headerUserMenu(false); ?>
                  <div class="header-box-register-bonus" data-status="<?php echo intval($settings["bonus_program"]["register"]["status"]); ?>" >

                     <span class="header-box-register-bonus-close" ><i class="las la-times"></i></span>
                     
                     <h5><?php echo $ULang->t("Зарегистрируйтесь на нашем сайте"); ?></h5>

                     <p><?php echo $ULang->t("и получите"); ?> <strong><?php echo $Main->price($settings["bonus_program"]["register"]["price"]); ?></strong> <?php echo $ULang->t("на свой бонусный счет!"); ?></p>

                     <a href="<?php echo _link("auth"); ?>" class="btn-custom btn-color-white" ><?php echo $ULang->t("Зарегистрироваться"); ?></a>

                  </div>
               </div>

           </div>
       </div>

       </div>

   </div>


   <div class="header-wow-sticky" >
       
       <div class="header-wow-sticky-container" >
       <div class="container" >
         
           <div class="row" >
               
               <div class="col-lg-2 col-md-2 col-sm-2" >

                 <span class="header-wow-sticky-menu btn-color-blue open-big-menu" > <i class="las la-bars"></i> <i class="las la-times"></i> <?php echo $ULang->t('Разделы'); ?></span>

                 <div class="header-big-category-menu" >

                      <div class="row no-gutters" >
                         <div class="col-lg-4" >
                             <div class="header-big-category-menu-left" >
                             <?php

                                if(count($getCategoryBoard["category_board_id_parent"][0])){
                                      foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {

                                        ?>
                                         <div data-id="<?php echo $value["category_board_id"]; ?>" >

                                            <a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>" <?php echo $active; ?> >
                                            <?php if( $value["category_board_image"] ){ ?>
                                            <div class="category-menu-left-image" >
                                              <img src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>" >
                                            </div>
                                            <?php } ?>
                                            <div class="category-menu-left-name" ><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></div>
                                            <div class="clr" ></div>
                                            </a>

                                         </div>
                                        <?php

                                      }
                                }

                             ?>
                             </div>
                         </div>
                         <div class="col-lg-8" >
                             <div class="header-big-category-menu-right" >

                             <?php

                                $count_key = 0;

                                if(count($getCategoryBoard["category_board_id_parent"][0])){
                                      foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {

                                           if( $getCategoryBoard["category_board_id_parent"][ $value["category_board_id"] ] ){
                                                
                                                $show = '';

                                                if( $count_key == 0 ){
                                                    $show = ' style="display: block;" ';
                                                }

                                                $count_key++;

                                                echo '
                                                  <div class="header-big-subcategory-menu-list" '.$show.' data-id-parent="'.$value["category_board_id"].'" >
                                                  <h4>'.$Seo->replace($ULang->t( $value["category_board_title"], [ "table" => "uni_category_board", "field" => "category_board_title" ] )).'</h4>
                                                  <div class="row no-gutters" >
                                                ';

                                                foreach ($getCategoryBoard["category_board_id_parent"][ $value["category_board_id"] ] as $subvalue1) {

                                                    echo '
                                                       <div class="col-lg-6" >
                                                       <div data-id="'.$subvalue1["category_board_id"].'" >
                                                         <a href="'.$CategoryBoard->alias($subvalue1["category_board_chain"]).'">'.$ULang->t( $subvalue1["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'</a>
                                                       </div>
                                                       </div>
                                                    ';

                                                }

                                                echo '
                                                  </div>
                                                  </div>
                                                ';

                                           }

                                      }
                                }

                             ?>

                             </div>
                         </div>
                      </div>
                   
                 </div>
                  
               </div>
               <div class="col-lg-8 col-md-8 col-sm-8" >
                  <form class="form-ajax-live-search" method="get" action="<?php echo $_SESSION["geo"]["alias"] ? _link($_SESSION["geo"]["alias"]) : _link($settings["country_default"]); ?>" >
                  <div class="header-wow-sticky-search" >
                      
                      <input type="text" name="search" class="ajax-live-search" autocomplete="off" placeholder="<?php echo $ULang->t("Поиск по объявлениям"); ?>" value="<?php echo clear($_GET["search"]); ?>" >

                      <div class="main-search-results" ></div>

                      <button class="header-wow-sticky-search-action" ><i class="las la-search"></i></button>

                  </div>
                  </form>
               </div>
               <div class="col-lg-2 col-md-2 col-sm-2" >
                    
                    <a href="<?php echo _link("ad/create"); ?>" class="header-wow-sticky-add" > <?php echo $ULang->t("Разместить"); ?> </a>

               </div>

           </div>

       </div>
       </div>

   </div>

</header>

<header class="header-wow-mobile d-block d-lg-none" >
   
   <div class="header-wow-top-geo-mobile" >
      <div class="container" >
         <div class="row" >
             <div class="col-2" >
                <a class="h-logo-mobile" href="<?php echo _link(); ?>" title="<?php echo $ULang->t($settings["title"]); ?>" >
                    <img src="<?php echo $settings["logotip-mobile"]; ?>" data-inv="<?php echo $settings["logo_color_inversion"]; ?>" alt="<?php echo $ULang->t($settings["title"]); ?>">
                </a>
             </div>   
             <div class="col-5" >
                <div class="toolbar-link" > <span <?php if(!$settings["city_id"]){ ?> class="open-modal" data-id-modal="modal-geo" <?php } ?> ><i class="las la-map-marker-alt icon-link-middle"></i> <?php if($_SESSION["geo"]["data"]){ echo $ULang->t($Geo->change()["name"], [ "table"=>"geo", "field"=>"geo_name" ] ); }else{ echo $ULang->t('Выберите город'); } ?></span></div>
             </div>       
             <div class="col-5 text-right" >

                <div class="toolbar-link" > <span class="open-menu-mobile mobile-open-big-menu" ><i class="las la-bars mobile-icon-menu-open"></i><i class="las la-times mobile-icon-menu-close"></i> <?php echo $ULang->t("Меню"); ?> </span> </div>

                <div class="toolbar-link toolbar-link-profile" >
                  <?php echo $Profile->headerUserMenu(false); ?>
                </div>

             </div>
         </div>
      </div>
   </div>

</header>

<div class="header-mobile-menu" >

   <span class="header-mobile-menu-close" ><i class="las la-times"></i></span>

   <div class="header-mobile-menu-theme" >

        <span class="header-mobile-menu-theme-name" ><?php echo $ULang->t("Тема:"); ?></span>

        <span class="site-color-change <?php if( $_SESSION["schema-color"] == "white" || !$_SESSION["schema-color"] ){ echo 'toolbar-theme-toggle-active'; } ?>" data-color="white" ><?php echo $ULang->t("Светлая"); ?></span>
        <span class="site-color-change <?php if( $_SESSION["schema-color"] == "dark" ){ echo 'toolbar-theme-toggle-active-dark'; } ?>" data-color="dark" ><?php echo $ULang->t("Темная"); ?></span>

   </div>
   
   <?php if($settings["visible_lang_site"]){ ?>
   <div class="header-mobile-menu-lang mt10" >

   	 <span class="header-mobile-menu-lang-title" ><?php echo $ULang->t("Язык:"); ?></span>
      
      <div class="header-mobile-menu-lang-box" > <span class="header-mobile-menu-lang-name header-mobile-menu-dropdown-open" ><?php echo $_SESSION["langSite"]["name"]; ?></span>

	    <div class="header-mobile-menu-dropdown">

            <?php
              $getLang = getAll("select * from uni_languages where status=?", [1]);
              if(count($getLang)){
                 foreach ($getLang as $key => $value) {
                    ?>
                    <a href="<?php echo trim($config["urlPath"] . "/" . $value["iso"] . "/" . REQUEST_URI, "/"); ?>"> <img src="<?php echo Exists( $config["media"]["other"],$value["image"],$config["media"]["no_image"] ); ?>"> <span><?php echo $value["name"]; ?></span> </a>
                    <?php
                 }
              }
            ?>

	    </div>

      </div>

   </div>

   <?php } ?>

  <hr>

  <?php if( count($settings["frontend_menu"]) ){
      
      foreach ($settings["frontend_menu"] as $key => $value) {
         $link = strpos($value["link"], "http") !== false ? $value["link"] : _link($value["link"]);
         $target = strpos($value["link"], "http") !== false ? 'target="_blank"' : '';
         ?>
         <a href="<?php echo $link; ?>" <?php echo $target; ?> ><?php echo $ULang->t($value["name"]); ?></a>
         <?php
      }
  
  }
  ?>

  <h5> <strong><?php echo $ULang->t("Категории"); ?></strong> </h5>

  <?php
      if(count($getCategoryBoard["category_board_id_parent"][0])){
        foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {

           ?>
           <a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>"  >
            
            <?php if( $value["category_board_image"] ){ ?>
            <div class="category-menu-left-image" >
              <img src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>" >
            </div>
            <?php } ?>
            <div class="category-menu-left-name" ><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></div>
            <div class="clr" ></div>

           </a>
           <?php

        }
      }
  ?>

  <div class="clr" ></div>

  
</div>

<?php
if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_tpl"] ){

  ?>

    <div class="modal-custom-bg"  id="modal-edit-site-menu" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 600px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <h4> <strong>Редактирование меню</strong> </h4>

          <div class="mt30" ></div>
          
          <form class="modal-edit-site-menu-form" >
          <div class="modal-edit-site-menu-list" >

             <?php
                if( count($settings["frontend_menu"]) ){
                    foreach ($settings["frontend_menu"] as $key => $value) {

                       $key = uniqid();
                       ?>
                       <div class="modal-edit-site-menu-item" >
                          <div class="row" >
                             <div class="col-lg-6 col-6" >
                                <input type="text" name="menu[<?php echo $key; ?>][name]" class="form-control" placeholder="Название" value="<?php echo $value["name"]; ?>" >
                             </div>
                             <div class="col-lg-5 col-5" >
                                <input type="text" name="menu[<?php echo $key; ?>][link]" class="form-control" placeholder="Ссылка" value="<?php echo $value["link"]; ?>" >
                             </div>
                             <div class="col-lg-1 col-1" >
                                <span class="modal-edit-site-menu-delete" > <i class="las la-trash"></i> </span>
                             </div>                                                
                          </div>
                       </div>                       
                       <?php

                    }
                }
             ?>

          </div>
          
          <div class="mt10" ></div>

          <span class="modal-edit-site-menu-add btn-custom-mini btn-color-light" >Добавить</span>

          </form>

          <div class="mt30" ></div>

          <button class="button-style-custom schema-color-button color-green mb10 width100 modal-edit-site-menu-save" >Сохранить</button>

        </div>
    </div>    

  <?php

}
?>


<?php echo $Banners->out( ["position_name"=>"stretching", "current_id_cat"=>$data["category"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>