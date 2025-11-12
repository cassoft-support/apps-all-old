<noindex>
<div class="index-sidebar-subscribe" >
   
   <img src="<?php echo $settings["path_tpl_image"] ?>/mail_subscribe.png" >

   <h5> <strong><?php echo $ULang->t("Новостная подписка"); ?> <?php echo $ULang->t($settings["site_name"]); ?></strong> </h5>
   
   <form class="form-user-subscribe" >
   <p><?php echo $ULang->t("Подпишитесь на нашу рассылку, чтобы получать актуальные новости и акции сервиса."); ?></p>

   <input type="text" name="email" class="form-control" placeholder="<?php echo $ULang->t("Ваш E-mail"); ?>" >

   <button class="btn-custom btn-color-blue" ><?php echo $ULang->t("Подписаться"); ?></button>
   </form>

   <p class="user-subscribe-success" ><?php echo $ULang->t("Подписка почти оформлена. Мы отправили на вашу почту письмо для подтверждения подписки."); ?></p>
  
</div>
</noindex>

<?php if($data["article_rand"]["count"]){ ?>
<h3 class="mt20 mb15 blog-view-article-rand-title" > <strong><?php echo $ULang->t("Блог"); ?></strong> </h3>

<div class="blog-view-article-rand" >   
  <div class="row" >
  <?php
  
    foreach ($data["article_rand"]["all"] as $key => $value) {
      ?>
      <div class="col-lg-12 col-6" >
        <a href="<?php echo $Blog->aliasArticle($value); ?>"  >
         <div class="view-article-rand-image" style="background-image: url(<?php echo Exists($config["media"]["big_image_blog"],$value["blog_articles_image"],$config["media"]["no_image"]); ?>);" ></div>
         <p><?php echo $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?></p>
        </a>
        </div>
      <?php
    }
  
  ?>
  </div>
</div>
<?php } ?>

<?php echo $Banners->out( ["position_name"=>"index_sidebar"] ); ?>