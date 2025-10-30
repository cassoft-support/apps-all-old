<!doctype html>
<head>
  <link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
  <link href="/local/lib/css/cassoft/style.css" rel="stylesheet">
  <link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">
  <link href="/local/lib/css/cassoft/cs-table.css" rel="stylesheet">
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
</head>
<body>
  <style>
    body{
      font-size:16px;
    }
    .table td {
    font-size: 1.5rem!important;
    }
    @media (min-width: 1400px) {
        .container {
            width: 100%;
            max-width: 2400px;
            margin: 0;
            padding: 0;
        }
    }
  </style>

    <div class="wrapper" style="position: relative">
    <div class="main-block">

<!--    <input  id="UserAutStage" name="UserAutStage" class="fields-input" style="display:none;"  value='--><?php //echo json_encode($arResult['UserAutStage']) ?><!--' >-->
<!--        <input  id="stageDeal" name="stageDeal" class="fields-input" style="display:none;" value='--><?php //= json_encode($arResult['stageDeal']) ?><!--' >-->
</div>
        <div class="container" style=" background: #fff">
            <div class=""> Стадии кандидата</div>
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
                                <th data-field="TYPE_STAGE_NAME">Тип стадии</th>
                                <th data-field="STAGE_DEAL_NAME">Стадия смарта</th>
<!--                                <th data-field="CS_COLOR">Цвет</th>-->
                                <th data-field="DATE_MODIFIED">Дата изменения</th>
                                <th data-field="MODIFIED_BY">Изменил</th>
                                
                              <!--  <th data-field="actions" data-formatter="operateFormatter" data-events="operateEvents">Действие</th>-->
                            </thead>
                            <tbody>
                                    <?php foreach( $arResult['entity'] as $key => $value){
                                       
                                        ?>
                                          <tr  id="el-<?= $value["ID"]?>">
                                          <td>
                                  <a href='javascript:editRecordStage(<?= json_encode($value)?>)' title='Редактирование'><i class="fa fa-edit"></i></a>
                                  <a href='javascript:deleteRecordStage(<?= json_encode($value["ID"])?>)' title='Удалить'><i class="fa fa-remove"></i></a>
                                    </td>
                                    <td ><?=$value["ID"]?></td>
                                    <td><?=$value["NAME"] ?></td>
                                    <td><div class="block-flex-row --align-center"><div id="m-<?= $value["ID"]?>" class="marker" style="background: <?=$value["CS_COLOR"] ?>;"></div><div><?=$value["TYPE_STAGE_NAME"] ?></div></div></td>
                                    <td><?=$value["STAGE_SMART_NAME"] ?></td>
                                    <td><?=$value["DATE_MODIFIED"] ?></td>
                                    <td><?=$value["MODIFIED_BY"] ?></td>
                                    
                                    </tr>
                                    <?}?>
                                    
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
<script type="text/javascript" src="/local/components/hr/stageCandidates/templates/.default/script.js?120709"></script>
<script type="text/javascript" src="/local/components/hr/stageCandidates/templates/.default/table.js"></script>
<script type="text/javascript">
  
</script>

</html>