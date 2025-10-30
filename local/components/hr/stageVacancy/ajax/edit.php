<?

$file_log = "/home/bitrix/www/local/components/brokci_settings/favouritesStage/ajax/logEdit.txt";
//$file_log = "/local/components/brokci_settings/plan_type/ajax/logEdit.txt";
    function d($val)
    {
        echo "<pre>";
        print_r($val);
        echo "</pre>";
    }
    $Result = $_REQUEST['value'];
    //pr($Result['assigned_id']);
  //  file_put_contents($file_log, print_r("555",true));
    file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
 

?>
<div class="slider-card slideLeft" >
                                                                        <div class=" slider-card-header">
                                                                                <div>
                                                                                <!--   <img class="cassoft-install-image" src="/pub/images/brokci.png" alt="brokci-logo">-->
                                                                                </div>
                                                                                <div> Редактирование стадий для избранных </div>
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
                                                                            
                                                                            <div class="add-item" style="display:none;">
                                                                                    <span for="ID" class="add-label">ID</span>
                                                                                    <input  type="number" step="1" name="ID" id="ID" value="<?=$Result['ID']?>"  class="fields-input fields-input--add" >
                                                                            </div>
                                                                            </div>
                                                         <div class="row-cards"style="margin-bottom:50px;">             
                                                                         <div class="add-item" style="margin-right: 30px;">
                                                                                    <span for="NAME" class="add-label">Наименование стадии</span>
                                                                                    <input type="text" name="NAME" id="NAME" value="<?=$Result['NAME']?>"
                                                                                                class="fields-input fields-input--add"
                                                                                                placeholder="(Нет данных)"
                                                                                                require='false'>
                                                                            </div>
                                                                            <div class="row-cards"style="margin-bottom:100px;">
                                                                          <div class="add-item" style="margin-right: 30px;">
                                                                                <span for="CS_TYPE_STAGE"  class="add-label">Тип стадии</span>
                                                                                <select class="select-new select-new--add select-type fields-input"   name="CS_TYPE_STAGE" id="CS_TYPE_STAGE" require='true'>
                                                                                <option value="new_action" <?if($Result['CS_TYPE_STAGE']==="Начальная стадия"){echo 'selected';}?> >Начальная стадия</option> 
                                                                                <option value="action"<?if($Result['CS_TYPE_STAGE']==="Активная  стадия"){echo 'selected';}?> >Активная  стадия</option>
                                                                                <option value="end_plus" <?if($Result['CS_TYPE_STAGE']==="Упешная стадия"){echo 'selected';}?>>Упешная стадия</option>
                                                                                <option value="end_minus" <?if($Result['CS_TYPE_STAGE']==="Отрицательная  стадия (объект активен)"){echo 'selected';}?>>Отрицательная  стадия (объект активен)</option>
                                                                                <option value="end_close" <?if($Result['CS_TYPE_STAGE']==="Отрицательная  стадия (объект не активен)"){echo 'selected';}?>>Отрицательная  стадия (объект не активен)</option>                                                                                    

                                                                        </select>
                                                                         </div>
                                                                            <div class="add-item" style="margin-right: 30px;">
                                                                                    <span for="CS_COLOR" class="add-label">Цвет стадии</span>
                                                                                    <input  type="color" name="CS_COLOR" id="CS_COLOR" value="<?=$Result['CS_COLOR']?>"
                                                                                                class="fields-input fields-input--add"
                                                                                                require='false'>
                                                                            </div>
                                                            </div>

<br>                   
                    <div class="btn-help-row">
                              <div class="btn-blue">
                                        <div><i class="fa fa-check-circle fa-2x "></i></div>
                                        <div><button id="save">Сохранить</button></div>
                              </div>
                    
                    </div>
           
                    </div>
                    <script type="text/javascript" src="/local/components/brokci_settings/favouritesStage/templates/b4/script.js"></script>
                  