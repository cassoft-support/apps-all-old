<?php
/*
$patch ='https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/';
    $propertyHandlerUrl = $patch.'carrier.php';
    $propertyType = $CloudApp;
    $isPropertyExist = false;

    $userFieldsParams = [
        'USER_TYPE_ID' => $propertyType,
        'HANDLER' => $propertyHandlerUrl,
        'TITLE' => 'Представитель перевозчика',
        'OPTIONS' => [
            'height' => 100,
        ],
        'DESCRIPTION' => 'Поле добавление представителя Перевозчика ' . $propertyType
    ];



  //  $userfieldTypeList = $auth->core->call('userfieldtype.list')->getResponseData()->getResult()->getResultData();
   // $debug->printR($userfieldTypeList, 'userfieldtype.list');
    if (!empty($userfieldTypeList)) {
        foreach ($userfieldTypeList as $key => $value) {
            if ($value['USER_TYPE_ID'] == $propertyType and $value['HANDLER'] == $propertyHandlerUrl) {
                $isPropertyExist = true;
                unset($userFieldsParams['HANDLER']);
                $updateUserfield = $auth->core->call('userfieldtype.update', $userFieldsParams);
            }
        }
    }
    if (!$isPropertyExist) {
        $addUserfield = $auth->core->call('userfieldtype.add', $userFieldsParams);
    }

    
    $fieldsCode = 'UF_CRM_CS_DEAL_CARRIER_SELECT';
    $isFieldExist = false;
    $fieldsParams = [
        'USER_TYPE_ID' => $propertyType,
        'FIELD_NAME' => $fieldsCode,
        'XML_ID' => $fieldsCode,
        'MANDATORY' => 'N',
        'SHOW_IN_LIST' => 'Y',
        'EDIT_IN_LIST' => 'Y',
        'EDIT_FORM_LABEL' => 'Представитель Перевозчика ' . $propertyType,
        'LIST_COLUMN_LABEL' => 'Привязка представителя Перевозчика ' . $propertyType,
        'SETTINGS' => []
    ];

    $userfieldList = $auth->core->call('crm.deal.userfield.list', [
        'FILTER' => [
            'FIELD_NAME' => $fieldsCode
        ]
    ])->getResponseData()->getResult()->getResultData();
    if (!empty($userfieldList['0'])) {
        $isFieldExist = true;
        $updateField = $auth->core->call('crm.deal.userfield.update', [
            'ID' => $userfieldList['0']['ID'],
            'fields' => $fieldsParams,
        ]);
    }
    if (!$isFieldExist) {
        $addField = $auth->core->call('crm.deal.userfield.add', $fieldsParams);
    }

    //--------------------------------------событие на изменение сделки
    $isEventExist = false;
    $eventParmas = [
        'event' => 'onCrmDealUpdate',
        'handler' => $CloudApplication->getIndexFile()
    ];
    $eventGet = $auth->core->call('event.get')->getResponseData()->getResult()->getResultData();
    foreach ($eventGet as $event) {
        if (($event['handler'] == $CloudApplication->getIndexFile()) && ($event['event'] == 'ONCRMDEALUPDATE')) {  
            $isEventExist = true;
            break;
        }
    }
    if (!$isEventExist) {
        $addEvent = $auth->core->call('event.bind', $eventParmas);
    }
 
  //--------------------------------------событие на создание Лида
 $isEventExist = false;
 $eventParmas = [
     'event' => 'onCrmLeadAdd',
     'handler' => $CloudApplication->getIndexFile()
 ];
 $eventGet = $auth->core->call('event.get');
 foreach ($eventGet as $event) {
     if (($event['handler'] == $CloudApplication->getIndexFile()) && ($event['event'] == 'ONCRMLEADADD')) {  //onCrmLeadUpdate
         $isEventExist = true;
         break;
     }
 }
 if (!$isEventExist) {
     $addEvent = $auth->core->call('event.bind', $eventParmas);
 }

  

 //--------------------------------------событие на изменение Лида
 $isEventExist = false;
 $eventParmas = [
     'event' => 'onCrmLeadUpdate',
     'handler' => $CloudApplication->getIndexFile()
 ];
 $eventGet = $auth->core->call('event.get');
 foreach ($eventGet as $event) {
     if (($event['handler'] == $CloudApplication->getIndexFile()) && ($event['event'] == 'ONCRMLEADUPDATE')) {  //onCrmLeadUpdate
         $isEventExist = true;
         break;
     }
 }
 if (!$isEventExist) {
     $addEvent = $auth->core->call('event.bind', $eventParmas);
 }




//---------------- вкладка в сделке

    $isPlacementExist = false;
    $placementParams = [
        'PLACEMENT' => 'CRM_DEAL_DETAIL_TAB',
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
    $getPlacement = $auth->core->call('placement.get')->getResponseData()->getResult()->getResultData();
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
