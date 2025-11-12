<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="<?php echo $data["meta_desc"]; ?>">

    <title><?php echo $data["meta_title"]; ?></title>

    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>"  data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" >
    
    <?php include $config["basePath"] . "/templates/header.tpl"; ?>

    <div class="container" >

       <nav aria-label="breadcrumb">
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo $config["urlPath"]; ?>">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span>
              </a>
              <meta itemprop="position" content="1">
            </li>

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php echo $data["h1"]; ?></span>
              <meta itemprop="position" content="2">
            </li>                 
          </ol>

        </nav>

         <div class="row" >

             <div class="col-lg-12" >
                <h1 class="catalog-title"> <?php echo $data["h1"]; ?> </h1>
                <div class="mt50" ></div>               
             </div>

             <div class="col-lg-9 min-height-600" >

                <div class="catalog-results" >

                    <div class="preload" >

                        <div class="spinner-grow mt80 preload-spinner" role="status">
                          <span class="sr-only"></span>
                        </div>

                    </div>

                </div>

             </div>
             <div class="col-lg-3 d-none d-lg-block" >

             <div class="shop-category-list">

                 <a href="<?php echo _link("shops"); ?>" <?php if( !$data["current_category"] ){ echo 'class="active"'; } ?> > <?php echo $ULang->t( "Все категории" ); ?> <i class="las la-check"></i></a>

                 <ul>

                  <?php
                      if( $getCategoryBoard["category_board_id_parent"][0] ){
                          foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
                              ?>
                              <li>
                                <a <?php if( $value["category_board_id"] == $data["current_category"]["category_board_id"] ){ echo 'class="active"'; } ?> href="<?php echo _link( "shops/" . $value["category_board_chain"] ); ?>"><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?> <i class="las la-check"></i></a>
                              </li>                              
                              <?php
                          }
                      }
                  ?>

                 </ul>

             </div>

             <input type="hidden" name="id_c" value="<?php echo $data["current_category"]["category_board_id"]; ?>" />
             
             </div>

         </div>

         <?php if($data["seo_text"]){ ?> <div class="mt15" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>
        
    </div>
    
    <div class="mt35" ></div>

    <noindex>

    <div class="modal-custom-bg" id="modal-shops-category" style="display: none;" >
        <div class="modal-custom" style="max-width: 750px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <h4 class="mb20" > <strong><?php echo $ULang->t("Категории"); ?></strong> </h4>

             <div class="shop-category-list">

                 <a href="<?php echo _link("shops"); ?>" <?php if( !$data["current_category"] ){ echo 'class="active"'; } ?> > <?php echo $ULang->t( "Все категории" ); ?> <i class="las la-check"></i></a>

                 <ul>

                  <?php
                      if( $getCategoryBoard["category_board_id_parent"][0] ){
                          foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
                              ?>
                              <li>
                                <a <?php if( $value["category_board_id"] == $data["current_category"]["category_board_id"] ){ echo 'class="active"'; } ?> href="<?php echo _link( "shops/" . $value["category_board_chain"] ); ?>"><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?> <i class="las la-check"></i></a>
                              </li>                              
                              <?php
                          }
                      }
                  ?>

                 </ul>

             </div>

        </div>
    </div>


    </noindex>
    
    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>