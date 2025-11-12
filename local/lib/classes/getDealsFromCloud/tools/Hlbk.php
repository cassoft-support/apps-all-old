<?php
namespace Cassoft\SelfProg;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
Loader::IncludeModule('highloadblock');

class Hlbk
{
	private $tableName;
	private $hblkId;

    public static function getHlbk($tableName) {

        $hl= HL\HighloadBlockTable::getList(array(
                'filter' => array(
                    'TABLE_NAME' => $tableName,
                ))
        )->fetch();

        if(empty($hl['ID']) || $hl['ID'] < 1) {
            return "Нету highloadblock\'a {$tableName}!";
        }
        
        $hlBlock = HL\HighloadBlockTable::getById($hl['ID'])->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);    
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
    
    protected function getErrorMessage($error) {
        return $error;
	}

}
?>
