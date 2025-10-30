<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Отписка от рассылок"); ?></title>

    <?php include $config["basePath"] . "/templates/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>">
    
    <?php include $config["basePath"] . "/templates/header.tpl"; ?>

    <div class="container" >
       
        <div class="row" >
            <div class="col-lg-12" >

              <div class="minheight400" >

               <div class="icon-circle-status mt50" >
                  <div class="status-green" >
                    <i class="las la-check"></i>
                  </div>
               </div>

                <h4 class="text-center mt30" > <strong><?php echo $ULang->t("Вы успешно отписались от рассылок сайта"); ?></strong> </h4>

              </div>

            </div>
        </div>
         
          
       <div class="mt50" ></div>


    </div>


    <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

  </body>
</html>