<?php
$ratings = $Profile->outRating( $value["clients_shops_id_user"] );
$count_ads = $Ads->getCount( "ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_id_user='{$value["clients_shops_id_user"]}'" );
?>
<div class="shop-item-card-list" >

    <div class="row" >
        
        <div class="col-lg-3 col-12 col-md-3 col-sm-12 text-center" >
          
            <div class="shop-item-card-logo" >
               <img class="image-autofocus" src="<?php echo Exists($config["media"]["other"], $value["clients_shops_logo"], $config["media"]["no_image"]); ?>">
            </div>

            <div class="shop-item-card-content" >

               <div class="board-view-stars">
                   
                 <?php echo $ratings; ?>
                 <div class="clr"></div>   

               </div>

               <a href="<?php echo $Shop->link($value["clients_shops_id_hash"]); ?>" class="shop-item-card-name" ><?php echo $value["clients_shops_title"]; ?></a>

               <a href="<?php echo $Shop->link($value["clients_shops_id_hash"]); ?>" class="shop-item-card-button" ><?php echo $ULang->t("Перейти в магазин"); ?></a>

            </div>

        </div>
        <div class="col-lg-9 col-12 col-md-9 col-sm-12" >

          <div class="shop-item-card-list-content" >
             
             <?php if( $value["clients_shops_desc"] ){ ?>
             <div class="shop-item-card-list-desc" > <?php echo $value["clients_shops_desc"]; ?> </div>
             <? } ?>

             <div class="shop-item-card-list-count" > <?php echo $count_ads; ?> <?php echo ending($count_ads, $ULang->t("объявление"), $ULang->t("объявления"), $ULang->t("объявлений") ) ?> </div>

             <div class="row" >

             <?php
               $getAds = $Shop->getAdsUser( [ "id_user" => $value["clients_shops_id_user"], "limit" => 4 ] );

               if( $getAds["count"] ){
                   foreach ($getAds["all"] as $ad_value) {
                       $image = $Ads->getImages($ad_value["ads_images"]);
                       ?>
                       <div class="col-lg-3 col-6 col-md-3 col-sm-3" >
                          <div class="shop-item-card-list-content-ad" >
                             
                             <a href="<?php echo $Ads->alias($ad_value); ?>" >
                               
                               <div class="shop-item-card-list-content-ad-img" >
                                 <img alt="<?php echo $ad_value["ads_title"]; ?>" class="image-autofocus" src="<?php echo Exists($config["media"]["big_image_ads"],$image[0],$config["media"]["no_image"]); ?>" >
                               </div>

                               <?php
                               echo $Ads->outPrice( ["data"=>$ad_value,"class_price"=>"shop-item-card-list-content-ad-price-now","class_price_old"=>"shop-item-card-list-content-ad-price-old", "abbreviation_million" => true] );
                               ?>

                               <span class="shop-item-card-list-content-ad-title" ><?php echo custom_substr($ad_value["ads_title"], 35, "..."); ?></span>

                             </a>

                          </div>
                       </div>
                       <?php
                   }
               }
             ?>

             </div>
             

          </div>

        </div>

    </div>

</div>