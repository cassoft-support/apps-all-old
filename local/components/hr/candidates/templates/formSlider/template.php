
<?php
//d("REQ");
//d($_REQUEST);
//d("arResult");
//d($arResult);
//d("post");
//d($_POST);
require_once("ajax.php");
$item= itemData($arResult);
d($item);
require($_SERVER['DOCUMENT_ROOT'] . '/local/components/hr/candidates/ajax/creat.php');
?>