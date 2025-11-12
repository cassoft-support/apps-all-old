<?php

namespace Cassoft\Adver;


class Cian
{
    public $data;
    public $windowDir = [
        '1' => 'yard',
        '2' => 'street',
        '3' => 'yardAndStreet',
    ];
    public $renovationGloss = [
        '1' => 'no',
        '2' => 'no',
        '3' => 'no',
        '4' => 'no',
        '5' => 'design',
        '6' => 'euro',
        '7' => 'cosmetic',
    ];
    public $conditionTypeOffice = [
        '1' => 'majorRepairsRequired',
        '2' => 'finishing',
        '3' => 'cosmeticRepairsRequired',
        '4' => 'cosmeticRepairsRequired',
        '5' => 'office',
        '6' => 'office',
        '7' => 'office',
    ];
    public $conditionTypeIndustry = [
        '1' => 'majorRepairsRequired',
        '2' => 'finishing',
        '3' => 'cosmeticRepairsRequired',
        '4' => 'cosmeticRepairsRequired',
        '5' => 'typical',
        '6' => 'typical',
        '7' => 'typical',
    ];
    public $conditionTypeShop = [
        '1' => 'majorRepairsRequired',
        '2' => 'finishing',
        '3' => 'cosmeticRepairsRequired',
        '4' => 'cosmeticRepairsRequired',
        '5' => 'design',
        '6' => 'design',
        '7' => 'typical',
    ];

    public $materialWallGloss = [
        '1' => 'brick',
        '2' => 'panel',
        '3' => 'block',
        '4' => 'monolithBrick',
        '5' => 'wood',
        '6' => 'monolith',
        '7' => 'old',
        '8' => 'block',
        '9' => 'monolith',
        '10' => 'monolith',
        '11' => 'panel',
        '12' => 'panel',
        '13' => 'panel',
        '14' => 'block',
        '15' => 'panel',
    ];
    public $heatingTypeGloss = [
        '1' => 'no',
        '2' => 'centralCoal',
        '3' => 'centralGas',
        '4' => 'autonomousGas',
        '5' => 'solidFuelBoiler',
        '6' => 'stove',
        '7' => 'solidFuelBoiler',
    ];
    public $heatingTypeCommercial = [
        '1' => 'no',
        '2' => 'central',
        '3' => 'central',
        '4' => 'autonomous',
        '5' => 'autonomous',
        '6' => 'autonomous',
        '7' => 'autonomous',
    ];
    public $commercialBuildingType;
    public $ventilationType;
    public $conditioningType;
    public $extinguishingSystemType;
    public $objectClass;
    public $plotType;

    function __construct(array $data)
    {
        $this->data = $data;
        $this->commercialBuildingType = $this->getVocabulary('commercial_building_type');
        $this->ventilationType = $this->getVocabulary('ventilation_type');
        $this->conditioningType = $this->getVocabulary('conditioning_type');
        $this->extinguishingSystemType = $this->getVocabulary('extinguishing_system_type');
        $this->objectClass = $this->getVocabulary('object_class');
        $this->plotType = $this->getVocabulary('plot_type');
    }

    public function makeFeed()
    {
        $result = [];
        foreach ($this->data as $rs) {
            $ad = $this->validate($rs);
            if ($ad !== false) {
                $result[] = $ad;
            }
        }
        return $result;
    }

    private function validate($rs)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $result = $this->generalRequirements($rs);
        $category = $this->category($rs);
        if ($rs['CATEGORY_CODE'] == 'rent') {
            $rentProperties = $this->rentProperties($rs);
            $result = array_merge($result, $rentProperties);
        }
        switch ($category) {
            case 'flatSale':
            case 'flatRent':
            case 'newBuildingFlatSale':
                $premises = $this->flatSale($rs, $category);
                break;
            case 'roomSale':
            case 'roomRent':
                $premises = $this->roomSale($rs, $category);
                break;
            case 'houseSale':
            case 'houseRent':
                $premises = $this->houseSale($rs, $category);
                break;
            case 'cottageSale':
            case 'cottageRent':
                $premises = $this->cottageSale($rs, $category);
                break;
            case 'townhouseSale':
            case 'townhouseRent':
                $premises = $this->townhouseSale($rs, $category);
                break;
            case 'landSale':
                $premises = $this->landSale($rs, $category);
                break;
            case 'officeSale':
            case 'officeRent':
                $premises = $this->officeSale($rs, $category);
                break;
            case 'industrySale':
            case 'industryRent':
                $premises = $this->industrySale($rs, $category);
                break;
            case 'warehouseSale':
            case 'warehouseRent':
                $premises = $this->warehouseSale($rs, $category);
                break;
            case 'shoppingAreaSale':
            case 'shoppingAreaRent':
                $premises = $this->shoppingAreaSale($rs, $category);
                break;
            default:
                $premises = false;
        }

        if ($premises !== false) {
            $result = array_merge($result, $premises);
        } else {
            $result = false;
        }
        return $result;
    }
    private function category($rs)
    {
        switch ($rs['UF_CS_RS_PREMISES_TYPE']['UF_CS_D_TABLE_NAME']) {
            case 'flats':
                $rsType = 'flat';
                break;
            case 'rooms':
                $rsType = 'room';
                break;
            case 'houses':
                if (!empty($rs['UF_CS_RS_BUILD']['UF_CS_B_BUILT_YEAR']) && $rs['UF_CS_RS_BUILD']['UF_CS_B_BUILT_YEAR'] >= 1970) {
                    $rsType = 'cottage';
                } else {
                    $rsType = 'house';
                }
                break;
            case 'townhouses':
                $rsType = 'townhouse';
                break;
            case 'dachas':
                $rsType = 'house';
                break;
            case 'plots':
                $rsType = 'land';
                break;
            case 'offices':
                $rsType = 'office';
                break;
            case 'warehouses':
                $rsType = 'warehouse';
                break;
            case 'shops':
                $rsType = 'shoppingArea';
                break;
            case 'industry':
                $rsType = 'industry';
                break;
        }

        switch ($rs['CATEGORY_CODE']) {
            case 'sell':
                $type = 'Sale';
                break;
            case 'rent':
                $type = 'Rent';
                break;
            case 'new':
                $rsType = 'newBuildingFlat';
                $type = 'Sale';
                break;
        }
        return "{$rsType}{$type}";
    }
    private function landSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        $result = [];

        $result['Category'] = $category;
        $land['Area'] = (float)$plot['UF_CS_PS_PLOT_AREA'] / 100;
        $land['AreaUnitType'] = 'sotka';
        $land['Status'] = $this->plotType[$plot['UF_CS_PL_PLOT_TYPE']];

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['VatType'] = 'included';

        $result['BargainTerms'] = array_filter($terms);
        $result['Land'] = array_filter($land);

        $result = array_filter($result);
        return $result;
    }
    private function officeSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];

        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['ConditionType'] = $this->conditionTypeOffice[$premises['UF_CS_P_RENOVATION']];

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['Type'] = $this->commercialBuildingType[$premises['UF_BUILDING_TYPE']];
        $building['VentilationType'] = $this->ventilationType[$premises['UF_VENTILATION_TYPE']];
        $building['ConditioningType'] = $this->conditioningType[$premises['UF_AIR_CONDITIONING']];
        $building['ExtinguishingSystemType'] = $this->extinguishingSystemType[$premises['UF_EXTINGUISHING_SYSTEM_TYPE']];
        $building['ClassType'] = $this->objectClass[$premises['UF_CLASS']];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeCommercial[$build['UF_CS_B_HEATING_TYPE']];

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['VatType'] = 'included';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }
    private function industrySale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];

        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['ConditionType'] = $this->conditionTypeIndustry[$premises['UF_CS_P_RENOVATION']];

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['Type'] = $this->commercialBuildingType[$premises['UF_BUILDING_TYPE']];
        $building['VentilationType'] = $this->ventilationType[$premises['UF_VENTILATION_TYPE']];
        $building['ConditioningType'] = $this->conditioningType[$premises['UF_AIR_CONDITIONING']];
        $building['ExtinguishingSystemType'] = $this->extinguishingSystemType[$premises['UF_EXTINGUISHING_SYSTEM_TYPE']];
        $building['ClassType'] = $this->objectClass[$premises['UF_CLASS']];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeCommercial[$build['UF_CS_B_HEATING_TYPE']];

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['VatType'] = 'included';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }
    private function warehouseSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];

        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['ConditionType'] = $this->conditionTypeIndustry[$premises['UF_CS_P_RENOVATION']];

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['Type'] = $this->commercialBuildingType[$premises['UF_BUILDING_TYPE']];
        $building['VentilationType'] = $this->ventilationType[$premises['UF_VENTILATION_TYPE']];
        $building['ConditioningType'] = $this->conditioningType[$premises['UF_AIR_CONDITIONING']];
        $building['ExtinguishingSystemType'] = $this->extinguishingSystemType[$premises['UF_EXTINGUISHING_SYSTEM_TYPE']];
        $building['ClassType'] = $this->objectClass[$premises['UF_CLASS']];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeCommercial[$build['UF_CS_B_HEATING_TYPE']];

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['VatType'] = 'included';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }
    private function shoppingAreaSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];

        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['ConditionType'] = $this->conditionTypeShop[$premises['UF_CS_P_RENOVATION']];

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['Type'] = $this->commercialBuildingType[$premises['UF_BUILDING_TYPE']];
        $building['VentilationType'] = $this->ventilationType[$premises['UF_VENTILATION_TYPE']];
        $building['ConditioningType'] = $this->conditioningType[$premises['UF_AIR_CONDITIONING']];
        $building['ExtinguishingSystemType'] = $this->extinguishingSystemType[$premises['UF_EXTINGUISHING_SYSTEM_TYPE']];
        $building['ClassType'] = $this->objectClass[$premises['UF_CLASS']];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeGloss[$build['UF_CS_B_HEATING_TYPE']];

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['VatType'] = 'included';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }
    private function flatSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];
        $result['Category'] = $category;

        if ($premises['UF_CS_P_ROOMS'] > 5) {
            $result['FlatRoomsCount'] = 6;
        } else {
            $result['FlatRoomsCount'] = $premises['UF_CS_P_ROOMS'];
        }

        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['LivingArea'] = $premises['UF_CS_P_LIVING_AREA'];
        $result['KitchenArea'] = $premises['UF_CS_P_KITCHEN_AREA'];
        $result['WindowsViewType'] = $this->windowDir[$premises['UF_CS_P_WINDOW_DIRECTION']];
        $result['RepairType'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['BuildYear'] = $build['UF_CS_B_BUILT_YEAR'];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['CeilingHeight'] = $premises['UF_CS_P_HEIGHT'];
        $result['JKSchema']['Id'] = $build['UF_CS_CIAN_GK_ID'];
        $result['JKSchema']['House']['Id'] = $build['UF_CS_CIAN_BUILD_ID'];
        if ($build['UF_CS_B_GARBAGE_CHUTE_AMOUNT'] > 0) {
            $building['HasGarbageChute'] = 'true';
        } else {
            $building['HasGarbageChute'] = 'false';
        }

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['SaleType'] = 'free';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }

    private function roomSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $result = [];
        $result['Category'] = $category;
        $result['RoomsForSaleCount'] = 1;

        if ($premises['UF_CS_P_ROOMS'] > 5) {
            $result['FlatRoomsCount'] = 6;
        } else {
            $result['FlatRoomsCount'] = $premises['UF_CS_P_ROOMS'];
        }

        $result['RoomsCount'] = $premises['UF_CS_P_ROOMS'];
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['RoomArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['FloorNumber'] = $premises['UF_CS_P_FLOOR'];
        $result['LivingArea'] = $premises['UF_CS_P_LIVING_AREA'];
        $result['KitchenArea'] = $premises['UF_CS_P_KITCHEN_AREA'];
        $result['WindowsViewType'] = $this->windowDir[$premises['UF_CS_P_WINDOW_DIRECTION']];
        $result['RepairType'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['BuildYear'] = $build['UF_CS_B_BUILT_YEAR'];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['CeilingHeight'] = $premises['UF_CS_P_HEIGHT'];

        if ($build['UF_CS_B_GARBAGE_CHUTE_AMOUNT'] > 0) {
            $building['HasGarbageChute'] = 'true';
        } else {
            $building['HasGarbageChute'] = 'false';
        }

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $terms['SaleType'] = 'free';

        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result = array_filter($result);
        return $result;
    }
    private function rentProperties($rs)
    {
        $result = [];
        if ($rs['UF_CRM_CS_DEAL_HAS_INTERNET'] == '1') {
            $result['HasInternet'] = 'true';
        } else {
            $result['HasInternet'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_FURNITURE'] == '1') {
            $result['HasFurniture'] = 'true';
        } else {
            $result['HasFurniture'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_PHONE'] == '1') {
            $result['HasPhone'] = 'true';
        } else {
            $result['HasPhone'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_KITCHEN_FURNITURE'] == '1') {
            $result['HasKitchenFurniture'] = 'true';
        } else {
            $result['HasKitchenFurniture'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_TV'] == '1') {
            $result['HasTv'] = 'true';
        } else {
            $result['HasTv'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_WASHER'] == '1') {
            $result['HasWasher'] = 'true';
        } else {
            $result['HasWasher'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_CONDITIONER'] == '1') {
            $result['HasConditioner'] = 'true';
        } else {
            $result['HasConditioner'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_RAMP'] == '1') {
            $result['HasRamp'] = 'true';
        } else {
            $result['HasRamp'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_BATHTUB'] == '1') {
            $result['HasBathtub'] = 'true';
        } else {
            $result['HasBathtub'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_IS_IN_HIDDEN_BASE'] == '1') {
            $result['IsInHiddenBase'] = 'true';
        } else {
            $result['IsInHiddenBase'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_SHOWER'] == '1') {
            $result['HasShower'] = 'true';
        } else {
            $result['HasShower'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_DISHWASHER'] == '1') {
            $result['HasDishwasher'] = 'true';
        } else {
            $result['HasDishwasher'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_PETS_ALLOWER'] == '1') {
            $result['PetsAllowed'] = 'true';
        } else {
            $result['PetsAllowed'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_FRIDGE'] == '1') {
            $result['HasFridge'] = 'true';
        } else {
            $result['HasFridge'] = 'false';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_CHILDREN_ALLOWED'] == '1') {
            $result['ChildrenAllowed'] = 'true';
        } else {
            $result['ChildrenAllowed'] = 'false';
        }
        return $result;
    }
    private function houseSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        $result = [];
        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['RepairType'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $result['HasSecurity'] = $premises['UF_CS_B_SECURITY'];

        if (!empty($build['UF_CS_B_ELECTRICAL_TYPE']) && $build['UF_CS_B_ELECTRICAL_TYPE'] != 1) {
            $result['HasElectricity'] = 'true';
        } else {
            $result['HasElectricity'] = 'false';
        }

        if (!empty($build['UF_CS_B_COLD_WATER_TYPE']) && $build['UF_CS_B_COLD_WATER_TYPE'] != 1) {
            $result['HasWater'] = 'true';
        } else {
            $result['HasWater'] = 'false';
        }

        if (!empty($build['UF_CS_B_GAS_TYPE']) && $build['UF_CS_B_GAS_TYPE'] != 1) {
            $result['HasGas'] = 'true';
        } else {
            $result['HasGas'] = 'false';
        }

        if (!empty($build['UF_CS_B_SEWERAGE_TYPE']) && $build['UF_CS_B_SEWERAGE_TYPE'] != 1) {
            $result['HasDrainage'] = 'true';
        } else {
            $result['HasDrainage'] = 'false';
        }

        if ($premises['UF_CS_P_ROOMS'] > 5) {
            $result['FlatRoomsCount'] = 6;
        } else {
            $result['FlatRoomsCount'] = $premises['UF_CS_P_ROOMS'];
        }

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['BuildYear'] = $build['UF_CS_B_BUILT_YEAR'];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeGloss[$build['UF_CS_B_HEATING_TYPE']];

        $land['Area'] = (float)$plot['UF_CS_PS_PLOT_AREA'] / 100;
        $land['AreaUnitType'] = 'sotka';

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result['Land'] = array_filter($land);
        $result = array_filter($result);
        return $result;
    }

    private function cottageSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        $result = [];
        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['RepairType'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $result['HasSecurity'] = $premises['UF_CS_B_SECURITY'];

        if (!empty($build['UF_CS_B_ELECTRICAL_TYPE']) && $build['UF_CS_B_ELECTRICAL_TYPE'] != 1) {
            $result['HasElectricity'] = 'true';
        } else {
            $result['HasElectricity'] = 'false';
        }

        if (!empty($build['UF_CS_B_COLD_WATER_TYPE']) && $build['UF_CS_B_COLD_WATER_TYPE'] != 1) {
            $result['HasWater'] = 'true';
        } else {
            $result['HasWater'] = 'false';
        }

        if (!empty($build['UF_CS_B_GAS_TYPE']) && $build['UF_CS_B_GAS_TYPE'] != 1) {
            $result['HasGas'] = 'true';
        } else {
            $result['HasGas'] = 'false';
        }

        if (!empty($build['UF_CS_B_SEWERAGE_TYPE']) && $build['UF_CS_B_SEWERAGE_TYPE'] != 1) {
            $result['HasDrainage'] = 'true';
        } else {
            $result['HasDrainage'] = 'false';
        }

        if ($premises['UF_CS_P_ROOMS'] > 5) {
            $result['FlatRoomsCount'] = 6;
        } else {
            $result['FlatRoomsCount'] = $premises['UF_CS_P_ROOMS'];
        }

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['BuildYear'] = $build['UF_CS_B_BUILT_YEAR'];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeGloss[$build['UF_CS_B_HEATING_TYPE']];

        $land['Area'] = $plot['UF_CS_PS_PLOT_AREA'] / 100;
        $land['AreaUnitType'] = 'sotka';

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result['Land'] = array_filter($land);
        $result = array_filter($result);
        return $result;
    }

    private function townhouseSale($rs, $category)
    {
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        $result = [];
        $result['Category'] = $category;
        $result['TotalArea'] = $premises['UF_CS_P_TOTAL_AREA'];
        $result['RepairType'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        $result['HasSecurity'] = $premises['UF_CS_B_SECURITY'];

        if (!empty($build['UF_CS_B_ELECTRICAL_TYPE']) && $build['UF_CS_B_ELECTRICAL_TYPE'] != 1) {
            $result['HasElectricity'] = 'true';
        } else {
            $result['HasElectricity'] = 'false';
        }

        if (!empty($build['UF_CS_B_COLD_WATER_TYPE']) && $build['UF_CS_B_COLD_WATER_TYPE'] != 1) {
            $result['HasWater'] = 'true';
        } else {
            $result['HasWater'] = 'false';
        }

        if (!empty($build['UF_CS_B_GAS_TYPE']) && $build['UF_CS_B_GAS_TYPE'] != 1) {
            $result['HasGas'] = 'true';
        } else {
            $result['HasGas'] = 'false';
        }

        if (!empty($build['UF_CS_B_SEWERAGE_TYPE']) && $build['UF_CS_B_SEWERAGE_TYPE'] != 1) {
            $result['HasDrainage'] = 'true';
        } else {
            $result['HasDrainage'] = 'false';
        }

        if ($premises['UF_CS_P_ROOMS'] > 5) {
            $result['FlatRoomsCount'] = 6;
        } else {
            $result['FlatRoomsCount'] = $premises['UF_CS_P_ROOMS'];
        }

        $building['FloorsCount'] = $build['UF_CS_B_FLOOR_MAX'];
        $building['BuildYear'] = $build['UF_CS_B_BUILT_YEAR'];
        $building['MaterialType'] = $this->materialWallGloss[$build['UF_CS_B_MATERIAL_WALL']];
        $building['HeatingType'] = $this->heatingTypeGloss[$build['UF_CS_B_HEATING_TYPE']];

        $land['Area'] = $plot['UF_CS_PS_PLOT_AREA'] / 100;
        $land['AreaUnitType'] = 'sotka';

        $terms['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $result['BargainTerms'] = array_filter($terms);
        $result['Building'] = array_filter($building);
        $result['Land'] = array_filter($land);
        $result = array_filter($result);
        return $result;
    }

    public function generalRequirements($rs)
    {

        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];
        $result = [];
        $result['ExternalId'] = $rs['ID'];
        if (!empty($rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE'])) {
            $addressMass = explode('|', $rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE']);
            $result['Address'] = $addressMass['0'];
            $coormass = explode(';', $addressMass['1']);
            $coordinates['Lat'] = $coormass['0'];
            $coordinates['Lng'] = $coormass['1'];
        } elseif ($rs['UF_CS_RS_PREMISES_TYPE']['UF_CS_D_TABLE_NAME'] == 'plots') {
            $result['Address'] = $plot['UF_CS_PL_ADDRESS'];
            $coordinates['Lat'] = $plot['UF_CS_PL_LAT'];
            $coordinates['Lng'] = $plot['UF_CS_PL_LONG'];
        } else {
            $result['Address'] = $build['UF_CS_B_ADDRESS'];
            $coordinates['Lat'] = $build['UF_CS_B_LAT'];
            $coordinates['Lng'] = $build['UF_CS_B_LONG'];
        }
        $result['CadastralNumber'] = $premises['UF_CS_P_CADASTRAL_NUMBER'];
        $result['Description'] = $rs['DESCRIPTION'];
        if (!empty($rs['UF_CRM_CS_DEAL_OBJECT_VIDEO'])) {
            $result['Videos']['VideoSchema']['Url'] = $rs['UF_CRM_CS_DEAL_OBJECT_VIDEO'];
        }

        $result['LayoutPhoto'] = $rs['PLANS']['0'];
        if (!empty($rs['promotion'])) {
        }


        if (!empty($rs['ASSIGNED_PHONE'])) {
            $result['Phones']['PhoneSchema'] = [
                'CountryCode' => '+7',
                'Number' => substr(preg_replace("/[^0-9]/", '',  $rs['ASSIGNED_PHONE']), -10),
            ];
        }
        $subAgent['FirstName'] = $this->ucfirst_utf8($rs['ASSIGNED_NAME']);
        $subAgent['LastName'] = $this->ucfirst_utf8($rs['ASSIGNED_LAST_NAME']);
        $subAgent['Email'] = $rs['ASSIGNED_EMAIL'];
        $subAgent['AvatarUrl'] = $rs['ASSIGNED_AVATAR'];
        if (!empty($rs['ASSIGNED_PHONE'])) {
            $phone =  substr(preg_replace("/[^0-9]/", '',  $rs['ASSIGNED_PHONE']), -10);
            $subAgent['Phone'] = "+7{$phone}";
        }
        $result['Photos'] = $this->photo($rs['PHOTOS']);
        $result['SubAgent'] = array_filter($subAgent);
        $result['Coordinates'] = array_filter($coordinates);
        $result = array_filter($result);
        return $result;
    }

    public function ucfirst_utf8($str)
    {
        return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr(mb_strtolower($str, 'utf-8'), 1, mb_strlen($str) - 1, 'utf-8');
    }

    public function getVocabulary($tableName)
    {
        $hl = new \Cassoft\Services\HlService($tableName);
        return $hl->makeIdToField('UF_CIAN_CODE');
    }

    public function photo($photo)
    {
        $result = [];
        if (!empty($photo)) {
            foreach ($photo as $key => $val) {
                //$result[]['PhotoSchema'] = [
                $result[] = [
                    'FullUrl' => $val,
                    'IsDefault' => ($key == 0) ? 'true' : 'false',
                ];
            }
        }
        return $result;
    }
}