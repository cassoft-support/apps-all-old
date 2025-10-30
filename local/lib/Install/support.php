<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/tools.php';

$arParams['app'] ="brokci";
$CSRest = new CSRest($arParams['app']);

//авторизация под пользователь
$clientApp = [
    'DOMAIN' => $arParams['auth']['domain'],
    'member_id' => $arParams['auth']['member_id'],
    'AUTH_ID' => $arParams['auth']['access_token'],
    'REFRESH_ID' => ""
];

$auth = new Auth($arParams['app'], $clientApp, __DIR__ . '/');
//авторизация под админ
$clientAppAdm = getAuthHl($arParams['app'], $arParams['auth']['member_id']);
$authAdm = new Auth($arParams['app'], $clientAppAdm, __DIR__ . '/');
$deal = $auth->core->call("crm.deal.get", array('id' => $arParams['data']['FIELDS']['ID']))->getResponseData()->getResult()->getResultData();

// хранилище
$filter=[];
foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'complex', [], $filter, 6000) as  $value) {
    $complexAll[$value['ID']]=$value['NAME'];
}
$resItem = $this->auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0];
//
$order =['ID' => 'DESC'];
$select =['*'];
$filter = [];
//🟢 🟠

foreach ($auth->batch->getTraversableList('crm.product.property.list', $order, $filter, $select, 6000) as $arProperty) {

}

?>
<link rel="stylesheet" href="/local/lib/css/cassoft/sidepanel.css?1111">
<div id="panel-close" class="side-panel-label" style="display: none;">
    <div class="side-panel-label-icon-box" title="Закрыть">
        <div class="side-panel-label-icon side-panel-label-icon-close"></div>
    </div><span class="side-panel-label-text"></span>
</div>
<script>
function resizeFrame() {
var currentSize = BX24.getScrollSize();
console.log(currentSize)
minHeight = currentSize.scrollHeight;
var FrameWidth = document.getElementById("workarea").offsetWidth;
console.log(FrameWidth)
if (minHeight < 300){
frameHeight = 300;
} else{
frameHeight = minHeight + 50;
}
console.log(frameHeight)
BX24.resizeWindow(FrameWidth, frameHeight);
}
</script>