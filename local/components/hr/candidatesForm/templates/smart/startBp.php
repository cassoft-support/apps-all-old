<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$date = date("d.m.YTH:i");
$file_log = __DIR__ . "/logStartBP.txt";
file_put_contents($file_log, print_r($date . "\n", true));
file_put_contents($file_log, print_r($_POST , true), FILE_APPEND);


if($_POST['UserAut']['member_id']){
    $memberId = $_POST['UserAut']['member_id'];
}
if($_POST['auth']['member_id']){
    $memberId = $_POST['auth']['member_id'];
}

if ($_POST['app'] && $memberId) {

    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $memberId);
  $dataNew = strtotime(date("d.m.YTH:i:s"))-50;
   //$smartUp = $auth->CScore->call("crm.item.update",array('entityTypeId'=>$_POST['entityTypeId'], 'id' => $_POST['smartElId'], 'fields'=>[$_POST['smartId'] => $dataNew]))['item'];
    file_put_contents($file_log, print_r($dataNew , true), FILE_APPEND);
    file_put_contents($file_log, print_r($smartUp, true), FILE_APPEND);
    $comments = "🟢 Запускаем процессы";
    $fields = ['fields' => ["ENTITY_ID" => $_POST['smartElId'], "ENTITY_TYPE" => "dynamic_" . $_POST['entityTypeId'], "COMMENT" => $comments]];
    file_put_contents($file_log, print_r($fields , true), FILE_APPEND);
    $smartComment = $auth->CScore->call("crm.timeline.comment.add", $fields);
    echo json_encode($smartComment);
}
