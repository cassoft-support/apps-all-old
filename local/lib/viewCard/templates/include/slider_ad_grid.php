<?php 
$image = $Ads->getImages($value["ads_images"]);
$service = $Ads->adServices($value["ads_id"]);
$getShop = $Shop->getUserShop( $value["ads_id_user"] );
?>
<div class="col-lg-3 col-md-3 col-sm-6 col-6" >
<div class="item-grid" title="<?php echo $value["ads_title"]; ?>" >
   <div class="item-grid-img" >

       <div class="item-labels" >
          <?php 
          if($Ads->getStatusSecure($value)){
             ?>
             <div class="item-secure" data-tippy-placement="top" title="<?php echo $ULang->t("Доступна безопасная сделка"); ?>" > <i class="las la-shield-alt"></i> </div>
             <?php            
          }          
          if($value["ads_auction"]){
             ?>
             <div class="item-auction" data-tippy-placement="top" title="<?php echo $ULang->t("Аукцион"); ?>" > <i class="las la-gavel"></i> <span <?php echo $Ads->auctionTimeCompletion( $value["ads_auction_duration"], "pulse-time-grid" ); ?> ></span> </div>
             <?php
          }
          ?>
       </div>

       <?php echo $Ads->outGallery( [ "ad" => $value, "image" => $image, "service" => $service, "shop" => $getShop, "height" => 180 ] ); ?>

       <span class="item-grid-city" ><?php echo $ULang->t( $value["city_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?></span>

   </div>

   <div class="item-grid-info" >
        
        <?php echo $Ads->adActionFavorite($value, "catalog", "item-grid-favorite"); ?>

        <div class="item-grid-price" >
            <?php
                  echo $Ads->outPrice( ["data"=>$value,"class_price"=>"item-grid-price-now","class_price_old"=>"item-grid-price-old", "abbreviation_million" => true] );
            ?>        
        </div> 

      <a href="<?php echo $Ads->alias($value); ?>" ><?php echo custom_substr($value["ads_title"], 35, "..."); ?></a>
   </div>
</div>
</div>