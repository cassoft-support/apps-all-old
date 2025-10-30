<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/pub/cassoftApp/ModulbankPay/signature.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
$file_log = "/home/bitrix/www/pub/cassoftApp/ModulbankPay/logOrderPay.txt";

file_put_contents($file_log, print_r("order+",true));
  file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
  file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
  file_put_contents($file_log, print_r("\nGET\n",true), FILE_APPEND);
  file_put_contents($file_log, print_r($_GET,true), FILE_APPEND);
$CSRest = new CSRest("testirovanie_metodov");

function d($print){echo "<pre>"; print_r($print); echo "</pre>";}

/*
$merchant=$_REQUEST['merchant'];
$amount	=$_REQUEST['sum'];
$order_id	=$_REQUEST['ORDER_ID'];
$custom_order_id=$_REQUEST['ACCOUNT_NUMBER'];
$description	="Заказ ".$order_id;

$success_url="https://pay.modulbank.ru/success";	//$_REQUEST['BX_RETURN_URL];
$testing =1;
$signKey ='2C71BACA7FB4C8053768FCF07542E36A';
$unix_timestamp=strtotime(date('d.m.YTH:i'));
$receipt_contact ="cas@cassoft.ru";
//$date =strtotime(date('d.m.YTH:i'));


$receipt_items =[
   '0'=>[
    'name'=> 'Тестовая услуга 1',
    'payment_method'=> 'full_prepayment',
    'payment_object'=> 'service',
    'price'=> $amount,
    'quantity'=> 1,
    'sno'=> 'usn_income',
    'vat'=> "none"
   ]
  ];
 
 $receipt_itemsObJs =json_encode($receipt_items,  JSON_HEX_APOS | JSON_HEX_QUOT);
 
$items = array(
  "testing" =>$testing,
  "order_id" =>$order_id,
  "amount" =>$amount,
  "custom_order_id" =>$custom_order_id,	
  "merchant" =>$merchant,
  "description" =>$description,
  "success_url" =>$success_url,
  "receipt_contact" =>$receipt_contact,
  "unix_timestamp" =>$unix_timestamp,
  "receipt_items" =>$receipt_itemsObJs
);

$signature=get_signature($items, $signKey);
file_put_contents($file_log, print_r($signature,true), FILE_APPEND);

$resData = array();
$resData=$items;
$resData["signature"]=$signature;

file_put_contents($file_log, print_r($resData,true), FILE_APPEND);
$ch = curl_init('https://pay.modulbank.ru/pay');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($resData, '', '&'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$html = curl_exec($ch);
curl_close($ch);	
 
echo $html;
//file_put_contents($file_log, print_r($signature,true), FILE_APPEND);