<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/Services/HlService.php");

class UserField
{
    public $id;
    public $name;
    public $type;
    public $xml;
    public $code;
    public $multiple;
    public $fields;

    public function __construct($args)
    {
        $HlFieldType = new \Cassoft\Services\HlService('crm_userfield_types');

        $this->id = $args['ID'];
        $this->name = $args['UF_NAME'];
        $this->type = 'string';
        $this->code = $args['UF_USER_CODE'];
        $this->xml = $args['UF_USER_CODE'];
        $this->multiple = $this->composeBoolenCode($args['UF_MULTIPLE_DEAL']);
        $this->composeFields();
    }

    private function composeBoolenCode($code)
    {
        return ($code == '0') ? 'N' : 'Y';
    }


    private function composeFields()
    {
        $this->fields['FIELD_NAME'] = $this->code;
        $this->fields['XML_ID'] = $this->xml;
        //$this->fields['LABEL'] = $this->name;
        $this->fields['HELP_MESSAGE'] = $this->name;
        $this->fields['EDIT_FORM_LABEL'] = $this->name;
        $this->fields['LIST_COLUMN_LABEL'] = $this->name;
        $this->fields['LIST_FILTER_LABEL'] = $this->name;
        $this->fields['ERROR_MESSAGE'] = $this->name;
        $this->fields['USER_TYPE_ID'] = $this->type;
        $this->fields['MULTIPLE'] = $this->multiple;
        $this->fields['SHOW_FILTER'] = 'Y';
        $this->fields['SORT'] = '100';
        //$this->fields['EDIT_IN_LIST'] = $this->statusEdit;
        //$this->fields['SHOW_IN_LIST'] = $this->statusListView;
    }
}
