$(document).ready(function () {
   
   var url_path = $("body").data("prefix");
   var currentCountPage = 1;
   var loadingAdScroll = true;
   
   $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
   });

   function formFilter(){
        return $.param($('.form-filter').serializeArray().filter(function(el) {
            return $.trim(el.value);
        }));  
   }

   function auctionTime(){
      $('[data-countdown="true"]').each(function (index, element) {
          $(element).countdown( $(element).attr("data-date") )
          .on('update.countdown', function(event) {
            var format = '%M '+$(".lang-js-2").html()+' %S '+$(".lang-js-3").html();
            $(element).html(event.strftime(format));
          })
          .on('finish.countdown', function(event) {
              $(element).removeClass("pulse-time").html( $(".lang-js-1").html() );
          });

      });
   }

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

   function shopLoadAds( _page = 1, _this_button = null, _scroll = false ){

      loadingAdScroll = false;

        if( _this_button != null ) _this_button.prop('disabled', true);
        $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: formFilter() + "&page="+_page+"&action=load_shop_ads",dataType: "json",cache: false,success: function (data) { 
            
            $(".action-load-span-start").hide();
            $(".action-load-span-end").show();

            $(".action-shop-load-ads").hide();
            $(".catalog-results").append('<div class="load-page'+_page+' col-lg-12" ></div><div class="row no-gutters gutters10" style="display: none;" >'+data["content"]+'</div>');
            $('.load-page'+_page).next().fadeIn('slow');

            if(_scroll){

              $('html, body').animate({
              scrollTop: $('.load-page'+_page).offset().top-50
              }, 500, 'linear');

            }

            auctionTime();  
            tippyLoad();   
            initializeSlider();

            loadingAdScroll = data["found"]; 
     
            $(".preload, .preload-scroll").hide();

        }});

   }

   shopLoadAds();

   $(document).on('click','.action-shop-load-ads > button', function () { 
      
      currentCountPage = currentCountPage + 1;
      
      $(".action-load-span-start").show();
      $(".action-load-span-end").hide();

      shopLoadAds( currentCountPage , $(this), true );   
    
   });

   if( $("body").data("type-loading") == 2 ){

      $(window).scroll(function(){ 

         if( ( $(document).scrollTop() + 500 ) >= $(".catalog-results").height() ){
            if(loadingAdScroll == true){

               $(".preload-scroll").show();
               
               currentCountPage = currentCountPage + 1;
              
               shopLoadAds( currentCountPage , null, false );

            }
         }

      });

   }

    
   $('.shop-sliders-items').slick({
        dots: false,
        arrows: true,
        nextArrow: '<span class="sliders-wide-next" style="right: 15px;" ><i class="las la-arrow-right"></i></span>',
        prevArrow: '<span class="sliders-wide-prev" style="left: 15px;" ><i class="las la-arrow-left"></i></span>',
        infinite: true, 
        autoplay: true,         
        slidesToShow: 1,   
        speed: 300,
        centerMode: false
   });


   $(document).on('click','.shop-container-sliders-img span', function () {  

        var el = $(this).parent(); 
      
        $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "id="+$(this).data("id")+"&action=delete_slide",dataType: "html",cache: false,
               success: function (data) {
                   el.hide();
               }
        });

   });

   $(document).on('click','.shop-container-sliders-add', function () {     
       $(".shop-slider-form input").click();
   });

   $(document).on('click','.action-shop-slide-accept', function () {     
       location.reload();
   });

   $(document).on('change','.shop-slider-form input', function () {
      if(this.files.length > 0){
          var data = new FormData($('.shop-slider-form')[0]);   
          data.append('action', 'add_slide');    
          $.ajax({
              type: "POST",url: url_path + "systems/ajax/shop.php",data: data,dataType: "json",cache: false,contentType: false,processData: false,                                                
              success: function (data) {
                  
                  if( data["status"] == true ){
                      $(".shop-container-sliders-append").append(data["img"]);
                  }else{
                      alert( data["answer"] );
                  }

                  $(".shop-slider-form input").val("");
                                                              
              }
          });
      }
   });

   $(document).on('click','.user-subscribe', function () {    
      $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "id_shop=" + $(this).data("shop") + "&id_user=" + $(this).data("id") + "&action=subscribe",dataType: "json",cache: false,success: function (data) { 

         if(data["auth"]){
           location.reload();
         }else{
           $("#modal-auth").show();
           $("body").css("overflow", "hidden");
         }

      }});
   });

   $(document).on('click','.action-shop-add-page', function () {    
    $(".action-shop-add-page").prop('disabled', true);
    $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: $(".form-shop-add-page").serialize() + "&action=add_page",dataType: "json",cache: false,success: function (data) { 

       if(data["status"] == true){
         location.href = data["link"];
       }else{
         alert( data["answer"] );
       }

       $(".action-shop-add-page").prop('disabled', false);

    }});
   });

   $(document).on('click','.shop-page-control-delete', function () {   

      if (confirm( $(".lang-js-9").html() )) {
        
          $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "id_shop=" + $(this).data("shop") + "&id_page=" + $(this).data("page") + "&action=delete_page",dataType: "html",cache: false,success: function (data) { 

            location.href = data;
      
          }});
        
      }

   });

   $(document).on('click','.shop-page-control-save', function () {   
        $(".shop-page-control-save").prop('disabled', true);
        $.ajax({type: "POST",url: url_path + "systems/ajax/shop.php",data: "text=" + encodeURIComponent( theEditor.getData() ) + "&id_shop=" + $(this).data("shop") + "&id_page=" + $(this).data("page") + "&action=save_text_page",dataType: "html",cache: false,success: function (data) { 

          location.reload();
    
        }});
      
   });

    $(document).on('change','.form-filter input', function (e) { 

        var id_filter = $(this).closest(".filter-items").attr("id-filter");
        var id_parent = $(this).closest(".filter-items").attr("main-id-filter");
        var id_item = $(this).val();
        var element = $(this);
        
        if($(this).closest(".filter-items").attr("data-ids") != undefined){
           var ids = $(this).closest(".filter-items").attr("data-ids").split(",");
        }

        if(ids){

          $.each(ids,function(index,value){

            $('div[id-filter="'+value+'"]').remove();

          });

        }

        if($(this).val() != "null"){ 
            
                $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_filter="+id_filter+"&id_item="+id_item+"&view=catalog&action=load_items_filter",dataType: "html",cache: false,success: function (data) {

                    element.closest(".filter-items").after(data);

                }});

        }

        e.preventDefault();
    });

    $(document).on('click','.toggle-list-options > span', function (e) { 
        
        $(this).parent().find(".catalog-list-options-content").toggle();
        $(this).parent().toggleClass("catalog-list-options-active");

    });

    function setFiltersUrl( selector ){

      var hashes = window.location.href.split('?');
          
      var params = $.param(selector.serializeArray().filter(function(el) {
              return $.trim(el.value);
          }));

          history.pushState("", "", hashes[0]+"?"+params);

    }

    $(document).on('click','.submit-filter-form', function (e) { 

        setFiltersUrl( $(this).parents(".form-filter") );
        location.reload();

        e.preventDefault();

    });

    $(document).on("click", ".action-clear-filter", function(e) {

       location.href = window.location.href.split("?")[0];
       e.preventDefault();

    });

    $(document).on('click','.profile-user-block', function (e) {  

      $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id=" + $(this).data("id") + "&action=profile_user_locked",dataType: "json",cache: false,success: function (data) { 

         location.reload();

      }});

      e.preventDefault();

    });

    $(document).on('input','.catalog-list-options-search input', function () {
      var str = $(this).val().toLowerCase();
        
        $(this).parent().parent().find('.catalog-list-options-items > div').show();
        
        $(this).parent().parent().find('.catalog-list-options-items > div').each(function(){
          if ($(this).find("label").text().toLowerCase().indexOf(str) < 0){
              $(this).hide();
          }
        });  
          
    });

    $(document).on('click','.toggle-shop-filter-menu', function () {
       
       $(".modal-shop-filters-container").html("");
       $(".sidebar-shop-filter").clone().appendTo(".modal-shop-filters-container");
       $("#modal-shop-filters").show();
       $("body").css("overflow", "hidden");

    });



});
