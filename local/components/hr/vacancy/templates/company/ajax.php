<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/Api/pochtaRU/function.php';

$date = date("d.m.YTH:i");
$file_log = __DIR__ . "/logAjax.txt";
file_put_contents($file_log, print_r($date . "\n", true));
file_put_contents($file_log, print_r($_POST, true), FILE_APPEND);
$params = json_decode($_POST['reg'], true);
$_REQUEST = $params;
//file_put_contents($file_log, print_r($params, true), FILE_APPEND);




/*
 * [CsTracer] => 30500186038305
    [CsTracCompany] => Почта России
Account/Идентификатор	Secure password/Пароль
2lRAfpMc5dWaRUWRcNgD8uHZiUEs8IP1	y7WerTGCFzvHzYKAf7EktBEaYwKEzYeB
 */
$i=0;
if($_POST['CsTracCompany'] === 'Почта России') {
    $result = getTracHistory($_POST['CsTracer']);
    $resTracStart = $result->OperationHistoryData->historyRecord[0];
    foreach ($result->OperationHistoryData->historyRecord as $key => $record) {
        $resTracHistory[$key]['OperDate'] = $record->OperationParameters->OperDate;
        $resTracHistory[$key]['Description'] = $record->AddressParameters->OperationAddress->Description;
        $resTracHistory[$key]['Name'] = $record->OperationParameters->OperAttr->Name;
        $resTracfinish = $record;
    };
    if($resTracStart->OperationParameters->OperType->Id=== 1){
        $resTracStartDate =   $resTracStart->OperationParameters->OperDate;
    }
    if($resTracfinish->OperationParameters->OperType->Id=== 2){
        $resTracCloseDate =   $resTracfinish->OperationParameters->OperDate;
    }

   $resTracInfo[$resTracStart->ItemParameters->ComplexItemName.", "]=$resTracStart->ItemParameters->Mass."г.";
   $resTracInfo["Отправлено: "]=date("d.m.Y - H:i", strtotime($resTracStartDate));
   $resTracInfo["От кого: "]=$resTracStart->UserParameters->Sndr;
   $resTracInfo["Кому: "]=$resTracStart->UserParameters->Rcpn;
   $resTracInfo["Куда: "]=$resTracStart->AddressParameters->DestinationAddress->Index.", ".$resTracStart->AddressParameters->DestinationAddress->Description;
    $resTracInfo["Получено: "]=date("d.m.Y - H:i", strtotime($resTracCloseDate));
   $resTracAddress=$resTracStart->AddressParameters->DestinationAddress->Index.", ".$resTracStart->AddressParameters->DestinationAddress->Description;

      file_put_contents($file_log, print_r($resTracStartDate, true), FILE_APPEND);
      file_put_contents($file_log, print_r($resTracStart, true), FILE_APPEND);
    file_put_contents($file_log, print_r($resTracInfo, true), FILE_APPEND);
    $resTracStatusFull['OperType']=(array)$resTracfinish->OperationParameters->OperType;
    $resTracStatusFull['OperAttr']=(array)$resTracfinish->OperationParameters->OperAttr;
    $resTracStatusFull['OperDate']=$resTracfinish->OperationParameters->OperDate;
    file_put_contents($file_log, print_r($resTracStatusFull, true), FILE_APPEND);
$smartId=$_POST['smartId'];
    $fields =[
    'ufCrm'.$smartId.'CsTracStatus'=> $resTracfinish->OperationParameters->OperAttr->Name,
    'ufCrm'.$smartId.'CsTracDate'=> $resTracStartDate,
    'ufCrm'.$smartId.'CsTracAddress'=> $resTracAddress,
    'ufCrm'.$smartId.'CsTracStatusFull'=> json_encode($resTracStatusFull),
    'ufCrm'.$smartId.'CsTracInfo'=> json_encode($resTracInfo),
    'ufCrm'.$smartId.'CsTracDateClose'=> $resTracCloseDate,
    'ufCrm'.$smartId.'CsTracPrice'=> $resTracStart->FinanceParameters->MassRate,
    'ufCrm'.$smartId.'CsTracHistory'=> json_encode($resTracHistory),
];
    file_put_contents($file_log, print_r($fields, true), FILE_APPEND);
if ($params['app']) {
    $CloudApp = $params['app'];
    $appAccess = 'app_' . $CloudApp . '_access';

    $CloudApplication = new \Cloud\App\CloudApplication($CloudApp);
    $HlClientAppCASSOFT = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientAppCASSOFT->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $params['member_id']]
    ])->fetchAll();
    $hlKeys = [
        'UF_CS_CLIENT_PORTAL_MEMBER_ID',
        'UF_CS_CLIENT_PORTAL_DOMEN',
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
    ];

    $clientApp = $clientsApp['0'];
//d($clientApp);
//  $debug->printR($clientApp, "clientApp");

    $auth = new Auth($CloudApplication, $clientApp, 'log.log', __DIR__ . '/');
    try {
        $startAuth = $auth->startAuth();

        if ($needUpdate = $auth->needUpdateAuth()) {
            $HlClientAppCASSOFT->hl::update(
                $clientApp['ID'],
                [
                    'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $needUpdate['access_token_new'],
                    'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $needUpdate['refresh_token_new']
                ]
            );
        }
    } catch (\Exception $e) {
        d($e->getMessage());
    }
}



$smartUp = $auth->core->call("crm.item.update", ['entityTypeId'=> $_POST['entityTypeId'],'id' => $_POST['smartElId'], 'fields' => $fields])->getResponseData()->getResult()->getResultData();

}

 $res['result'] = 'close';

echo json_encode($res);