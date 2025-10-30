<?php
namespace CSlibs\B24\UserFields;
class UserFields
{
    private $auth;
    private $installApp;


    public function __construct($auth, $app)
    {
        $this->auth = $auth;
        $HlPropertyType = new \CSlibs\B24\HL\HlService('app_auth_params');
        $this->installApp = $HlPropertyType->installApp($app);
//        file_put_contents(__DIR__."/logCScore.txt", print_r("core", true));
//        file_put_contents(__DIR__."/logCScore.txt", print_r($core, true), FILE_APPEND);

    }

    public function userFieldsTypeAdd()
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logFieldsTypeAdd.txt";
        p("start", "start", $log);

        $add = 0;
        $up = 0;
        $i = 0;
        $userfieldTypeList = $this->auth->CScore->call('userfieldtype.list');
        $userfieldTypeMapCode = array_column($userfieldTypeList,  'USER_TYPE_ID');
        $HlField = new \CSlibs\B24\HL\HlService('install_user_field_type');
        $fieldType = $HlField->getByIdList($this->installApp['UF_USER_FIELD_TYPE']);
        $fieldTypeHLMapCode = array_column($fieldType, 'UF_USER_TYPE_ID');
        $fieldTypeInstall = array_diff($fieldTypeHLMapCode, $userfieldTypeMapCode);

        foreach ($fieldType as $key => $fieldsVal) {
            $i++;
            $fieldsParams = [];
            $options = [];
            foreach ($fieldsVal['UF_OPTIONS'] as $keyOp) {
                $resOptions = explode(":", $keyOp);
                $options[$resOptions[0]] = $resOptions[1];
            }
            $fieldsParams = [
                'USER_TYPE_ID' => $fieldsVal['UF_USER_TYPE_ID'],
                'HANDLER' => $this->installApp['UF_HANDLER'] . $fieldsVal['UF_HANDLER_FILE'],
                'TITLE' => $fieldsVal['UF_TITLE'],
                'OPTIONS' => $options,
                'DESCRIPTION' => $fieldsVal['DESCRIPTION'],
            ];
            p($fieldsParams, "fieldsParams", $log);
            if(in_array($fieldsVal['UF_USER_TYPE_ID'], $fieldTypeInstall)){
                $addUserfield = $this->auth->CScore->call('userfieldtype.add', $fieldsParams);
                $add++;
                p($addUserfield, "addUserfield", $log);
            }else{
                $updateUserfield = $this->auth->CScore->call('userfieldtype.update', $fieldsParams);
                $up++;
                p($updateUserfield, "updateUserfield", $log);
            }
            }
        $result['fieldType'] = 'success';
        $result['fieldTypeAdd'] = $add;
        $result['fieldTypeUp'] = $up;
        return $result;
    }

    function fieldsAdd($entity)
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logFieldsAdd-".$entity.".txt";
        //  $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/brokci/install/logSmartStage".$entityTypeId."-".$idCategory.".txt";
        p("start", "idCategory", $log);
        p($entity, "entity", $log);

        $y = 0;
        $up = 0;
        $i = 0;
        if($entity === 'user'){
            $list = 'user.userfield.list';
            $update = 'user.userfield.update';
            $add = 'user.userfield.add';
            $table='install_fields_user';
            $fieldName = 'UF_USR_';
        }else{
        $list = 'crm.' . $entity . '.userfield.list';
        $update = 'crm.' . $entity . '.userfield.update';
        $add = 'crm.' . $entity . '.userfield.add';
        $table='install_fields_crm';
            $fieldName = 'UF_CRM_';
        }
        p($list, "list", $log);
        foreach($this->auth->batch->getTraversableList($list, [], [], ['*'], 6000) as $arFields) {
            // d($arFields);
//    if($arFields['XML_ID']){
//        $userfieldMapCode[]=$arFields['XML_ID'];
//    }
            p($arFields, "arFields", $log);
            if($arFields['FIELD_NAME']){
                $userfieldMapCode[str_replace($fieldName, '', $arFields['FIELD_NAME'])]=$arFields['ID'];
              //  $userfieldMapCode[$arFields['FIELD_NAME']]=$arFields['ID'];
                p($userfieldMapCode, "userfieldMapCode", $log);
            }
        }

        $HlField = new \CSlibs\B24\HL\HlService($table);
        $fields = $HlField->getByIdList($this->installApp['UF_FIELDS_'.mb_strtoupper($entity)]);
        p($fields, "fields", $log);
        $fieldHLMapCode = array_column($fields, 'ID','UF_FIELD_NAME');
        if($userfieldMapCode) {
            $fieldTypeInstall = array_diff_key($fieldHLMapCode, $userfieldMapCode);
        }else{
            $fieldTypeInstall = $fieldHLMapCode;
        }
        $idRest=$this->auth->CScore->call("app.info")['ID'];
        p($idRest."\n", "--------------------------------idRest", $log);
         p($fieldTypeInstall, "fieldTypeInstall", $log);
        $HlTypeFields = new \CSlibs\B24\HL\HlService('crm_userfield_types');
        $typeFields = $HlTypeFields->makeFieldToFieldRest("ID", "UF_CODE", $idRest);
        p($typeFields, "typeFields", $log);
        $Y=[
            '0'=>'N',
            '1' => 'Y'
        ];
//d($typeFields);
        foreach ($fields as $field) {
            $fieldsParams=[];
            $settings=[];
            if($field["UF_SETTINGS"]){
                foreach ($field["UF_SETTINGS"] as $set){
                    $res =  explode("|", $set);
                    $settings[$res[0]]= $res[1];
                }
//d($settings);
            }

            $fieldsParams=[

                    "FIELD_NAME"=> $field["UF_FIELD_NAME"],
                    "EDIT_FORM_LABEL"=> $field["UF_EDIT_FORM_LABEL"],
                    "LIST_COLUMN_LABEL"=>$field["UF_LIST_COLUMN_LABEL"],
                    "LIST_FILTER_LABEL"=>$field["UF_LIST_FILTER_LABEL"],
                    "USER_TYPE_ID"=> $typeFields[$field["UF_CRM_FIELD_TYPE"]],
                    "XML_ID"=> $field["UF_FIELD_NAME"],
                    "MULTIPLE" => $Y[$field["UF_MULTIPLE"]],
                    "MANDATORY" => $Y[$field["UF_MANDATORY"]],
                    "SHOW_FILTER" => $Y[$field["UF_SHOW_FILTER"]],
                    "SHOW_IN_LIST" => $Y[$field["UF_SHOW_IN_LIST"]],
                    "EDIT_IN_LIST" => $Y[$field["UF_EDIT_IN_LIST"]],
                    "IS_SEARCHABLE" => $Y[$field["UF_IS_SEARCHABLE"]],
                    "SETTINGS"=> $settings,
                ];
            p($fieldsParams, "fieldsParams", $log);
            if(in_array($field['ID'], $fieldTypeInstall)){
                $addField = $this->auth->CScore->call($add,['fields' => $fieldsParams,]);
                $i++;
                p($addField, "addField", $log);
            }else{
                $id=$userfieldMapCode[$field["UF_FIELD_NAME"]];
                $updateField = $this->auth->CScore->call($update, ['ID' => $id, 'fields' => $fieldsParams,]);
                p($updateField, "updateField", $log);
                $up++;
            }

        }


        $result['fields-'.$entity] = 'success';
        $result['fieldsAdd-'.$entity] = $i;
        $result['fieldsUp-'.$entity] = $up;
        return $result;



    }

    function userFieldsAdd($params)
    {
        $y = 0;
        $up = 0;
        $i = 0;
        $list = 'user.userfield.list';
        $update = 'user.userfield.update';
        $add = 'user.userfield.add';

        foreach ($params as $key => $fieldsParams) {
            $y++;
            //  d($fieldsParams["fields"]["XML_ID"]);
            $userfieldList = $this->auth->CScore->call($list, ['filter' => ['XML_ID' => $fieldsParams["fields"]["XML_ID"]]])['result']['0'];
            //  d($userfieldList);
            if ($userfieldList['ID']) {
                $updateField = $this->auth->CScore->call($update, ['ID' => $userfieldList['ID'], 'fields' => $fieldsParams,]);
                $up++;
            } else {
                $addField = $this->auth->CScore->call($add, $fieldsParams);
                //   d($fieldsParams);
                //  d($addField);
                $i++;
            }
        }
        $res = "Всего - " . $y . ", обновлено - " . $up . ", создано - " . $i;
        return $res;
    }

    function fieldGuide( $params, $entity1, $entity2)
    {
        $list1 = 'crm.' . $entity1 . '.userfield.list';
        $list2 = 'crm.' . $entity2 . '.userfield.list';
        $qFieldList1 = $this->auth->CScore->call($list1, ['filter' => ['XML_ID' => $params]])['result']['0']['LIST'];
        foreach ($qFieldList1 as $k => $v) {
            $resQ1[$v['VALUE']] = $v['ID'];

        }
        $qFieldList2 = $this->auth->CScore->call($list2, ['filter' => ['XML_ID' => $params]])['result']['0']['LIST'];
        foreach ($qFieldList2 as $k => $v) {
            $resQ2['code'][$v['ID']] = $resQ1[$v['VALUE']];
            $resQ2['value'][$v['ID']] = $v['VALUE'];
        }
        return $resQ2;
    }

    function fieldGuideVal( $params, $entity)
    {
        $list = 'crm.' . $entity . '.userfield.list';
        $qFieldList = $this->auth->CScore->call($list, ['filter' => ['XML_ID' => $params]])['result']['0']['LIST'];
        foreach ($qFieldList as $k => $v) {
            $resQ[$v['ID']] = $v['VALUE'];
        }
        return $resQ;
    }

}
