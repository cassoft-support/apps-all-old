<?php

namespace Cassoft\Services;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

Loader::IncludeModule('highloadblock');

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
                'UF_CS_FOLDER'
            ],
            'filter' => ['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId]
        ])->fetch();
        return $res['UF_CS_FOLDER'];
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


}
