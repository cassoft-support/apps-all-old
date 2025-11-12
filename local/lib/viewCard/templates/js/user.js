$(document).ready(function () {
   
   var url_path = $("body").data("prefix");
   var id_dialog = 0;
   var id_review = 0;

   $.ajaxSetup({
       headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

   $('.lightgallery').lightGallery();

   function loadChat( event ){

     id_dialog = event.data("id");

     if(id_dialog){

        $(".module-chat-users > div").removeClass("active");
        event.addClass("active");

        $(".module-chat-dialog").html('<div class="chat-dialog-spinner"><div class="spinner-border text-primary" role="status"></div></div>');

         $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + id_dialog + "&action=load_chat",dataType: "json",cache: false,success: function (data) { 
            $(".module-chat-dialog").html(data["dialog"]);
            if(data["count_msg"]) $(".update-count-message").html(data["count_msg"]); else $(".update-count-message").hide();
            $("[data-id="+id_dialog+"]").find(".module-chat-users-count-msg").hide();
            $(".module-chat-dialog-content").scrollTop($(".module-chat-dialog-content").get(0).scrollHeight);
            $(".module-chat-mobile .module-chat-dialog-content").scrollTop($(".module-chat-mobile .module-chat-dialog-content").get(0).scrollHeight);
         }});
         
      }

   }

   loadChat( $(".module-chat-users .active") );

   $(document).on('click','.module-chat-users > div', function () {  
      
     loadChat( $(this) );

     $(".module-chat-mobile .module-chat-users").hide();
     $(".module-chat-mobile .module-chat-dialog").show();
     $(".module-chat-mobile .module-chat-dialog-prev").show();

   });

   $(document).on('click','.module-chat-dialog-prev span', function () {  
      
     $(".module-chat-mobile .module-chat-users").show();
     $(".module-chat-mobile .module-chat-dialog").hide();
     $(".module-chat-mobile .module-chat-dialog-prev").hide();

   });

   $(document).on('click','.chat-dialog-send', function (e) {  

   	  var this_ = $(this);
      var text = $(this).parents(".module-chat-dialog-footer").find("textarea").val();

      if(text){

      this_.prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + id_dialog + "&text=" + encodeURIComponent(text)  + "&action=send_chat",dataType: "json",cache: false,success: function (data) { 
         
         $(".module-chat-dialog").html(data["dialog"]);
         $(".chat-dialog-text").val("");

         $(".module-chat-dialog-content").scrollTop($(".module-chat-dialog-content").get(0).scrollHeight);
         $(".module-chat-mobile .module-chat-dialog-content").scrollTop($(".module-chat-mobile .module-chat-dialog-content").get(0).scrollHeight);

         $('html, body').animate({
          scrollTop: $(".module-chat-mobile").offset().top
         }, 200, 'linear');

         this_.prop('disabled', false);

      }});

      }

      e.preventDefault();

   });

   $(document).on('click','.user-avatar-replace', function (e) { $('#user-form-avatar > input').click(); });
   $('#user-form-avatar > input').on('change', function(){ 

        var data_form = new FormData($('#user-form-avatar')[0]);
        data_form.append('action', 'user-avatar'); 

        $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: data_form,dataType: "json",cache: false,contentType: false,processData: false,                        
            success: function (data){
               if(!data["error"]){ 
                  $('.user-avatar-img img').attr("src",data["img"]);
               }else{
                  alert(data["error"]);
               }                                            
            }
        });

   });

   $(document).on('click','.chat-user-delete', function (e) {  

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + id_dialog + "&action=delete_chat",dataType: "json",cache: false,success: function (data) { 

         if(data["count_chat_users"]){        
            $(".module-chat-dialog").html(data["dialog"]);
            $("[data-id="+id_dialog+"]").hide();
         }else{
            $(".module-chat").html(data["dialog"]); 
         }

         $(".modal-custom-bg").hide();
         $("body").css("overflow", "auto"); 

      }});

      e.preventDefault();

   });

   $(document).on('click','.chat-user-block', function (e) {  

      $('.chat-user-block').prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + id_dialog + "&action=chat_user_locked",dataType: "json",cache: false,success: function (data) { 

         $(".module-chat-dialog").html(data["dialog"]);
         $(".modal-custom-bg").hide();
         $("body").css("overflow", "auto");
         $('.chat-user-block').prop('disabled', false);

      }});

      e.preventDefault();

   });

   $(document).on('click','.profile-user-block', function (e) {  

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + $(this).data("id") + "&action=profile_user_locked",dataType: "json",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

   });

   $(document).on('click','.dialog-header-menu i', function (e) {  

      $(".chat-options-list").toggle();

   });


   $(document).on('click','.user-menu-tab > div', function () {  

      var tab = $(this).data("id-tab");

      $(".user-menu-tab-content").hide();
      $(".user-menu-tab > div").removeClass("active");
      $(this).addClass("active");
      $(".user-menu-tab-content[data-id-tab="+tab+"]").show();

   });

   $(document).on('click','.user-balance-payment > div', function () {  

      $(".user-balance-payment > div").removeClass("active");
      $(this).addClass("active");
      $(this).find("input").prop("checked", true);

   });

   $(document).on('click','.user-balance-summa > div', function () {  

      $(".user-balance-summa > div").removeClass("active");
      $(this).addClass("active");
      $(this).find("input").prop("checked", true);

      if( $(this).find("input").val() ){
         $(".balance-input-amount").hide();
      }else{
         $(".balance-input-amount").show();
      }

   });

   $(document).on('submit','.form-balance', function (e) {  

      $('.form-balance').find("button").prop("disabled", true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: $(this).serialize() + "&action=balance_payment",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            if( data["redirect"]["link"] ){
               location.href = data["redirect"]["link"];
            }else{
               $(".redirect-form-pay").html(data["redirect"]["form"]);
               $(".form-pay .pay-trans").click();               
            }
         }else{
            alert(data["answer"]);
         }

         $('.form-balance').find("button").prop("disabled", false);

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-mobile-menu > span', function (e) {  

      $(this).next().toggle();

   });

   $(document).on('submit','.user-form-settings', function (e) {  

      $(".user-form-settings button").prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: $(this).serialize() + "&action=user_edit",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){

            location.href = data["location"];

         }else{

            $(".user-form-settings button").prop('disabled', false);

            var temp = [];

            $.each( data["answer"] ,function(index,value){
              
              temp.push(index);

              $(".msg-error[data-name="+index+"]").html(value).show();

            });

            $('html, body').animate({ scrollTop: $(".msg-error[data-name="+temp[0]+"]").offset().top-130 }, 500);

         }

      }});

      e.preventDefault();

   });

  $(document).on('change','input[name=status]', function (e) { 
      
      if($(this).val() == "user"){
         $(".user-name-company").hide();
      }else{
         $(".user-name-company").show();
      }

  });

   $(document).on('click','.user-edit-pass', function (e) {  

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "user_current_pass=" + $("input[name=user_current_pass]").val() + "&user_new_pass=" + $("input[name=user_new_pass]").val() + "&action=user_edit_pass",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            location.reload();
         }else{
            alert(data["answer"]);
         }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-edit-email', function (e) {  
      
      $('.user-edit-email').prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "user_email=" + $("input[name=email]").val() + "&action=user_edit_email",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            $(".confirm-edit-email").html(data["answer"]).show();
            $("input[name=email]").hide();
         }else{
            alert(data["answer"]);
            $('.user-edit-email').prop('disabled', false);
         }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-edit-phone-send', function (e) {  
      
      $('.user-edit-phone-send').prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "phone=" + $("input[name=phone]").val() + "&action=user_edit_phone_send",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            $("#modal-edit-phone input[name=code]").show();
            $("#modal-edit-phone input[name=phone]").hide();
            $(".user-edit-phone-send").hide();
            $(".user-edit-phone-save").show();
         }else{
            alert(data["answer"]);
            $('.user-edit-phone-send').prop('disabled', false);
         }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-edit-phone-save', function (e) {  
      
      $('.user-edit-phone-save').prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "code=" + $("input[name=code]").val() + "&phone=" + $("input[name=phone]").val() + "&action=user_edit_phone_save",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            location.reload();
         }else{
            alert(data["answer"]);
            $('.user-edit-phone-save').prop('disabled', false);
         }

      }});

      e.preventDefault();

   });

   $(document).on('change','.modal-edit-notifications-content input', function (e) {  
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: $(".form-edit-notifications").serialize() + "&action=user_edit_notifications",dataType: "html",cache: false,success: function (data) { 
      }});

      e.preventDefault();

   });

   $(document).on('click','.user-edit-bank-card', function (e) {  
      
      $(this).prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "bank_card=" + $("input[name=bank_card]").val() + "&action=user_edit_bank_card",dataType: "json",cache: false,success: function (data) { 

         if(data["status"]){
            location.reload();
         }else{
            alert(data["answer"]);
            $('.user-edit-bank-card').prop('disabled', false);
         }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-delete-review', function (e) {  
      
      $(this).prop('disabled', true);

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + id_review + "&action=delete_review",dataType: "html",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-review-item-delete', function (e) {  
      
      id_review = $(this).data("id");

   });

   $(document).on('click','.profile-subscriptions-ad-delete', function (e) {  
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + $(this).data("id") + "&action=delete_ads_subscriptions",dataType: "html",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

   });

   $(document).on('click','.profile-subscriptions-ad-period', function (e) {  
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + $(this).data("id") + "&period=" + $(this).data("period") + "&action=period_ads_subscriptions",dataType: "html",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

   });

   $(document).on('click','.profile-subscriptions-shop-delete', function (e) {  

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + $(this).data("id") + "&action=delete_shop_subscriptions",dataType: "html",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

   });

   $(".js-range-slider").ionRangeSlider({
        skin: "round",
        onStart: function (data) {
          
            $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "month=" + data.from + "&action=price_calc",dataType: "html",cache: false,success: function (data) { 

                $(".js-shop-price-calc").html(data);

            }});

        },
        onFinish: function (data) {
          
            $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "month=" + data.from + "&action=price_calc",dataType: "html",cache: false,success: function (data) { 

                $(".js-shop-price-calc").html(data);

            }});

        }
   });

   $(document).on('click','.user-open-shop', function (e) {  

      $(this).prop('disabled', true);
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "month=" + $("input[name=open-shop-month]").val() + "&action=open_shop",dataType: "json",cache: false,success: function (data) { 

           if( data["status"] == true ){
              
              location.reload();

           }else{

              $(this).prop('disabled', false);

              $("#modal-open-shop").hide();
              $("#modal-balance").show();
              $(".modal-balance-summa").html( data["balance"] );
              $("body").css("overflow", "hidden");

           }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-extend-shop', function (e) {  

      $(this).prop('disabled', true);
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "month=" + $("input[name=extend-shop-month]").val() + "&action=extend_shop",dataType: "json",cache: false,success: function (data) { 

           if( data["status"] == true ){
              
              location.reload();

           }else{

              $(this).prop('disabled', false);

              $("#modal-extend-shop").hide();
              $("#modal-balance").show();
              $(".modal-balance-summa").html( data["balance"] );
              $("body").css("overflow", "hidden");

           }

      }});

      e.preventDefault();

   });

   $(document).on('submit','.user-form-shop', function (e) {  

      $(".user-form-shop button").prop('disabled', true);

      var data_form = new FormData($(this)[0]);
      data_form.append('action', 'edit_shop'); 

      $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: data_form,dataType: "json",cache: false,contentType: false,processData: false,success: function (data) { 

         if(data["status"]){

            location.reload();

         }else{

            $(".user-form-shop button").prop('disabled', false);

            var temp = [];

            $.each( data["answer"] ,function(index,value){
              
              temp.push(index);

              $(".msg-error[data-name="+index+"]").html(value).show();

            });

            $('html, body').animate({ scrollTop: $(".msg-error[data-name="+temp[0]+"]").offset().top-130 }, 500);

         }

      }});

      e.preventDefault();

   });

   $(document).on('click','.user-delete-shop', function (e) {  

      $(this).prop('disabled', true);
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "id=" + $(this).data("id") + "&action=delete_shop",dataType: "html",cache: false,success: function (data) { 

           location.reload();

      }});

      e.preventDefault();

   });

   $('.user-shop-logo-delete').on('click', function(){ 

        $(".user-shop-logo-container").html( '<input type="file" name="image" class="input-user-shop-logo" ><div class="msg-error" data-name="image" ></div>' );

   });
   
   var change_ad_review = 0;

   $('.user-add-review-list-ads .mini-list-ads').on('click', function(){ 

       change_ad_review = $(this).data("id");
       $(".user-add-review-tab-1").hide();
       $(".user-add-review-tab-2").show();
       $(".form-user-add-review input[name=id_ad]").val(change_ad_review);
        
   });

   $('.user-add-review-tab-prev').on('click', function(){ 

       var parent = $(this).parents(".user-add-review-tab");

       parent.hide();
       parent.prev().show();

       $('.user-add-review-tab-2 input[type=radio]').prop("checked", false);
        
   });

   $('.user-add-review-tab-2 input[type=radio]').on('change', function(){ 

       $(".user-add-review-tab-1, .user-add-review-tab-2").hide();
       $(".user-add-review-tab-3").show();
        
   });

   function getRandomInt(min, max)
   {   
       return Math.floor(Math.random() * (max - min + 1)) + min;
   }

   function attach_reviews(input) {

      var data = new FormData();
      $.each( input.files, function( key, value ){
          data.append( key, value );
      });

      data.append('action', 'load_reviews_attach_files');
     
     var i = 0;
     var count_load_img = input.files.length;

      while (i < count_load_img) {

        if (input.files && input.files[i]) {
            var reader = new FileReader();
            
            reader.onload = function (e) { 


                    var uid = getRandomInt(10000, 90000);
                    
                    $(".user-add-review-attach-files").append('<div class="id'+uid+' attach-files-preview attach-files-loader" ><img class="image-autofocus" src="'+e.target.result+'" /></div>'); 
            
               
            };

            reader.readAsDataURL(input.files[i]);
        }
        
        i++
      }
   
      $.ajax({url: url_path + "systems/ajax/profile.php",type: 'POST',data: data,cache: false,dataType: 'html',processData: false,contentType: false,
          success: function( respond, textStatus, jqXHR ){

               $(".user-add-review-attach-files").append(respond);
               $(".attach-files-loader").hide();
               $('.form-user-add-review button').prop('disabled', false);

          }
      });

      $(".input_attach_files").val("");

   }

   $(document).on('click','.user-add-review-attach-change', function () { $('.input_attach_files').click(); });
   $(document).on('change','.input_attach_files', function () {  
       if(this.files.length > 0){  
          $('.form-user-add-review button').prop('disabled', true);
          attach_reviews(this);
       }   
   });

    $(document).on("click", ".attach-files-delete", function(e) {
        $(this).parents(".attach-files-preview").hide().remove();
        e.preventDefault();
    });

    $(document).on('submit','.form-user-add-review', function (e) {  

      $(this).prop('disabled', true);
      
      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: $(this).serialize() + "&action=add_review_user",dataType: "json",cache: false,success: function (data) { 

           if( data["status"] == true ){
               location.reload();
           }else{
               alert( data["answer"] );
           }

      }});

      e.preventDefault();

    });

   var cookieOptions = {expires: 30, path: '/'};

    $(document).on("click", ".warning-seller-safety-close", function(e) {
        $(".user-warning-seller-safety").hide();
        $.cookie("seller-safety", "hide", cookieOptions);
    });

    if($.cookie("seller-safety") != "hide"){
       $(".user-warning-seller-safety").show();
    }

});