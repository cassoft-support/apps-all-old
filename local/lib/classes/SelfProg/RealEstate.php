<?php

namespace Cassoft\SelfProg;

class RealEstate extends Hbk
{


    public static function getList($select, $filter)
    {

        $hlRs = parent::getHlbk('real_estates');
        $hlFlat = parent::getHlbk('flats');
        $hlRoom = parent::getHlbk('rooms');
        $hlHouse = parent::getHlbk('houses');
        $hlTownhouse = parent::getHlbk('townhouses');
        $hlDacha = parent::getHlbk('dachas');

        $selectRs = self::checkSelect($select['rs']);
        $filterRs = self::checkFilter($filter['rs']);
        $selectBuild = self::checkSelect($select['build']);
        $filterBuild = self::checkFilter($filter['build']);
        $selectType = self::checkSelect($select['type']);
        $filterType = self::checkFilter($filter['type']);
        $selectPremises  = self::checkSelect($select['premises']);
        $filterPremises = self::checkFilter($filter['premises']);
        $selectPlot  = self::checkSelect($select['plot']);
        $filterPlot = self::checkFilter($filter['plot']);

        $result = [];
        $rs = [];
        $buildIds = [];
        $premisesTypes = [];
        $resRs = $hlRs::getList([
            'select' => $selectRs,
            'filter' => $filterRs
        ]);
        while ($row = $resRs->fetch()) {
            $premisesTypes[] = $row['UF_CS_RS_PREMISES_TYPE'];
            $buildIds[] = $row['UF_CS_RS_BUILD'];
            $plotIds[] = $row['UF_CS_RS_PLOT_ID'];
            $rsIds[] = $row['ID'];
            $rs[] = $row;
        }

        $filterBuild['ID'] = $buildIds;
        $filterPlot['ID'] = $plotIds;

        $builds = self::getBuild($selectBuild, $filterBuild);
        $plots = self::getPlot($selectPlot, $filterPlot);
        $balkons = self::getBalkons(['UF_CS_PP_REAL_ESTATE_ID' => $rsIds]);
        $premisesType = self::getPremisesType($selectType, $filterType);
        $premises = self::getPremises($premisesTypes, $rs, $selectPremises, $filterPremises);
        foreach ($rs as $row) {
            if (!empty($row['UF_CS_RS_PLOT_ID'])) {
                $thisRsWithPlot = true;
            } else {
                $thisRsWithPlot = false;
            }
            $premisesTypeId = $row['UF_CS_RS_PREMISES_TYPE'];
            $premisesId = $row['UF_CS_RS_PREMISES_ID'];
            $row['UF_CS_RS_PREMISES_ID'] = $premises[$premisesTypeId][$premisesId];
            $row['UF_CS_RS_BUILD'] = $builds[$row['UF_CS_RS_BUILD']];
            $row['UF_CS_RS_PLOT_ID'] = $plots[$row['UF_CS_RS_PLOT_ID']];
            $row['UF_CS_RS_PREMISES_TYPE'] = $premisesType[$row['UF_CS_RS_PREMISES_TYPE']];
            $row['balcony'] = $balkons[$row['ID']];

            if ($thisRsWithPlot === false) {
                if (empty($row['UF_CS_RS_PREMISES_ID']) || empty($row['UF_CS_RS_BUILD'])) {
                    continue;
                }
            }
            if ($thisRsWithPlot === true && empty($row['UF_CS_RS_PLOT_ID'])) {
                continue;
            }
            $result[$row['ID']] = $row;
        }
        return $result;
    }

    public static function getPremises($premisesTypes, $rs, $selectPremises, $filterPremises)
    {
        $hlTypes = parent::getDicId('premises_type');
        $types = array_unique($premisesTypes);
        foreach ($types as $type) {
            $tableName = $hlTypes[$type]['UF_CS_D_TABLE_NAME'];
            $premisesOneType = array_filter($rs, function ($val) use ($type) {
                return in_array($type, $val) !== false;
            });
            $hl = parent::getHlbk($tableName);
            $filterPremises['=ID'] = array_column($premisesOneType, 'UF_CS_RS_PREMISES_ID');
            $premises[$type] = self::getPremisesOneType($hl, $selectPremises, $filterPremises);
        }
        return $premises;
    }
    public static function getPremisesOneType($object, $select, $filter)
    {
        $resPremises = $object::getList([
            'select' => $select,
            'filter' => $filter
        ]);

        while ($row = $resPremises->fetch()) {
            $premises[$row['ID']] = $row;
        }
        return $premises;
    }

    public static function getPremisesType($select, $filter)
    {
        $hlPt = parent::getHlbk('premises_type');
        $premisesType = [];
        $resType = $hlPt::getList([
            'select' => $select,
            'filter' => $filter
        ]);

        while ($row = $resType->fetch()) {
            $premisesType[$row['ID']] = $row;
        }
        return $premisesType;
    }

    public static function getBuild($select, $filter)
    {

        $hlBuild = parent::getHlbk('buildings_main');
        $builds = [];

        $resBuild = $hlBuild::getList([
            'select' => $select,
            'filter' => $filter
        ]);

        while ($row = $resBuild->fetch()) {
            $builds[$row['ID']] = $row;
        }
        return $builds;
    }

    public static function getBalkons($filter)
    {

        $hl = parent::getHlbk('balkons');
        $balkons = [];

        $res = $hl::getList([
            'select' => ['UF_*', 'ID'],
            'filter' => $filter
        ]);

        while ($row = $res->fetch()) {
            $balkons[$row['UF_CS_PP_REAL_ESTATE_ID']] = $row;
        }
        return $balkons;
    }

    public static function getPlot($select, $filter)
    {

        $hlPlot = parent::getHlbk('plots');
        $plots = [];

        $resPlot = $hlPlot::getList([
            'select' => $select,
            'filter' => $filter
        ]);

        while ($row = $resPlot->fetch()) {
            $plots[$row['ID']] = $row;
        }
        return $plots;
    }

    private static function checkSelect($array)
    {
        $result = ['*'];
        if ($array) {
            return $array;
        } else {
            return $result;
        }
    }

    private static function checkFilter($array)
    {
        $result = [];
        if ($array) {
            return $array;
        } else {
            return $result;
        }
    }
}