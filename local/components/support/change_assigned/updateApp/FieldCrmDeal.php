<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/Services/HlService.php");
class FieldCrmDeal
{

    public $id;
    public $type;
    public $code;
    public $multiple;
    public $statusEdit;
    public $statusListView;
    public $values_table;
    public $values;
    public $fields;

    public function __construct($args)
    {
        $HlFieldType = new \Cassoft\Services\HlService('crm_userfield_types');

        $this->id = $args['ID'];
        $this->name = $args['UF_NAME'];
        $this->type = $HlFieldType->getValue($args['UF_CRM_FIELD_TYPE'], 'UF_CODE');
        $this->code = $args['UF_DEAL_CODE'];
        $this->multiple = $this->composeBoolenCode($args['UF_MULTIPLE_DEAL']);
        $this->statusEdit = $this->composeBoolenCode($args['UF_CRM_EDIT']);
        $this->statusListView = $this->composeBoolenCode($args['UF_CRM_LIST']);
        if ($this->type === 'enumeration') {
            $this->values_table = new \Cassoft\Services\HlService($args['UF_VALUES']);
            $this->composeValues();
        }
        $this->composeFields();
    }

    private function composeBoolenCode($code)
    {
        return ($code == '0') ? 'N' : 'Y';
    }

    private function composeValues()
    {
        $values = $this->values_table->getVocabulary();
        $this->values = [];
        $sort = 10;
        foreach ($values as $value) {
            $this->values[] = $this->composeValuesField(
                $value['UF_CS_D_NAME'],
                $sort
            );
            $sort += 10;
        }
    }
    private function composeValuesField($value, $sort, $id = '', $del = '')
    {
        $result = [];
        if ($id !== '') {
            $result['ID'] = $id;
        }
        if ($del !== '') {
            $result['DEL'] = $del;
        }
        $result['VALUE'] = $value;
        $result['SORT'] = $sort;
        return $result;
    }
    public function combineValues($oldValues)
    {
        $oldValuesMap = array_column($oldValues, 'VALUE', 'ID');
        $values = [];
        $counter = 0;
        $sort = 10;
        foreach ($this->values as $newValue) {
            $newValueId = "counter{$counter}";
            $key = array_search($newValue['VALUE'], $oldValuesMap);
            if ($key !== false) {
                $values[$key] = $this->composeValuesField(
                    $newValue['VALUE'],
                    $sort,
                    $key
                );
                unset($oldValuesMap[$key]);
            } else {
                $values[$newValueId] = $this->composeValuesField(
                    $newValue['VALUE'],
                    $sort
                );
            }
            $counter++;
            $sort += 10;
        }
        if (!empty($oldValuesMap)) {
            foreach ($oldValuesMap as $key => $val) {
                $values[$key] = $this->composeValuesField(
                    $val,
                    $sort,
                    $key,
                    'Y'
                );
                $sort += 10;
            }
        }
        $this->values = $values;
        $this->composeFields();
    }
    private function composeFields()
    {
        $this->fields['FIELD_NAME'] = $this->code;
        $this->fields['EDIT_FORM_LABEL'] = $this->name;
        $this->fields['LIST_COLUMN_LABEL'] = $this->name;
        $this->fields['LIST_FILTER_LABEL'] = $this->name;
        $this->fields['ERROR_MESSAGE'] = $this->name;
        $this->fields['USER_TYPE_ID'] = $this->type;
        $this->fields['MULTIPLE'] = $this->multiple;
        $this->fields['FIELD_NAME'] = $this->code;
        $this->fields['EDIT_IN_LIST'] = $this->statusEdit;
        $this->fields['SHOW_IN_LIST'] = $this->statusListView;
        if ($this->type === 'enumeration') {
            $this->fields['LIST'] = $this->values;
            $this->fields['SETTINGS'] = [
                'DISPLAY' => 'CHECKBOX'
            ];
        }
    }
}