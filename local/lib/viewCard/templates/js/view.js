$(document).ready(function () {
   
var url_path = $("body").data("prefix");

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

function tippyLoad(){
    tippy('[data-tippy-placement]', {
      delay: 100,
      arrow: true,
      arrowType: 'sharp',
      size: 'regular',
      duration: 200,
      animation: 'shift-away',
      animateFill: true,
      theme: 'dark',
      distance: 10,
    });
}
 
$(document).on('click','.modal-ad-new-close', function () {    

    var hashes = window.location.href.split('?');
    history.pushState("", "", hashes[0]);

});
   
$(document).on('click','.ads-remove-publication', function () {     
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=remove_publication",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.ads-status-sell', function () {     
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_status_sell",dataType: "html",cache: false,success: function (data) { 
       $("#modal-ads-success-sell").show();
       $("#modal-remove-publication").hide();
       $("body").css("overflow", "hidden");
  }});
});


$(document).on('click','.ads-publication', function () {     
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_publication",dataType: "html",cache: false,success: function (data) { 
     location.href = data;
  }});
});

$(document).on('click','.ads-delete', function () {     
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_delete",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.ads-extend', function () {     
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_extend",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.show-phone', function () {    

  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad=" + $(this).data("id") + "&action=show_phone",dataType: "json",cache: false,success: function (data) { 

     if(data["auth"]){
       $("#modal-view-phone").show();
       $("body").css("overflow", "hidden");          
       $(".modal-view-phone-display").html(data["html"]);
     }else{
       $("#modal-auth").show();
       $("body").css("overflow", "hidden");
     }

  }});

});

$(document).on('click','.ads-services-tariffs', function () {      
  $(".ads-services-tariffs").removeClass("active");
  $(this).addClass("active");
  $(".form-ads-services input[name=id_s]").val( $(this).data("id") );
  $(".ads-services-tariffs-btn-order").show();
});

$(document).on('submit','.form-ads-services', function (e) {   
  $(".ads-services-tariffs-btn-order").prop('disabled', true);   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize()+"&action=service_activation",dataType: "json",cache: false,success: function (data) { 

     var hashes = window.location.href.split('?');
     history.pushState("", "", hashes[0]);

     if(data["status"] == true){
           
           $("#modal-services-access").show();
           $("#modal-order-service").hide();
           $("body").css("overflow", "hidden");

     }else{

        if(data["balance"]){
           
           $("#modal-order-service,#modal-ad-new").hide();
           $("#modal-balance").show();
           $(".modal-balance-summa").html( data["balance"] );
           $("body").css("overflow", "hidden");

        }else{

           alert(data["answer"]);

        }

     }

     $(".ads-services-tariffs-btn-order").prop('disabled', false);

  }});
  e.preventDefault();
});

$(document).on('click','.ads-view-open-map', function (e) {      
  $(".ads-view-map").toggle();
  e.preventDefault();
});

$(document).on('click','.modal-complaint li', function (e) { 

    if( $(this).data("name") == "other" ){
      $(".modal-complaint-tab-1").hide();
      $(".modal-complaint-tab-2").show();          
    }else{
      $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(".ads-form-complaint input[name=id_ad]").val()+"&text="+$(this).data("name")+"&action=complaint",dataType: "json",cache: false,success: function (data) { 
          if( data["auth"] == true ){

            if( data["status"] == true ){
               $(".modal-notification-text").html( data["answer"] );
               $("#modal-complaint").hide();
               $("#modal-notification").show();
               $("body").css("overflow", "hidden");
            }else{
               alert( data["answer"] );
            }

          }else{
               
               $("#modal-complaint").hide();
               $("#modal-auth").show();
               $("body").css("overflow", "hidden");                

          }
      }});          
    }
    

});

$(document).on('submit','.ads-form-complaint', function (e) { 

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize()+"&action=complaint",dataType: "json",cache: false,success: function (data) { 
        if( data["auth"] == true ){

          if( data["status"] == true ){
             $(".modal-notification-text").html( data["answer"] );
             $("#modal-complaint").hide();
             $("#modal-notification").show();
             $("body").css("overflow", "hidden");
          }else{
             alert( data["answer"] );
          }

        }else{
             
             $("#modal-complaint").hide();
             $("#modal-auth").show();
             $("body").css("overflow", "hidden");                

        }
    }});

e.preventDefault();
    
});

$(document).on('click','.modal-complaint-back', function (e) { 
    
    $(".modal-complaint-tab-2").hide();
    $(".modal-complaint-tab-1").show();

});

$('.lightgallery').lightGallery();

 $('.slick').slick({
  slidesToShow: $(".slick").data("count"),
  slidesToScroll: 1,
  arrows: false,
  fade: false,
  infinite: true,
  arrows: true,
  prevArrow: '<div class="variable-photo-prev" > <i class="las la-angle-left"></i> </div>',
  nextArrow: '<div class="variable-photo-next" > <i class="las la-angle-right"></i> </div>',  
  variableWidth: $(".slick").data("width"),
  focusOnSelect: false,
  centerMode: $(".slick").data("center"),
  centerPadding: '20px',
  asNavFor: '.variable-photo-gallery'
});

$('.variable-photo-gallery').slick({
  slidesToShow: 8,
  slidesToScroll: 1,
  asNavFor: '.slick',
  infinite: true,
  dots: false,
  centerMode: false,
  centerPadding: '10px',
  focusOnSelect: true,
  variableWidth: true,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
});

$(document).on('click','.variable-photo-gallery > div', function (e) { 
  $('.slick').slick('slickGoTo', $(this).data("pos") );
});

$(document).on('click','.top-views-change', function (e) { 
  
  $("#modal-top-views").hide();
  $("#modal-order-service").show();
  $("body").css("overflow", "hidden");  

  e.preventDefault();
});

$(document).on('click','.top-views-up', function (e) {   
  $(this).prop('disabled', true);   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&id_s=1&action=service_activation",dataType: "json",cache: false,success: function (data) { 
     
     if(data["status"] == true){
        location.reload();
     }else{
        if(data["balance"]){
           
           $("#modal-order-service,#modal-ad-new,#modal-top-views").hide();
           $("#modal-balance").show();
           $(".modal-balance-summa").html( data["balance"] );
           $("body").css("overflow", "hidden");

        }else{

           alert(data["answer"]);

        }
        $(".top-views-up").prop('disabled', false);
     }

  }});
  e.preventDefault();
});

$(document).on('submit','.form-chat-message', function (e) { 
  if( $(this).find("textarea").val() ){    
      $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize()+"&action=send_chat_message",dataType: "html",cache: false,success: function (data) { 
          $(".modal-notification-text").html(data);
          $("#modal-message").hide();
          $("#modal-notification").show();
          $("body").css("overflow", "hidden");
      }});
  }
  e.preventDefault();
});

$(document).on('click','.list-properties-toggle', function (e) { 

    var status = $(this).attr("data-status");

    if(status == 0){
       $(".list-properties-display").addClass("heightAuto");
       $(this).html( $(".lang-js-7").html() );
       $(this).attr("data-status", 1);
    }else{
       $(".list-properties-display").removeClass("heightAuto");
       $(this).html( $(".lang-js-8").html() );
       $(this).attr("data-status", 0);
    }
    
});

function initializeSlider(){

  var slider = $('.photo-mobile-slider').not('.lightSlider').lightSlider({
      item:1,
      loop:false,
      pager:false,
      slideMargin:2,
      slideMove:1,
      speed:600,
      responsive : [
          {
              breakpoint:800,
              settings: {
                item:1,
                slideMove:1
              }
          },
          {
              breakpoint:480,
              settings: {
                item:1,
                slideMove:1
              }
          }
      ]
  });

}

function similar(){

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_cat=" + $("body").attr("data-id-cat") + "&id_ad=" + $("body").attr("data-id-ad") + "&action=ad_similar",dataType: "html",cache: false,success: function (data) { 
        $(".ajax-container-similar").html(data).show();
        tippyLoad();
        initializeSlider();
    }});

}

$(document).on('click','.action-auction-rate', function (e) { 
    
    var rate = $("#modal-auction input[name=rate]").val();
    var id = $(this).data("id");

      $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&rate="+rate+"&action=auction_rate",dataType: "json",cache: false,success: function (data) { 
          if( data["auth"] == true ){

            if( data["status"] == true ){
               $("#modal-auction").hide();
               $("#modal-auction-success").show();
            }else{
               alert(data["answer"]);
            }

          }else{
             $("#modal-auction").hide();
             $("#modal-auth").show();
          }
      }});

  e.preventDefault();
});

$(document).on('click','.action-auction-cancel-rate', function (e) { 
    
    var id = $(this).data("id");

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=auction_cancel_rate",dataType: "html",cache: false,success: function (data) { 
        location.reload();
    }});

  e.preventDefault();
});

$('[data-countdown="true"]').each(function (index, element) {
    $(element).countdown( $(element).attr("data-date") )
    .on('update.countdown', function(event) {
      var format = '%M '+$(".lang-js-2").html()+' %S ' + $(".lang-js-3").html();
      $(element).html(event.strftime(format));
    })
    .on('finish.countdown', function(event) {
        $(element).removeClass("pulse-time").html( $(".lang-js-6").html() );
    });

});

$(document).on('click','.module-comments-otvet', function () { 

  $(this).parent().parent().find(".module-comments-form-otvet").toggle();
  $("input[name=id_msg]").val( $(this).data("id") );

});

$(document).on('submit','.module-comments-form', function (e) { 

  $(".module-comments-form button").prop('disabled', true);

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize() + "&action=add_comment",dataType: "json",cache: false,                        
        success: function (data){
            if( data["status"] == true ){
               location.reload();
            }else{
               $(".module-comments-form button").prop('disabled', false);
            }                                            
        }
    });

  e.preventDefault();
});

$(document).on('click','.module-comments-delete', function (e) { 

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id=" + $(this).data("id") + "&action=delete_comment",dataType: "json",cache: false,                        
        success: function (data){
            location.reload();                                            
        }
    });

  e.preventDefault();
});



$(window).load(function() { 
    
    $(".ads-view-photo").css("visibility", "visible");
    $(".variable-photo-gallery").css("visibility", "visible");
    similar();

});

$(document).on('click','.action-accept-auction-order-reservation', function (e) { 
    
    var id = $(this).data("id");

    $(this).prop('disabled', true);

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=auction_accept_order_reservation",dataType: "html",cache: false,success: function (data) { 
        location.reload();
    }});

  e.preventDefault();
});



});


