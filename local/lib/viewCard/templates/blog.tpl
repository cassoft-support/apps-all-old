<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="<?php echo $data["meta_desc"]; ?>">

    <title><?php echo $data["meta_title"]; ?></title>

    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>">
    
    <?php include $config["basePath"] . "/templates/header.tpl"; ?>

    <div class="container" >
       
       <nav aria-label="breadcrumb" class="mt10" >
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo $config["urlPath"]; ?>">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span>
              </a>
              <meta itemprop="position" content="1">
            </li>

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo _link("blog"); ?>">
              <span itemprop="name"><?php echo $ULang->t("Блог"); ?></span>
              </a>
              <meta itemprop="position" content="2">
            </li>

            <?php echo $data["breadcrumb"]; ?>                 
          </ol>

        </nav>


        <div class="row" >

            <div class="col-lg-12" >
              
                <?php echo $Banners->out( ["position_name"=>"blog_top", "current_id_cat"=>$data["category"]["blog_category_id"], "categories"=>$getCategoryBlog] ); ?>
          
                <div class="blog-header mt10" >
                   <h1 class="mb30" ><?php echo $data["h1"]; ?></h1>
                </div>

            </div>

            <div class="col-lg-9" >

                <div class="row no-gutters gutters10 min-height250" >

                  <?php
                    if( $data["articles"]["count"] ){
                       foreach ($data["articles"]["all"] as $key => $value) {
                          include $config["basePath"] . "/templates/include/blog_grid.php";
                       }
                    }else{
                       ?>

                       <div class="col-lg-12" >
                       <div class="catalog-no-results" >
                          <div> <i class="las la-search"></i> </div>
                          <h5><strong><?php echo $ULang->t("Публикаций нет"); ?></strong></h5>
                       </div>
                       </div>

                       <?php
                    }
                  ?>

                </div>

                <ul class="pagination justify-content-center mt15">  
                 <?php echo out_navigation( array("count"=>$data["articles"]["count"], "output"=>$settings["blog_out_content"], "url"=>"", "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
                </ul>
                
                <?php if($data["seo_text"] || !$_GET["page"]){ ?>
                <div class="text-left" >
                  <?php echo $data["seo_text"]; ?>
                </div>
                <?php } ?>


            </div>

            <div class="col-lg-3 d-none d-lg-block" >
              
                <div class="blog-category-list" >

                <a href="<?php echo _link("blog"); ?>" <?php if( !$data["category"] ){ echo ' class="active" '; } ?> ><?php echo $ULang->t("Все категории"); ?> <i class="las la-check"></i></a>

                <?php
                 echo $data["sidebar_category"];
                ?>
                </div>

            </div>

        </div>

        <?php echo $Banners->out( ["position_name"=>"blog_bottom", "current_id_cat"=>$data["category"]["blog_category_id"], "categories"=>$getCategoryBlog] ); ?>
         
          
       <div class="mt50" ></div>


    </div>

    <noindex>

    <div class="modal-custom-bg" id="modal-blog-category" style="display: none;" >
        <div class="modal-custom" style="max-width: 750px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <h4 class="mb20" > <strong><?php echo $ULang->t("Категории"); ?></strong> </h4>

              <div class="blog-category-list" >

                <a href="<?php echo _link("blog"); ?>" <?php if( !$data["category"] ){ echo ' class="active" '; } ?> ><?php echo $ULang->t("Все категории"); ?> <i class="las la-check"></i></a>

                <?php
                 echo $data["sidebar_category"];
                ?>
                
              </div>

        </div>
    </div>

    <div class="d-block d-md-none" >

        <div class="mobile-footer-menu" >
          <div class="row no-gutters" >

                  <div class="col-lg-12 col-12" >
                    <div style="margin-right: 5px;" >
                      <div class="mobile-footer-menu-item open-modal btn-color-blue" data-id-modal="modal-blog-category" ><i class="las la-bars"></i> <?php echo $ULang->t("Категории"); ?></div>
                    </div>
                  </div>

          </div>
        </div>

    </div>

    </noindex>

    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>