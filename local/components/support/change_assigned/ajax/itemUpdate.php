<?

    define(NOT_CHECK_PERMISSIONS, true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/install/base/settings.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
    //require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/debug.php");

    $debug = new \debug('debug');
    $date = date("d.m.YTH:i");
    $file_log = __DIR__ . "/ajaxUpdate.txt";
    file_put_contents($file_log, print_r($date . "\n", true));
    $arParams = json_decode($_POST['request'], true);
    $_REQUEST = json_decode($arParams['UserAut'], true);
    //$debug->printR($_POST, "POST");
    $CSRest = new  \CSRest($arParams['app']);

    if ($_POST['smart']) {
        $params = [
            "entityTypeId" => $_POST['smart'],
            //  "select" => ["*"],
            "select" => ["id", 'stageId', 'assignedById', 'opened'],
            "order" => [],
            "filter" => [
                // 'id' => 1584,
                '!assignedById' => 102,
                //  'opened' => 'N',
                //  'ufCrm22_1695632032679' => '',
                //  'ufCrm66_1659435988' => '',
                // 'categoryId' => 22,
                'stageId' => 'DT152_36:CLIENT',
                //'<ufCrm18CsCitizenship'=>1,
                //  '<ufCrm22CsDateUp' => "25.09.2023MSK15:50",
                //  '<updatedTime' => '2023-09-25T10:50:00+03:00',
                //     'ufCrm18CsCurrency' => null
            ],
            "start" => 0,
        ];


        $getSmart = $CSRest->call('crm.item.list', $params);//$el_id
        $itemSmart = $getSmart['result']['items'][0];
        d($itemSmart);
        // $debug->printR($getSmart, "smart");
        //$itemReestr = $getReestr['result']['item'];


        if ($getSmart['total'] > $getSmart['next']) {
            // $margin=ceil(($getSmart['total']-$getSmart['next'])/50);
            $margin = ceil(($getSmart['total']) / 50);
            $m = 1;
            echo '<pre>';
            print_r($margin);
            echo '</pre>';
            $start = 0;

            for ($m = 1; $m <= $margin; $m++) {
                $params['start'] = $start;
                $itemSmart = $CSRest->call('crm.item.list', $params);
                //echo '<pre>'; print_r($getSmart['total']);  echo '</pre>';
                echo '<pre>';
                print_r("Цикл " . $m);
                echo '</pre>';
                $start = $start + 50;
                foreach ($itemSmart['result']['items'] as $key => $itemSmartVal) {
                    $data = date("d.m.YTH:i");
                    $upParams = [
                        "entityTypeId" => $_POST['smart'],
                        "id" => $itemSmartVal['id'],
                        'fields' => [
                            // 'ufCrm66_1659435988' => '0|RUB'
                            'assignedById' => 102,
                            //    'ufCrm22_1695632032679' => 0
                            //   'ufCrm22CsDateUp' => $data,
                            //  'ufCrm18CsCurrency' => 7148
                        ]
                    ];
                    d($upParams);

                    $upSmart = $CSRest->call('crm.item.update', $upParams);
                    // d($upSmart);
                    $i++;
                }
            }
        }
        $debug->printR($i, "i");
        // d($i);
    }