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

        <?php echo $Banners->out( ["position_name"=>"blog_view_top", "current_id_cat"=>$data["article"]["blog_articles_id_cat"], "categories"=>$getCategoryBlog] ); ?>

        <div class="blog-view-header mt10" >
           <h1 class="mb15" ><?php echo $ULang->t( $data["article"]["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?></h1>
           <span><i class="las la-clock"></i> <?php echo datetime_format($data["article"]["blog_articles_date_add"], false); ?></span>
           <span><i class="las la-eye"></i> <?php echo $data["article"]["blog_articles_count_view"]; ?></span>
        </div>

        <div class="row mt30" >
            <div class="col-lg-9" >
                <div class="bg-container" >
                   
                   <div class="blog-view-text-content" >
                     <?php echo $ULang->t( urldecode($data["article"]["blog_articles_text"]), [ "table"=>"uni_blog_articles", "field"=>"blog_articles_text" ] ); ?>
                   </div>

                   <h4 class="mb30 mt30" > <strong> <?php echo $ULang->t("Комментарии"); ?> </strong> </h4>
                   
                   <?php if($_SESSION['profile']['id']){ ?>
                     <div class="module-comments-form-otvet mb25" >
                       <form class="module-comments-form" >
                       <textarea name="text" placeholder="<?php echo $ULang->t("Ваш комментарий ..."); ?>" ></textarea>
                         <button class="module-comments-form-send" ><i class="las la-arrow-right"></i></button>
                         <input type="hidden" name="id_article" value="<?php echo $data["article"]["blog_articles_id"]; ?>" >
                       </form>
                     </div>
                   <?php }else{ ?>
                     <div class="alert alert-primary mb25" role="alert">
                      <?php echo $ULang->t("Добавлять комментарии могут только авторизованные пользователи!"); ?>
                     </div>                    
                   <?php } ?>


                   <div class="module-comments" >
                      
                      <noindex>
                      <?php
                      echo $Blog->outComments(0, $Blog->getComments($data["article"]["blog_articles_id"]));
                      ?>
                      </noindex>
                      
                   </div>

                </div>
            </div>
            <div class="col-lg-3" >
                <?php include $config["basePath"] . "/templates/blog_view_sidebar.tpl"; ?>
            </div>
        </div>

       <?php echo $Banners->out( ["position_name"=>"blog_view_bottom", "current_id_cat"=>$data["article"]["blog_articles_id_cat"], "categories"=>$getCategoryBlog] ); ?>
          
       <div class="mt50" ></div>


    </div>


    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>