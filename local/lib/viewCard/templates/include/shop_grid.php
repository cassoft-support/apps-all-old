<?php
$ratings = $Profile->outRating( $value["clients_shops_id_user"] );
$count_ads = $Ads->getCount( "ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_id_user='{$value["clients_shops_id_user"]}'" );
?>
<div class="col-lg-4 col-md-4 col-sm-6 col-12" >

  <div class="shop-item-card" >

      <div class="shop-item-card-logo" >
         <img class="image-autofocus" src="<?php echo Exists($config["media"]["other"], $value["clients_shops_logo"], $config["media"]["no_image"]); ?>">
      </div>

      <div class="shop-item-card-content" >

         <div class="board-view-stars">
             
           <?php echo $ratings; ?>
           <div class="clr"></div>   

         </div>

         <a href="<?php echo $Shop->link($value["clients_shops_id_hash"]); ?>" class="shop-item-card-name" ><?php echo custom_substr($value["clients_shops_title"], 35, "..."); ?></a>

         <span class="shop-item-card-count" ><?php echo $count_ads; ?> <?php echo ending($count_ads, $ULang->t("объявление"), $ULang->t("объявления"), $ULang->t("объявлений") ) ?></span>

         <a href="<?php echo $Shop->link($value["clients_shops_id_hash"]); ?>" class="shop-item-card-button" ><?php echo $ULang->t("Перейти в магазин"); ?></a>
      </div>      

  </div>

</div>