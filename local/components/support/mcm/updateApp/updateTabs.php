<?php

    $patch = 'https://city.brokci.ru/cassoftApp/market/logisticsPro/';


//---------------- вкладка в сделке

    $isPlacementExist = false;
    $placementParamsDeal = [
        'PLACEMENT' => 'CRM_DEAL_DETAIL_TAB',
        'HANDLER' => $patch . "index.php",
        'TITLE' => 'Логистик-PRO',
        'DESCRIPTION' => 'cassoft.ru'
    ];
    $placementParamsLead = [
        'PLACEMENT' => 'CRM_LEAD_DETAIL_TAB',
        'HANDLER' => $patch . "index.php",
        'TITLE' => 'Логистик-PRO',
        'DESCRIPTION' => 'cassoft.ru'
    ];
    $placementParamsCompany = [
        'PLACEMENT' => 'CRM_COMPANY_DETAIL_TAB',
        'HANDLER' => $patch . "index.php",
        'TITLE' => 'Логистик-PRO',
        'DESCRIPTION' => 'cassoft.ru'
    ];

    $isPlacementSliderExist = false;
    $placementSliderParams = [
        'PLACEMENT' => 'REST_APP_URI',
        'HANDLER' => $patch . "/ajax/app_uri.php",
        'TITLE' => 'Логистик-PRO',
        'DESCRIPTION' => 'cassoft.ru'
    ];
    $addPlacementDeal = $auth->core->call('placement.bind', $placementParamsDeal);
    $addPlacementLead = $auth->core->call('placement.bind', $placementParamsLead);
    $addPlacementCompany = $auth->core->call('placement.bind', $placementParamsCompany);
    $addPlacement = $auth->core->call('placement.bind', $placementSliderParams);
    $getPlacement = $auth->core->call('placement.get')->getResponseData()->getResult()->getResultData();
    $debug->printR($getPlacement);
    /*
    if (!empty($getPlacement['0'])) {
        foreach ($getPlacement as $placement) {
            if (($placement['handler'] == $placementParams['HANDLER']) && ($placement['placement'] == $placementParams['PLACEMENT'])) {
                $isPlacementExist = true;
            }
            if (($placement['handler'] == $placementSliderParams['HANDLER']) && ($placement['placement'] == $placementSliderParams['PLACEMENT'])) {
                $isPlacementSliderExist = true;
            }
        }
    }
    if (!$isPlacementExist) {
        $addPlacement = $auth->core->call('placement.bind', $placementParams);
    }

    if (!$isPlacementSliderExist) {
        $addPlacementSlider = $auth->core->call('placement.bind', $placementSliderParams);
    }
 //   $result['object'] = 'test';
   // echo json_encode($result);
//}

//----------вкладка в Лиде----------------------------------

$isPlacementExist = false;
$placementParams = [
    'PLACEMENT' => 'CRM_LEAD_DETAIL_TAB',
    'HANDLER' => $CloudApplication->getIndexFile(),
    'TITLE' => 'BROKCI-PRO',
    'DESCRIPTION' => 'cassoft.ru'
];

$isPlacementSliderExist = false;
$placementSliderParams = [
    'PLACEMENT' => 'REST_APP_URI',
    'HANDLER' => 'https://city.brokci.ru/pub/cassoftApp/brokci/pages/formObject.php',
    'TITLE' => 'BROKCI-PRO',
    'DESCRIPTION' => 'cassoft.ru'
];
$getPlacement = $auth->core->call('placement.get');
if (!empty($getPlacement['0'])) {
    foreach ($getPlacement as $placement) {
        if (($placement['handler'] == $placementParams['HANDLER']) && ($placement['placement'] == $placementParams['PLACEMENT'])) {
            $isPlacementExist = true;
        }
        if (($placement['handler'] == $placementSliderParams['HANDLER']) && ($placement['placement'] == $placementSliderParams['PLACEMENT'])) {
            $isPlacementSliderExist = true;
        }
    }
}
if (!$isPlacementExist) {
    $addPlacement = $auth->core->call('placement.bind', $placementParams);
}

if (!$isPlacementSliderExist) {
    $addPlacementSlider = $auth->core->call('placement.bind', $placementSliderParams);
}
$result['object'] = 'test';
echo json_encode($result);
}
