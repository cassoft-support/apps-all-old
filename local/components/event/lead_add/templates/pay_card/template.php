<<link rel="stylesheet" href="/local/lib/css/cassoft/style.css">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/app.css">
    <!--link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"-->
    <title>Контроль пропущенных звонков</title>
</head>

<body>

<?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/lib/classes/Services/HlService.php');

    /*
        $file_log = __DIR__ . '/logs/install.txt';

        $arSettings['member_id'] = $_REQUEST['member_id'];
        $arSettings['domain'] = $_REQUEST['DOMAIN'];
        $arSettings['access_token'] = $_REQUEST['AUTH_ID'];
        $arSettings['refresh_token'] = $_REQUEST['REFRESH_ID'];

        require_once('CSRest.php');
        $CRest = new CSRest();
        $CRest->setSettingDataCS($arSettings);
    */


    require_once('crest.php');
    //include('addUserInfo.php');


    $result = CRest::installApp();

    $resUserParams = CRest::call('user.current');
    $userParams = $resUserParams['result'];
    $file_log = "/home/bitrix/www/pub/cassoftApp/callsСontroller/log.txt";
    file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);
    file_put_contents($file_log, print_r($userParams, true), FILE_APPEND);
    file_put_contents($file_log, print_r($userID, true), FILE_APPEND);

    $HlUserParmas = new \Cassoft\Services\HlService('client_app_cassoft');
    $userData = [
        'UF_CS_CLIENT_FIO' => "{$userParams['LAST_NAME']} {$userParams['NAME']} {$userParams['SECOND_NAME']}",
        'UF_CS_CLIENT_PHOTO' => $userParams['PERSONAL_PHOTO'],
        'UF_CS_CLIENT_TEL' => $userParams['WORK_PHONE'],
        'UF_CS_CLIENT_MAIL' => $userParams['EMAIL'],
        'UF_CS_CALLS_CONTROLLER' => 'true',
        'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $_REQUEST['member_id'],
        'UF_CS_CLIENT_PORTAL_DOMEN' => $_REQUEST['DOMAIN'],

    ];
    file_put_contents($file_log, print_r($userData, true), FILE_APPEND);
    $filterHb = ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $_REQUEST['member_id']];

    $checkUser = $HlUserParmas->hl::getList([
        'select' => ['*'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $_REQUEST['member_id']],
        'order' => ['ID' => 'DESC'],
        'limit' => 1,
    ]);
    $resultCheck = $checkUser->fetch();
    file_put_contents($file_log, print_r($resultCheck, true), FILE_APPEND);

    if ($resultCheck['ID']) {
        $update = $HlUserParmas->hl::update($resultCheck['ID'], $userData);
        file_put_contents($file_log, print_r($update, true), FILE_APPEND);
    } else {
        $add = $HlUserParmas->hl::add($userData);
        file_put_contents($file_log, print_r($add, true), FILE_APPEND);
    }

    $HlUserParmasAcc = new \Cassoft\Services\HlService('app_call_contorller__accesses');
    $userDataAcc = [
        'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $_REQUEST['member_id'],
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $_REQUEST['REFRESH_ID'],
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $_REQUEST['AUTH_ID'],
        'UF_CS_CLIENT_PORTAL_DOMEN' => $_REQUEST['DOMAIN'],
        'UF_CS_CLIENT_USER_ADMIN' => $userParams['ID']

    ];
    file_put_contents($file_log, print_r($userDataAcc, true), FILE_APPEND);

    $checkUserAcc = $HlUserParmasAcc->hl::getList([
        'select' => ['*'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $_REQUEST['member_id']],
        'order' => ['ID' => 'DESC'],
        'limit' => 1,
    ]);
    $resultCheckAcc = $checkUserAcc->fetch();
    file_put_contents($file_log, print_r($resultCheck, true), FILE_APPEND);

    if ($resultCheckAcc['ID']) {
        $updateAcc = $HlUserParmasAcc->hl::update($resultCheckAcc['ID'], $userDataAcc);
        file_put_contents($file_log, print_r($updateAcc, true), FILE_APPEND);
    } else {
        $addAcc = $HlUserParmasAcc->hl::add($userDataAcc);
        file_put_contents($file_log, print_r($addAcc, true), FILE_APPEND);
    }

    // handler for events "handler.php"
    $handlerBackUrl = ($_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] === '443' ? 'https' : 'http')
        . '://'
        . $_SERVER['SERVER_NAME']
        . (in_array($_SERVER['SERVER_PORT'], ['80', '443'], true) ? '' : ':' . $_SERVER['SERVER_PORT'])
        . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__)
        . '/handler.php';


?>

<div id="app" style="padding: 20px;" class="container-fluid">
    <div class="install">
        <div>
            <img style="height: 45px;" src="/pub/images/app_call.png">
        </div>
        <div>
            <h4 style=" margin: 10px; color: #000; font-size: 25px;">Установка приложения "Закрытие пропущенных
                звонков"</h4>
        </div>
    </div>
    <form id="call-settings">
        <input type="hidden" name="save" value="Y">
        <input type="hidden" name="access_token" value="<?= $_REQUEST['AUTH_ID']; ?>">
        <input type="hidden" name="refresh_token" value="<?= $_REQUEST['REFRESH_ID']; ?>">
        <input type="hidden" name="domain" value="<?= $_REQUEST['DOMAIN']; ?>">
        <input type="hidden" name="member_id" value="<?= $_REQUEST['member_id']; ?>">
        <div class="form-group">
            <h3>Настройки приложения</h3>
            <div>
                <h4>Приложение по закрытию пропущенных звонков после успешного звонка</h4>
            </div>
            <div>
                <p>сейчас нет дополнительных настроек</p>

            </div>
            <!--<input type="text" class="form-control" name="sms_token" id="sms_token" placeholder="FastSMS API token">-->
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-save">Установить</button>
    </form>


</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="//api.bitrix24.com/api/v1/dev/"></script>

<script>
    BX24.init(function () {
        var size = BX24.getScrollSize();
        // BX24.resizeWindow(size.scrollWidth, 600);
        BX24.fitWindow();

        console.log(size);

        $('#call-settings').submit(function (e) {

            e.preventDefault();

            var params = {
                EVENT: 'ONVOXIMPLANTCALLEND',
                HANDLER: '<?= $handlerBackUrl ?>',
            };

            BX24.callMethod(
                'event.bind',
                params,
                function (result) {
                    if (result.error()) {
                        alert("Error: " + result.error());

                    } else {
                        /**
                         * Provider handler registered successfully
                         */

                        BX24.installFinish();

                    }
                }
            );
        });

    });
</script>


</body>

</html>