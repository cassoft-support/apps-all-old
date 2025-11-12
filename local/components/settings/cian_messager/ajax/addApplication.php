<?php
//d($_POST);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/fulfillment/applications/ajax/function_app.php");
$log = __DIR__ . "/logAdd.txt";
p($_POST, "start", $log);

if($_POST['member_id'] && $_POST['app']) {
    $memberId = $_POST['member_id'];
    $app = $_POST['app'];
    $auth = new \CSlibs\B24\Auth\Auth($app, [], $memberId);

$arrToAdd = [];
foreach ($_POST['formData'] as $value){
    $arrToAdd[$value['name']] = $value['value'];
}
    if ($arrToAdd['ID'] > 0) {
    $paramsUp = [
        'ENTITY' => 'application',
        'NAME' => $arrToAdd['NAME'],
        'ID' =>$arrToAdd['ID'],
        'PROPERTY_VALUES' => [
            'COMMENTS' =>$_POST['desc'],
            'USER_ID' => $_POST['client_id'],
            'MARKETS' => $arrToAdd['MARKETS'],
            'SERVICES' => $arrToAdd['SERVICES'],
            'PRODUCT_ID' => $arrToAdd['PRODUCT_ID'],
            'COUNT' => $arrToAdd['COUNT'],
        ]];

    p($paramsUp, "paramsUp", $log);
        $entityUp = $auth->CScore->call('entity.item.update', $paramsUp);
        $response['ID'] = $arrToAdd['ID'];
    } else {
        $product = $auth->CScore->call('entity.item.get', ['ENTITY'=> 'product', 'filter'=>['ID'=>$arrToAdd['PRODUCT_ID']]])[0]['PROPERTY_VALUES'];

        $data=
            [
                'NAME' => $arrToAdd['NAME'],
                'COUNT' => $arrToAdd['COUNT'],
                'CODE' => $product['VENDOR_CODE'],
                'PRODUCT_ID' => $arrToAdd['PRODUCT_ID'],
                'DESCRIPTION_APP' => $_POST['desc'],
                'USER_ID' => $_POST['client_id'],
                'SERVICES' => $arrToAdd['SERVICES'],
            ];
        p($data, "data", $log);
        $applicationAdd =   addApplication($auth, $data);
        p($applicationAdd, "applicationAdd", $log);
        $response['ID'] = $applicationAdd;
    }
    echo json_encode($response);
}
