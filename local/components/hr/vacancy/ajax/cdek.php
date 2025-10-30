<?php


function cdekHistory($auth, $data, $clientId, $clientKey)
{
// Авторизация
    $array = array();
    $array['grant_type'] = 'client_credentials';
    $array['client_id'] = $clientId;
    $array['client_secret'] = $clientKey;

    $ch = curl_init('https://api.cdek.ru/v2/oauth/token?parameters');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($html, true);

    $access_token = $res['access_token'];
    p($access_token, "access_token");
    $cdek_id = $data['CsTracer'];
    p($cdek_id);
    //$ch = curl_init('https://api.cdek.ru/v2/orders?cdek_number=' . $cdek_id);
    //$ch = curl_init('https://api.cdek.ru/v2/orders?im_number=1506876977');
    $ch = curl_init('https://api.cdek.ru/v2/orders?im_number=00004792842619');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($html, true);
p($res, "res");
   // echo '<p>Номер отправления: ' . $res['entity']['cdek_number'] . '</p>';
  //  echo '<p>Статус: ' . $res['requests'][0]['state'] . '</p>';
  //  echo '<p>' . $res['requests'][0]['errors'][0]['message'] . '</p>';
/*
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
*/

return $smartUp;

}