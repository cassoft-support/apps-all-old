<?php
$file_log = "/home/bitrix/www/local/components/brokci_settings/plan_type/ajax/logCreate.txt";

    
    //pr($Result['assigned_id']);
  //  file_put_contents($file_log, print_r("create",true));
   // file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
?>
<div class="CS-card">
        <div class="cards-edit">  
                                                                <div class="row-cards"style="margin-bottom:50px;">
                                                                       <div id="assigned_id" class="subtitle-block"  style="margin-right: 30px;">
                                                                        <span class="banner-title">Создание стадии для 2</span>
                                                                        </div>          
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
                   <script type="text/javascript" src="/local/components/hr/stageCandidates/templates/.default/script.js"></script>