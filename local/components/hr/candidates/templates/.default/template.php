<?php
require_once(__DIR__."/ajax/ajax.php");
$itemsData= itemsData($arResult);
d($smartData);
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
        <div class="slider-card-header-name">Соискатели </div>
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
                        <th data-field="last_name" data-sortable="true">Фамилия</th>
                        <th data-field="name" data-sortable="true">Имя</th>
                        <th data-field="second_name" data-sortable="true">Отчество</th>
                        <th data-field="stage" data-sortable="true">Стадия кандидата</th>
                        <th data-field="contact_id" data-sortable="true">Контакт в CRM</th>
<!--                        <th data-field="password" data-sortable="true">Пароль</th>-->
<!--                        <th data-field="code_auth" data-sortable="true">Код авторизации</th>-->
                        <th data-field="phone" data-sortable="true">Телефон</th>
                        <th data-field="email" data-sortable="true">Почта</th>
<!--                        <th data-field="phone_check" data-sortable="true">Телефон подтвержден</th>-->
<!--                        <th data-field="email_check" data-sortable="true">Почта подтверждена</th>-->
<!--                        <th data-field="code_email_auth" data-sortable="true">Код подтверждения почты</th>-->
<!--                        <th data-field="date_auth" data-sortable="true">Дата послезней авторизации</th>-->
                        <th data-field="birthdate" data-sortable="true">Дата рождения</th>
                        <th data-field="photo" data-sortable="true">Аватар</th>
                        <th data-field="personal_check" data-sortable="true">Согласие на использование персональных данных</th>
                        <th data-field="inn" data-sortable="true">ИНН</th>
                        <th data-field="resume" data-sortable="true">Резюме</th>
                        <th data-field="cover_letter" data-sortable="true">Cопроводительное письмо</th>
                        <th data-field="assigned_by_id" data-sortable="true">Ответственный</th>


                        <!--  <th data-field="actions" data-formatter="operateFormatter" data-events="operateEvents">Действие</th>-->
                        </thead>
                        <tbody>
                        <?php foreach( $itemsData['items'] as $key => $value){

                            ?>
                            <tr  id="el-<?= $value["ID"]?>">
                                <td>
                                    <a href='javascript:editRecordStage(<?= json_encode($value)?>)' title='Редактирование'><i class="fa fa-edit"></i></a>
                                    <a href='javascript:deleteRecordStage(<?= json_encode($value["ID"])?>)' title='Удалить'><i class="fa fa-remove"></i></a>
                                </td>
                                <td ><?=$value["ID"]?></td>
                                <td ><?=$value["NAME"]?></td>
                                <td><?=$value["last_name"] ?></td>
                                <td><?=$value["name"] ?></td>
                                <td><?=$value["second_name"] ?></td>
                                <td><div class="block-flex-row --align-center"><div id="m-<?= $value["ID"]?>" class="marker" style="background: <?=$value["stageColor"] ?>;"></div><div><?=$value["stageName"] ?></div></div></td>
                                <td><?=$value["contact_id"] ?></td>
                                <td><?=$value["phone"] ?></td>
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
<script type="text/javascript" src="/local/components/hr/candidates/templates/.default/script.js?120716"></script>
<script type="text/javascript" src="/local/components/hr/candidates/templates/.default/table.js"></script>
<script type="text/javascript">

</script>

</html>
