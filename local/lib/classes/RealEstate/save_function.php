<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/var/www/www-root/data/www/brokci.cassoft.ru";
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

function savePlot($hlRs, $hlPlot, $resRs, $plot, $fullAddressString)
{
    if ($resRs) {
        $response['status'] = "Объект недвижимости обновлен";

        // Собраем данные о здании и помещении
        $rsId = $resRs['ID'];


        if ($plot) {
            // Записываем участок
            if (!empty($resRs['UF_CS_RS_PLOT_ID'])) {
                $resPlot = getOneElem(
                    $hlPlot,
                    ['ID' => $resRs['UF_CS_RS_PLOT_ID']]
                );

                $plotId = $resPlot['ID'];
                $plot['UF_CS_PL_ADDRESS'] = $fullAddressString;
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

        // Если нет то проверям если такой участок в базе

        $resPlot = [];

        $resPlot = getOneElem(
            $hlPlot,
            ['UF_CS_PL_ADDRESS' => $fullAddressString]
        );
        if ($resPlot) {

            // участок
            $plot['UF_CS_PL_ADDRESS'] = $fullAddressString;

            $writePlot = $hlPlot::update($plotId, $plot);
            if (!$writePlot->isSuccess()) {
                $errors[] = $writePlot->getErrorMessages();
            } else {
                $plotIdNew = $writePlot->getId();
            }

            $response['plot']['oldId'] = $plotId;
            $response['plot']['newId'] = $plotIdNew;


            $realEstate['UF_CS_RS_ADDRESS_FULL'] = $fullAddressString;
            $realEstate['UF_CS_RS_BUILD'] = '';
            $realEstate['UF_CS_RS_PREMISES_TYPE'] = '6';
            $realEstate['UF_CS_RS_PREMISES_ID'] = '';
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



            $plot['UF_CS_PL_ADDRESS'] = $fullAddressString;

            $writePlot = $hlPlot::add($plot);
            if (!$writePlot->isSuccess()) {
                $errors[] = $writePlot->getErrorMessages();
            } else {
                $plotIdNew = $writePlot->getId();
            }

            $response['plot']['oldId'] = $plotId;
            $response['plot']['newId'] = $plotIdNew;


            $realEstate['UF_CS_RS_ADDRESS_FULL'] = $fullAddressString;
            $realEstate['UF_CS_RS_BUILD'] = '';
            $realEstate['UF_CS_RS_PREMISES_TYPE'] = '6';
            $realEstate['UF_CS_RS_PREMISES_ID'] = '';
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
    return $response;
}
