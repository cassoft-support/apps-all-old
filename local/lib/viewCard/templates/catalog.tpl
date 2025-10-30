<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="<?php echo $data["meta_desc"]; ?>">

    <title><?php echo $data["meta_title"]; ?></title>

    <?php if(!$data["seo_allowed"]){ ?> <meta name="robots" content="noindex, nofollow" /> <?php }else{ ?> <link rel="canonical" href="<?php echo _link( explode("?", $_SERVER['REQUEST_URI'])[0] ); ?>"/> <?php } ?>

    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" >
    
    <div class="modal-custom-bg" id="modal-catalog-filters" style="display: none;" >
        <div class="modal-custom" style="max-width: 750px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <?php if( isset( $getCategoryBoard["category_board_id_parent"][$data["category"]["category_board_id"]] ) ){ ?>
          <div class="catalog-list-options toggle-list-options">
              <span class="catalog-list-options-name"><?php echo $ULang->t("Категории"); ?> <i class="las la-angle-down"></i></span>
              
              <div class="catalog-list-options-content catalog-list-category">
                
                    <?php 
                    echo $CategoryBoard->outParent($getCategoryBoard, [ "tpl_parent" => '<a {ACTIVE} href="{PARENT_LINK}">{PARENT_NAME}</a>', "tpl" => '{PARENT_CATEGORY}', "category" => $data["category"] ]); 
                    ?>        

              </div>

          </div>
          <?php }elseif( count($getCategoryBoard["category_board_id_parent"][0]) ){ ?>
          <div class="catalog-list-options toggle-list-options">
              <span class="catalog-list-options-name"><?php echo $ULang->t("Категории"); ?> <i class="las la-angle-down"></i></span>
              
              <div class="catalog-list-options-content catalog-list-category">
                
                    <?php 
                      foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {
                         ?>
                         <a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>"><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></a>
                         <?php
                      } 
                    ?>        

              </div>

          </div>            
          <?php } ?>

          <div class="mobile-filter-content" ></div>

        </div>
    </div>
   
    <?php include $config["basePath"] . "/templates/header.tpl"; ?>

    <div class="container" >

       <?php echo $Banners->out( ["position_name"=>"catalog_top", "current_id_cat"=>$data["category"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

       <nav aria-label="breadcrumb">
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo $config["urlPath"]; ?>">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
              <meta itemprop="position" content="1">
            </li>

            <?php echo $data["breadcrumb"]; ?>

          </ol>

        </nav>
          
          <div class="row" >
              <div class="col-lg-12 min-height-600" >

                <div class="row" >
                   <div class="col-lg-12" >
                     <h1 class="catalog-title" ><?php echo $data["h1"]; ?></h1>
                   </div>
                </div>


               <?php
               $outParent = $CategoryBoard->outParent($getCategoryBoard, [ "tpl_parent" => '<div class="col-lg-3 col-6 col-md-4 col-sm-4" ><a {ACTIVE} href="{PARENT_LINK}">{PARENT_NAME}</a></div>', "tpl" => '{PARENT_CATEGORY}', "category" => $data["category"] ]);
               ?>
               
               <?php if( $outParent ){ ?>
               <div class="catalog-subcategory mb15 mt25" >
                  
                  <div class="row" >
                  <?php 
                    echo $outParent; 
                  ?>
                  </div>

               </div>
               <?php } ?>

               <?php echo $Filters->outSeoAliasCategory( $data["category"]["category_board_id"] ); ?>

                <div class="catalog-filters-top" >

                   <form class="form-filter" >
                   
                   <div class="row" >
                       
                       <div class="col-lg-3 col-12" >
                         
                           <div class="catalog-filters-top-item catalog-filters-top-item-checkbox" >
                             
                                <label class="checkbox">
                                  <input type="checkbox" name="filter[vip]" value="1" <?php if($data["param_filter"]["filter"]["vip"]){ echo 'checked=""'; } ?> >
                                  <span><?php echo $ULang->t("VIP объявления"); ?></span>
                                </label>

                                <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_secure"] && $settings["secure_status"] ){ ?>

                                <label class="checkbox">
                                  <input type="checkbox" name="filter[secure]" value="1" <?php if($data["param_filter"]["filter"]["secure"]){ echo 'checked=""'; } ?> >
                                  <span><?php echo $ULang->t("Безопасная сделка"); ?></span>
                                </label>

                                <?php } ?>
                                
                                <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_auction"] ){ ?>
                               
                                <label class="checkbox">
                                  <input type="checkbox" name="filter[auction]" value="1" <?php if($data["param_filter"]["filter"]["auction"]){ echo 'checked=""'; } ?> >
                                  <span><?php echo $ULang->t("Аукционные товары"); ?></span>
                                </label>
                               
                               <?php } ?>
                                
                                <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_online_view"] ){ ?>
                                
                                <label class="checkbox">
                                  <input type="checkbox" name="filter[online_view]" value="1" <?php if($data["param_filter"]["filter"]["online_view"]){ echo 'checked=""'; } ?> >
                                  <span><?php echo $ULang->t("Онлайн-показ"); ?></span>
                                </label>
                                
                                <?php } ?>   

                           </div>

                       </div>
                       <div class="col-lg-6  col-12" >
                         
                           <div class="catalog-filters-top-item-price" >
                            
                            <?php  
                            if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_variant_price"] == 1 ){
                            ?>
                               <a href="?filter[price][from]=15000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(15000); ?></a>
                               <a href="?filter[price][from]=30000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(30000); ?></a>
                               <a href="?filter[price][from]=50000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(50000); ?></a>
                               <a href="?filter[price][from]=100000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(100000); ?></a>
                            <?php 
                            }else{
                              ?>
                               <a href="?filter[price][from]=0&filter[price][to]=1000"><?php echo $ULang->t("до"); ?> <?php echo $Main->price(1000); ?></a>
                               <a href="?filter[price][from]=0&filter[price][to]=5000"><?php echo $ULang->t("до"); ?> <?php echo $Main->price(5000); ?></a>
                               <a href="?filter[price][from]=5000&filter[price][to]=15000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(5000); ?> <?php echo $ULang->t("до"); ?> <?php echo $Main->price(15000); ?></a>
                               <a href="?filter[price][from]=15000&filter[price][to]=30000"><?php echo $ULang->t("от"); ?> <?php echo $Main->price(15000); ?> <?php echo $ULang->t("до"); ?> <?php echo $Main->price(30000); ?></a>                              
                              <?php
                            }
                            ?>
                             
                           </div>

                       </div>
                       <div class="col-lg-3 col-12" >
                            
                            <div class="catalog-filters-top-more-filters" >
                            <div class="btn-custom btn-color-blue"  > <?php echo $ULang->t("Еще фильтры"); ?> </div>
                              <div class="catalog-filters-top-more-filters-items" >
                                  
                                  <div class="catalog-filters-top-scroll" >

                                  <div class="catalog-list-options toggle-list-options" >
                                      <span class="catalog-list-options" >
                                          
                                      <?php if($data["city_areas"] || $data["city_metro"]){ ?>

                                          <span class="catalog-list-options-name open-modal" data-id-modal="modal-geo-options" >
                                          <?php 
                                                if($data["city_areas"] && $data["city_metro"]){
                                                       echo $ULang->t("Метро / Районы");
                                                }elseif($data["city_areas"]){
                                                       echo $ULang->t("Районы");
                                                }elseif($data["city_metro"]){
                                                       echo $ULang->t("Метро");
                                                } 
                                                ?>

                                                <?php if($Ads->getCountChangeOptionsCity( $data )){ echo '<span class="city-option-count" >'.$Ads->getCountChangeOptionsCity( $data ).'</span>'; } ?>
                                          </span>

                                           <?php } ?>

                                      </span>
                                  </div>

                                  <?php 
                                  if($data["category"]["category_board_id"]){
                                      if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_display_price"] ){ 
                                      ?>
                                      <div class="catalog-list-options toggle-list-options <?php if( $data["param_filter"]["filter"]["price"]["from"] || $data["param_filter"]["filter"]["price"]["to"] ){ echo 'catalog-list-options-active'; } ?>" >

                                          <span class="catalog-list-options-name" >
                                          <?php 
                                          if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_variant_price"] == 1 ){
                                            echo $ULang->t('Зарплата'); 
                                          }else{ 
                                            echo $ULang->t('Цена'); 
                                          }
                                          ?>  
                                          <i class="las la-angle-down"></i>
                                          </span>
                                          
                                          <div class="catalog-list-options-content" >
                                          <div class="filter-input" >
                                            <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                                            <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                                          </div>
                                          </div>

                                      </div>
                                      <?php 
                                      } 
                                  }else{
                                      ?>
                                      <div class="catalog-list-options toggle-list-options <?php if( $data["param_filter"]["filter"]["price"]["from"] || $data["param_filter"]["filter"]["price"]["to"] ){ echo 'catalog-list-options-active'; } ?>" >

                                          <span class="catalog-list-options-name" >
                                          <?php echo $ULang->t("Цена"); ?>  
                                          <i class="las la-angle-down"></i>
                                          </span>
                                          
                                          <div class="catalog-list-options-content" >
                                          <div class="filter-input" >
                                            <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                                            <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                                          </div>
                                          </div>

                                      </div>    
                                      <?php
                                  }

                                  ?>

                                  <div class="catalog-list-options toggle-list-options <?php if($data["param_filter"]["filter"]["period"]){ echo 'catalog-list-options-active'; } ?>" >
                                      <span class="catalog-list-options-name" ><?php echo $ULang->t("Срок размещения"); ?> <i class="las la-angle-down"></i></span>
                                      
                                      <div class="catalog-list-options-content" >
                                        
                                          <div class="custom-control custom-radio">
                                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 1){ echo 'checked=""'; } ?> id="flPeriod1" value="1" >
                                              <label class="custom-control-label" for="flPeriod1"><?php echo $ULang->t("За 24 часа"); ?></label>
                                          </div>                        

                                          <div class="custom-control custom-radio">
                                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 7){ echo 'checked=""'; } ?> id="flPeriod2" value="7" >
                                              <label class="custom-control-label" for="flPeriod2"><?php echo $ULang->t("За 7 дней"); ?></label>
                                          </div>

                                          <div class="custom-control custom-radio">
                                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if(!$data["param_filter"]["filter"]["period"]){ echo 'checked=""'; } ?> id="flPeriod3" value="" >
                                              <label class="custom-control-label" for="flPeriod3"><?php echo $ULang->t("За все время"); ?></label>
                                          </div>

                                      </div>

                                  </div>

                                  <div class="catalog-more-filter" >
                                  <?php echo $Filters->load_filters_catalog( $data["category"]["category_board_id"] , $data["param_filter"], "filters_catalog" ); ?>
                                  </div>

                                  </div>


                                  <div class="form-filter-submit" >
                                      <button class="btn-custom btn-color-blue submit-filter-form"  > <?php echo $ULang->t("Применить"); ?> </button>
                                      <?php if($data["param_filter"]["filter"] && !$data["filter"]){ ?>
                                      <button class="btn-custom action-clear-filter btn-color-light"> <?php echo $ULang->t("Сбросить фильтры"); ?> </button>
                                      <?php } ?>
                                  </div>

                                  <input type="hidden" name="id_c" value="<?php echo $data["category"]["category_board_id"]; ?>" >
                                  <input type="hidden" name="filter[sort]" value="<?php echo $data["param_filter"]["filter"]["sort"]; ?>" >

                                  <?php if($data["param_filter"]["search"]){ ?>
                                  <input type="hidden" name="search" value="<?php echo $data["param_filter"]["search"]; ?>" >
                                  <?php } ?>


                              </div>
                            </div>

                       </div>

                   </div>

                   </form>
                  
                </div>
                
                <div class="mt25" ></div>
                
                <div class="row" >
                  
                   <div class="col-lg-12 text-right" >

                      <div class="catalog-sort" >

                         <div>
                           
                            <?php echo $Ads->outSorting(); ?>

                         </div>

                         <div>

                          <a class="catalog-sort-link-button" data-tippy-placement="bottom" title="<?php echo $ULang->t("Поиск на карте"); ?>" href="<?php echo $Ads->linkMap( $data ); ?>"><i class="las la-map-marked-alt"></i> </a>

                         </div>                         
                         <div>
                           
                           <span class="catalog-sort-link-button catalog-ads-subscriptions-add" data-tippy-placement="bottom" title="<?php echo $ULang->t("Подписка на поиск"); ?>" ><i class="las la-bell"></i></span>

                         </div>

                         <div data-view="grid" class="catalog-ad-view <?php if($_SESSION["catalog_ad_view"] == "grid" || !$_SESSION["catalog_ad_view"]){ echo 'active'; } ?>" > <i class="las la-border-all"></i> </div>
                         <div data-view="list" class="catalog-ad-view <?php if($_SESSION["catalog_ad_view"] == "list"){ echo 'active'; } ?>" > <i class="las la-list"></i> </div>

                      </div>

                   </div>

                </div>

                <div class="mt25" ></div>

                <div class="catalog-results" >
                  
                    <div class="preload" >

                        <div class="spinner-grow mt80 preload-spinner" role="status">
                          <span class="sr-only"></span>
                        </div>

                    </div>

                </div>

                <div class="form-search-subscribe" >
                    <div> <i class="las la-bell"></i> </div>
                    <p> <strong><?php echo $ULang->t("Оформите подписку"); ?></strong> <?php echo $ULang->t("на поиск и получайте первым новые объявления по Вашим параметрам"); ?></p>
                    <div> <span class="btn-custom btn-color-blue catalog-ads-subscriptions-add" ><?php echo $ULang->t("Подписаться"); ?></span> </div>
                </div>

              </div>
          </div>
          
          <?php if($data["seo_text"]){ ?> <div class="mt35 schema-text" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>

       <div class="mt50" ></div>
       
       <?php echo $Banners->out( ["position_name"=>"catalog_bottom", "current_id_cat"=>$data["category"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

    </div>

    <noindex>

    <div class="modal-custom-bg bg-click-close" id="modal-ads-subscriptions" style="display: none;" >
        <div class="modal-custom" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-ads-subscriptions-block-1" >

              <h4> <strong><?php echo $ULang->t("Подписка на объявления"); ?></strong> </h4>

              <p><?php echo $ULang->t("Новые объявления будут приходить на электронную почту"); ?></p>
              
              <?php if( !$_SESSION["profile"]["id"] ){ ?>
              <div class="create-info" >
                <?php echo $ULang->t("Для удобного управления подписками"); ?> - <a href="<?php echo _link("auth"); ?>"><?php echo $ULang->t("войдите в личный кабинет"); ?></a>
              </div>
              <?php } ?>
              
              <form class="modal-ads-subscriptions-form mt20" >
                 
                 <label><?php echo $ULang->t("Ваш e-mail"); ?></label>

                 <input type="text" name="email" class="form-control" value="<?php echo $_SESSION["profile"]["data"]["clients_email"]; ?>" >
                 
                 <label class="mt15" ><?php echo $ULang->t("Частота уведомлений"); ?></label>

                 <select name="period" class="form-control" >
                    <option value="1" selected="" ><?php echo $ULang->t("Раз в день"); ?></option>
                    <option value="2" ><?php echo $ULang->t("Сразу при публикации"); ?></option>
                 </select>

                 <input type="hidden" name="url" value="<?php echo $Ads->buildUrlCatalog( $data ); ?>" >

              </form>

              <div class="mt30" >
                 <button class="btn-custom btn-color-blue width100 modal-ads-subscriptions-add mb5" ><?php echo $ULang->t("Подписаться"); ?></button>
              </div>

              <p style="font-size: 13px; color: #7a7a7a;" class="mt15" ><?php echo $ULang->t("При подписке вы принимаете условия"); ?> <a href="<?php echo _link("polzovatelskoe-soglashenie"); ?>"><?php echo $ULang->t("Пользовательского соглашения"); ?></a> <?php echo $ULang->t("и"); ?> <a href="<?php echo _link("privacy-policy"); ?>"><?php echo $ULang->t("Политики конфиденциальности"); ?></a></p>

          </div>

          <div class="modal-ads-subscriptions-block-2" style="text-align: center;" >

              <i class="las la-check checkSuccess"></i>

              <h3> <strong><?php echo $ULang->t("Подписка оформлена"); ?></strong> </h3>

              <p><?php echo $ULang->t("Если вы захотите отписаться от рассылки - просто нажмите на соответствующую кнопку в тексте письма, либо перейдите в раздел"); ?> <a href="<?php if($_SESSION["profile"]["id"]){ echo _link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/subscriptions" ); }else{ echo _link( "auth" ); } ?>"><?php echo $ULang->t("управления подписками"); ?></a></p>

          </div>

        </div>
    </div>

    </noindex>

    <div class="mt35" ></div>
    
    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>