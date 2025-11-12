$(document).ready(function () {
   
   var url_path = $("body").data("prefix");

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

   $(document).on('click','.auth-forgot', function (e) {   
      $(".msg-error").hide();  
      $(this).find(".button-ajax-loader").show();
      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "login=" + $(".auth-forgot-login").val() + "&action=forgot",dataType: "json",cache: false,success: function (data) { 
          if(data["status"] == true){
              $(".modal-custom-bg").hide();
              $("#modal-auth-recovery-success").show();
              $("body").css("overflow", "hidden");
              $("#modal-auth-recovery-success p").html(data["answer"]);
          }else{
              $(".msg-error[data-name=user_recovery_login]").html(data["answer"]).show();
          }
          $(".button-ajax-loader").hide();
      }});
      e.preventDefault();
   });


   $(document).on('click','.auth-reg-send', function (e) {  
      
      $(".msg-error").hide();
      var this_ = $(this);
      this_.prop('disabled', true);
      $(this).find(".button-ajax-loader").show();

      if($(this).data("action") == "login_pass"){

         $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "user_login=" + $("input[name=user_login]").val() + "&user_pass=" + $("input[name=user_pass]").val() + "&captcha=" + $("input[name=captcha]").val() + "&action=check_auth_login",dataType: "json",cache: false,success: function (data) { 
            this_.prop('disabled', false);

            if(data["captcha"]){
               $(".auth-captcha").show();
            }

            if( data["status"] == true ){
               
               if(data["reg"]){
                  location.reload();
               }else{
                  $(".auth-block-right-box-tab-1-1").hide();
                  $(".auth-block-right-box-tab-1-2").show();
               }

            }else{

               if(data["status_user"] == 2){
                  $("#modal-auth-block").show();
                  $("body").css("overflow", "hidden");
               }else if(data["status_user"] == 3){
                  $("#modal-auth-delete").show();
                  $("body").css("overflow", "hidden");
               }else{

                  $.each( data["answer"] ,function(index,value){
                    
                    $(".msg-error[data-name="+index+"]").html(value).show();

                  });

               }

            }

            $(".button-ajax-loader").hide();   

         }});

      }else if($(this).data("action") == "verify_login"){

         $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "user_login=" + $("input[name=user_login]").val() + "&user_code_login=" + $("input[name=user_code_login]").val() + "&action=verify_login",dataType: "json",cache: false,success: function (data) { 
            this_.prop('disabled', false);

            if( data["status"] == true ){
               
               $(".auth-block-right-box-tab-1-2").hide();
               $(".auth-block-right-box-tab-1-3").show();

            }else{

               $(".msg-error[data-name=user_code_login]").html(data["answer"]).show();

            }

            $(".button-ajax-loader").hide();   

         }});

      }else if($(this).data("action") == "reg_finish"){

         $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "user_login=" + $("input[name=user_login]").val() + "&user_code_login=" + $("input[name=user_code_login]").val() + "&user_pass=" + $("input[name=user_pass]").val() + "&user_name=" + $("input[name=user_name]").val() + "&action=reg_login_finish",dataType: "json",cache: false,success: function (data) { 
            this_.prop('disabled', false);

            if( data["status"] == true ){
               
               location.reload();

            }else{

               $(".msg-error[data-name=user_name]").html(data["answer"]).show();

            }

            $(".button-ajax-loader").hide();   

         }});

      }

      e.preventDefault();

   });


   $(document).on('click','.auth-block-right-box-tab-1-2-back > span', function (e) {  

      $(".auth-block-right-box-tab-1-2").hide();
      $(".auth-block-right-box-tab-1-1").show();

   });


});