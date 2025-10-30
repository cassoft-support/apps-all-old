<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/poligon/classes/Hlbk.php');

function getFullAddress($arr)
{
    $daAdr = $arr['data'];
    $fullAddress = [];
    if ($daAdr['cassoft_country']) {
        $fullAddress['cassoft_country'] = $daAdr['cassoft_country'];
    }
    if ($daAdr['region']) {
        $fullAddress['region'] = "{$daAdr['region_type']} {$daAdr['region']}";
    }

    if ($daAdr['area']) {
        $fullAddress['area'] = "{$daAdr['area_type']} {$daAdr['area']}";
    }
    if ($daAdr['city']) {
        $fullAddress['city'] = "{$daAdr['city_type']} {$daAdr['city']}";
    }
    if ($daAdr['city_area']) {
        $fullAddress['city_area'] = "{$daAdr['city_area']}";
    }
    if ($daAdr['settlement']) {
        $fullAddress['settlement'] = "{$daAdr['settlement_type']} {$daAdr['settlement']}";
    }
    if ($daAdr['street']) {
        $fullAddress['street'] = "{$daAdr['street_type']} {$daAdr['street']}";
    }
    if ($daAdr['house']) {
        $fullAddress['house'] = "{$daAdr['house_type']} {$daAdr['house']}";
    }
    if ($daAdr['block']) {
        $fullAddress['block'] = "{$daAdr['block_type']} {$daAdr['block']}";
    }
    if ($daAdr['flat']) {
        $fullAddress['flat'] = "{$daAdr['flat_type']} {$daAdr['flat']}";
    }
    return $fullAddress;
}

function getAddressNoFlat($arr)
{
    $daAdr = $arr['data'];
    $fullAddress = [];
    if ($daAdr['cassoft_country']) {
        $fullAddress['cassoft_country'] = $daAdr['cassoft_country'];
    }
    if ($daAdr['region']) {
        $fullAddress['region'] = "{$daAdr['region_type']} {$daAdr['region']}";
    }

    if ($daAdr['area']) {
        $fullAddress['area'] = "{$daAdr['area_type']} {$daAdr['area']}";
    }
    if ($daAdr['city']) {
        $fullAddress['city'] = "{$daAdr['city_type']} {$daAdr['city']}";
    }
    if ($daAdr['city_area']) {
        $fullAddress['city_area'] = "{$daAdr['city_area']}";
    }
    if ($daAdr['settlement']) {
        $fullAddress['settlement'] = "{$daAdr['settlement_type']} {$daAdr['settlement']}";
    }
    if ($daAdr['street']) {
        $fullAddress['street'] = "{$daAdr['street_type']} {$daAdr['street']}";
    }
    if ($daAdr['house']) {
        $fullAddress['house'] = "{$daAdr['house_type']} {$daAdr['house']}";
    }
    if ($daAdr['block']) {
        $fullAddress['block'] = "{$daAdr['block_type']} {$daAdr['block']}";
    }
    return $fullAddress;
}
function getOneElem($object, $filter)
{
    $result = $object::getList([
        'select' => [
            '*'
        ],
        'filter' => $filter
    ])->fetch();
    return $result;
}
function getAllElem($object, $filter)
{
    $result = [];
    $res = $object::getList([
        'select' => [
            '*'
        ],
        'filter' => $filter
    ]);
    while ($row = $res->fetch()) {
        $result[] = $row;
    }
    return $result;
}
function getObjectName($arr)
{
    $daAdr = $arr['data'];
    $fullAddress = [];

    if (!$daAdr['city']) {
        if ($daAdr['region']) {
            $fullAddress['region'] = "{$daAdr['region_type']} {$daAdr['region']}";
        }

        if ($daAdr['area']) {
            $fullAddress['area'] = "{$daAdr['area_type']} {$daAdr['area']}";
        }
    }

    if ($daAdr['city']) {
        $fullAddress['city'] = "{$daAdr['city_type']} {$daAdr['city']}";
    }
    if ($daAdr['city_area']) {
        $fullAddress['city_area'] = "{$daAdr['city_area']}";
    }
    if ($daAdr['settlement']) {
        $fullAddress['settlement'] = "{$daAdr['settlement_type']} {$daAdr['settlement']}";
    }
    if ($daAdr['street']) {
        $fullAddress['street'] = "{$daAdr['street_type']} {$daAdr['street']}";
    }
    if ($daAdr['house']) {
        $fullAddress['house'] = "{$daAdr['house_type']} {$daAdr['house']}";
    }
    if ($daAdr['block']) {
        $fullAddress['block'] = "{$daAdr['block_type']} {$daAdr['block']}";
    }
    if ($daAdr['flat']) {
        $fullAddress['flat'] = "{$daAdr['flat_type']} {$daAdr['flat']}";
    }

    $fullAddressString = implode(', ', $fullAddress);
    return $fullAddressString;
}

function areaValidate($area)
{
    if (!empty($area)) {
        return "{$area} кв.м";
    } else {
        return '(Нет данных)';
    }
}
function heightValidate($height)
{
    if (!empty($height)) {
        return "$height м.";
    } else {
        return '(Нет данных)';
    }
}
function getVocabulary($value, $hlCode, $fieldsName = null)
{
    if (empty($fieldsName)) {
        $fieldsName = 'UF_CS_D_NAME';
    }
    if ($value) {
        $hl = \Cassoft\SelfProg\Hlbk::getHlbk($hlCode);
        $res = getOneElem(
            $hl,
            ['ID' => $value]
        );
        if (!empty($res)) {
            return $res[$fieldsName];
        } else {
            return '(Нет данных)';
        }
    } else {
        return '(Нет данных)';
    }
}
