<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logLeadAdd.txt";
p($_REQUEST, "start", $log);


$idGroup ="";
$keyBot ="";



$resLead = $auth->CScore->call(
    "crm.lead.get",
    array(
        "id" => $arParams['LEAD_ID']
    )
);


file_put_contents($file_log, print_r($arLead,true), FILE_APPEND);
if($keyBot && $arLead['SOURCE_ID'] !== "CALL"){


    $leadLink = '<a href="https://'.$arParams['domain'].'/crm/lead/details/'.$arLead['ID'].'/">Создан новый лид №'.$arLead['ID'].'</a>';
    $linkTel ='<a href="tel:'.$arLead['PHONE']['0']['VALUE'].'">'.$arLead['PHONE']['0']['VALUE'].'</a>';
    $message = $leadLink."\n Имя: <strong>".$arLead['NAME']."</strong>\n тел: ".$linkTel ;

    $data = [
        'chat_id' =>$idGroup,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview'=>'true'
    ];
    $dataStr = http_build_query($data);
    $zapros = "https://api.telegram.org/bot".$keyBot."/sendMessage?".$dataStr;
    $response = file_get_contents("https://api.telegram.org/bot".$keyBot."/sendMessage?".$dataStr);
    file_put_contents($file_log, print_r($data,true), FILE_APPEND);
    file_put_contents($file_log, print_r($response ,true), FILE_APPEND);
}



?>