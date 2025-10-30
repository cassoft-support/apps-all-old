<div class="col-lg-4" >
<div class="article-item" >
   <div class="article-item-image" > <a href="<?php echo $Blog->aliasArticle($value); ?>"> <img alt="<?php echo $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?>" class="image-autofocus" src="<?php echo Exists($config["media"]["big_image_blog"],$value["blog_articles_image"],$config["media"]["no_image"]); ?>" > </a> </div>
   <div class="article-item-content" >

   	  <div> <span class="article-item-cat-name" ><?php echo $ULang->t( $value["blog_category_name"] , [ "table"=>"uni_blog_category", "field"=>"blog_category_name" ] ); ?></span> </div>
      <a href="<?php echo $Blog->aliasArticle($value); ?>"><?php echo custom_substr( $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ) , 50, "..."); ?></a>
      <span><i class="la la-clock"></i> <?php echo datetime_format($value["blog_articles_date_add"], false); ?> <i class="la la-eye ml5ib"></i> <?php echo $value["blog_articles_count_view"]; ?></span>

   </div>
</div>
</div>