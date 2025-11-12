<?php

namespace Cassoft\Adver;

class DoubleGis
{
    private $data;
    public $categories;
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->getPremisesTypeId();
    }
    private function getPremisesTypeId()
    {
        $hl =  new \Cassoft\Services\HlService('premises_type');
        $this->categories = $hl->makeIdToField('UF_CS_D_NAME');
        return $this;
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
        $ad = [];
        $ad['id'] = $rs['ID'];
        if ($rs['UF_CS_RS_PREMISES_TYPE']['ID'] == 6) {
            $ad['name'] = $rs['UF_CS_RS_PLOT_ID']['UF_CS_PL_ADDRESS'];
        } else {
            $ad['name'] = $rs['UF_CS_RS_BUILD']['UF_CS_B_ADDRESS'];
        }
        $ad['price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);
        $ad['categoryId'] = $rs['UF_CS_RS_PREMISES_TYPE']['ID'];
        $ad['picture'] = $rs['PHOTOS']['0'];
        $ad['description'] = strip_tags(htmlspecialchars($rs['DESCRIPTION']));
        return $ad;
    }
}