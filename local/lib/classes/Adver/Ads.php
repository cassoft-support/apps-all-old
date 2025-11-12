<?php

namespace Cassoft\Adver;


class Ads
{
    public $deals;
    public $objects;

    function __construct($deals, $objects)
    {
        $this->deals = $deals;
        $this->objects = $objects;
    }

    public function getAds()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            $deal['DESCRIPTION'] = "{$deal['DESCRIPTION']} Номер заявки в базе {$deal['COMPANY_NAME']}: {$deal['ID']}. При обращении в команию назовите этот номер сотруднику, это поможет быстрее обработать Ваш запрос.";
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }

    public function getAdsCian()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('cian', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }

    public function getAdsYandex()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('yandex', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }

    public function getAdsAvito()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('avito', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }

    public function getAdsFree()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('free', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }

    public function getAdsDomclick()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('domclick', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }
    public function getDoubleGis()
    {
        $ads = [];
        foreach ($this->deals as $deal) {
            if (!in_array('2gis', $deal['IMPORT_SITES'])) {
                continue;
            }
            $object = $this->objects[$deal['UF_CRM_CS_DEAL_OBJECT_ID']];
            unset($object['ID']);
            $ad = array_merge($deal, $object);
            $ads[] = $ad;
        }
        return $ads;
    }
}