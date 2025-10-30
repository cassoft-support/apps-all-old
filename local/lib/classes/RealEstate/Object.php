<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/var/www/www-root/data/www/brokci.cassoft.ru";
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


require($_SERVER['DOCUMENT_ROOT'] . '/poligon/classes/Hlbk.php');
require($_SERVER['DOCUMENT_ROOT'] . '/poligon/classes/Dadata.php');
require($_SERVER['DOCUMENT_ROOT'] . '/poligon/classes/ReformaGKH.php');
require('functions.php');
include('save_function.php');

use Bitrix\Highloadblock as HL;
use \Cassoft\SelfProg\Hlbk as Hlbk;


class RealEstateObject
{

    public static function saveObject($args)
    {

        $require = self::checkRequireFields($args['premises'], $args['premises_type']);
        if ($require != false) {
            return $require;
        }

        $hlBase = Hlbk::getHlbk('buildings_main');
        $hlRs = Hlbk::getHlbk('real_estates');
        $hlPt = Hlbk::getHlbk('premises_type');
        $hlPlot = Hlbk::getHlbk('plots');
        $hlApiKeys = Hlbk::getHlbk('apy_keys');
        $hlRooms = Hlbk::getHlbk('living_rooms');
        $hlBaths = Hlbk::getHlbk('bathrooms');
        $hlBalkons = Hlbk::getHlbk('balkons');
        $hlBlocks = Hlbk::getHlbk('buildings_block');






        // Берем данные для реформыЖКХ
        $rowApiKeys = getOneElem(
            $hlApiKeys,
            ['UF_NAME' => 'reformaGKH']
        );

        $tokenDadata = '8025a6f35c4076535b48ef6021c562765bcd73f1';
        $reformaObject = new \Cassoft\Api\ReformaGKH($rowApiKeys['UF_LOGIN'], $rowApiKeys['UF_PASSWORD'], $tokenDadata);
        $dadata = new \Cassoft\Api\Dadata($tokenDadata);
        $dadata->init();

        $fullAddress = [];
        if ($args) {

            $address = $args['address'];
            $build = $args['build'];
            $premises = $args['premises'];
            $premisesType = $args['premises_type'];
            $plot = $args['plot'];
            $rooms = $args['rooms'];
            $baths = $args['baths'];

            $hlPremises = Hlbk::getHlbk($premisesType);
            // Здание

            if (empty($args['country'])) {
                $dadataAddress = $dadata->exectSuggest('address', $address);
                $dadataAddress = $dadataAddress['suggestions'];
            } else {
                $dadataAddress['0'] = $args['addressNoRussia'];
            }


            $dadataAddress['0']['data']['house'] = $build['UF_CS_B_HOUSE'];
            $daAdr = $dadataAddress['0'];

            $daAdr['data']['house'] = $build['UF_CS_B_HOUSE'];
            $daAdr['data']['flat'] = $premises['UF_CS_P_NUMBER'];

            $response = [];
            $response['object_name'] = getObjectName($daAdr);

            $addressNoFlat = getAddressNoFlat($daAdr);
            $addressNoFlatString = implode(', ', $addressNoFlat);

            $build['UF_CS_B_FEDERAL_DISTRICT'] = $dadataAddress['0']['data']['federal_district'];
            $build['UF_CS_B_SUBJECT_TYPE'] = $dadataAddress['0']['data']['region_type_full'];
            $build['UF_CS_B_SUBJECT'] = $dadataAddress['0']['data']['region'];
            $build['UF_CS_B_DISTRICT_TYPE'] = $dadataAddress['0']['data']['area_type_full'];
            $build['UF_CS_B_DISTRICT'] = $dadataAddress['0']['data']['area'];
            $build['UF_CS_B_CITY_TYPE'] = $dadataAddress['0']['data']['city_type_full'];
            $build['UF_CS_B_CITY'] = $dadataAddress['0']['data']['city'];
            $build['UF_CS_B_CITY_DISTRICT'] = $dadataAddress['0']['data']['city_area'];
            $build['UF_CS_B_LOCALITY_TYPE'] = $dadataAddress['0']['data']['city_district_type_full'];
            $build['UF_CS_B_LOCALITY'] = $dadataAddress['0']['data']['city_district'];
            $build['UF_CS_B_SETTLEMENT_TYPE'] = $dadataAddress['0']['data']['settlement_type_full'];
            $build['UF_CS_B_SETTLEMENT'] = $dadataAddress['0']['data']['settlement'];
            $build['UF_CS_B_STREET_TYPE'] = $dadataAddress['0']['data']['street_type_full'];
            $build['UF_CS_B_STREET'] = $dadataAddress['0']['data']['street'];
            $build['UF_CS_B_HOUSE_TYPE'] = $dadataAddress['0']['data']['house_type_full'];
            //$build['UF_CS_B_HOUSE'] = $dadataAddress['0']['data']['house'];
            $build['UF_CS_B_BLOCK_TYPE'] = $dadataAddress['0']['data']['block_type_full'];
            $build['UF_CS_B_BLOCK'] = $dadataAddress['0']['data']['block'];
            $build['UF_CS_B_GUID'] = $dadataAddress['0']['data']['fias_id'];
            $build['UF_CS_B_ADDRESS'] = implode(', ', $fullAddress);
            //$build['UF_CS_B_LAT'] = $dadataAddress['0']['data']['geo_lat'];
            //$build['UF_CS_B_LONG'] = $dadataAddress['0']['data']['geo_lon'];
            $build['UF_CS_B_ADDRESS'] = $addressNoFlatString;

            if ($premisesType == 'plots') {
                $dadataAddressPlot = $dadata->exectSuggest('address', $address);
                $plot['UF_CS_PL_LAT'] = $dadataAddressPlot['suggestions']['0']['data']['geo_lat'];
                $plot['UF_CS_PL_LONG'] = $dadataAddressPlot['suggestions']['0']['data']['geo_lon'];
                $fullAddress = getFullAddress($dadataAddressPlot['suggestions']['0']);
                $fullAddressString = implode(', ', $fullAddress);
            } else {
                $fullAddress = getFullAddress($daAdr);
                $fullAddressString = implode(', ', $fullAddress);
            }


            // Проверяем есть ли такой ОБ

            $resPt = getOneElem(
                $hlPt,
                ['UF_CS_D_TABLE_NAME' => $args['premises_type']]
            );
            $resRs = getOneElem(
                $hlRs,
                [
                    '=UF_CS_RS_ADDRESS_FULL' => $fullAddressString,
                    'UF_CS_RS_PREMISES_TYPE' => $resPt['ID']
                ]
            );

            if ($premisesType == 'plots') {

                $responsePlot = savePlot($hlRs, $hlPlot, $resRs, $plot, $fullAddressString);
                $responsePlot['dadata'] = $dadataAddressPlot['suggestions'];
                $responsePlot['dadataString'] = $fullAddressString;
                $responsePlot['object_name'] = getObjectName($dadataAddressPlot['suggestions']['0']);
                echo json_encode($responsePlot);
                die;
            }
            if ($resRs) {
                $response['status'] = "Объект недвижимости обновлен";

                // Собраем данные о здании и помещении
                $rsId = $resRs['ID'];

                // Записываем здание
                $resBuild = [];
                $resBuild = getOneElem(
                    $hlBase,
                    ['ID' => $resRs['UF_CS_RS_BUILD']]
                );
                $response['dadata'] = $dadatatest;
                $buildId = $resBuild['ID'];

                $writeBuild = $hlBase::update($buildId, $build);
                if (!$writeBuild->isSuccess()) {
                    $errors[] = $writeBuild->getErrorMessages();
                } else {
                    $buildIdNew = $writeBuild->getId();
                }

                $response['build']['oldId'] = $buildId;
                $response['build']['newId'] = $buildIdNew;

                //Записываем подъезд
                if (!empty($premises['UF_CS_P_ENTRANCE_NUMBER'])) {
                    $resBlock = getOneElem(
                        $hlBlocks,
                        [
                            'UF_CS_BB_BUILDING_ID' => $buildIdNew,
                            'UF_CS_BB_ENTRANCE_NUMBER' => $premises['UF_CS_P_ENTRANCE_NUMBER']
                        ]
                    );
                    if ($resBlock) {
                        $blockId = $resBlock['ID'];
                    } else {
                        $writeBlock = $hlBlocks::add(array(
                            'UF_CS_BB_BUILDING_ID' => $buildIdNew,
                            'UF_CS_BB_ENTRANCE_NUMBER' => $premises['UF_CS_P_ENTRANCE_NUMBER']
                        ));
                        if (!$writeBlock->isSuccess()) {
                            $errors[] = $writeBlock->getErrorMessages();
                        } else {
                            $blockId = $writeBlock->getId();
                        }
                    }
                    $premises['UF_CS_P_ENTRANCE_NUMBER'] = $blockId;
                }

                // Записваем помещение
                $resPremises = getOneElem(
                    $hlPremises,
                    ['ID' => $resRs['UF_CS_RS_PREMISES_ID']]
                );

                $premisesId = $resPremises['ID'];
                $writePremises = $hlPremises::update($premisesId, $premises);
                if (!$writePremises->isSuccess()) {
                    $errors[] = $writePremises->getErrorMessages();
                } else {
                    $premisesIdNew = $writePremises->getId();
                }

                $response['premises']['oldId'] = $premisesId;
                $response['premises']['newId'] = $premisesIdNew;
                if ($plot) {
                    // Записываем участок
                    if (!empty($resRs['UF_CS_RS_PLOT_ID'])) {
                        $resPlot = getOneElem(
                            $hlPlot,
                            ['ID' => $resRs['UF_CS_RS_PLOT_ID']]
                        );

                        $plotId = $resPlot['ID'];

                        $writePlot = $hlPlot::update($plotId, $plot);
                        if (!$writePlot->isSuccess()) {
                            $errors[] = $writePlot->getErrorMessages();
                        } else {
                            $plotIdNew = $writePlot->getId();
                        }

                        $response['plot']['oldId'] = $plotId;
                        $response['plot']['newId'] = $plotIdNew;
                    }
                }


                $response['rs'] = $rsId;
            } else {

                // Если нет то проверям если такое здание в базе

                $resBuild = [];

                $resBuild = getOneElem(
                    $hlBase,
                    ['UF_CS_B_ADDRESS' => $addressNoFlatString]
                );
                if ($resBuild) {
                    $response['status'] = "Объект недвижимости дополнен";
                    // Здание
                    $buildId = $resBuild['ID'];

                    $writeBuild = $hlBase::update($buildId, $build);
                    if (!$writeBuild->isSuccess()) {
                        $errors[] = $writeBuild->getErrorMessages();
                    } else {
                        $buildIdNew = $writeBuild->getId();
                    }

                    $response['build']['oldId'] = $buildId;
                    $response['build']['newId'] = $buildIdNew;

                    // Помешение

                    $writePremises = $hlPremises::add($premises);
                    if (!$writePremises->isSuccess()) {
                        $errors[] = $writePremises->getErrorMessages();
                    } else {
                        $premisesIdNew = $writePremises->getId();
                    }

                    $response['premises']['oldId'] = $premisesId;
                    $response['premises']['newId'] = $premisesIdNew;

                    // участок
                    if ($plot) {
                        $writePlot = $hlPlot::update($plotId, $plot);
                        if (!$writePlot->isSuccess()) {
                            $errors[] = $writePlot->getErrorMessages();
                        } else {
                            $plotIdNew = $writePlot->getId();
                        }

                        $response['plot']['oldId'] = $plotId;
                        $response['plot']['newId'] = $plotIdNew;
                    }

                    $realEstate['UF_CS_RS_ADDRESS_FULL'] = $fullAddressString;
                    $realEstate['UF_CS_RS_BUILD'] = $buildIdNew;
                    $realEstate['UF_CS_RS_PREMISES_TYPE'] = $resPt['ID'];
                    $realEstate['UF_CS_RS_PREMISES_ID'] = $premisesIdNew;
                    $realEstate['UF_CS_RS_PLOT_ID'] = $plotIdNew;

                    $writeRs = $hlRs::add($realEstate);
                    if (!$writeRs->isSuccess()) {
                        $errors[] = $writeRs->getErrorMessages();
                    } else {
                        $rsIdNew = $writeRs->getId();
                    }


                    $response['rs'] = $rsIdNew;
                } else {
                    // Если нет в то создаем все

                    $response['status'] = "Создан объект недвижимости";
                    $result = [];
                    $dadata = new \Cassoft\Api\Dadata($tokenDadata);
                    $dadata->init();
                    $dadataAddressFias = [];
                    $dadataAddressFias = $dadata->dadataToFias($addressNoFlatString);
                    $response['dadataToFias'] = $dadataAddressFias;
                    try {
                        $houseInfo = $reformaObject->getBuildProp($dadataAddressFias);
                    } catch (Exception $e) {
                        $errorMes = $e->getMessage();
                        $errorCode = $e->getCode();
                    }
                    if ($houseInfo) {
                        $build['UF_CS_B_REFORMA_ID'] = $houseInfo['house_id'];
                        $build['UF_CS_B_CADASTRAL_NUMBER'] = $houseInfo['house_profile_data']['cadastral_numbers']['0']['cadastral_number'];
                        $build['UF_CS_B_FLOOR_MIN'] = $houseInfo['house_profile_data']['floor_count_min'];
                        $build['UF_CS_B_FLOOR_MAX'] = $houseInfo['house_profile_data']['floor_count_max'];
                        $build['UF_CS_B_MATERIAL_WALL'] = $houseInfo['house_profile_data']['wall_material'];
                        $build['UF_CS_B_BUILT_YEAR'] = $houseInfo['house_profile_data']['built_year'];
                        $build['UF_CS_B_EXPLOITATION_START_YEAR'] = $houseInfo['house_profile_data']['exploitation_start_year'];
                        $build['UF_CS_B_HEATING_TYPE'] = $houseInfo['house_profile_data']['heating_type'];
                        $build['UF_CS_B_HOT_WATER_TYPE'] = $houseInfo['house_profile_data']['hot_water_type'];
                        $build['UF_CS_B_COLD_WATER_TYPE'] = $houseInfo['house_profile_data']['cold_water_type'];
                        $build['UF_CS_B_GAS_TYPE'] = $houseInfo['house_profile_data']['gas_type'];
                        $build['UF_CS_B_ELECTRICAL_TYPE'] = $houseInfo['house_profile_data']['electrical_type'];
                        $build['UF_CS_B_SEWERAGE_TYPE'] = $houseInfo['house_profile_data']['sewerage_type'];
                        $build['UF_CS_B_GARBAGE_CHUTE_TYPE'] = $houseInfo['house_profile_data']['chute_type'];
                        $build['UF_CS_B_GARBAGE_CHUTE_AMOUNT'] = $houseInfo['house_profile_data']['chute_count'];
                        $build['UF_CS_B_PREMISES_AMOUNT'] = $houseInfo['house_profile_data']['flats_count'];
                        $build['UF_CS_B_ENTRANCE_AMOUNT'] = $houseInfo['house_profile_data']['entrance_count'];
                    }
                    $writeBuild = $hlBase::add($build);
                    if (!$writeBuild->isSuccess()) {
                        $errors[] = $writeBuild->getErrorMessages();
                    } else {
                        $buildIdNew = $writeBuild->getId();
                    }

                    $response['build']['oldId'] = $buildId;
                    $response['build']['newId'] = $buildIdNew;

                    // Помешение

                    $writePremises = $hlPremises::add($premises);
                    if (!$writePremises->isSuccess()) {
                        $errors[] = $writePremises->getErrorMessages();
                    } else {
                        $premisesIdNew = $writePremises->getId();
                    }

                    $response['premises']['oldId'] = $premisesId;
                    $response['premises']['newId'] = $premisesIdNew;

                    if ($plot) {
                        $writePlot = $hlPlot::add($plot);
                        if (!$writePlot->isSuccess()) {
                            $errors[] = $writePlot->getErrorMessages();
                        } else {
                            $plotIdNew = $writePlot->getId();
                        }

                        $response['plot']['oldId'] = $plotId;
                        $response['plot']['newId'] = $plotIdNew;
                    }

                    $realEstate['UF_CS_RS_ADDRESS_FULL'] = $fullAddressString;
                    $realEstate['UF_CS_RS_BUILD'] = $buildIdNew;
                    $realEstate['UF_CS_RS_PREMISES_TYPE'] = $resPt['ID'];
                    $realEstate['UF_CS_RS_PREMISES_ID'] = $premisesIdNew;
                    $realEstate['UF_CS_RS_PLOT_ID'] = $plotIdNew;

                    $writeRs = $hlRs::add($realEstate);
                    if (!$writeRs->isSuccess()) {
                        $errors[] = $writeRs->getErrorMessages();
                    } else {
                        $rsIdNew = $writeRs->getId();
                    }

                    $response['rs'] = $rsIdNew;
                }
            }

            $response['code'] = 'success';
        } else {
            $response['status'] = "Техническая ошибка! Уже ведуться работы";
            $response['code'] = 'error';
        }

        return $response;
    }

    public static function checkRequireFields($args, $premisesType)
    {
        if ($premisesType == 'flats' || $premisesType == 'townhouses') {
            $errors = [];
            if (empty($args['UF_CS_P_ROOMS'])) {
                $errors = 'Отуствует значение UF_CS_P_ROOMS';
            }
            if (empty($args['UF_CS_P_NUMBER'])) {
                $errors = 'Отуствует значение UF_CS_P_NUMBER';
            }
            if (empty($args['UF_CS_P_TOTAL_AREA'])) {
                $errors = 'Отуствует значение UF_CS_P_TOTAL_AREA';
            }
            if (empty($args['UF_CS_P_FLOOR'])) {
                $errors = 'Отуствует значение UF_CS_P_FLOOR';
            }
        }
        if ($premisesType == 'rooms') {
            $errors = [];
            if (empty($args['UF_CS_P_NUMBER'])) {
                $errors = 'Отуствует значение UF_CS_P_NUMBER';
            }
            if (empty($args['UF_CS_P_TOTAL_AREA'])) {
                $errors = 'Отуствует значение UF_CS_P_TOTAL_AREA';
            }
            if (empty($args['UF_CS_P_FLOOR'])) {
                $errors = 'Отуствует значение UF_CS_P_FLOOR';
            }
        }
        if ($premisesType == 'houses' || $premisesType == 'dachas') {
            $errors = [];

            if (empty($args['UF_CS_P_TOTAL_AREA'])) {
                $errors = 'Отуствует значение UF_CS_P_TOTAL_AREA';
            }
        }
        if ($premisesType == 'plots') {
            $errors = [];
            if (empty($args['UF_CS_PS_PLOT_AREA'])) {
                $errors = 'Отуствует значение UF_CS_PS_PLOT_AREA';
            }
            if (empty($args['UF_CS_PL_PLOT_TYPE'])) {
                $errors = 'Отуствует значение UF_CS_PL_PLOT_TYPE';
            }
            if (empty($args['UF_CS_PL_PREMITTED_TYPE'])) {
                $errors = 'Отуствует значение UF_CS_PL_PREMITTED_TYPE';
            }
        }

        if (!empty($errors)) {
            $errors['status'] = 'error';
            return $errors;
        } else {
            return false;
        }
    }
}