<?//файл получает из хайблока и записывает в хайблок настройки для облака
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once('tools/HlbkForRealEst.php');
$file_log =  'loginstall.txt';
$domain = $_REQUEST["domain"];
$access_token = $_REQUEST["access_token"];
$domain_full = ($_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://') . $domain;
$member_id = $_REQUEST["member_id"];
$refresh_token = $_REQUEST["refresh_token"];
$action = $_REQUEST["action"];

//проверка не устарели ли данные для авторизации
if (!$access_token)
{
    $uploadResult = array(
        "error" => 1,
        "message" => 'Данные для авторизации устарели'
    );
    echo json_encode($uploadResult);
    exit;
}

//проверка является ли пользователь админом
$url = $domain_full.'/rest/user.admin.json';
$additionalParameters = array(
    "auth" => $access_token
);
$isAdmin = executeRequest($url, $additionalParameters);
if (!$isAdmin)
{
    $uploadResult = array(
        "error" => 2,
        "message" => 'Не достаточно прав. Настройки доступны только администратору'
    );
    echo json_encode($uploadResult);
    exit;
}

/*---------- запись в хайблок настроек для облака -----------*/
if ($action === 'setSettings')
{
    /*---------- сохранение авторизационных данных -----------*/
    //поиск авторизационных данных в хайблоке
    $hl = \Cassoft\SelfProg\HlbkForRealEst::getHlbk('app_brokci_accesses');
    $arAccessParams = getAccessParams($member_id, $hl);

    //подготовка данных для хайблока
    $data = array(
        'UF_CS_CLIENT_PORTAL_MEMBER_ID'     => $member_id,
        'UF_CS_CLIENT_PORTAL_DOMEN'         => $domain,
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'  => $access_token,
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $refresh_token
    );

    if ($arAccessParams["ID"]) //если данные уже существуют, обновим их
    {
        $res = $hl::update($arAccessParams["ID"], $data);
    }
    else //иначе добавим новую запись
    {
        $res = $hl::add($data);
    }

    /*---------- сохранение настроек -----------*/
    //получение id пользователя
    $url = $domain_full.'/rest/user.current.json';
    $additionalParameters = array(
        "auth" => $access_token
    );
    $arUser= executeRequest($url, $additionalParameters);
    //file_put_contents($file_log, print_r($arUser,true), FILE_APPEND);
   // file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
    //подготовка данных для хайблока
    $data = array(
      //  'UF_CS_CLIENT_SELL_CATEGORY_ID'      => $_REQUEST["sell"],
      //  'UF_CS_CLIENT_NEW_CATEGORY_ID'       => $_REQUEST["new"],
      //  'UF_CS_CLIENT_RENT_CATEGORY_ID'      => $_REQUEST["rent"],
      //  'UF_CS_CLIENT_SELL_STAGES_ID'        => json_encode($_REQUEST["sell-stages"]),
      //  'UF_CS_CLIENT_NEW_STAGES_ID'         => json_encode($_REQUEST["new-stages"]),
      //  'UF_CS_CLIENT_RENT_STAGES_ID'        => json_encode($_REQUEST["rent-stages"]),
        'UF_CS_CLIENT_MEMBER_ID'             => $_REQUEST["member_id"],
        'UF_CS_CLIENT_DOMAIN'                => $_REQUEST["domain"],
        'UF_CS_CLIENT_COMPANY_NAME'          => $_REQUEST["company"],
        'UF_CS_CLIENT_DESCRIPTION_FILD'      => $_REQUEST["description"],
        'UF_CS_CLIENT_SITE_ADDRESS'          => $_REQUEST["site"],
        'UF_CS_CLIENT_SETTINGS_CHANGE_BY_ID' => $arUser["ID"],
        'UF_CS_CLIENT_COUNTRY' => $_REQUEST["country"],
        'UF_CS_CLIENT_REGION' => $_REQUEST["region"],
        'UF_CS_CLIENT_CITY' => $_REQUEST["city"],
        'UF_CS_CLIENT_FIO' => $_REQUEST["UserAdm"],
        'UF_CS_CLIENT_TEL' => $_REQUEST["UserTel"],
        'UF_CS_CLIENT_TEL2' => $_REQUEST["UserTel2"],
        'UF_CS_CLIENT_MAIL' => $_REQUEST["UserMail"],
        'UF_CS_CLIENT_FOTO' => $_REQUEST["UserFoto"],
        'UF_CS_VIDJET' => $_REQUEST["vidjet"],
        'UF_CS_CLIENT_SETTINGS_CHANGE_DATE'  => ConvertTimeStamp(time(), "FULL") //время в формате сайта
    );
   // file_put_contents($file_log, print_r($data,true), FILE_APPEND);
    $hl = \Cassoft\SelfProg\HlbkForRealEst::getHlbk('client_portals_settings');
    $res = $hl::add($data);

    if ($res->isSuccess())
    {
        $uploadResult = array(
            "result" => 1,
            "message" => $_REQUEST["site"].' Настройки сохранены'
        );
        echo json_encode($uploadResult);
        exit;
    }
    else{
        $uploadResult = array(
            "error" => 1,
            "message" => $_REQUEST["site"].' Не удалось сохранить настройки'
        );
        echo json_encode($uploadResult);
       // file_put_contents($file_log, print_r($uploadResult,true), FILE_APPEND);
        //TODO: сделать вывод ошибки в лог:
        //$arErrors = $result->getErrorMessages();
        //$errorMessage = json_encode($arErrors);
        exit;
    }
}

//функция получает авторизационные данные для облака из хайблока
function getAccessParams($member_id, $hl)
{
    $rowsResult = $hl::getList(array(
        'select' => array('*'),
        'filter' => array("UF_CS_CLIENT_PORTAL_MEMBER_ID" => $member_id),
        'order' => array("ID" => "DESC"),
        'limit' => 1,
    ));
    $res = $rowsResult->fetch();
    return $res;
}

//функция выполненяет запрос к облаку и возвращает результат запроса
function executeRequest($url, array $additionalParameters = array())
{
    $curlOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_VERBOSE => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($additionalParameters),
        CURLOPT_URL => $url
    );

    $curl = curl_init();
    curl_setopt_array($curl, $curlOptions);
    $curlResult = '';

    try {
        $curlResult = curl_exec($curl);
    }
    catch(Exception $e) {
        //TO DO вывод ошибки в лог
    }

    try {
        $curlResult  = json_decode($curlResult, true);
    }
    catch(Exception $e) {
        //TO DO вывод ошибки в лог
    }
    curl_close($curl);

    return $curlResult["result"];
}
