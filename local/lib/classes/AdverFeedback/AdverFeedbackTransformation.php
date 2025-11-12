<?php

namespace Cassoft\AdverFeedback;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::IncludeModule('crm');

class AdverFeedbackTransformation
{

    public $xmlKeyFields;
    public $dealFields;
    public $xmlFields;
    public $avito;
    public $cian;
    public $domclick;
    public $importSiteCode;
    public $stages;
    public $stagesImport;

    public function __construct(array $args, AvitoTransformation $avito, CianTransformation $cian, DomclickTransformation $domclick)
    {
        $this->link = $args['url'];
        $this->importSiteCode = $args['importSiteCode'];
        $this->stages = $args['stages'];
        $this->stagesImport = $args['stagesImport'];
        $this->avito = $avito;
        $this->cian = $cian;
        $this->domclick = $domclick;
    }
    //Ищем среди всех полей в сделке, нужные нам по XML_ID 
    private function fieldsXml()
    {

        $dealFields = $this->dealFields;
        $mass = $this->xmlFields;

        $dealKeys = array_keys($dealFields);
        $dealXML = array_column($dealFields, 'XML_ID');
        $dealFields = array_combine($dealKeys, $dealXML);
        $dealFields = array_flip(array_diff($dealFields, array('')));
        $xmlKeyFields = [];
        foreach ($mass as $value) {
            $xmlKeyFields[$value] = $dealFields[$value];
        }
        $this->xmlKeyFields = $xmlKeyFields;
    }

    public function getDeals($filter = null)
    {
        $arFilterDeal = [];

        $arFilterDeal['CHECK_PERMISSIONS'] = 'N';

        if (!$filter['STAGE_ID']) {
            $arFilterDeal['STAGE_ID'] = array_keys($this->stages);
        }

        if ($filter && !empty($filter)) {
            $arFilterDeal = array_merge($arFilterDeal, $filter);
        }

        $arSelectDeal = [
            'ID',
            'CATEGORY_ID',
            'STAGE_ID',
            'ASSIGNED_BY_ID',   //Ответственный
            'ASSIGNED_BY_NAME',
            'ASSIGNED_BY_LAST_NAME',
            'TITLE',
        ];
        $this->fieldsXml();
        $xmlKeyFields = $this->xmlKeyFields;
        // Скливаем два массива для $arSelect. Первый это массив битровых полей, второй пользовательских
        $arSelectDeal = array_merge($arSelectDeal, $xmlKeyFields);

        $resDeals = \CCrmDeal::GetListEx(array(), $arFilterDeal, false, false, $arSelectDeal);

        $deals = [];
        $xmlKeyFields = array_flip($xmlKeyFields);

        while ($deal = $resDeals->Fetch()) {
            $id = $deal['ID'];
            $dealWithXml = array_merge(array_diff_key($deal, $xmlKeyFields), array_combine($xmlKeyFields, array_intersect_key($deal, $xmlKeyFields)));
            $deals[] = $dealWithXml;
        }
        return $deals;
    }

    private function validateDate($date)
    {
        if (!empty($date)) {
            $timestamp = strtotime($date);
            $newDate = date('d.m.Y', $timestamp);
            return $newDate;
        }
    }

    public function validateAvito($avitoJson)
    {
        $avito = json_decode($avitoJson, true);
        $result = [];
        $result['status'] = $avito['statuses']['general']['help'];
        $result['link'] = $avito['url'];
        $result['dateEnd'] = $this->validateDate($avito['avito_date_end']);
        $tooltip = $this->avito->validateAvitoErrorReport(
            (string) $avito['statuses']['general']['value'],
            (string) $avito['statuses']['avito']['help'],
            (array) $avito['messages']
        );
        $result['text'] = $this->makeTooltipForTippy($tooltip);
        return $result;
    }
    public function avitoNoReport()
    {
        $result = [];
        $result['status'] = 'Разрешено';
        $result['link'] = null;
        $result['dateEnd'] = null;
        $result['text'] = 'В данный момент отчет отсутствует';
        return $result;
    }
    public function avitoNoPublished()
    {
        $result = [];
        $result['status'] = 'Запрещено';
        $result['link'] = null;
        $result['dateEnd'] = null;
        $result['text'] = null;
        return $result;
    }

    public function validateCian($cianJson)
    {
        $cian = json_decode($cianJson, true);
        $result = [];
        $result['status'] = $this->cian->statusTransform((string) $cian['status']);
        $result['link'] = $cian['url'];
        $tooltip = $this->cian->errorsTransform(
            (string) $cian['status'],
            (array) $cian['errors'],
            (array) $cian['warnings']
        );
        $result['text'] = $this->makeTooltipForTippy($tooltip);
        return $result;
    }

    public function cianNoReport()
    {
        $result = [];
        $result['status'] = 'Разрешено';
        $result['link'] = null;
        $result['text'] = 'В данный момент отчет отсутствует';
        return $result;
    }

    public function cianNoPublished()
    {
        $result = [];
        $result['status'] = 'Запрещено';
        $result['link'] = null;
        $result['text'] = null;
        return $result;
    }

    public function validateDomclick($domclickJson)
    {
        $domclick = json_decode($domclickJson, true);
        $result = [];
        $result['status'] = $domclick['Status']['Descr'];
        $result['link'] = $domclick['Publication']['DomclickURL'];
        $result['statusDiscount'] = $domclick['DiscountStatus']['Descr'];
        $tooltip = $this->domclick->validateDomclickErrorReport(
            (string) $domclick['Status']['Code'],
            $domclick['PublishRejectionReasons']
        );

        $tooltipDiscount = $this->domclick->validateDomclickErrorReportDiscount(
            (string) $domclick['Status']['Code'],
            $domclick['DiscountStatus']['RejectionReasons']
        );
        $result['text'] = $this->makeTooltipForTippy($tooltip);
        $result['textDiscount'] = $this->makeTooltipForTippy($tooltipDiscount);
        return $result;
    }

    public function domclickNoReport()
    {
        $result = [];
        $result['status'] = 'Разрешено';
        $result['link'] = null;
        $result['statusDiscount'] = null;
        $result['text'] = 'В данный момент отчет отсутствует';
        $result['textDiscount'] = null;
        return $result;
    }

    public function domclickNoPublished()
    {
        $result = [];
        $result['status'] = 'Запрещено';
        $result['link'] = null;
        $result['statusDiscount'] = null;
        $result['text'] = null;
        $result['textDiscount'] = null;
        return $result;
    }

    public function validateDealId(int $id)
    {
        $dealId = [];
        $dealId['id'] = $id;
        $dealId['link'] = "$this->link/crm/deal/details/$id/";
        return $dealId;
    }

    public function validateAssigned($firstName, $lastName, $assignedId)
    {
        $name = [];
        $name['assignedName'] = "$lastName $firstName";
        $name['id'] = $assignedId;
        return $name;
    }

    public function validatePrice($val, $type = null)
    {
        if (!empty($val)) {
            $price =  preg_replace("/[^0-9]/", '', $val);
            if ($type !== null) {
                if (is_array($type) && !empty($type)) {
                    $price = "$price|{$type['0']}";
                } else {
                    $price = "$price|$type";
                }
            }
        } else {
            $price = '-';
        }
        return $price;
    }

    public function validateStage($stage)
    {
        $result = [];
        $result['text'] = $this->stages[$stage];
        //if($this->stagesImport[$stage]) {
        if (in_array($stage, $this->stagesImport)) {
            $result['value'] = 'stagePub';
        } else {
            $result['value'] = 'stageNoPub';
        }
        return $result;
    }

    public function changeImportSiteCode($importSiteIds)
    {
        $result = [];
        foreach ($importSiteIds as $id) {
            $result[] = $this->importSiteCode[$id];
        }
        return $result;
    }

    private function makeTooltipForTippy(array $value)
    {
        $result = [];
        foreach ($value as $val) {
            $result[] = "<li>{$val}</li>";
        }
        $result = implode('', $result);
        $result = "<ul>{$result}</ul>";
        return $result;
    }
    public function setDealFields(array $val)
    {
        $this->dealFields = $val;
    }

    public function setStages($stages)
    {
        $this->stages = $stages;
    }

    public function setStagesImport($stagesImport)
    {
        $this->stagesImport = $stagesImport;
    }

    public function setXmlFields(array $val)
    {
        $this->xmlFields = $val;
    }
    public function getXmlKeyFields()
    {
        return $this->xmlKeyFields;
    }
}
