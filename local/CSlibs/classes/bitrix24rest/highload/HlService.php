<?php

namespace CSlibs\B24\HL;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\UserFieldTable;
Use \CUserTypeEntity;
Loader::includeModule('highloadblock');
Loader::includeModule('main');



class HlService
{
    public $hl;
    public $entity;

    public function __construct(string $tableName)
    {
        $hl = HL\HighloadBlockTable::getList(
            array(
                'filter' => array(
                    'TABLE_NAME' => $tableName,
                )
            )
        )->fetch();

        if (empty($hl['ID']) || $hl['ID'] < 1) {
            return false;
        }

        $hlBlock = HL\HighloadBlockTable::getById($hl['ID'])->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
        $this->entity = $entity;
        $entity_data_class = $entity->getDataClass();
        $this->hl = $entity_data_class;
    }

    public function getVocabulary()
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
        ]);
        $result = [];
        while ($row = $res->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function getById($id)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
            'filter' => ['ID' => $id]
        ])->fetch();
        return $res;
    }
    public function installApp($app)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
            'filter' => ['UF_APP_NAME' => $app]
        ])->fetch();
        return $res;
    }
    public function searchEntity($app)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                'UF_ENTITY'
            ],
            'filter' => ['UF_APP_NAME' => $app]
        ])->fetch();
        return $res['UF_ENTITY'];
    }
    public function searchPatch($memberId)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                'UF_CS_FOLDER',
                'ID'
            ],
            'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId]
        ])->fetch();
        return $res;
    }
    public function searchID($memberId)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                'ID'
            ],
            'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId]
        ])->fetch();
        return $res;
    }
    public function searchAppID($memberId, $app)
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                'ID'
            ],
            'filter' => [
                'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId,
                'UF_APP' => $app,
            ]
        ])->fetch();
        return $res;
    }
    public function entityList($id)
    {
      //  $items = $this->getVocabulary();
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
            'filter' => ['ID' => $id]
        ]);
        $result = [];
        while ($row = $res->fetch()) {
            $result[] = $row;
        }
        return $result;
       // return $items;
    }
    public function getByIdList($id)
    {
      //  $items = $this->getVocabulary();
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
            'filter' => ['ID' => $id]
        ]);
        $result = [];
        while ($row = $res->fetch()) {
            $result[] = $row;
        }
        return $result;
       // return $items;
    }
    public function getByFilterList($filter)
    {
        //  $items = $this->getVocabulary();
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                '*'
            ],
            'filter' => $filter
        ]);
        $result = [];
        while ($row = $res->fetch()) {
            $result[] = $row;
        }
        return $result;
        // return $items;
    }
    public function makeIdToValue($items = null)
    {
        if ($items === null) {
            $items = $this->getVocabulary();
        }
        $result = [];
        foreach ($items as $item) {
            $result[$item['ID']] = $item;
        }
        return $result;
    }
    public function guideIdToName()
    {
         $items = $this->getVocabulary();
        $result = [];
        foreach ($items as $item) {
            $result[$item['ID']] = $item['UF_CS_NAME'];
        }
        return $result;
    }
    public function getValue($itemId, $fieldName = "", $items = null)
    {
        if ($items === null) {
            $items = $this->makeIdToValue();
        }
        if ($fieldName !== '') {
            if (array_key_exists($itemId, $items) && array_key_exists($fieldName, $items[$itemId])) {
                return $items[$itemId][$fieldName];
            } else {
                return null;
            }
        } else {
            if (array_key_exists($itemId, $items)) {
                return $items[$itemId];
            } else {
                return null;
            }
        }
    }
    public function makeIdToField(string $fieldName, $items = null)
    {
        if ($items === null) {
            $items = $this->getVocabulary();
        }
        $result = [];
        foreach ($items as $item) {
            $result[$item['ID']] = $item[$fieldName];
        }
        return $result;
    }
    public function makeFieldToField(string $fieldKey, string $fieldValue, $items = null)
    {
        if ($items === null) {
            $items = $this->getVocabulary();
        }
        $result = [];
        foreach ($items as $item) {
            $result[$item[$fieldKey]] = $item[$fieldValue];
        }
        return $result;
    }
    public function makeFieldToFieldRest(string $fieldKey, string $fieldValue, $appRest)
    {
        $items = $this->getVocabulary();
        $result = [];
        foreach ($items as $item) {
            $resCode= explode('_', $item[$fieldValue])[0];
            if($resCode === 'cs'){
                $result[$item[$fieldKey]] = "rest_".$appRest."_".$item[$fieldValue];
            }else{
                $result[$item[$fieldKey]] = $item[$fieldValue];
            }

        }
        return $result;
    }
    public function makeFieldToValue(string $fieldKey, $items = null)
    {
        if ($items === null) {
            $items = $this->getVocabulary();
        }
        $result = [];
        foreach ($items as $item) {
            $result[$item[$fieldKey]] = $item;
        }
        return $result;
    }
    protected function getErrorMessage($error)
    {
        return $error;
    }

public function hbAdd($name, $tableName, $langRu, $fields){
    $data = [
        'NAME' => $name, // Имя хайблока на английском (системное)
        'TABLE_NAME' => $tableName, // Имя таблицы в базе данных
    ];
// Создание хайблока
//    $result = HL\HighloadBlockTable::add($data);
//    if ($result->isSuccess()) {
//        $highloadBlockId = $result->getId();
//            $resultLang = HighloadBlockLangTable::add([
//                'ID' => $highloadBlockId,
//                'LID' => 'ru',
//                'NAME' => $langRu,
//            ]);
        $highloadBlockId=88;
        $hlblock = HL\HighloadBlockTable::getById($highloadBlockId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
       // $entityDataClass = $entity->getDataClass();

// Добавление поля
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//    \Bitrix\Main\Loader::includeModule('main');
    $userTypeEntity = new \CUserTypeEntity();
        foreach ($fields as $field) {
            $fieldData = [
                'ENTITY_ID' => 'HLBLOCK_' . $highloadBlockId,
                'FIELD_NAME' => $field['FIELD_NAME'],
                'USER_TYPE_ID' => $field['USER_TYPE_ID'],
                'XML_ID' => $field['XML_ID'],
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'Y',
                'EDIT_IN_LIST' => '',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => [
                    'SIZE' => 20,
                    'ROWS' => 1,
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => [
                    'ru' => $field['LABEL'], // Название на русском языке
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => $field['LABEL'],
                ],
            ];
          //  $result = UserFieldTable::add($fieldData);
            $fieldId = $userTypeEntity->Add($fieldData);
        }
//        } else {
//        echo "Ошибка при создании хайблока: " . implode(', ', $result->getErrorMessages());
//    }
   return $highloadBlockId;
}
public function elementAdd($data){

// Добавление записи
    $result = $this->hl::add($data);
    if ($result->isSuccess()) {
       $elId = $result->getId();
//    if ($result->isSuccess()) {
//        echo "Запись успешно добавлена.";
//    } else {
//        echo "Ошибка при добавлении записи: " . implode(', ', $result->getErrorMessages());
   }
    return $elId;
}
public function elementUpdate($id, $data){

// изменение записи
    if (!empty($id) || empty(!$data)) {

        // Изменение записи
        $result = $this->hl::update($id, $data);
        if ($result->isSuccess()) {
            $elId = $result->getId();
        }
    }
    return $elId;
}

    public function getCSCode()
    {
        $hl = $this->hl;
        $res = $hl::getList([
            'select' => [
                'UF_CS_CODE'
            ],
        ]);
        $result = [];
        while ($row = $res->fetch()) {
            $result[] = $row['UF_CS_CODE'];
        }
        return $result;
    }



}
