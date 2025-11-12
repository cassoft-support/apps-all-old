<?php

    $patch = 'https://city.brokci.ru/cassoftApp/market/logisticsPro/ajax/';
    $propertyHandlerUrl = $patch . 'carrier.php';
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


    $userfieldTypeList = $auth->core->call('userfieldtype.list')->getResponseData()->getResult()->getResultData();
    $debug->printR($userfieldTypeList, 'userfieldtype.list');
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

