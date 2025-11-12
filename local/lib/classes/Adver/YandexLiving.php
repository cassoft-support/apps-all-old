<?php

namespace Cassoft\Adver;

require($_SERVER['DOCUMENT_ROOT'] . '/poligon/classes/Dadata.php');


class YandexLiving
{

    private static $premisesTypeGloss = [
        '1' => 'flat',
        '2' => 'room',
        '3' => 'house with lot',
        '4' => 'townhouse',
        '5' => 'cottage',
        '6' => 'lot',
    ];

    private static $renovationGloss = [
        '1' => 'требует ремонта',
        '2' => 'требует ремонта',
        '3' => 'требует ремонта',
        '4' => 'требует ремонта',
        '5' => 'дизайнерский',
        '6' => 'евроремонт',
        '7' => 'косметический',
    ];

    private static $plotType = [
        '1' => 'ИЖС',
        '2' => 'садоводство',
        '3' => 'садоводство',
    ];

    private static $windowDirGloss = [
        '1' => 'во двор',
        '2' => 'на улицу',
        '3' => 'во двор',
    ];

    private static $materialWallGloss = [
        '1' => 'кирпичный',
        '2' => 'панельный',
        '3' => 'блочный',
        '4' => 'панельный',
        '5' => 'деревянный',
        '6' => 'монолит',
        '7' => 'панельный',
        '8' => 'блочный',
        '9' => 'монолит',
        '10' => 'монолит',
        '11' => 'панельный',
        '12' => 'панельный',
        '13' => 'панельный',
        '14' => 'блочный',
        '15' => 'панельный',
    ];

    public static function validate(array $rs)
    {
        $result = [];
        $general = self::generalInfo($rs);
        $sellerInfo = self::sellerInfo($rs);
        $rsInfo = self::rsInfo($rs);
        $premisesInfo = self::premisesInfo($rs);
        $buildInfo = self::buildInfo($rs);
        $terms = self::terms($rs);
        $result = array_merge($result, $general, $sellerInfo, $terms, $rsInfo, $premisesInfo, $buildInfo);
        return $result;
    }
    private static function terms($rs)
    {
        $terms = [];
        $terms['price'] = [
            'value' => preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']),
            'currency' => 'RUB',
        ];
        if ($rs['CATEGORY_CODE'] === 'rent') {
            if ($rs['UF_CRM_CS_DEAL_TYPE_RENT'] == 'Посуточно') {
                $terms['price']['period'] = 'day';
            } else {
                $terms['price']['period'] = 'month';
            }
            if ($rs['UF_CRM_CS_DEAL_DEPOSIT'] == 'Без залога' || empty($rs['UF_CRM_CS_DEAL_DEPOSIT'])) {
                $terms['price']['rent-pledge'] = 'false';
            } else {
                $terms['price']['rent-pledge'] = 'true';
            }
        }
        return array_filter($terms);
    }
    private static function buildInfo($rs)
    {
        $buildInfo = [];
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $buildInfo['floors-total'] = $build['UF_CS_B_FLOOR_MAX'];
        $buildInfo['security'] = $build['UF_CS_B_SECURITY'];
        $buildInfo['building-type'] = self::$materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $buildInfo['ceiling-height'] = $premises['UF_CS_P_HEIGHT'];

        if (!empty($build['UF_CS_YANDEX_GK_ID'])) {
            $buildInfo['yandex-building-id'] = $build['UF_CS_YANDEX_GK_ID'];
        }
        if (!empty($build['UF_CS_YANDEX_BUILD_ID'])) {
            $buildInfo['yandex-house-id'] = $build['UF_CS_YANDEX_BUILD_ID'];
        }

        if (!empty($build['UF_CS_B_EXPLOITATION_START_YEAR'])) {
            $buildInfo['built-year'] = $build['UF_CS_B_EXPLOITATION_START_YEAR'];
        } else {
            $buildInfo['built-year'] = $build['UF_CS_B_BUILT_YEAR'];
        }

        if (!empty($build['UF_CS_B_GARBAGE_CHUTE_AMOUNT']) &&  $build['UF_CS_B_GARBAGE_CHUTE_AMOUNT'] != 0) {
            $buildInfo['rubbish-chute'] = 'true';
        } else {
            $buildInfo['rubbish-chute'] = 'false';
        }

        if (!empty($build['UF_CS_B_ELECTRICAL_TYPE']) && $build['UF_CS_B_ELECTRICAL_TYPE'] != 1) {
            $buildInfo['electricity-supply'] = 'true';
        } else {
            $buildInfo['electricity-supply'] = 'false';
        }

        if (!empty($build['UF_CS_B_COLD_WATER_TYPE']) && $build['UF_CS_B_COLD_WATER_TYPE'] != 1) {
            $buildInfo['water-supply'] = 'true';
        } else {
            $buildInfo['water-supply'] = 'false';
        }

        if (!empty($build['UF_CS_B_GAS_TYPE']) && $build['UF_CS_B_GAS_TYPE'] != 1) {
            $buildInfo['gas-supply'] = 'true';
        } else {
            $buildInfo['gas-supply'] = 'false';
        }

        if (!empty($build['UF_CS_B_SEWERAGE_TYPE']) && $build['UF_CS_B_SEWERAGE_TYPE'] != 1) {
            $buildInfo['sewerage-supply'] = 'true';
        } else {
            $buildInfo['sewerage-supply'] = 'false';
        }

        if (!empty($build['UF_CS_B_HOT_WATER_TYPE']) && $build['UF_CS_B_HOT_WATER_TYPE'] != 1) {
            $buildInfo['heating-supply'] = 'true';
        } else {
            $buildInfo['heating-supply'] = 'false';
        }


        return array_filter($buildInfo);
    }
    private static function premisesInfo($rs)
    {
        $premisesInfo = [];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $premisesInfo['rooms'] = $premises['UF_CS_P_ROOMS'];

        if ($rs['UF_CS_RS_PREMISES_TYPE']['ID'] == 2) {
            $premisesInfo['rooms-offered'] = $premises['UF_CS_P_ROOMS'];
        }

        $premisesInfo['floor'] = $premises['UF_CS_P_FLOOR'];

        if ($rs['UF_CRM_CS_DEAL_HAS_INTERNET'] == '0') {
            $premisesInfo['internet'] = 'false';
        } else {
            $premisesInfo['internet'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_FURNITURE'] == '0') {
            $premisesInfo['room-furniture'] = 'false';
        } else {
            $premisesInfo['room-furniture'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_PHONE'] == '0') {
            $premisesInfo['phone'] = 'false';
        } else {
            $premisesInfo['phone'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_KITCHEN_FURNITURE'] == '0') {
            $premisesInfo['kitchen-furniture'] = 'false';
        } else {
            $premisesInfo['kitchen-furniture'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_TV'] == '0') {
            $premisesInfo['television'] = 'false';
        } else {
            $premisesInfo['television'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_WASHER'] == '0') {
            $premisesInfo['washing-machine'] = 'false';
        } else {
            $premisesInfo['washing-machine'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_CONDITIONER'] == '0') {
            $premisesInfo['air-conditioner'] = 'false';
        } else {
            $premisesInfo['air-conditioner'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_DISHWASHER'] == '0') {
            $premisesInfo['dishwasher'] = 'false';
        } else {
            $premisesInfo['dishwasher'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_PETS_ALLOWER'] == '0') {
            $premisesInfo['with-pets'] = 'false';
        } else {
            $premisesInfo['with-pets'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_FRIDGE'] == '0') {
            $premisesInfo['refrigerator'] = 'false';
        } else {
            $premisesInfo['refrigerator'] = 'true';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_CHILDREN_ALLOWED'] == '0') {
            $premisesInfo['with-children'] = 'false';
        } else {
            $premisesInfo['with-children'] = 'true';
        }
        $premisesInfo['window-view'] = self::$windowDirGloss[$premises['UF_CS_P_WINDOW_DIRECTION']];
        return array_filter($premisesInfo);
    }

    private static function rsInfo($rs)
    {
        $buildInfo = [];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        if (!empty($premises['UF_CS_P_TOTAL_AREA'])) {
            $buildInfo['area'] = [
                'value' => $premises['UF_CS_P_TOTAL_AREA'],
                'unit' => 'кв.м'
            ];
        }
        if (!empty($premises['UF_CS_P_LIVING_AREA'])) {
            $buildInfo['living-space'] = [
                'value' => $premises['UF_CS_P_LIVING_AREA'],
                'unit' => 'кв.м'
            ];
        }
        if (!empty($premises['UF_CS_P_KITCHEN_AREA'])) {
            $buildInfo['kitchen-space'] = [
                'value' => $premises['UF_CS_P_KITCHEN_AREA'],
                'unit' => 'кв.м'
            ];
        }
        if ($rs['UF_CS_RS_PREMISES_TYPE']['ID'] == 2 && !empty($premises['UF_CS_P_TOTAL_AREA'])) {
            $buildInfo['room-space'] = [
                'value' => $premises['UF_CS_P_TOTAL_AREA'],
                'unit' => 'кв.м'
            ];
        }
        if (!empty($plot['UF_CS_PS_PLOT_AREA'])) {
            $buildInfo['lot-area'] = [
                'value' => $plot['UF_CS_PS_PLOT_AREA'],
                'unit' => 'кв.м'
            ];
        }
        if (!empty($plot['UF_CS_PL_PLOT_TYPE'])) {
            $buildInfo['lot-type'] = self::$plotType[$plot['UF_CS_PL_PLOT_TYPE']];
        }
        if (!isset($rs['PHOTOS'])) {
            $rs['PLANS'] = [];
        }
        if (!isset($rs['PLANS'])) {
            $rs['PLANS'] = [];
        }

        $buildInfo['image'] = array_filter(array_merge(array_values($rs['PHOTOS']), array_values($rs['PLANS'])));
        $buildInfo['is-image-order-change-allowed'] = 'false';
        $buildInfo['renovation'] = self::$renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $buildInfo['description'] = $rs['DESCRIPTION'];
        if (!empty($rs['PLANS'])) {
            $buildInfo['disable-flat-plan-guess'] = 'true';
        }

        return array_filter($buildInfo);
    }
    private static function sellerInfo($rs)
    {
        $sellerInfo = [];
        $seller['name'] = $rs['ASSIGNED_NAME'];
        if (!empty($rs['ASSIGNED_PHONE'])) {
            $seller['phone'] = implode(array(
                '0' => '+7',
                '1' => substr(preg_replace("/[^0-9]/", '',  $rs['ASSIGNED_PHONE']), -10),
            ));
        }
        $seller['organization'] = $rs['COMPANY_NAME'];
        $seller['email'] = $rs['ASSIGNED_EMAIL'];
        $seller['photo'] = $rs['ASSIGNED_AVATAR'];
        $seller['category'] = 'agency';
        $sellerInfo['sales-agent'] = array_filter($seller);
        return $sellerInfo;
    }

    private static function generalInfo(array $rs)
    {
        $result = [];
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        if ($rs['CATEGORY_CODE'] === 'sell' || $rs['CATEGORY_CODE'] === 'new') {
            $result['type'] = 'продажа';
        } else {
            $result['type'] = 'аренда';
        }
        $result['internal-id'] = $rs['ID'];
        $result['creation-date'] = date("c");

        $result['property-type'] = 'living';
        $result['category'] = self::$premisesTypeGloss[$rs['UF_CS_RS_PREMISES_TYPE']['ID']];
        if (in_array($rs['UF_CS_RS_PREMISES_TYPE']['ID'], ['1', '2'])) {
            $result['cadastral-number'] = $premises['UF_CS_P_CADASTRAL_NUMBER'];
        } else {
            $result['cadastral-number'] = $build['UF_CS_B_CADASTRAL_NUMBER'];
        }
        if ($rs['UF_CS_RS_PREMISES_TYPE']['ID'] == 6) {
            $result['cadastral-number'] = $plot['UF_CS_PL_CADASTRAL_NUMBER'];
        }
        if ($result['category'] == 'lot' && empty($rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE'])) {
            $rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE'] = $plot['UF_CS_PL_ADDRESS'];
        }
        $result['location'] = self::location($rs, $build);
        return array_filter($result);
    }

    private static function location($rs, $build)
    {
        $location = [];
        if (!empty($rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE'])) {
            $tokenDadata = '8025a6f35c4076535b48ef6021c562765bcd73f1';
            $dadata = new \Cassoft\Api\Dadata($tokenDadata);
            $dadata->init();
            $addressMass = explode('|', $rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE']);
            $fullAddress = $addressMass['0'];
            $coormass = explode(';', $addressMass['1']);
            $dadataAddress = $dadata->exectSuggest('address', $fullAddress);
            $dadataAddress = $dadataAddress['suggestions'];
            $location['country'] = 'Россия';
            $location['region'] = trim("{$dadataAddress['0']['data']['region']} {$dadataAddress['0']['data']['region_type_full']}");
            $location['district'] = trim("{$dadataAddress['0']['data']['area']} {$dadataAddress['0']['data']['area_type_full']}");
            if (!empty($dadataAddress['0']['data']['settlement'])) {
                $location['locality-name'] = trim("{$dadataAddress['0']['data']['settlement_type_full']} {$dadataAddress['0']['data']['settlement']}");
            } else {
                $location['locality-name'] = trim("{$dadataAddress['0']['data']['city_type_full']} {$dadataAddress['0']['data']['city']}");
            }
            $location['sub-locality-name'] = trim("{$dadataAddress['0']['data']['city_district']} {$dadataAddress['0']['data']['city_district_type_full']}");
            $address[] = trim("{$dadataAddress['0']['data']['street_type_full']} {$dadataAddress['0']['data']['street']}");
            $address[] = trim("{$dadataAddress['0']['data']['house_type_full']} {$dadataAddress['0']['data']['house']}");
            $address[] = trim("{$dadataAddress['0']['data']['block_type_full']} {$dadataAddress['0']['data']['block']}");
            $location['address'] = implode(', ', array_filter($address));
            $location['latitude'] = $coormass['0'];
            $location['longitude'] = $coormass['1'];
        } else {
            $location['country'] = 'Россия';
            $location['region'] = trim("{$build['UF_CS_B_SUBJECT']} {$build['UF_CS_B_SUBJECT_TYPE']}");
            $location['district'] = trim("{$build['UF_CS_B_DISTRICT']} {$build['UF_CS_B_DISTRICT_TYPE']}");
            if (!empty($build['UF_CS_B_SETTLEMENT'])) {
                $location['locality-name'] = trim("{$build['UF_CS_B_SETTLEMENT_TYPE']} {$build['UF_CS_B_SETTLEMENT']}");
            } else {
                $location['locality-name'] = trim("{$build['UF_CS_B_CITY_TYPE']} {$build['UF_CS_B_CITY']}");
            }
            $location['sub-locality-name'] = trim("{$build['UF_CS_B_LOCALITY']} {$build['UF_CS_B_LOCALITY_TYPE']}");
            $address[] = trim("{$build['UF_CS_B_STREET_TYPE']} {$build['UF_CS_B_STREET']}");
            $address[] = trim("{$build['UF_CS_B_HOUSE_TYPE']} {$build['UF_CS_B_HOUSE']}");
            $address[] = trim("{$build['UF_CS_B_BLOCK_TYPE']} {$build['UF_CS_B_BLOCK']}");
            $location['address'] = implode(', ', array_filter($address));
            $location['latitude'] = $build['UF_CS_B_LAT'];
            $location['longitude'] = $build['UF_CS_B_LONG'];
        }



        return array_filter($location);
    }
}