<?

    define(NOT_CHECK_PERMISSIONS, true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/install/base/settings.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
    //require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/Api/ati_su/CSCurl.php");
    $debug = new \debug('debug');
    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/ajaxLoad.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    $arParams = json_decode($_POST['request'], true);
    $_REQUEST = json_decode($arParams['UserAut'], true);
    // d($_REQUEST);
    $memberId = $arParams['member_id'];
    $CloudApp = $arParams['app'];
    $appAccess = 'app_' . $arParams['app'] . '_access';
    $debug->console($appAccess, "app");
    $CloudApplication = new \Cloud\App\CloudApplication($CloudApp);
    $HlClientAppCASSOFT = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientAppCASSOFT->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId]
    ])->fetchAll();
    $hlKeys = [
        'UF_CS_CLIENT_PORTAL_MEMBER_ID',
        'UF_CS_CLIENT_PORTAL_DOMEN',
        'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN',
        'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'
    ];

    $clientApp = $clientsApp['0'];


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
    $CSRest = new CSRest($CloudApp);
    $userCur = $CSRest->call('user.current');
    // d($userCur);
    // $token = 'f44d3cb9238b4aa2941216854cac56f8'; //a3d9943e3b454bddbdf630e4027c95f2
    // $token = 'a3d9943e3b454bddbdf630e4027c95f2';
    $resSetup = $auth->core->call('entity.item.get', ['ENTITY' => 'setup', 'SORT' => [], 'FILTER' => []]
    )->getResponseData()->getResult()->getResultData();
    $arSetup = $resSetup[0]['PROPERTY_VALUES'];
    $token = $arSetup['UF_CS_KEY_ATI'];
    // $debug->printR($resSetup, "setup");
    $keyDadata = "37e84e13164831626cf935884808e3abc8477333";
    $CSCurl = new \CSCurl($token);
    // $debug->printR($_POST, "POST");
    // payment_terms  application   cargo
    $filterUser = [
        'USER_TYPE' => 'employee',
        'ACTIVE' => 1,
        '!UF_USR_1649594686040' => null
    ];
    $order = ['ID' => 'ASC'];
    $select = ["*"];

    $user[0] = 1;
    foreach ($auth->batch->getTraversableList('user.get', $order, $filterUser, $select, 6000) as $arVal) {
        //   $debug->printR($arVal, "user");
        $user[$arVal["UF_USR_1649594686040"]] = $arVal["ID"];
    }
    //  $debug->printR($user, "user");
    /*

        $resEvent = $auth->core->call('entity.item.property.get', ['ENTITY' => 'cargo'])->getResponseData()->getResult(
       )->getResultData();
       $resApplication = [];
       foreach ($resEvent as $key => $value) {
           $resApplication[$value['PROPERTY']] = $value['NAME'];
       }
       //  $debug->printR($resApplication, "поля");
   */

    if ($_POST['eventLoad']) {
        if ($_POST['eventLoad'] === '/v1.0/loads') {
            $active = "Y";
        } else {
            $active = "N";
        }
        $arResult = $CSCurl->callEvent($_POST['eventLoad']);
        // $debug->printR($active, "POST");
    } else {
        $arResult = $CSCurl->callAccount();
    }
    $x = 0;
    $elAppAll = [];
    $elAppPayAll = [];

    foreach ($arResult as $keyResult => $valResult) {
        $date = date("'H:i:s.v'");
        //   sleep(1);
        usleep(50000);
        //  d($date);

        //if ($keyResult >= 230 && $keyResult < 260) {
        $resEvent = $CSRest->call('entity.item.get', [
                'ENTITY' => 'application',
                'SORT' => [],
                'FILTER' => [
                    'PROPERTY_UF_CS_ID_ATI' => $valResult['Id']
                ]
            ]
        );
        // d($resEvent['result']);
        if (empty($resEvent['result'])) {
            $debug->printR($keyResult, "keyResult");

            $x++;
            // $valResult = $arResult[0];
            $elApp = [];
            $elAppPay = [];
            $elCargoAll = [];
            $elCargo = [];

            $elAppPay['UF_CS_CURRENCY_ID'] = $valResult['Payment']['CurrencyId']; //валюта расчетов
            $elAppPay['UF_CS_AUCTION'] = $valResult['Payment']['Torg']; //Торг
            $elAppPay['UF_CS_STARTING_BET'] = $valResult['Payment']['RateSum']; //Начальная ставка
            $elAppPay['UF_CS_PAYMENT_TYPE'] = $valResult['Payment']['AcceptPaymentTypes']; //Тип платежа
            $elAppPay['UF_CS_RATE_WITH_VAT'] = $valResult['Payment']['SumWithNDS']; //Ставка с НДС
            $elAppPay['UF_CS_RATE_WITHOUT_VAT'] = $valResult['Payment']['SumWithoutNDS']; //Ставка без НДС
            /* $elAppPay['UF_CS_RATE_CASH'] = $valResult['Payment']['']; //Ставка наличными
             $elAppPay['UF_CS_PAY_CARD'] = $valResult['Payment']['']; //Оплата на карту
             $elAppPay['UF_CS_OFFERS_CLOSE'] = $valResult['Payment']['']; //Встречные предложения видны только вам
             $elAppPay['UF_CS_OFFERS_BEST'] = $valResult['Payment']['']; //Предложить груз участнику с лучшей ставкой
             $elAppPay['UF_CS_BET_EXCEED'] = $valResult['Payment']['']; //Лучшая ставка не может превышать начальную больше, чем на
             $elAppPay['UF_CS_OFFERS_PERIOD'] = $valResult['Payment']['']; //Время действия предложения

             $elAppPay['UF_CS_STEP'] = $valResult['Payment']['']; //Шаг уменьшения ставки
             $elAppPay['UF_CS_TRADING_DURATION'] = $valResult['Payment']['']; //Длительность Торгов
             */
            $elAppPay['UF_CS_ACCEPT_OFFERS'] = $valResult['Payment']['HideResponses']; //Принимать встречные предложения
            /*

             $elAppPay['UF_CS_STARTING_BET_VAT'] = $valResult['Payment']['']; //Начальная ставка с НДС/без НДС
             $elAppPay['UF_CS_STARTING_BET_N_VAT'] = $valResult['Payment']['']; //Принимать ставки без НДС
             $elAppPay['UF_CS_START_TRADING_DURATION'] = $valResult['Payment']['']; //Начало отсчета длительность Торгов
             $elAppPay['UF_CS_END_ACCEPT_OFFERS'] = $valResult['Payment']['']; //Окончание приема встречных предложений
             $elAppPay['UF_CS_AUTO_RENEWAL_ON'] = $valResult['Payment']['']; //Автопродление на
             $elAppPay['UF_CS_AUTO_RENEWAL_FOR'] = $valResult['Payment']['']; //Автопродление не более чем на
             $elAppPay['UF_CS_BET_RATES_EVERY'] = $valResult['Payment']['']; //Ставки повышать каждые
             $elAppPay['UF_CS_BET_RATES_TO'] = $valResult['Payment']['']; //Ставки повышать до
             $elAppPay['UF_CS_WINNER_DOCS_FOR'] = $valResult['Payment']['']; //Победитель должен предоставить документы за
             $elAppPay['UF_CS_AUTO_WINNER_NEXT'] = $valResult['Payment']['']; //Автоматически назначать следующего победителя
             $elAppPay['UF_CS_RESTART_TRADES'] = $valResult['Payment']['']; //Перезапускать Торги
             $elAppPay['UF_CS_TRADING_DURATION_AFTER'] = $valResult['Payment']['']; //Длительность Торгов после перезапуска
             $elAppPay['UF_CS_BET_AFTER_RESTART'] = $valResult['Payment']['']; //Увеличить начальную ставку торгов после перезапуска на
            */
            $elAppPay['UF_CS_PAYMENT_DAY'] = $valResult['Payment']['PayDays']; //Оплата через банковских дней
            $elAppPay['UF_CS_PREPAYMENT'] = $valResult['Payment']['PrepayPercent']; //Предоплата %
            $elAppPay['UF_CS_PREPAYMENT_FUEL'] = $valResult['Payment']['InFuel']; //Предоплата топливом
            $elAppPay['UF_CS_PAYMENT_UNLOADING'] = $valResult['Payment']['OnUnloading']; //Оплата на выгрузке
            $elAppPay['UF_CS_DIRECT_CONTRACT'] = $valResult['Payment']['DirectContract']; //Прямой договор

            $appAddPay = $CSRest->call('entity.item.add', [
                "ENTITY" => 'payment_terms',
                "NAME" => $valResult['LoadNumber'],
                "ACTIVE" => $active,
                "PROPERTY_VALUES" => $elAppPay
            ]);
            $resAppPay = $appAddPay['result'];
            //$debug->printR($appAddPay, "appAddPay");
            $elCargo['UF_CS_NAME'] = $valResult['Cargo']['CargoTypeId']; // Название груза
            $elCargo['UF_CS_WEIGHT'] = $valResult['Cargo']['Weight']; // Вес груза
            //  $elCargo['UF_CS_TYPE_WEIGHT'] = $valResult['Cargo']['']; // Единица измерения веса
            $elCargo['UF_CS_VOLUME'] = $valResult['Cargo']['Volume']; // Объем
            $elCargo['UF_CS_CONTAINER'] = $valResult['Cargo']['PackType']; // Упаковка
            $elCargo['UF_CS_COUNT'] = $valResult['Cargo']['PalletCount']; // Количество
            $elCargo['UF_CS_LENGTH'] = $valResult['Cargo']['Size']['Length']; // Длина
            $elCargo['UF_CS_LENGTH_SPECIAL'] = $valResult['Cargo']['Size']['LengthHighlight']; // Длина особая
            $elCargo['UF_CS_WIDTH_SPECIAL'] = $valResult['Cargo']['Size']['WidthHighlight']; // Ширина особая
            $elCargo['UF_CS_WIDTH'] = $valResult['Cargo']['Size']['Width']; // Ширина
            $elCargo['UF_CS_HEIGHT_SPECIAL'] = $valResult['Cargo']['Size']['HeightHighlight']; // Высота особая
            $elCargo['UF_CS_HEIGHT'] = $valResult['Cargo']['Size']['Height']; // Высота
            $elCargo['UF_CS_DIAMETER'] = $valResult['Cargo']['Size']['Diameter']; // Диаметр

            $ch_coords = curl_init(
                "https://suggestions.dadata.ru/suggestions/api/4_1/rs/geolocate/address?lat=" . $valResult['Loading']['Latitude'] . "&lon=" . $valResult['Loading']['Longitude'] . "&token=" . $keyDadata
            );
            curl_setopt($ch_coords, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_coords, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_coords, CURLOPT_HEADER, false);
            $result_coords = curl_exec($ch_coords);
            $result_coords_array = json_decode($result_coords, true);
            curl_close($ch_coords);
            $loading = json_encode($result_coords_array["suggestions"][0]);
            //$debug->printR($result_coords_array, "Longitude");
            $elCargo['UF_CS_DOWNLOAD_ADDRESS'] = $loading; // Адрес загрузки

            $ch_coords = curl_init(
                "https://suggestions.dadata.ru/suggestions/api/4_1/rs/geolocate/address?lat=" . $valResult['Unloading']['Latitude'] . "&lon=" . $valResult['Unloading']['Longitude'] . "&token=" . $keyDadata
            );
            curl_setopt($ch_coords, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_coords, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_coords, CURLOPT_HEADER, false);
            $result_coords = curl_exec($ch_coords);
            $result_coords_array = json_decode($result_coords, true);
            curl_close($ch_coords);
            $unloading = json_encode($result_coords_array["suggestions"][0]);
            $elCargo['UF_CS_UNLOADING_ADDRESS'] = $unloading; // Адрес разгрузки
            $cargoAdd = $CSRest->call('entity.item.add', [
                "ENTITY" => 'cargo',
                "NAME" => $valResult['LoadNumber'],
                "ACTIVE" => $active,
                "PROPERTY_VALUES" => $elCargo
            ]);
            $resCargo = $cargoAdd['result'];
             $debug->printR($cargoAdd, "cargoAdd");


            $elApp['UF_CS_BODY'] = $valResult['Transport']['CarType']; // Кузов
            $elApp['UF_CS_TYPE_LOADING'] = $valResult['Transport']['LoadingType']; // Тип загрузки
            $elApp['UF_CS_TYPE_LOADING_ALL'] = $valResult['Transport']['LoadingLogicalOperator']; // Тип загрузки учитывать все
            $elApp['UF_CS_TYPE_UNLOAD'] = $valResult['Transport']['UnloadingType']; // Тип разгрузки
            $elApp['UF_CS_TYPE_UNLOAD_ALL'] = $valResult['Transport']['UnloadingLogicalOperator']; // Тип разгрузки учитывать все
            $elApp['UF_CS_TEMP_FROM'] = $valResult['Transport']['TemperatureFrom']; // Температура от
            $elApp['UF_CS_TEMP_TO'] = $valResult['Transport']['TemperatureTo']; // Температура до
            $elApp['UF_CS_GPS'] = $valResult['Transport']['IsTracking']; // GPS-мониторинг
            $elApp['UF_CS_NUMBER_CARS'] = $valResult['Transport']['TrucksQuantity']; // Кол-во машин
            // $elApp['UF_CS_ADR'] = $valResult['Transport']['']; // Класс опасности
            $elApp['UF_CS_COUPLING'] = $valResult['Transport']['Stsepka']; // Сцепка
            // $elApp['UF_CS_LENGTH_SPECIAL'] = $valResult['Transport']['']; // Длина особая
            $elApp['UF_CS_PNEUMATIC'] = $valResult['Transport']['Pnevmohod']; // Пневмоход
            $elApp['UF_CS_CONICS'] = $valResult['Transport']['Koniki']; // Коники
            $elApp['UF_CS_TIR'] = $valResult['Transport']['TIR']; // TIR
            $elApp['UF_CS_T1'] = $valResult['Transport']['T1']; // T1
            $elApp['UF_CS_CMR'] = $valResult['Transport']['CMR']; // CMR
            $elApp['UF_CS_MEDICAL_BOOK'] = $valResult['Transport']['SanPassport']; // Медкнижка
            $elApp['UF_CS_COMMENTS'] = $valResult['Note'];//Комментарий
            $elApp['UF_CS_ASSIGNED'] = $user[$valResult['ContactId1']];//Ответственный
            $elApp['UF_CS_PAYMENT'] = $resAppPay; //Условия оплаты
            $elApp['UF_CS_LOADING'] = $resCargo;//Загрузка(грузы)
            $elApp['UF_CS_ID_ATI'] = $valResult['Id'];//ID ATI
            $elApp['DATE_ACTIVE_TO'] = $valResult['ArchiveDate'];//дата закрытия

            /*

                    $elApp['UF_CS_DEAL'] = Сделка
                    $elApp['UF_CS_CONTACT'] = Контакт
                    $elApp['UF_CS_COMPANY'] = Компания
                    $elApp['UF_CS_LEAD'] = Лид
                    $elApp['UF_CS_QUOTE'] = Предложения
                    $elApp['UF_CS_PHOTO'] = Фото груза и документов
                    $elApp['UF_CS_STAGE'] = Стадия
             */
d($elApp);
            $appAdd = $CSRest->call('entity.item.add', [
                "ENTITY" => 'application',
                "NAME" => $valResult['LoadNumber'],
                "ACTIVE" => $active,
                "PROPERTY_VALUES" => $elApp
            ]);
            d($appAdd);
        }
        //   }

    }

    //cargoAdd[  }
    //  $debug->printR($elAppAll, "elAppAll");
    // $debug->printR($elAppPay, "elAppPay");
    // $debug->printR($elAppPay, "elAppPay");
    // $debug->printR($appAdd, "appAdd");
    $debug->printR($x, "Result");
    $i = 0;
    $y = 0;
    $z = 0;
    $filter = [];

    foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'payment_terms', [], $filter, 60000) as $val) {
        $i++;
        /*  usleep(50000);
          //  $debug->printR($val['ID'], "resEventPay");
          if ($val['ID']) {
              $resEventPayDel = $CSRest->call('entity.item.delete', ['ENTITY' => 'payment_terms', 'ID' => $val['ID']]);
          }*/
    }
    $debug->printR($i, "resEventPay");

    foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'cargo', [], $filter, 60000) as $val) {
        $y++;

        //  $debug->printR($val['ID'], "resEventCargo");
        /*  if ($val['ID']) {
              d($val['ID']);
              $resEventCargoDel = $CSRest->call('entity.item.delete', ['ENTITY' => 'cargo', 'ID' => $val['ID']]);
              d($resEventCargoDel);
              usleep(50000);
          }*/
    }
    $debug->printR($y, "resEventCargo");
    // de371e59-7014-44fb-81e2-002152b94ef7

    foreach ($auth->batch->getTraversableListEntity('entity.item.get', 'application', [], $filter, 60000) as $val) {
        // d($value['PROPERTY_VALUES']['UF_CS_ID_ATI']);

        $z++;
        /*  if ($val['ID']) {
          $resEventDel = $CSRest->call('entity.item.delete', ['ENTITY' => 'application', 'ID' => $val['ID']]);
              d($resEventDel);
              usleep(50000);
          }*/
    }

    $debug->printR($z, "resEvent");

?>

