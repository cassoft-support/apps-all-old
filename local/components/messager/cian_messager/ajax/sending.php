<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


//$log = __DIR__."/home/bitrix/www/local/a_logs/logistics/cron/atiUp".date("d-m-Y").".txt";
$log = __DIR__."/logSening.txt";
//Установка значений, которые не приходят
$_POST['app'] = 'brokci';
//$_POST['UserAut']['member_id'] = '137f3595ca3a2c72c95bd79acb765bef';
d("start");
p("Начало", "time", $log);

$HlClientApp = new \CSlibs\B24\HL\HlService('app_' . $_POST['app'] . '_access');
$clientsApp = $HlClientApp->hl::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    'filter' => [
        'UF_ACTIVE' => 1
    ]
])->fetchAll();

foreach ($clientsApp as $client) {
  $i=0;
    $token='';
    $memberId = $client['UF_CS_CLIENT_PORTAL_MEMBER_ID'];
   if($client['UF_CS_CLIENT_PORTAL_DOMEN'] === 'darhaus.bitrix24.ru'){
    d($client);
  $app = $_POST['app'];
  $authParams = [];
  $member = $memberId;
  $auth = new \CSlibs\B24\Auth\Auth($app, $authParams, $member);


// получить токен для ATI.SU
    $arSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0];
$keyWazzup = $arSetup['PROPERTY_VALUES']['CS_API_WAZZUP'];
//$keyWazzup = '55fcc907daa0466ab80fb2b413b30e61';
$keyChatApp = $arSetup['PROPERTY_VALUES']['CS_API_WHATSAPP'];
$lineChatApp = $arSetup['PROPERTY_VALUES']['CS_LINE_WHATSAPP'];

       // d($arSetup);

//есть id на ati, дата обновления< текущей на час, есть признак публикации
        $resultAti = [];
        $date = strtotime(Date('c'));
       $paramsApp = ['ENTITY' => 'messages','filter' => ['id' => 768 ]];
       $resElement = $auth->CScore->call('entity.item.get', $paramsApp)[0];
       if (!empty($resElement['ID'])) {
           $arElement = $resElement['PROPERTY_VALUES'];
           $arElement['id'] = $resElement['ID'];
           $arElement['NAME'] = $resElement['NAME'];
       }
       d($arElement);
       $desc = json_decode($arElement['message']);
       $select = ['NAME', 'SECOND_NAME', 'LAST_NAME', 'PHONE', 'BIRTHDATE'];
       $filter=[
           'BIRTHDATE'=>date('c'),
          // 'ID'=>3666
       ];
        foreach ($auth->batch->getTraversableList('crm.contact.list',  ['ID'=>'ASC'], $filter, $select, 6000) as $value) {
            d($value);
           $descName = str_replace('#NAME', $value['NAME'], $desc);
           $descLastName = str_replace('#LAST_NAME', $value['LAST_NAME'], $descName);
           $descFinal= str_replace('#SECOND_NAME', $value['SECOND_NAME'], $descLastName);
           $descFinal= str_replace('&nbsp;', " ", $descFinal);
           $descFinal= str_replace('<p>', "", $descFinal);
           $descFinal= str_replace('<br>', "", $descFinal);
           $descFinal= str_replace('<strong>', "*", $descFinal);
           $descFinal= str_replace('</strong>', "*", $descFinal);
           $descFinal= str_replace('<i>', "_", $descFinal);
           $descFinal= str_replace('</i>', "_", $descFinal);
           $descFinal= str_replace('</p>', "\n", $descFinal);

d($descFinal);
$contactPhone = mb_eregi_replace('[^0-9]', '', $value['PHONE']['0']['VALUE']);
$contactPhone= mb_substr_replace($contactPhone, '7', 0, 1);
// $one= mb_substr($contactPhone, 0, 1);
// if($one == 8){
//
// }
d($contactPhone);

if($arElement['messager_type'] === 'wazzup'){

    $array = array(
        'channelId' =>'30fcf5ab-8831-4eab-bec5-6a7d944c0434',
        'chatType'=>"whatsapp",
        'chatId'    => $contactPhone,
        'text'=> $descFinal
    );
    $arrayJson = json_encode($array);
    d($arrayJson);
//    $array = array(
//    'data' => Array
//    (
//        'fields' => Array
//        (
//            'chatType'=>"whatsapp",
//            'chatId'    => $contactPhone,
//            'text'=> $descFinal
//        )
//    )
//    );
  // $data_string = file_get_contents("data.json");
//    $curl = curl_init('https://api.hh.ru/vacancies/drafts');
//    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
//
// $array = json_encode($array, JSON_UNESCAPED_UNICODE);
    $data = http_build_query($array, '', '&');
    d($data);
        $ch = curl_init("https://api.wazzup24.com/v3/message");
        curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $keyWazzup
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
        // отображаем ответ
        $res = json_decode($html, true);
//    d($html);
    d($res);
    sleep(60);

}
        }


        p($i, "обновлено", $file_log);

//d($resultAti);
    }
}

//curl --location --request GET 'https://api.wazzup24.com/v3/channels' \
//--header 'Authorization: Bearer c8cf90444023482f909520d454368d27'
//$ch = curl_init('https://api.wazzup24.com/v3/channels');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//     'Content-Type: application/json',
//     'Authorization: Bearer ' . $keyWazzup
// ));
//curl_setopt($ch, CURLOPT_HEADER, false);
//$html = curl_exec($ch);
//curl_close($ch);

echo $html;