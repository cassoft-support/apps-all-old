<!doctype html>
<html lang="<?php echo getLang(); ?>">
   <head>
      
      <title><?php echo $ULang->t("Публикация объявления"); ?></title>
      
      <?php include $config["basePath"] . "/templates/head.tpl"; ?>

   </head>
   <body data-prefix="<?php echo $config["urlPrefix"]; ?>" >

     <?php include $config["basePath"] . "/templates/header.tpl"; ?>

      <div class="ads-create-update-container" >
        
           <div class="row" >
              
              <div class="col-lg-12" >
                <h1 class="h1title mt30 mb30" > <?php echo $ULang->t("Публикация объявления"); ?> </h1>
              </div>

              <div class="col-lg-9" >
                             
                   <form class="ads-form-ajax" >

                      <div class="ads-create-main-category mt30" >
                          
                          <div class="row" >
                             <?php
                                if(count($getCategoryBoard["category_board_id_parent"][0])){
                                  foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {
                                    ?>
                                      <div class="col-lg-3 col-6" >
                                        <div class="ads-create-main-category-list-item" data-id="<?php echo $value["category_board_id"]; ?>" >

                                          <div class="ads-create-main-category-icon-circle" style="background-color: <?php echo generateRandomColor(); ?>" ></div>

                                            <span class="ads-create-main-category-icon" >
                                              
                                            <img alt="<?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?>" src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>">

                                            </span>
                                            <span class="ads-create-main-category-name" ><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span>

                                        </div>
                                      </div>
                                    <?php
                                  }
                                }
                             ?>
                          </div>

                      </div>

                      <div class="ads-create-subcategory" ></div>

                      <div class="msg-error" data-name="c_id" ></div>

                      <div class="ads-create-main-data" >
                        
                           <div class="ads-create-main-data-title" ></div>
                           
                           <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
                               <p class="ads-create-subtitle" ><?php echo $ULang->t("Фотографии"); ?></p>
                               <p class="create-info" > <i class="las la-question-circle"></i> <?php echo $ULang->t("Первое фото будет отображаться в результатах поиска, выберите наиболее удачное. Вы можете загрузить до"); ?> <?php echo $settings["count_images_add_ad"]; ?> <?php echo $ULang->t("фотографий в формате JPG или PNG. Максимальный размер фото"); ?> — <?php echo $settings["size_images_add_ad"]; ?>mb.</p>

                               <div id="dropzone" class="dropzone mt20 sortable" id="dropzone" ></div>
                               <div class="msg-error" data-name="gallery" ></div>
                           </div>
                           
                           <div class="ads-create-main-data-box-item" >
                               <p class="ads-create-subtitle" ><?php echo $ULang->t("Видео"); ?></p>
                               <p class="create-info" > <i class="las la-question-circle"></i> <?php echo $ULang->t("Можно добавить ссылку на видеоролик в Youtube, Rutube или Vimeo"); ?></p>
                               <input type="text" name="video" class="ads-create-input mt20" >
                           </div>
                           
                           <div class="ads-create-main-data-box-item" >
                               <p class="ads-create-subtitle" ><?php echo $ULang->t("Описание"); ?></p>
                               <textarea name="text" class="ads-create-textarea" rows="5" ></textarea>  
                               <p class="create-input-length" ><?php echo $ULang->t("Символов"); ?> <span>0</span> <?php echo $ULang->t("из"); ?> <?php echo $settings["ad_create_length_text"]; ?></p>
                               <div class="msg-error" data-name="text" ></div> 
                           </div> 

                           <?php if( $settings["ad_create_period"] ){ ?>
                           <div class="ads-create-main-data-box-item" >
                                <p class="ads-create-subtitle" ><?php echo $ULang->t("Срок публикации"); ?></p>
                                <div class="row" >
                                  <div class="col-lg-6" >
                                    
                                       <div class="uni-select" data-status="0">

                                           <div class="uni-select-name" data-name="<?php echo $ULang->t("Не выбрано"); ?>"> <span><?php echo $ULang->t("Не выбрано"); ?></span> <i class="la la-angle-down"></i> </div>
                                           <div class="uni-select-list">
                                               
                                                <?php echo $list_period; ?>
                                
                                           </div>
                                          
                                        </div>

                                  </div>
                                </div>
                            </div>
                            <?php } ?>                 

                           <div class="ads-create-main-data-filters" ></div>   

                           <div class="ads-create-main-data-price" ></div>     
                           <div class="ads-create-main-data-online-view" ></div>     

                           <div class="ads-create-main-data-box-item" >
                              
                              <p class="ads-create-subtitle" ><?php echo $ULang->t("Город"); ?></p>

                              <div class="container-custom-search" >
                                <input type="text" autocomplete="nope" class="ads-create-input action-input-search-city" placeholder="<?php echo $ULang->t("Начните вводить город, а потом выберите его из списка"); ?>" >
                                <div class="custom-results SearchCityResults SearchCityOptions" ></div>
                              </div>

                              <div class="msg-error" data-name="city_id" ></div>
                              <input type="hidden" name="city_id" value="0" > 

                           </div>

                           <div class="ads-create-main-data-city-options" ></div> 

                           <div class="ads-create-main-data-box-item" >
                                
                                <p class="ads-create-subtitle" ><?php echo $ULang->t("Адрес"); ?></p>

                                <div class="boxSearchAddress" >
                                   <input type="text" class="ads-create-input searchMapAddress" id="searchMapAddress" autocomplete="nope" name="address" placeholder="<?php echo $ULang->t("Начните вводить адрес, а потом выберите его из списка"); ?>" >
                                   <div class="custom-results SearchAddressResults" ></div>
                                </div>

                                <div class="msg-error" data-name="address" ></div>

                                <div class="mapAddress" id="mapAddress" ></div>
                                <input type="hidden" name="map_lat" value="0" >
                                <input type="hidden" name="map_lon" value="0" >

                           </div>
                           
                           <?php if( $data["display_phone"] ){ ?>
                           <div class="ads-create-main-data-box-item" >

                              <p class="ads-create-subtitle" ><?php echo $ULang->t("Номер телефона"); ?></p>

                              <div class="ads-create-main-data-user-options" >
                                  
                                  <?php echo $ULang->t("Для публикации объявления необходимо указать номер телефона. Скрыть его или изменить Вы сможете в настройках профиля."); ?>
                                  
                                  <div class="ads-create-main-data-user-options-phone-1" >
                                  <div class="mt15" >
                                      <div class="row" >
                                         <div class="col-lg-6" >
                                            <input type="text" name="phone" class="ads-create-input phone-mask create-phone" maxlength="30" data-format="<?php echo $settings["format_phone"]; ?>" placeholder="<?php echo $ULang->t("Номер телефона"); ?>" >
                                            <div class="msg-error" data-name="phone" ></div>
                                         </div>
                                         <div class="col-lg-6" >
                                            <button class="btn-custom btn-color-green create-accept-phone" ><?php echo $ULang->t("Подтвердить"); ?></button>
                                         </div>                               
                                      </div>
                                  </div>
                                  </div>

                                  <div class="ads-create-main-data-user-options-phone-2" >
                                  <div class="mt15" >
                                      <div class="row" >
                                         <div class="col-lg-6" >
                                            <input type="text" class="ads-create-input create-verify-phone" maxlength="4" placeholder="<?php echo $ULang->t("Код из смс"); ?>" >
                                            <div class="msg-error" data-name="verify-code" ></div>
                                         </div>
                                         <div class="col-lg-6" >
                                            <button class="btn-custom btn-color-blue create-cancel-phone" ><?php echo $ULang->t("Изменить номер"); ?></button>
                                         </div>                               
                                      </div>
                                  </div>
                                  </div>                        


                              </div>
                           </div>
                           <?php } ?>
                           
                           <button class="ads-create-publish btn-color-blue" data-action="ad-create" > <span class="action-load-span-start" > <i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> </span> <?php echo $ULang->t("Опубликовать"); ?></button>
                           
                      </div>


                      <input type="hidden" name="currency" value="<?php echo $settings["currency_main"]["code"]; ?>" >
                      <input type="hidden" name="c_id" value="0"  >
                      <input type="hidden" name="var_price" value=""  >

                   </form>
                                  
              </div>

              <div class="col-lg-3" >
                
                <div class="mt30 ad-create-sidebar" >
                    
                    

                </div>

              </div>

           </div>

      </div>


      <div class="mt45" ></div>

      <?php include $config["basePath"] . "/templates/footer.tpl"; ?>

      <script type="text/javascript">
      Dropzone.autoDiscover = false;
      $(document).ready(function() {

        $( ".sortable" ).sortable({ handle: '.sortable-handle', zIndex: 1000 });

        var myDrop= new Dropzone("#dropzone", {
          paramName: "file",
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },          
          acceptedFiles: "image/jpeg,image/png",
          maxFiles: <?php echo $settings["count_images_add_ad"]; ?>,
          url: $("body").data("prefix") + 'systems/ajax/dropzone.php',
          maxFilesize: <?php echo $settings["size_images_add_ad"]; ?>,
          timeout: 300000,
          dictDefaultMessage: '<?php echo $ULang->t('Выберите или перетащите изображения'); ?>',
          init: function() {
              thisDropzone = this;
              this.on("addedfile", function(file) {
                  var removeButton = Dropzone.createElement("<div class='dz-dropzone-delete' ><i class='las la-trash-alt'></i></div>");
                  var sortableButton = Dropzone.createElement("<div class='dz-dropzone-sortable sortable-handle' ><i class='las la-arrows-alt'></i></div>");
                  var _this = this;
                  removeButton.addEventListener("click", function(e) {
                      e.preventDefault();
                      e.stopPropagation();
                      _this.removeFile(file);
                  });
                  file.previewElement.appendChild(removeButton);
                  file.previewElement.appendChild(sortableButton);
              });
              this.on('completemultiple', function(file, json) {
              });        
          },
          success: function(file, response){

              var response = jQuery.parseJSON( response );
              file.previewElement.appendChild( Dropzone.createElement(response["input"]) );

              $( file.previewTemplate ).find("img").attr( "src", response["link"] );
              $( file.previewTemplate ).find("img").addClass( "image-autofocus" );
      
          }          
        });
      });
      </script>

      <?php echo $Geo->vendorMap(); ?>

      <?php echo $Ads->mapAdAddress(); ?>

   </body>
</html>