<?php

namespace CSlibs\B24\Product;

class PropertyCatalog
{
    public $id;
    public $name;
    public $type;
    public $code;
    public $multiple;
    public $xmlId;
    public $values_table;
    public $xmlIdOld;
    public $values;
    public $fields;

    public function __construct($args)
    {
        $HlPropertyType = new \CSlibs\B24\HL\HlService('product_property_type');

        $this->id = $args['ID'];
        $this->name = $args['UF_NAME'];
        $this->type = $HlPropertyType->getValue($args['UF_TYPE'], 'UF_CODE');
        $this->code = $args['UF_CODE'];
        $this->xmlId = $args['UF_XML_ID'];
        $this->multiple = $this->composeMultipleCode($args['UF_MULTIPLE']);
        $this->xmlIdOld = $args['UF_XML_ID_OLD'];
        if ($this->type === 'L') {
            $this->values_table = new \CSlibs\B24\HL\HlService($args['UF_VALUES']);
            $this->composeValues();
        }
        $this->composeFields();
    }

    private function composeValues()
    {
        $values = $this->values_table->getVocabulary();
        $this->values = [];
        foreach ($values as $value) {
            if (!key_exists('UF_CS_CATALOG_CODE', $value)) {
                $value['UF_CS_CATALOG_CODE'] = $value['UF_CS_XML_ID'];
            }
            $this->values[] = $this->composeValuesField(
                $value['UF_CS_D_NAME'],
                $value['UF_CS_CATALOG_CODE']
            );
        }
    }

    private function composeMultipleCode($multipleId)
    {
        return ($multipleId == '0') ? 'N' : 'Y';
    }

    private function composeFields()
    {
        $this->fields['NAME'] = $this->name;
        $this->fields['CODE'] = $this->code;
        $this->fields['PROPERTY_TYPE'] = $this->type;
        $this->fields['MULTIPLE'] = $this->multiple;
        $this->fields['XML_ID'] = $this->xmlId;
        $this->fields['VALUES'] = $this->values;
    }

    public function combineValues($oldValues)
    {
        $oldValuesMap = array_column($oldValues, 'XML_ID', 'ID');
        $values = [];
        $counter = 0;
        foreach ($this->values as $newValue) {
            $key = array_search($newValue['XML_ID'], $oldValuesMap);
            $key = ($key !== false) ? $key : $counter;
            $values[$key] = $this->composeValuesField(
                $newValue['VALUE'],
                $newValue['XML_ID']
            );
            $counter++;
        }
        $this->values = $values;
        $this->composeFields();
    }

    private function composeValuesField($value, $xmlId)
    {
        return [
            'VALUE' => $value,
            'SORT' => 10,
            'DEF' => "N",
            'XML_ID' => $xmlId,
        ];
    }
}