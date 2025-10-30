<?php 
$getAd = $Ads->get("ads_id=?", [$value["secure_id_ad"]]);

$image = $Ads->getImages($getAd["ads_images"]);

?>
<div class="col-lg-12" >
  <div class="item-list-order" >

     <div class="row" >
       <div class="col-lg-3 col-12 col-md-3 col-sm-12" >
         
         <a class="item-list-img" href="<?php echo $Ads->alias($getAd); ?>" >
           <img alt="<?php echo $getAd["ads_title"]; ?>" src="<?php echo Exists($config["media"]["big_image_ads"],$image[0],$config["media"]["no_image"]); ?>" >
         </a>

       </div>
       <div class="col-lg-5 col-sm-12 col-md-5 col-12" >
          <div class="item-list-content" >
            
            <a class="item-list-title" href="<?php echo $Ads->alias($getAd); ?>" ><?php echo $getAd["ads_title"]; ?></a>

            <a class="item-list-link-order" href="<?php echo _link( "order/" . $value["secure_id_order"] ); ?>"><?php echo $ULang->t("Перейти к заказу"); ?></a>

          </div>
       </div>
       <div class="col-lg-4 col-sm-12 col-md-4 col-12" >
          <div class="item-list-content" >
              
              <span class="item-list-order-number" ><?php echo $ULang->t("Заказ"); ?> №<?php echo $value["secure_id_order"]; ?></span>
              <span class="item-list-order-status" >
                 <?php echo $Ads->secureStatusLabel($value); ?>
              </span>
              <span class="item-list-order-date" ><?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($value["secure_date"]); ?></span>                

          </div>
       </div>
     </div>


  </div>
</div>