<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER['DOCUMENT_ROOT'] . '/pub/cassoftApp/cloudReceiptsMB/tools/signature.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
$file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/tools/logOrder.txt";


file_put_contents($file_log, print_r("order",true));
  file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
  file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
//$CSRest = new CSRest("testirovanie_metodov");

//function d($print){echo "<pre>"; print_r($print); echo "</pre>";}
//d($_REQUEST);
$Params=$_REQUEST;

$APPLICATION->IncludeComponent(
  'pay_system:cloud_receipts',
  'individuals_order',
  $Params,
);

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
$callback_url ="https://city.brokci.ru/pub/cassoftApp/ModulbankPay/modulbankIOrderPay.php";
$callback_on_failure="1";
$client_email	 ="cas@cassoft.ru";
  $client_name	="Alex";
  $lifetime = 3600; //строк жизни счета
  $send_lette =1;//отправить счет
  $reusable =0; //многоразовый счет
  $start_recurrent =0; //запомнить карту 
  $show_payment_methods=["sbp", "card"]; //метод оплаты карта или спб
  //$preauth	Указывает, что оплата будет двухстадийной. Подробнее в разделе"Холдирование"
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
 $salt	=random_str();
 file_put_contents($file_log, print_r($salt."\n",true), FILE_APPEND);
  
$items = array(
  "merchant" =>$merchant,
  "amount" =>$amount,//сумма
  "description" =>$description,
  "testing" =>$testing,//тестовый
  "client_email" => $client_email,
  "client_name"=> $client_name,
 // "order_id" =>$order_id,
 "custom_order_id" =>$custom_order_id,	
 "lifetime"=>$lifetime,
 "send_lette" =>$send_lette,
 "receipt_contact" =>$receipt_contact,
 "receipt_items" =>$receipt_itemsObJs,
 "unix_timestamp" =>$unix_timestamp,
 "salt"=>$salt,
 "reusable"=>$reusable,
 "callback_url" =>$callback_url,
  "callback_on_failure" =>	$callback_on_failure,
  "start_recurrent"=>$start_recurrent,
  "show_payment_methods"=>$show_payment_methods,
  "success_url" =>$success_url,
);
file_put_contents($file_log, print_r($items ,true), FILE_APPEND);
$signature=get_signature($items, $signKey);
file_put_contents($file_log, print_r($signature,true), FILE_APPEND);

$resData = array();
$resData=$items;
$resData["signature"]=$signature;
/*
file_put_contents($file_log, print_r($resData,true), FILE_APPEND);
$ch = curl_init('https://pay.modulbank.ru/api/v1/bill/');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($resData, '', '&'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$html = curl_exec($ch);
curl_close($ch);	
 
$resBill = json_decode($result, true);
 if($resBill['status'] =='ok'){
  header('Location: '.$resBill['bill']['url'], true, 303);
  exit();
 }
//file_put_contents($file_log, print_r($signature,true), FILE_APPEND);