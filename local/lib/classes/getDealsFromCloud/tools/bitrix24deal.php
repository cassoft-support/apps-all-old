<?php
namespace Bitrix24\Bitrix24Deal;
use Bitrix24\Bitrix24Entity;
use Bitrix24\Bitrix24Exception;

class Bitrix24Deal extends Bitrix24Entity
{
	/**
	 * 
	 */
	public function get($dealId)
	{
		$result = $this->client->call('crm.deal.get',
			array('id' => $dealId)
		);
		return $result;
	}

	//функция возвращает список значений поля типа список
	public function getEnumList($fieldName)
	{
		$result = $this->client->call('crm.deal.userfield.list',
			array('filter' => array("FIELD_NAME" => $fieldName))
		);
		return $result["result"][0]["LIST"];
	}
}
