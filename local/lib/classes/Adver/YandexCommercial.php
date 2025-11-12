<?php

namespace Cassoft\Adver;


class YandexCommercial
{

    private static $commercialType = [
        '7' => 'office',
        '8' => 'warehouse',
        '9' => 'manufacturing',
        '10' => 'retail',
    ];
    private static $renovationType = [
        '1' => 'черновая отделка',
        '2' => 'черновая отделка',
        '3' => 'частичный ремонт',
        '4' => 'требует ремонта',
        '5' => 'дизайнерский',
        '6' => 'евро',
        '7' => 'с отделкой',
    ];
    private static $objectClass = [
        '1' => 'A+',
        '2' => 'A',
        '3' => 'B+',
        '4' => 'B',
        '5' => 'C+',
        '6' => 'C',
        '7' => 'C',
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
    public static function generalInfo($rs)
    {
        $result = [];
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        if ($rs['CATEGORY_CODE'] === 'sell') {
            $result['type'] = 'продажа';
        } else {
            $result['type'] = 'аренда';
        }
        $result['internal-id'] = $rs['ID'];
        $result['category'] = 'commercial';
        $result['commercial-type'] = self::$commercialType[$rs['UF_CS_RS_PREMISES_TYPE']['ID']];
        $result['creation-date'] = date("c");
        $result['location'] = self::location($build);
        return array_filter($result);
    }
    private static function location($build)
    {
        $location = [];
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

        return array_filter($location);
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
    private static function terms($rs)
    {
        $terms = [];
        $terms['price'] = [
            'value' => preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']),
            'currency' => 'RUB',
        ];
        if ($rs['CATEGORY_CODE'] === 'rent') {
            $terms['deal-status'] = 'direct rent';
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


        if (!isset($rs['PHOTOS'])) {
            $rs['PLANS'] = [];
        }
        if (!isset($rs['PLANS'])) {
            $rs['PLANS'] = [];
        }

        $buildInfo['image'] = array_filter(array_merge(array_values($rs['PHOTOS']), array_values($rs['PLANS'])));
        $buildInfo['is-image-order-change-allowed'] = 'false';
        if (!empty($rs['PLANS'])) {
            $buildInfo['disable-flat-plan-guess'] = 'true';
        }
        $buildInfo['renovation'] = self::$renovationType[$premises['UF_CS_P_RENOVATION']];
        $buildInfo['description'] = $rs['DESCRIPTION'];
        return array_filter($buildInfo);
    }
    private static function premisesInfo($rs)
    {
        $premisesInfo = [];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $build = $rs['UF_CS_RS_BUILD'];

        $premisesInfo['floor'] = $premises['UF_CS_P_FLOOR'];
        $premisesInfo['floors-total'] = $build['UF_CS_B_FLOOR_MAX'];

        if ($premises['UF_AIR_CONDITIONING'] == '3' || empty($premises['UF_AIR_CONDITIONING'])) {
            $premisesInfo['air-conditioner'] = 'false';
        } else {
            $premisesInfo['air-conditioner'] = 'true';
        }
        if ($premises['UF_VENTILATION_TYPE'] == '3' || empty($premises['UF_VENTILATION_TYPE'])) {
            $premisesInfo['ventilation'] = 'false';
        } else {
            $premisesInfo['ventilation'] = 'true';
        }
        if ($premises['UF_EXTINGUISHING_SYSTEM_TYPE'] == '6' || empty($premises['UF_EXTINGUISHING_SYSTEM_TYPE'])) {
            $premisesInfo['fire-alarm'] = 'false';
        } else {
            $premisesInfo['fire-alarm'] = 'true';
        }
        if ($build['UF_CS_B_HEATING_TYPE'] == '1' || empty($build['UF_CS_B_HEATING_TYPE'])) {
            $premisesInfo['heating-supply'] = 'false';
        } else {
            $premisesInfo['heating-supply'] = 'true';
        }
        return array_filter($premisesInfo);
    }
    private static function buildInfo($rs)
    {
        $buildInfo = [];
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $buildInfo['office-class'] = self::$objectClass[$premises['UF_CLASS']];
        return array_filter($buildInfo);
    }
}