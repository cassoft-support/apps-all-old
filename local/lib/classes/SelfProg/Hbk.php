<?php

    namespace Cassoft\SelfProg;

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Highloadblock as HL;
    use Bitrix\Main\Loader;

    Loader::IncludeModule('highloadblock');

    class Hbk
    {

        public static function getDic($tableName)
        {
            $hl = \Cassoft\SelfProg\Hbk::getHlbk($tableName);
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

        public static function getAllElem($object, $filter)
        {
            $result = [];
            $res = $object::getList([
                'select' => [
                    '*'
                ],
                'filter' => $filter
            ]);
            while ($row = $res->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public static function getAllElemId($object, $filter)
        {
            $result = [];
            $res = $object::getList([
                'select' => [
                    '*'
                ],
                'filter' => $filter
            ]);
            while ($row = $res->fetch()) {
                $result = $row["ID"];
            }
            return $result;
        }

        public static function getDicId($tableName)
        {
            $hl = \Cassoft\SelfProg\Hbk::getHlbk($tableName);
            $res = $hl::getList([
                'select' => [
                    '*'
                ],
            ]);
            $result = [];
            while ($row = $res->fetch()) {
                $result[$row['ID']] = $row;
            }
            return $result;
        }

        public static function getDicIdKeyATI($tableName)
        {
            $hl = \Cassoft\SelfProg\Hbk::getHlbk($tableName);
            $res = $hl::getList([
                'select' => [
                    '*'
                ],
            ]);
            $result = [];
            while ($row = $res->fetch()) {
                $result[$row['UF_ID_ATI']] = $row['ID'];
            }
            return $result;
        }

        public static function getDicKey($tableName)
        {
            $hl = \Cassoft\SelfProg\Hbk::getHlbk($tableName);
            $res = $hl::getList([
                'select' => [
                    '*'
                ],
            ]);
            $result = [];
            while ($row = $res->fetch()) {
                $result[$row['ID']] = $row;
            }
            return $result;
        }

        public static function getHlbk($tableName)
        {
            $hl = HL\HighloadBlockTable::getList(
                array(
                    'filter' => array(
                        'TABLE_NAME' => $tableName,
                    )
                )
            )->fetch();

            if (empty($hl['ID']) || $hl['ID'] < 1) {
                return "Нету highloadblock\'a {$tableName} {$hl['ID']}";
            }

            $hlBlock = HL\HighloadBlockTable::getById($hl['ID'])->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
            $entity_data_class = $entity->getDataClass();
            return $entity_data_class;
        }

        protected function getErrorMessage($error)
        {
            return $error;
        }

        public static function getDicIdtoName($tableName)
        {
            $hl = \Cassoft\SelfProg\Hbk::getHlbk($tableName);
            $res = $hl::getList([
                'select' => [
                    '*'
                ],
            ]);
            $result = [];
            while ($row = $res->fetch()) {
                $result[$row['ID']] = $row['UF_NAME'];
            }
            return $result;
        }

        public static function getKey($memberID)
        {
            file_put_contents(__DIR__ . "hbk.txt", print_r("hbk\n", true), FILE_APPEND);
            file_put_contents(__DIR__ . "hbk.txt", print_r($memberID, true), FILE_APPEND);
            $hl = \Cassoft\SelfProg\Hbk::getHlbk("client_app_cassoft");
            $res = $hl::getList([
                'select' => ['*'],
                'order' => ['ID' => 'DESC'],
                'filter' => [
                    'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberID,
                ]
            ]);

            while ($row = $res->fetch()) {
                $result = $row['UF_CS_HOOK'];
            }
            return $result;
        }
    }
