<?php

namespace Cassoft\Adver;

class Yandex
{
    public $data;
    private $livingPremisesIds = [1, 2, 3, 4, 5, 6];

    function __construct(array $data)
    {
        $this->data = $data;
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

    public function validate($rs)
    {
        $result = [];
        if ($rs['CATEGORY_CODE'] === 'sell' || $rs['CATEGORY_CODE'] === 'rent' || $rs['CATEGORY_CODE'] === 'new') {
            if (in_array($rs['UF_CS_RS_PREMISES_TYPE']['ID'], $this->livingPremisesIds)) {
                $general = YandexLiving::validate($rs);
            } else {
                $general = YandexCommercial::validate($rs);
            }
        }
        $result = array_merge($result, $general);
        return $result;
    }

    public function ucfirst_utf8($str)
    {
        return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr(mb_strtolower($str, 'utf-8'), 1, mb_strlen($str) - 1, 'utf-8');
    }
}