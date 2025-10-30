<?
$file_log = "/home/bitrix/www/local/components/brokci_settings/plan_type/ajax/logCreate.txt";
//$file_log = "/local/components/brokci_settings/plan_type/ajax/logEdit.txt";
    function d($val)
    {
        echo "<pre>";
        print_r($val);
        echo "</pre>";
    }
    
    //pr($Result['assigned_id']);
  //  file_put_contents($file_log, print_r("create",true));
   // file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
?>
<div class="slider-card slideLeft" >
                                                                        <div class=" slider-card-header">
                                                                                <div>
                                                                                <!--   <img class="cassoft-install-image" src="/pub/images/brokci.png" alt="brokci-logo">-->
                                                                                </div>
                                                                                <div> Создание стадий для избранных </div>
                                                                        </div>
                                                                        <div class="slider-card-title" style=" display:none;">
                                                                        <div class=""><h4>Заполните форму</h4></div>
                                                                        
                                                                        </div>

                                                                <div id="panel-close" class="slider-card-panel-close" >
                                                                                <div class="slider-card-panel-close-icon"><i class="fa fa-times"></i> </div>
                                                                                <div class="slider-card-panel-close-label" > <span class="">ЗАКРЫТЬ</span></div>
                                                                </div>
                               <div class="row-cards"style="margin-bottom:50px;">
                                                                         
                                                                                        <div class="add-item" style="margin-right: 30px; display:none;">
                                                                                                <span  class="add-label">Создание подборки </span>
                                                                                                
                                                                                        </div>
                                                         <div class="row-cards"style="margin-bottom:50px;">             
                                                                         <div class="add-item" style="margin-right: 30px;">
                                                                                    <span for="NAME" class="add-label">Наименование стадии</span>
                                                                                    <input type="text" name="NAME" id="NAME" value=""
                                                                                                class="fields-input fields-input--add"
                                                                                                placeholder="(Нет данных)"
                                                                                                require='false'>
                                                                            </div>
                                                                            <div class="row-cards"style="margin-bottom:100px;">
                                                                          <div class="add-item" style="margin-right: 30px;">
                                                                                <span for="CS_TYPE_STAGE"  class="add-label">Тип стадии</span>
                                                                                <select class="select-new select-new--add select-type fields-input"   name="CS_TYPE_STAGE" id="CS_TYPE_STAGE" require='true'>
                                                                                <option value="" disabled  selected    class="option-dis">   (нет)</option>
                                                                                <option value="new_action" >Начальная стадия</option> 
                                                                                <option value="action" >Активная  стадия</option>
                                                                                <option value="end_plus" >Упешная стадия</option>
                                                                                <option value="end_minus" >Отрицательная  стадия (объект активен)</option>
                                                                                <option value="end_close" >Отрицательная  стадия (объект не активен)</option>                                                                                    
                                                                                 </select>
                                                                         </div>
                                                                            <div class="add-item" style="margin-right: 30px;">
                                                                                    <span for="CS_COLOR" class="add-label">Цвет стадии</span>
                                                                                    <input  type="color" name="CS_COLOR" id="CS_COLOR" value=""
                                                                                                class="fields-input fields-input--add"
                                                                                                require='false'>
                                                                            </div>
                                                            </div>

<br>                   
                    <div class="btn-help-row">
                              <div class="btn-blue">
                                        <div><i class="fa fa-check-circle fa-2x "></i></div>
                                        <div><button id="addRecord">Создать</button></div>
                              </div>
                    
                    </div>
            
                    </div>
                    </div>
                   <script type="text/javascript" src="/local/components/brokci_settings/favouritesStage/templates/b4/script.js"></script>