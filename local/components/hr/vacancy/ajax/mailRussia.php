<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/Api/pochtaRU/function.php';

function mailRusHistory($auth, $data)
{
    $result = getTracHistory($data['CsTracer']);
    $resTracStart = $result->OperationHistoryData->historyRecord[0];
    foreach ($result->OperationHistoryData->historyRecord as $key => $record) {
        $resTracHistory[$key]['OperDate'] = $record->OperationParameters->OperDate;
        $resTracHistory[$key]['Description'] = $record->AddressParameters->OperationAddress->Description;
        $resTracHistory[$key]['Name'] = $record->OperationParameters->OperAttr->Name;
        $resTracfinish = $record;
    };
    if ($resTracStart->OperationParameters->OperType->Id === 1) {
        $resTracStartDate = $resTracStart->OperationParameters->OperDate;
    }
    if ($resTracfinish->OperationParameters->OperType->Id === 2) {
        $resTracCloseDate = $resTracfinish->OperationParameters->OperDate;
    }

    $resTracInfo[$resTracStart->ItemParameters->ComplexItemName . ", "] = $resTracStart->ItemParameters->Mass . "г.";
    $resTracInfo["Отправлено: "] = date("d.m.Y - H:i", strtotime($resTracStartDate));
    $resTracInfo["От кого: "] = $resTracStart->UserParameters->Sndr;
    $resTracInfo["Кому: "] = $resTracStart->UserParameters->Rcpn;
    $resTracInfo["Куда: "] = $resTracStart->AddressParameters->DestinationAddress->Index . ", " . $resTracStart->AddressParameters->DestinationAddress->Description;
    $resTracInfo["Получено: "] = date("d.m.Y - H:i", strtotime($resTracCloseDate));
    $resTracAddress = $resTracStart->AddressParameters->DestinationAddress->Index . ", " . $resTracStart->AddressParameters->DestinationAddress->Description;


    $resTracStatusFull['OperType'] = (array)$resTracfinish->OperationParameters->OperType;
    $resTracStatusFull['OperAttr'] = (array)$resTracfinish->OperationParameters->OperAttr;
    $resTracStatusFull['OperDate'] = $resTracfinish->OperationParameters->OperDate;

    $smartId = $data['smartId'];
    $fields = [
        'ufCrm' . $smartId . 'CsTracStatus' => $resTracfinish->OperationParameters->OperAttr->Name,
        'ufCrm' . $smartId . 'CsTracDate' => $resTracStartDate,
        'ufCrm' . $smartId . 'CsTracAddress' => $resTracAddress,
        'ufCrm' . $smartId . 'CsTracStatusFull' => json_encode($resTracStatusFull),
        'ufCrm' . $smartId . 'CsTracInfo' => json_encode($resTracInfo),
        'ufCrm' . $smartId . 'CsTracDateClose' => $resTracCloseDate,
        'ufCrm' . $smartId . 'CsTracPrice' => $resTracStart->FinanceParameters->MassRate,
        'ufCrm' . $smartId . 'CsTracHistory' => json_encode($resTracHistory),
    ];

    $smartUp = $auth->core->call("crm.item.update", ['entityTypeId' => $data['entityTypeId'], 'id' => $data['smartElId'], 'fields' => $fields])->getResponseData()->getResult()->getResultData();
return $smartUp;
}