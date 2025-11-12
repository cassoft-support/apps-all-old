<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="description" content="<?php echo $Seo->out(array("page" => "index", "field" => "meta_desc")); ?>">

    <title><?php echo $Seo->out(array("page" => "index", "field" => "meta_title")); ?></title>
    
    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" >

    <?php include $config["basePath"] . "/templates/header.tpl"; ?>
    
     <?php
      if( count($data["sliders"]) ){
           ?>

              <div class="load-sliders-wide init_<?php echo base64_encode($settings["lnc_key"]); ?>" >
              <div class="sliders-wide" data-show-slider="<?php echo $settings["media_slider_count_show"]; ?>" data-autoplay="<?php echo $settings["media_slider_autoplay"]; ?>" data-arrows="<?php echo $settings["media_slider_arrows"]; ?>" >
                 
                 <?php
                 foreach ($data["sliders"] as $key => $value) {
                     ?>
                       <div class="sliders-wide-item" data-id="<?php echo $value["sliders_id"]; ?>" >

                            <a title="<?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?>. <?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?>" style="background: url(<?php echo Exists($config["media"]["other"],$value["sliders_image"],$config["media"]["no_image"]); ?>);
                              background-position: right;
                              background-size: contain;
                              background-repeat: no-repeat;
                              background-color: <?php echo $value["sliders_color_bg"]; ?>;
                              display: block;
                              border-radius: 10px;
                              height: <?php echo $settings["media_slider_height"]; ?>px;
                              " target="_blank"  href="<?php echo $Main->sliderLink( $value["sliders_link"] ); ?>">
                              
                              <span class="sliders-wide-title">
                                <span class="sliders-wide-title1"><?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?></span>
                                <span class="sliders-wide-title2"><?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?></span>
                              </span>

                            </a>

                      </div>               
                     <?php
                 }
                 ?>

              </div>
              </div>

           <?php
      }
     ?>


    <div class="container" >
       
       <?php echo $Banners->out( ["position_name"=>"index_top"] ); ?>

       <div class="row mt30" >
          
          <div class="col-lg-12" >
                           
            <?php if( $data["vip"]["count"] ){ ?>
            <h3 class="mt20 mb25 title-and-link" > <strong><?php echo $ULang->t( "VIP объявления" ); ?></strong> </h3>
              <div class="row no-gutters gutters10 mb25" >
                  <?php 
                  
                     foreach ( array_slice($data["vip"]["all"], 0,3, true) as $key => $value) {
                         include $config["basePath"] . "/templates/include/slider_ad_grid.php";
                     }
                  
                  ?>

                  <?php if( count($data["vip"]["all"]) > 3 ){ ?>
                  <div class="col-lg-3 col-md-3 col-sm-6 col-6" >
                      <div class="grid-more-link" >
                      <a href="<?php echo $data["vip_link"]; ?>" >
                          <?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i>
                      </a>
                      </div>
                  </div>
                  <?php } ?>

              </div>
            <?php
            } 
            ?>

            <?php
            if( count($data["slider_ad_category"]) ){
                foreach ($data["slider_ad_category"] as $id_category => $nested) {
                    ?>

                      <h3 class="mt20 mb25 title-and-link" > <strong><?php echo $ULang->t( $getCategoryBoard["category_board_id"][$id_category]["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></strong> </h3>
                      <div class="row no-gutters gutters10 mb25" >
                          <?php 
                          
                            foreach (array_slice($nested, 0, 3, true) as $key => $value) {
                                include $config["basePath"] . "/templates/include/slider_ad_grid.php";
                            }
                          
                          ?>

                          <?php if( count($nested) > 3 ){ ?>
                          <div class="col-lg-3 col-md-3 col-sm-6 col-6" >
                              <div class="grid-more-link" >
                              <a href="<?php echo $CategoryBoard->alias($getCategoryBoard["category_board_id"][$id_category]["category_board_chain"]); ?>" >
                                  <?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i>
                              </a>
                              </div>
                          </div>
                          <?php } ?>

                      </div>

                    <?php

                }
            }
            ?>

            <h1 style="font-size: 1.75rem;" class="mb25" > <strong><?php echo $data["h1"]; ?></strong> </h1>

            <div class="catalog-results" >
            
                <div class="preload" >

                    <div class="spinner-grow mt80 preload-spinner" role="status">
                      <span class="sr-only"></span>
                    </div>

                </div>

            </div>


          </div>
       </div>

       <div class="schema-text" >
         <?php if($data["seo_text"]){ ?> <div class="mt35" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>
       </div>

       <?php echo $Banners->out( ["position_name"=>"index_bottom"] ); ?>


    </div>

    <div class="mt35" ></div>

    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>