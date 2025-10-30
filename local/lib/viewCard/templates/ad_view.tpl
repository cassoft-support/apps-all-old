<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_desc"], $data); ?>">

    <meta property="og:image" content="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>">
    <meta property="og:title" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_title"], $data); ?>">
    <meta property="og:description" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_desc"], $data); ?>">

    <title><?php echo $Seo->out(["page" => "ad", "field" => "meta_title"], $data); ?></title>

    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-header-sticky="true" data-id-ad="<?php echo $data["ad"]["ads_id"]; ?>" data-id-cat="<?php echo $data["ad"]["category_board_id"]; ?>" >

    <?php include $config["basePath"] . "/templates/header.tpl"; ?>
     
    <div class="container" >

       <?php echo $Banners->out( ["position_name"=>"ad_view_top", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

       <div class="mt15" ></div>

       <?php if( $data["activity_ad"] ){ ?>
       
       <nav aria-label="breadcrumb" class="mb15" >
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo _link(); ?>">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
              <meta itemprop="position" content="1">
            </li>

            <?php
              echo $data["breadcrumb"];
            ?>                 
          </ol>

        </nav>

       <div class="board-view-container" >

          <div class="row" >

              <div class="col-lg-12" >
                
                   <div class="row" >
                     <div class="col-lg-11 col-md-12" >

                         <?php if( $data["ad"]["ads_status"] == 4 || $data["ad"]["ads_status"] == 5 || $data["ad"]["ads_status"] == 2 ){ ?>
                          <div class="view-list-status mb10" >
                            <span class="ad-status-label-<?php echo $data["ad"]["ads_status"]; ?>" ><?php echo $Ads->status( $data["ad"]["ads_status"] ); ?></span>
                          </div>
                          <?php }else{

                                if($data["ad"]["ads_auction"] || $data["ad"]["ads_online_view"]){
                                    ?>
                                    <div class="view-list-status mb10" >

                                        <?php if($data["ad"]["ads_auction"]){ ?>
                                        <span class="auctionlabel" > <?php echo $ULang->t("Аукцион"); ?> </span>
                                        <?php } ?>

                                        <?php if($data["ad"]["ads_online_view"]){ ?>
                                        <span data-tippy-placement="top" title="<?php echo $ULang->t("Продавец готов к видеозвонку"); ?>" > <?php echo $ULang->t("Онлайн-показ"); ?> </span>
                                        <?php } ?>

                                    </div>
                                    <?php
                                }

                          } ?>

                       <h1 class="h1title word-break" ><?php echo $data["ad"]["ads_title"]; ?></h1>

                     </div>
                     <div class="col-lg-1 text-right d-none d-md-block" >
                        <?php echo $Ads->adActionFavorite($data["ad"], "ad", "ad-view-favorite"); ?>
                     </div>
                   </div>
              </div>Карточка</div>
                   <div class="ads-view-photo" >
                    
                      <?php
                        if(count($data["ad"]["ads_images"])){
                            ?>
                            <div class="slick lightgallery" <?php echo $data["image_attr"]; ?> >
                            <?php
                            foreach ($data["ad"]["ads_images"] as $key => $value) {
                               ?>
                               <a href="<?php echo Exists($config["media"]["big_image_ads"],$value,$config["media"]["no_image"]); ?>" ><img src="<?php echo Exists($config["media"]["big_image_ads"],$value,$config["media"]["no_image"]); ?>"></a>
                               <?php
                            }
                            if($data["ad"]["ads_video"]){
                              ?>
                              <a href="<?php echo clear($data["ad"]["ads_video"]); ?>"  data-type="video" >
                              <iframe  width="650px" height="400px" src="<?php echo clear($data["ad"]["ads_video"]); ?>" frameborder="0" allowfullscreen></iframe></a>
                              <?php
                            }
                            ?>
                            </div>
                            <?php
                        }elseif($data["ad"]["ads_video"]){
                           ?>
                           <div>
                              <a href="<?php echo clear($data["ad"]["ads_video"]); ?>"  data-type="video" >
                              <iframe  width="100%" height="400px" src="<?php echo clear($data["ad"]["ads_video"]); ?>" frameborder="0" allowfullscreen></iframe></a>                             
                           </div>
                           <?php
                        }else{
                           ?>
                           <div class="text-center" >
                             <img style="height: 200px" src="<?php echo $config["urlPath"] . "/" . $config["media"]["no_image"]; ?>">
                           </div>
                           <?php
                        }
                      ?>

                   </div>
                   
                   <?php if( count($data["ad"]["ads_images"]) > 1 || (count($data["ad"]["ads_images"]) == 1 && $data["ad"]["ads_video"]) ){ ?>
                   <div class="variable-photo-gallery" >
                      <?php
                          foreach ($data["ad"]["ads_images"] as $key => $value) {
                             ?>
                             <div data-pos="<?php echo $key; ?>" ><img src="<?php echo Exists($config["media"]["small_image_ads"],$value,$config["media"]["no_image"]); ?>"></div>
                             <?php
                          }
                          if($data["ad"]["ads_video"]){
                             ?>
                             <div data-pos="<?php echo $key + 1; ?>" data-type="video" class="variable-video-gallery" >
                                <span></span>
                             </div>
                             <?php
                          }                          
                      ?>                    
                   </div>
                   <?php } ?>

                   <div class="mt40" ></div>

              </div>

              <div class="col-lg-9" >

                   <div class="board-view-button-mobile d-block d-md-none mb25" >
                     
                    <div class="row" >
                       <div class="col-8" >
                         <p class="board-view-price-mobile price-currency" >
                          <?php
                            echo $Ads->outAdViewPrice( ["data" => $data["ad"]] );
                          ?> 
                         </p>
                       </div>
                       <div class="col-4 text-right" >

                         <?php echo $Ads->adActionFavorite($data["ad"], "ad", "ad-view-favorite"); ?>

                       </div>
                    </div>

                    <div class="mt15" ></div>

                    <div class="d-block d-md-none" >
                    <?php echo $Ads->adAuctionSidebar( $data ); ?>
                    </div>

                    <?php
                    echo $Ads->adSidebar( $data ); 
                    ?>

                   </div>

                   <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Город"); ?></h5>
                   
                   <div class="mb20" >
                   <?php echo $ULang->t( $data["ad"]["city_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?>, <?php echo $ULang->t( $data["ad"]["region_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?>
                   </div>
                   
                   <?php if($data["ad"]["ads_address"] || $data["areas"] || $data["metro"]){ ?>

                   <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Местоположение"); ?></h5>

                   <div class="word-break mb20" >
                     
                    <?php 
                      
                      if($data["ad"]["ads_address"]){
                          echo $data["ad"]["ads_address"];
                          if($data["areas"]){
                             echo ", " . implode(", ", $data["areas"]);
                          } 
                          ?>
                            <div class="ads-view-metro" >
                               <?php
                                  foreach ($data["metro"] as $key => $value) {

                                     ?>

                                      <div  title="<?php echo $value["metro_name"]; ?>" >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="17" height="17"><path fill="<?php echo $value["metro_color"]; ?>" fill-rule="evenodd" d="M11.154 4L8 9.53 4.845 4 1.1 13.466H0v1.428h5.657v-1.428H4.81l.824-2.36L8 15l2.365-3.893.824 2.36h-.85v1.427H16v-1.428h-1.1z"></path></svg>
                                        <?php echo $value["station"]; ?>
                                      </div>

                                     <?php
                                     
                                  }
                               ?>
                            </div>
                          <?php                                                               
                      }else{
                          if($data["areas"]){
                             echo implode(", ", $data["areas"]);
                          } 
                          ?>
                            <div class="ads-view-metro" >
                               <?php
                                  foreach ($data["metro"] as $key => $value) {

                                     ?>

                                      <div  title="<?php echo $value["metro_name"]; ?>" >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="17" height="17"><path fill="<?php echo $value["metro_color"]; ?>" fill-rule="evenodd" d="M11.154 4L8 9.53 4.845 4 1.1 13.466H0v1.428h5.657v-1.428H4.81l.824-2.36L8 15l2.365-3.893.824 2.36h-.85v1.427H16v-1.428h-1.1z"></path></svg>
                                        <?php echo $value["station"]; ?>
                                      </div>

                                     <?php
                                     
                                  }
                               ?>
                            </div>
                          <?php 
                      }

                    ?>                     

                   <?php if($data["ad"]["ads_latitude"] && $data["ad"]["ads_longitude"]){ ?>

                   <div  class="ads-view-open-map" > <a href="#" ><?php echo $ULang->t("Показать на карте"); ?></a> </div> 

                   <div class="ads-view-map">
                       <div id="mapAd" ></div>
                   </div>

                   <?php } ?>

                   </div>

                   <?php } ?>
                   
                   <?php if($data["ad"]["ads_text"]){ ?>

                   <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Описание"); ?></h5>

                   <div class="word-break mb20" ><?php echo nl2br($data["ad"]["ads_text"]); ?> </div>

                   <?php } ?>

                   <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Характеристики"); ?></h5>
                   
                   <?php if($data["properties"]){ ?>

                   <div class="list-properties mb20" >
                     <div class="list-properties-display <?php if( $data["properties_count"] <= 5 ){ ?> heightAuto <?php } ?>" >
                       <div class="row" ><?php echo $data["properties"]; ?></div>
                     </div>
                     <?php if( $data["properties_count"] > 5 ){ ?>
                       <span class="list-properties-toggle link-button-toggle" data-status="0" ><?php echo $ULang->t("Все параметры"); ?></span>
                     <?php } ?>
                   </div>

                   <?php } ?>
                   
                   <?php if( $data["ad"]["ads_status"] != 0 ){ ?>
                   
                   <div class="ad-view-box-info" >
                    
                   <div class="row" >
                     
                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("В избранном"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" ><?php echo $Profile->getCountFavorites($data["ad"]["ads_id"]); ?></span>
                     </div>

                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("Просмотры"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" ><?php echo $Ads->getCountView($data["ad"]["ads_id"]); ?></span>
                     </div>

                     <?php if($_SESSION["profile"]["id"] == $data["ad"]["ads_id_user"]){ ?>
                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("Кол-во показов"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" >
                        <?php echo $Ads->getDisplayView($data["ad"]["ads_id"], date("Y-m-d")); ?>
                        <?php if( !$data["order_service_ids"] && $data["ad"]["ads_status"] == 1 && strtotime($data["ad"]["ads_period_publication"]) > time() ){ ?>
                            <span class="icon-title-question open-modal" data-id-modal="modal-top-views" ><i class="las la-question-circle"></i> <?php echo $ULang->t("Как повысить?"); ?></span> 
                        <?php } ?>                          
                       </span>
                     </div>
                     <?php } ?>

                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("Размещено"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" ><?php echo datetime_format($data["ad"]["ads_datetime_add"], false); ?></span>
                     </div>
                     
                     <?php if($_SESSION["profile"]["id"] == $data["ad"]["ads_id_user"]){ ?>

                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("Активно до"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" ><?php echo datetime_format($data["ad"]["ads_period_publication"], false); ?></span>
                     </div>

                     <?php } ?>

                     <div class="col-lg-3 col-md-3 col-sm-3 col-5" ><span class="span-style" ><?php echo $ULang->t("Поделиться"); ?></span></div>
                     <div class="col-lg-9 col-md-9 col-sm-9 col-7" >
                       <span class="span-style-height" ><?php echo $data["share"]; ?></span>
                     </div>

                   </div>

                   </div>

                   <div data-id-modal="modal-complaint" class="complain-toggle open-modal text-right" > <span><?php echo $ULang->t("Пожаловаться"); ?></span> </div>
                   
                   <?php if($data["ad"]["clients_comments"]){ ?>
                   
                   <noindex>
                    
                   <h4 class="mb30 mt15" > <strong> <?php echo $ULang->t("Комментарии"); ?> </strong> </h4>
                   
                   <?php 

                   if($_SESSION['profile']['id']){ 
                     
                     if( !$data["locked"] ){
                        ?>
                        <div class="module-comments-form-otvet mb25" >
                          <form class="module-comments-form" >
                          <textarea name="text" placeholder="<?php echo $ULang->t("Ваш комментарий ..."); ?>" ></textarea>
                            <button class="module-comments-form-send" ><i class="las la-arrow-right"></i></button>
                            <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
                          </form>
                        </div>
                        <?php
                     }else{
                        ?>
                        <div class="alert alert-primary mb25" role="alert">
                          <?php echo $ULang->t("Вы не можете оставить комментарий к данному объявлению"); ?>
                        </div>                        
                        <?php
                     } 
                     
                     }else{ ?>
                     <div class="alert alert-primary mb25" role="alert">
                      <?php echo $ULang->t("Добавлять комментарии могут только авторизованные пользователи"); ?>
                     </div>                    
                   <?php } ?>


                   <div class="module-comments" >
                      
                      <?php
                      echo $Ads->outComments(0, $Ads->getComments($data["ad"]["ads_id"]));
                      ?>
                      
                   </div>  
                   
                   </noindex>

                   <?php } ?> 

                   <?php } ?>               

              </div>
              <div class="col-lg-3 d-none d-md-block" >

                 <?php include $config["basePath"] . "/templates/ad_view_sidebar.tpl"; ?>

              </div>
          </div>
         
          

       </div>

    <?php }else{

       if($data["ad"]["ads_status"] == 8 || $data["ad"]["clients_status"] == 3){
          ?>
          <div class="row" >
            <div class="col-lg-12" >
               <div class="ads-status-block mt100" >
                 <div class="status-block-icon" >
                    <div><i class="las la-lock"></i></div>
                 </div>
                 <h5><strong><?php echo $ULang->t("Объявление удалено"); ?></strong></h5>
               </div>
            </div>
          </div>
          <?php        
        }elseif( $data["ad"]["ads_status"] == 3 || $data["ad"]["clients_status"] == 2 ){
          ?>
          <div class="row" >
            <div class="col-lg-12" >
               <div class="ads-status-block mt100" >
                 <div class="status-block-icon" >
                    <div><i class="las la-lock"></i></div>
                 </div>
                 <h5><strong><?php echo $ULang->t("Объявление заблокировано"); ?></strong></h5>
               </div>
            </div>
          </div>
          <?php        
        }else{
            ?>
            <div class="row" >
              <div class="col-lg-12" >
                 <div class="ads-status-block mt100" >
                   <div class="status-block-icon" >
                      <div><i class="las la-lock"></i></div>
                   </div>
                   <h5><strong><?php echo $ULang->t("Объявление неактивно"); ?></strong></h5>
                 </div>
              </div>
            </div>
            <?php
        }


    } ?>


    <div class="ajax-container-similar" ></div>


    <?php echo $Banners->out( ["position_name"=>"ad_view_bottom", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

    <div class="mt50" ></div>

    </div>
    
    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

    <?php echo $Geo->vendorMap($data["ad"]["ads_latitude"],$data["ad"]["ads_longitude"]); ?>
    
    <?php echo $Ads->adModalJs($data); ?>

    <noindex>
    
    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-message" >
        <div class="modal-custom width600 animation-modal" >

         <span class="modal-custom-close" ><i class="las la-times"></i></span>
           
         <div class="board-view-user" >

            <?php echo $Profile->cardUser($data); ?>

         </div>

         <div class="textarea-custom" >
             
             <form class="form-chat-message" >
             <textarea placeholder="<?php echo $ULang->t("Напишите сообщение ..."); ?>" name="text" ></textarea>
             <div class="textarea-custom-actions text-right" >
               <button class="button-style-custom schema-color-button color-green" ><?php echo $ULang->t("Отправить"); ?></button>
               <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
             </div>
             </form>
           
         </div>


        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-view-phone" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

               <div class="user-avatar" >

                   <div class="user-avatar-img" >
                      <img src="<?php echo $Profile->userAvatar($data["ad"]["clients_avatar"]); ?>" />
                   </div>  
                   <h4> <?php echo $Profile->name($data["ad"]); ?> </h4>  
                   <p>На <?php echo $settings["site_name"]; ?> с <?php echo date("d.m.Y", strtotime($data["ad"]["clients_datetime_add"])); ?></p>  

                   <div class="board-view-stars">
                       
                     <?php echo $data["ratings"]; ?>
                     <div class="clr"></div>   

                   </div>

               </div>

               <hr>
               
               <div class="modal-view-phone-display" ></div>

               <p class="mt10 text-center" ><?php echo $ULang->t("Скажите, что Вы нашли объявление на"); ?> <?php echo $settings["site_name"]; ?></p>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-complaint" >
        <div class="modal-custom width600 animation-modal" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-complaint" >
          
          <form method="post" class="ads-form-complaint" > 

              <div class="modal-complaint-tab-1" >
                <h3><?php echo $ULang->t("Пожаловаться на объявление"); ?></h3>
                <p><?php echo $ULang->t("Выберите причину жалобы"); ?></p>

                <ul>

                  <li data-name="<?php echo $static_msg["3"]; ?>" >
                     <h5><?php echo $ULang->t("Неверная категория"); ?></h5>
                     <p><?php echo $ULang->t("Объявление находится в неверной категории"); ?></p>
                  </li>

                  <li data-name="<?php echo $static_msg["4"]; ?>" >
                     <h5><?php echo $ULang->t("Запрещенный товар"); ?></h5>
                     <p><?php echo $ULang->t("Наркотическая и табачная продукция, алкоголь и другие запрещенные товары"); ?></p>
                  </li>

                  <li data-name="<?php echo $static_msg["5"]; ?>" >
                     <h5><?php echo $ULang->t("Непристойное содержание"); ?></h5>
                     <p><?php echo $ULang->t("Порнографическое, экстремистское, эротическое, нецензурное содержание"); ?></p>
                  </li>

                  <li data-name="<?php echo $static_msg["6"]; ?>" >
                     <h5><?php echo $ULang->t("Мошенничество"); ?></h5>
                     <p><?php echo $ULang->t("Обман, вымогательство, незаконные операции с целью наживы"); ?></p>
                  </li>

                  <li data-name="<?php echo $static_msg["7"]; ?>" >
                     <h5><?php echo $ULang->t("Спам"); ?></h5>
                     <p><?php echo $ULang->t("Повторное размещение объявления о продаже одного и того же товара, рассылка рекламы и.т д"); ?></p>
                  </li>

                  <li data-name="other" >
                     <h5><?php echo $ULang->t("Другое"); ?></h5>
                     <p><?php echo $ULang->t("Иная причина нарушения размещения объявления"); ?></p>
                  </li>    

                </ul>
              </div>

              <div class="modal-complaint-tab-2" >

                   <div class="row" >
                     <div class="col-lg-1" >
                        <i class="las la-arrow-left modal-complaint-back"></i>
                     </div>
                     <div class="col-lg-11" >
                        <h3 class="modal-complaint-name" ><?php echo $ULang->t("Другое"); ?></h3>
                     </div>
                   </div>


                   <div class="textarea-custom" >

                       <textarea placeholder="<?php echo $ULang->t("Опишите подробности нарушения"); ?>" name="text" ></textarea>
                       <div class="textarea-custom-actions text-right" >
                         <button class="button-style-custom schema-color-button color-green" ><?php echo $ULang->t("Отправить"); ?></button>
                       </div>
                     
                   </div>

                
              </div>

              <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >

          </form> 

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-ad-new" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 880px;" >

          <span class="modal-custom-close modal-ad-new-close" ><i class="las la-times"></i></span>


          <h3> <strong><?php echo $ULang->t("Поздравляем, объявление размещено!"); ?></strong> </h3>

          <h5><?php echo $ULang->t("Подключите услуги, чтобы продать свой товар быстрее"); ?></h5>


          <div class="mt40" ></div>

          <form method="post" class="form-ads-services" >

          <div class="row" >

              <?php echo $list_services; ?>

              <input type="hidden" name="id_s" value="0" >
              <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
             
          </div>
          
          <div class="row" >
             <div class="col-lg-4" >
               <button class="ads-services-tariffs-btn-order mt30" ><?php echo $ULang->t("Подключить"); ?></button>
             </div>             
          </div>

          </form>


        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-order-service" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 880px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <h4> <strong><?php echo $ULang->t("Подключите услуги, чтобы продать свой товар быстрее"); ?></strong> </h4>

          <div class="mt40" ></div>
          
          <form method="post" class="form-ads-services" >

          <div class="row" >

              <?php echo $list_services; ?>

              <input type="hidden" name="id_s" value="0" >
              <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
             
          </div>
          
          <div class="row" >
             <div class="col-lg-4" >
               <button class="ads-services-tariffs-btn-order mt30" ><?php echo $ULang->t("Подключить"); ?></button>
             </div>             
          </div>

          </form>


        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-remove-publication" >
        <div class="modal-custom animation-modal" style="max-width: 450px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Снять с публикации"); ?></h4>   
              <p><?php echo $ULang->t("Выберите причину"); ?></p>         
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button-list" >
            <button class="button-style-custom schema-color-button color-blue ads-status-sell" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Я продал на"); ?> <?php echo $settings["site_name"]; ?></button>
            <button class="button-style-custom color-light ads-remove-publication mt5" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Другая причина"); ?></button>
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-ads-success-sell" >
        <div class="modal-custom animation-modal" style="max-width: 500px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Поздравляем с продажей!"); ?></h4>   
              <p><?php echo $ULang->t('Оставить отзыв о покупателе Вы можете в его профиле в разделе "Отзывы"'); ?></p> 
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button-list" >
            <button class="button-style-custom color-light button-click-close mt5" ><?php echo $ULang->t("Закрыть"); ?></button>
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-delete-ads" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить объявление?"); ?></h4> 
              <p><?php echo $ULang->t("Ваше объявление будет безвозвратно удалено"); ?></p>           
          </div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom btn-color-danger ads-delete" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-top-views" style="display: none;" >
        <div class="modal-custom animation-modal no-padding" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-top-views-content" >

             <div class="modal-top-views-content-icon" >
             </div>

             <div class="modal-top-views-content-title" >
               <h4> <strong><?php echo $ULang->t("Поднятие объявления в ленте"); ?></strong> </h4>
               <p><?php echo $ULang->t("Воспользуйтесь услугой - поднятие объявление в ленте и ваше объявление будет на много чаще показываться в каталоге чем у остальных!"); ?></p>
             </div>
            
            <div class="modal-custom-button" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb25 top-views-up" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $Ads->buttonViewsUp(); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb25 top-views-change" ><?php echo $ULang->t("Выбрать другую услугу"); ?></button>
               </div>                                       
            </div>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-auction-content" >

            <h4> <strong><?php echo $ULang->t("Укажите ставку"); ?></strong> </h4>

            <input type="number" name="rate" class="form-control" maxlength="11" >

            <p><i class="las la-exclamation-circle"></i> <?php echo $ULang->t("Сумма не должна быть меньше"); ?> <?php echo $Main->price($data["ad"]["ads_price"]); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-auction-rate" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Сделать ставку"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
               </div>                                       
            </div>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-success" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 450px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <h4> <strong><?php echo $ULang->t("Ваша ставка принята!"); ?></strong> </h4>

          <p class="mt15" ><?php echo $ULang->t("Если ставка будет перебита, вы получите E-mail уведомление. Новую ставку вы можете сделать в любое время!"); ?></p>

          <div class="mt30" ></div>

          <div class="row" >
             <div class="col-lg-3" ></div>
             <div class="col-lg-6" >
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
             </div>
             <div class="col-lg-3" ></div>            
          </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-users" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 550px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-auction-users-content" >

            <h4> <strong><?php echo $ULang->t("Список ставок"); ?></strong> </h4>

            <div class="mt30" ></div>

               <?php
                 if(count($data["auction_users"])){
                   ?>
                   <div class="row no-gutters gutters10 mb25" >

                      <?php
                      foreach ($data["auction_users"] as $key => $value) {
                          ?>
                          <div class="col-lg-2 col-4 col-md-3 col-sm-3" >
                             <div class="auction-user-item" >
                                <div class="auction-user-item-avatar" >
                                   <img src="<?php echo $Profile->userAvatar($value["clients_avatar"]); ?>" title="<?php echo $Profile->name($value); ?>" >
                                </div>
                                <div class="auction-user-item-price" > <?php echo $Main->price($value["ads_auction_price"]); ?> </div>
                             </div>
                          </div>
                          <?php
                      }
                      ?>
                     
                   </div>
                   <?php
                 }else{
                   ?>
                    <p><?php echo $ULang->t("Ставок пока нет"); ?></p>
                    <?php if($_SESSION["profile"]["id"] != $data["ad"]["ads_id_user"]){ ?>
                    <div class="row mt30 mb5" >
                       <div class="col-lg-3" ></div>
                       <div class="col-lg-6" >
                         <button class="button-style-custom schema-color-button color-green open-modal" data-id-modal="modal-auction" ><?php echo $ULang->t("Сделать ставку"); ?></button>
                       </div>
                       <div class="col-lg-3" ></div>            
                    </div>                   
                   <?php
                    }
                 }
               ?>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-cancel" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4> <strong><?php echo $ULang->t("Отказ от покупки"); ?></strong> </h4>

            <p class="mt15" ><?php echo $ULang->t("Если победитель аукциона отказался по каким то причинам от покупки товара, то вы можете удалить его ставку и исключить из аукциона. Новый победитель будет выбран тот который шел за этим участником!"); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-auction-cancel-rate" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Удалить ставку"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
               </div>                                       
            </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-confirm-buy" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4> <strong><?php echo $ULang->t("Подтверждение заказа"); ?></strong> </h4>

            <p class="mt15" ><?php echo $ULang->t("После подтверждения заказ объявление будет зарезервировано за Вами, договоритесь с продавцом в чате или по телефону о способе передачи и оплате товара."); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-accept-auction-order-reservation" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Подтверждаю"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
               </div>                                       
            </div>

        </div>
    </div>

  </noindex>


  </body>
</html>