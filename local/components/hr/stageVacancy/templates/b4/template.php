<!doctype html>
<head>
  <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
  <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
</head>
<body>
<? //echo "<pre>"; print_r($plan); echo "</pre>";
//echo "<pre>"; print_r($arResult['UserAut']); echo "</pre>";
//echo "<pre>"; print_r($_REQUEST); echo "</pre>";

?>
    <div class="wrapper">
    <div class="main-block">
    <input  id="UserAutPlan" name="UserAutPlan" class="fields-input" style="display:none;" hidden
          value='<?php echo json_encode($arResult['UserAut']) ?>' ></input>
</div>
        <div class="container" style="width:100%!important;">
            <div class="row">
             <div style="width:100%;">
                    <div class="fresh-table toolbar-color-grey full-screen-table">
                        <!--    Available colors for the full background: full-color-blue, full-color-azure, full-color-green, full-color-red, full-color-orange                  
                        Available colors only for the toolbar: toolbar-color-blue, toolbar-color-azure, toolbar-color-green, toolbar-color-red, toolbar-color-orange
               btn-default alertBtn aria-hidden="true"
                    -->

                        <div class="toolbar btn-green-table">
                       <div><button id="create" class="btn btn-default"><i class="fa fa-plus" ></i>Создать</button></div>
                         
                           
                        </div>

                        <table id="fresh-table" class="table">
                            <thead>
                            <th data-field="actions" >Действие</th>
                                <th data-field="ID">ID</th>
                                <th data-field="NAME">Наименование</th>
                                <th data-field="CS_TYPE_STAGE">Тип стадии</th>
                                <th data-field="CS_COLOR">Цвет</th>
                                <th data-field="DATE_MODIFIED">Дата изменения</th>
                                <th data-field="MODIFIED_BY">Изменил</th>
                                
                              <!--  <th data-field="actions" data-formatter="operateFormatter" data-events="operateEvents">Действие</th>-->
                            </thead>
                            <tbody>
                                    <?foreach( $arResult['entity'] as $key => $value){
                                       
                                        ?>
                                          <tr>
                                          <td>
                                  <a href='javascript:editRecord(<?= json_encode($value)?>)' title='Редактирование'><i class="fa fa-edit"></i></a>
                                  <a href='javascript:deleteRecord(<?= json_encode($value["ID"])?>)' title='Удалить'><i class="fa fa-remove"></i></a>
                                    </td>
                                    <td><?=$value["ID"]?></td>
                                    <td><?=$value["NAME"] ?></td>
                                    <td><?=$value["CS_TYPE_STAGE"] ?></td>
                                    <td ><div style="background: <?=$value["CS_COLOR"] ?>; color:<?=$value["CS_COLOR"] ?>;">color</div></tdstyle=bgcolor=>
                                    <td><?=$value["DATE_MODIFIED"] ?></td>
                                    <td><?=$value["MODIFIED_BY"] ?></td>
                                    
                                    </tr>
                                    <?}?>
                                    <tr>
                            </tbody>
                        </table>
                    </div>

<div id="resultCard" class="slider-card-row"></div>
<div id="resultSave" style="display:none;" class="result-save"><h3>Запись сохранена</h3></div>
<div id="resultMessage" style="display:none;" class="result-save"></div>
<div id="overlay_popup_object_card" class="popup-wait" ></div>

                </div>
            </div>
        </div>
    </div>

</body>
<script type="text/javascript" src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="/local/components/brokci_settings/favouritesStage/templates/b4/script.js"></script>

<script type="text/javascript">
  
</script>

</html>