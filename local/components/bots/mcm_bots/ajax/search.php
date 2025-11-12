<?php
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    $log = __DIR__ . "/logSearch.txt";
    p($_POST, "start", $log);
if($_POST['member_id']) {
    $memberId = $_POST['member_id'];
}
if($_POST['app']) {
    $app = $_POST['app'];
}
if (!empty($memberId) && $app) {
   // $auth = new \CSlibs\B24\Auth\Auth($app, $_POST['authParams'], "");
    $auth = new \CSlibs\B24\Auth\Auth($app, [], $memberId);
    $userAdmin = $auth->CScore->call('user.admin')[0];
    if($userAdmin == true){
    $myUserId = $auth->CScore->call('user.current')['ID'];
//    pr($userAdmin, "userAdmin");
//    pr($myUserId);
//        $filterUser = [
//            'USER_TYPE' => 'employee',
//        ];
//        $order = ['ID' => 'ASC'];
//        $select = ["*"];
//        foreach ($auth->batch->getTraversableList( 'user.get',$order, $filterUser, $select,  6000) as $arVal) {
//            $user[$arVal["ID"]] = $arVal["LAST_NAME"] . " " . $arVal["NAME"];
//        }
//            pr($user);
  $prop = $auth->CScore->call('entity.item.property.get', ['ENTITY'=> 'messages']);
    //   pr($prop);
        $arApplications = [];
        $filter["ACTIVE"] = 'Y';
        foreach (  $auth->batch->getTraversableListEntity(   'entity.item.get', 'messages',  ['ID' => 'ASC'], $filter, 60000 ) as $value) {
            $arElement[] =$value;
        }
    p($arElement, "arElement", $log);
$filter["ACTIVE"] = 'Y';

?>

<div class="block-flex-columns ">

    <div class="fresh-table toolbar-color-grey full-screen-table">
        <div class="toolbar block-flex-row --justify-end --align-center" style="margin: auto">
<!--                <div><a id="create" class="form-small-button form-small-button-blue" href='javascript:edit()' ><i class="fa fa-plus" ></i>Создать</a></div>-->
               <a class="form-small-button form-small-button-blue block-flex-row --justify-center --align-center"  href='javascript:edit()' ><span style="color:#fff!important; text-decoration: none;">Создать</span></a>
        </div>
    </div>
        <table id="fresh-table" class="table table-checks" data-unique-id="ID">
            <thead>
<!--            <th data-field="check">-->
<!--                <span> <input class=" check-tabl-b4" type="checkbox" id="checkAll" title="Выбрать все"></span>-->
<!--            </th>-->
            <?php // аттрибут  data-visible="false" - не показывать строку ?>
            <th data-field="actions">Actions</th>
            <th data-field="ID" data-sortable="true">ID</th>
            <th data-field="NAME" data-sortable="true">Наименование</th>
            <th data-field="time" data-sortable="true">Время отправки</th>
            <th data-field="messager_type" data-sortable="true">Канал отправки</th>
            <th data-field="DATE_CREATE" data-sortable="true">Дата создания</th>

            </thead>
            <tbody name="editnews" id="controls">
            <?
                foreach ($arElement as $key => $value) {
                    ?>
                    <tr id="<?= $value["ID"] ?>">
<!--                        <td>-->
<!--                            <input class="tabl-check-input check-tabl-b4 check-tr" type="checkbox"-->
<!--                                   id="check---><?//= $value["ID"] ?><!--" value="--><?//= $value["ID"] ?><!--">-->
<!--                        </td>-->
                        <td>
                            <a href='javascript:edit(<?= $value["ID"] ?>)' title='Редактирование'><i class="fa fa-edit"></i></a>
                            <a href='javascript:deleteRecord(<?= $value["ID"]?>)' title='В архив'><i class="fa fa-remove"></i></a>
                        </td>

                        <td><?= $value["ID"] ?></td>
                        <td><?= $value["NAME"] ?></td>
                        <td><?= $value["PROPERTY_VALUES"]["time"] ?></td>
                        <td><?= $value["PROPERTY_VALUES"]["messager_type"] ?></td>
                        <td><?= date("d.m.Y", strtotime($value["DATE_CREATE"])) ?></td>

                    </tr>
                    <?
                } ?>

            </tbody>
        </table>
    </div>

    <?php }else{?>
            <style>
             .cs-block-no-access   {
                    background: #fff;
                    padding: 20px;
                    font-size: 20px;
                    color: red;
                    border-radius: 15px;
                }
            </style>
      <div class="cs-block-no-access">У вас нет прав на изменение алгоритмов</div>
  <?php  }
}?>