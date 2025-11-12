<div class="text-center" >
  <?php if( $route_name == "auth" ){ ?>
  <a href="<?php echo _link(); ?>"> <i class="las la-arrow-left"></i> <?php echo $ULang->t( "Вернуться на сайт" ); ?> </a>
  <?php } ?>
  <h3 class="text-center" > <strong><?php echo $ULang->t("Вход и регистрация"); ?></strong> </h3>
</div>

<?php if($settings["authorization_option"] == 1){ ?>
<p class="text-center mb30" ><?php echo $ULang->t("Укажите номер телефона и пароль для входа или регистрации на сайте"); ?></p>
<?php }elseif($settings["authorization_option"] == 2){ ?>
<p class="text-center mb30" ><?php echo $ULang->t("Укажите данные для входа или регистрации на сайте"); ?></p>
<?php }elseif($settings["authorization_option"] == 3){ ?>
<p class="text-center mb30" ><?php echo $ULang->t("Укажите e-mail адрес и пароль для входа или регистрации на сайте"); ?></p>
<?php } ?>

<div class="auth-block-right-box-tab-1-1" >
  
  <?php if($settings["authorization_option"] == 1){ ?>
  <input type="text"  class="form-control input-style2-custom phone-mask" data-format="<?php echo $settings["format_phone"]; ?>" placeholder="<?php echo $ULang->t("Номер телефона"); ?>" name="user_login">
  <?php }elseif($settings["authorization_option"] == 2){ ?>
  <input type="text"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Телефон или электронная почта"); ?>" name="user_login">
  <?php }elseif($settings["authorization_option"] == 3){ ?>
  <input type="text"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Электронная почта"); ?>" name="user_login">
  <?php } ?>
  
  <div class="msg-error mb10" data-name="user_login" ></div>

  <input type="password"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Пароль"); ?>" maxlength="25" name="user_pass">
  <div class="msg-error mb10" data-name="user_pass" ></div>  
  
  <div class="auth-captcha" <?php if($_SESSION["auth_captcha"]["status"]){ echo 'style="display: block;"'; } ?> >
    <div class="row" >
      <div class="col-lg-4 col-4" ><img style="margin-top: 5px;" class="captcha-update" src="<?php echo $config["urlPath"]; ?>/systems/captcha/captcha.php?name=auth" ></div>
      <div class="col-lg-8 col-8" ><input type="text"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Укажите код"); ?>" name="captcha"></div>
    </div>
    <div class="msg-error mb10" data-name="captcha" ></div>
  </div>

  <button class="button-style-custom schema-color-button color-green auth-reg-send mt20" data-action="login_pass" ><span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> <?php echo $ULang->t("Продолжить"); ?></button>
  <button class="button-style-custom color-light open-modal mt10" data-id-modal="modal-auth-recovery" ><?php echo $ULang->t("Восстановить пароль"); ?></button>
</div>

<div class="auth-block-right-box-tab-1-2" >
  <p class="auth-block-right-box-tab-1-2-back" > <span><i class="las la-arrow-left"></i> <?php echo $ULang->t("назад"); ?></span> </p>
  <input type="text"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Пароль из сообщения"); ?>" maxlength="5" name="user_code_login">
  <div class="msg-error mb10" data-name="user_code_login" ></div>
  <button class="button-style-custom schema-color-button color-green auth-reg-send mt20" data-action="verify_login" ><span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> <?php echo $ULang->t("Продолжить"); ?></button>                   
</div>

<div class="auth-block-right-box-tab-1-3" >
  <input type="text"  class="form-control input-style2-custom" placeholder="<?php echo $ULang->t("Ваше имя"); ?>" name="user_name">
  <div class="msg-error mb10" data-name="user_name" ></div>
  <button class="button-style-custom schema-color-button color-green auth-reg-send mt20" data-action="reg_finish" ><span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> <?php echo $ULang->t("Завершить регистрацию"); ?></button>                   
</div>

<div class="mt20" ></div>

<?php if($settings["authorization_social"]){ ?>

<p class="text-center" ><?php echo $ULang->t("или через соцсети"); ?></p>

<div class="mt20" ></div>
    
    <div class="auth-list" >

       <?php echo $Profile->socialAuth(); ?> 

    </div>

<?php } ?>

<div class="clr" ></div>

<div class="auth-agreement" >
  <?php echo $ULang->t("Авторизуясь на сайте, Вы принимаете условия"); ?> <a href="<?php echo _link("polzovatelskoe-soglashenie"); ?>"><?php echo $ULang->t("Пользовательского соглашения"); ?></a>, <a href="<?php echo _link("privacy-policy"); ?>"><?php echo $ULang->t("Политики конфиденциальности"); ?></a> <?php echo $ULang->t("и подтверждаете согласие на передачу и обработку своих данных"); ?>
</div>