<?php
require_once(__DIR__."/ajax/ajax.php");
$itemsData= itemsData($arParams);
//d($itemsData);
?>
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
    .btn-text{
        cursor: pointer;
        color: #0a98ff;
    }
    .rotate-180 {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    .icon {
        transition: transform 0.3s ease;
    }
    .slider-card-header-name{
        font-size: 26px;
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
       <div class="block-flex-columns --p10">
           <div class="slider-card-header-name">Вакансии </div>
        <div class="block-flex-columns --align-end">
            <div class="btn-text --mb15 block-flex-row --align-center " id="btnClick">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-down icon --mr5" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 6.646a.5.5 0 0 1 .708 0L8 12.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                    <path fill-rule="evenodd" d="M1.646 2.646a.5.5 0 0 1 .708 0L8 8.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg>
                <span>Импорт вакансий</span></div>
            <div class="block-flex-row --w100p --justify-end" style="display: none;" id="blockImport">
                <div class="--mr15">
                        <button id="loadingHH" class="form-small-button form-small-button-blue">Загрузить с HH</button>
                </div>
                <div class="--mr15">
                        <button id="loadingHH" class="form-small-button form-small-button-blue">Загрузить с Зарплата.ру</button>
                </div>
            </div>
        </div>
       </div>
        <div class="row">
            <div style="width:100%;">
                <div class="fresh-table toolbar-color-grey full-screen-table">
                    <!--    Available colors for the full background: full-color-blue, full-color-azure, full-color-green, full-color-red, full-color-orange
                    Available colors only for the toolbar: toolbar-color-blue, toolbar-color-azure, toolbar-color-green, toolbar-color-red, toolbar-color-orange
           btn-default alertBtn aria-hidden="true"
                -->

<!--                    <div class="toolbar btn-green-table">-->
                    <div class="toolbar " style="margin-top: 10px;">
                        <div><button id="create" class="form-small-button form-small-button-blue">Создать</button></div>



                    </div>

                    <table id="fresh-table" class="table">
                        <thead>
                        <th data-field="actions" >Действие</th>
                        <th data-field="ID" data-sortable="true">ID</th>
                        <th data-field="NAME" data-sortable="true">Название</th>
                        <th data-field="stage" data-sortable="true">Стадия вакансии</th>
                        <th data-field="ACTYVE" data-sortable="true">Активность</th>



                        <!--  <th data-field="actions" data-formatter="operateFormatter" data-events="operateEvents">Действие</th>-->
                        </thead>
                        <tbody>
                        <?php foreach( $itemsData['items'] as $key => $value){

                            ?>
                            <tr  id="el-<?= $value["ID"]?>">
                                <td>
                                    <a href='javascript:editRecord(<?= json_encode($value["ID"])?>)' title='Редактирование'><i class="fa fa-edit"></i></a>
                                    <a href='javascript:deleteRecord(<?= json_encode($value["ID"])?>)' title='Удалить'><i class="fa fa-remove"></i></a>
                                </td>
                                <td ><?=$value["ID"]?></td>
                                <td ><?=$value["NAME"]?></td>
                                <td><div class="block-flex-row --align-center"><div id="m-<?= $value["ID"]?>" class="marker" style="background: <?=$value["stageColor"] ?>;"></div><div><?=$value["stageName"] ?></div></div></td>
                                <td ><?=$value["ACTIVE"]?></td>

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
<script type="text/javascript" src="/local/components/hr/vacancy/templates/.default/script.js"></script>
<script type="text/javascript" src="/local/components/hr/candidates/templates/.default/table.js"></script>

    <script>
        // Получаем элементы по их ID
        const btnClick = document.getElementById('btnClick');
        const blockImport = document.getElementById('blockImport');
        const icon = btnClick.querySelector('.icon');
        // Добавляем обработчик события клика на кнопку
        btnClick.addEventListener('click', function() {
        // Проверяем текущий стиль отображения элемента
        if (blockImport.style.display === 'none' || blockImport.style.display === '') {
        // Если элемент скрыт, показываем его
        blockImport.style.display = 'flex';
    } else {
        // Если элемент показан, скрываем его
        blockImport.style.display = 'none';
    }
            icon.classList.toggle('rotate-180');
    });

</script>

</html>
