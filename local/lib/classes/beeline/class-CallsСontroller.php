<?php

class CallsСontroller {
	private $infoLog    = true;
	private $createLead = false;

	private $b42Domain;
	private $inputWebhook;
	private $userID;
	
	private $leadID;
	private $dealID;
	private $contactID;
	private $companyID;
	private $activityID;
	private $entityID;
	private $assignedID;

	private $callCode;
	private $callType;
	private $entityType;
	private $phoneNumber;

	private $clientName     = '';
	private $clientLastName = '';
	private $companyName    = '';

	private $searchLead = false;
	private $nextActivity = false;
	private $totalActivity = false;
	private $countActivity = 0;
	private $entityCodeType = array(
								1 => 'Лид',
								3 => 'Контакт',
								4 => 'Компания',
							);


	/*
	  * Проверяет переданы ли все необходимые данные для работы скрипта.
	  *
	  * @param array $b24Data - массив с настройками для подключение к Битрикс24 и работы скрипта.
	*/

	public function __construct($b24Data){
		
		$this->infoLog = true;
		$this->b42Domain     = $b24Data['domain'];
		$this->userID        = $b24Data['userID'];		
		$this->setCreateLead();
	}

	/**
	  * Устанавливает нужные данные для дальнейшего использования в запросах.
	  *
	  * @param array $callData - массив с данными звонка, которые получаны от Битрикс24.
	*/

	public function setCallData($callData){
		if (!$callData or !isset($callData['data']))
			throw new Exception('Битрикс24 не передал необходимых данных.');

		if (!isset($callData['data']['CALL_TYPE']))
			throw new Exception('Отсутствуют данные о типе звонка.');

		if (!isset($callData['data']['CRM_ACTIVITY_ID']))
			throw new Exception('Отсутствует ID дела в переданных данных.');

		$this->callType = $callData['data']['CALL_TYPE'];
		$this->callCode = $callData['data']['CALL_FAILED_CODE'];
		$this->activityID = $callData['data']['CRM_ACTIVITY_ID'];
		$this->phoneNumber = $callData['data']['PHONE_NUMBER'];
	}

	/**
	  * Устанавливает настройку, создавать новый лид или нет.
	*/

	public function setCreateLead(){
		if (defined('CREATE_LEAD'))
			$this->createLead = CREATE_LEAD;
	}


	/**
	  * Сохраняет данные сущности, к которой приндлежит дело, для дальнейшего использования.
	  *
	  * @param array $data - массив с данными дела
	*/

	private function setEntityData($data){
		if (!isset($data['OWNER_ID']))
			throw new Exception('У дела отсутствует ID сущности.');

		if (!isset($data['OWNER_TYPE_ID']))
			throw new Exception('У дела отсутствует тип сущности.');

		$this->entityType = (int) $data['OWNER_TYPE_ID'];
		
		$this->setEntityID($data['OWNER_ID']);
	}

	/**
	  * Сохраняет ID сущности в зависимости от типа сущности для дальшейшего использования.
	  *
	  * @param integer $entityID - ID сущности.
	*/

	private function setEntityID($entityID){
		$this->entityID = $entityID;

		switch ($this->entityType){
			case 1: $this->leadID    = $entityID; break;
			case 2: $this->dealID    = $entityID; break;
			case 3: $this->contactID = $entityID; break;
			case 4: $this->companyID = $entityID; break;
			default:
				throw new Exception('Поступивший звонок от неизвестный сущности ('.$this->entityType.').');
		}
	}


	/*
	  * Сохраняет ID лида для дальнейшего использования.
	  *
	  * @param array $data - массив с данными сделки.
	*/

	private function setLeadID($data){
		if (isset($data['LEAD_ID']) and $data['LEAD_ID'])
			$this->leadID = $data['LEAD_ID'];
	}

	/*
	  * Сохраняет ID контакта для дальнейшего использования.
	  *
	  * @param array $data - массив с данными сделки.
	*/

	private function setContactID($data){
		if (isset($data['CONTACT_ID']) and $data['CONTACT_ID'])
			$this->contactID = $data['CONTACT_ID'];
	}

	/*
	  * Сохраняет ID компании для дальнейшего использования.
	  *
	  * @param array $data - массив с данными сделки.
	*/

	private function setCompanyID($data){
		if (isset($data['COMPANY_ID']) and $data['COMPANY_ID'])
			$this->companyID = $data['COMPANY_ID'];
	}

	/**
	  * Сохраняет имя контакта для дальшейшего использования.
	  *
	  * @param array $data - массив с данными контакта.
	*/

	private function setContactName($data){
		if (isset($data['LAST_NAME']) and $data['LAST_NAME'])
			$this->clientLastName = $data['LAST_NAME'];

		if (isset($data['NAME']) and $data['NAME'])
			$this->clientName = $data['NAME'];
	}

	/**
	  * Сохраняет название компании для дальшейшего использования.
	  *
	  * @param array $data - массив с данными контакта.
	*/

	private function setCompanyName($data){
		if (isset($data['TITLE']) and $data['TITLE'])
			$this->companyName = $data['TITLE'];
	}

	/**
	  * Сохраняет ID ответственного для дальшейшего использования.
	  *
	  * @param array $data - массив с данными контакта.
	*/

	private function setAssignedID($data){
		if (isset($data['ASSIGNED_BY_ID']) and $data['ASSIGNED_BY_ID'])
			$this->assignedID = $data['ASSIGNED_BY_ID'];
	}

	/**
	  * Создавать новый лид или нет.
	  *
	  * @return (false/true) $createLead - нет/да.
	*/

	public function isCreateLead(){
		if ($this->createLead == false){
			$this->writedLog('Уведомление', 'Создание повторного лида отключено.');
		}
		
		return $this->createLead;
	}



	/**
	  * Возвращает ID сущности.
	  *
	  * @return integer $entityID - ID сущности.
	*/

	public function getEntityID(){
		return $this->entityID;
	}



	public function getDealID(){
		return $this->dealID;
	}



	public function getActivityID(){
		return $this->activityID;
	}

	/**
	  * Возвращает тип звонка (входящий/исходящий).
	  *
	  * @return integer $callType - число обозначающие тип звонка (смотреть документацию Битрикс24)
	*/

	public function getCallType(){
		return $this->callType;
	}

	/**
	  * Возвращает код статуса звонка (успешный/пропущенный).
	  *
	  * @return integer $callCode - код обозначающий статус звонка (смотреть документацию Битрикс24)
	*/

	public function getCallCode(){
		return $this->callCode;
	}

	/**
	  * Создает лида с привязкой к контакту или компании
	*/

	public function createLead(){
		$title = 'Пропущенный звонок от ';
		$sendData = array();

		if ($this->contactID)
			$this->getContact($this->contactID);
			
		if ($this->companyID)
			$this->getCompany($this->companyID);

		if ($this->clientName or $this->clientLastName)
			$title .= $this->clientName . ' ' . $this->clientLastName;
		else if ($this->companyName)
			$title .= $this->companyName;
		else
			$title .= '(Имя/название не найдены)';

		$sendData['fields']['TITLE'] = $this->phoneNumber.' - '.$title;
		$sendData['fields']['ASSIGNED_BY_ID'] = $this->assignedID;
		$sendData['fields']['PHONE'][0] = array(
											'VALUE' => $this->phoneNumber,
											'VALUE_TYPE' => ' WORK'
										);

		if ($this->contactID)
			$sendData['fields']['CONTACT_ID'] = $this->contactID;

		if ($this->companyID)
			$sendData['fields']['COMPANY_ID'] = $this->companyID;

		$b24Result = CSRest::call('crm.lead.add', $sendData);

		if ($b24Result){
			if (isset($b24Result['result']) and $b24Result['result'])
				$this->writedLog('Успех', 'Повторный лид с ID'.$b24Result['result'].' создан.');
			else {
				$textError = $this->getError($b24Result, __METHOD__);

				$this->writedLog('Ошибка', $textError);
			}
		}
	}

	/**
	  * Получает данные дела по ID
	*/

	public function getActivity($activityID){
		$sendData = array('id' => $activityID);

		$b24Result = CSRest::call('crm.activity.get', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$status = 'Успех';
				$message = 'Данные дела ID'.$this->activityID.' получены';
				
				$this->setEntityData($b24Result['result']);
			} else {
				$status = 'Ошибка';
				$message = 'Данные дела ID'.$this->activityID.' отсутсвуют в ответе Битрикс24.';
			}

			$this->writedLog($status, $message);
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);

			throw new Exception('Данные дела ID'.$this->activityID.' не удалось получить.');
		}
	}

	/**
	  * Получает данные сущности по ID
	*/

	public function getEntity($entityID, $entityType){
		if (!$entityID)
			throw new Exception('Данные сущности не удалось получить, нет ID сущности.');

		switch ($entityType){
			case 1:
					$this->getLead($entityID);
				break;
			case 2:
					$this->getDeal($entityID);
				break;
			case 3:
					$this->getContact($entityID);
				break;
			case 4:
					$this->getCompany($entityID);
				break;
			default:
				throw new Exception('Поступивший звонок от неизвестный сущности ('.$entityType.').');
		}
	}


	/**
	  * Получает данные сделки по ID
	*/

	public function getDeal($dealID){
		if (!$dealID)
			throw new Exception('Данные сделки не удалось получить, нет ID сделки.');

		$sendData = array('id' => $dealID);

		$b24Result = CSRest::call('crm.deal.get', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$status = 'Успех';
				$message = 'Данные сделки ID'.$dealID.' получены';
				
				$this->setContactID($b24Result['result']);
				$this->setCompanyID($b24Result['result']);
				$this->setLeadID($b24Result['result']);
				$this->setAssignedID($b24Result['result']);
			} else {
				$status = 'Ошибка';
				$message = 'Данные сделки ID'.$dealID.' отсутсвуют в ответе Битрикс24.';
			}

			$this->writedLog($status, $message);
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);

			throw new Exception('Данные сделки не удалось получить.');
		}
	}

	/**
	  * Получает данные лида по ID
	*/

	public function getLead($leadID){
		if (!$leadID)
			throw new Exception('Данные лида не удалось получить, нет ID лида.');

		$sendData = array('id' => $leadID);

		$b24Result = CSRest::call('crm.lead.get', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$message = 'Данные лида ID'.$leadID.' получены';
				$status = 'Успех';
				
				$this->setContactID($b24Result['result']);
				$this->setCompanyID($b24Result['result']);
			} else {
				$status = 'Ошибка';
				$message = 'Данные лида ID'.$leadID.' отсутсвуют в ответе Битрикс24.';
			}

			$this->writedLog($status, $message);
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);

			throw new Exception('Данные лида не удалось получить.');
		}
	}

	/**
	  * Получает список лидов по фильтру
	*/

	public function getListLead(){
		$sendData = $this->getDatasearch();

		$b24Result = CSRest::call('crm.lead.list', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$leadID = $this->getLeadID($b24Result['result']);

				$this->searchLead = true;

				$this->writedLog('Успех', 'Найден открытый лид с ID'.$leadID.'.');
			} else {
				$this->writedLog('Уведомление', 'Открытый лид не найден.');
			}
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}
	}

	/**
	  * Получает данные контакта по ID
	*/

	public function getContact($contactID){
		if (!$contactID)
			throw new Exception('Данные контакта не удалось получить, нет ID контакта.');

		$sendData = array('id' => $contactID);

		$b24Result = CSRest::call('crm.contact.get', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$status = 'Успех';
				$message = 'Данные контакта ID'.$this->contactID.' получены';
				
				$this->setContactName($b24Result['result']);
			} else {
				$status = 'Ошибка';
				$message = 'Данные контакта ID'.$contactID.' отсутсвуют в ответе Битрикс24.';
			}
			
			$this->writedLog($status, $message);
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}
	}

	/**
	  * Получает данные компании по ID
	*/

	public function getCompany($companyID){
		if (!$companyID)
			throw new Exception('Данные компании не удалось получить, нет ID компании.');

		$sendData = array('id' => $companyID);

		$b24Result = CSRest::call('crm.company.get', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$status = 'Успех';
				$message = 'Данные компании ID'.$companyID.' получены';

				$this->setCompanyName($b24Result['result']);
			} else {
				$status = 'Ошибка';
				$message = 'Данные компании ID'.$companyID.' отсутсвуют в ответе Битрикс24.';
			}

			$this->writedLog($status, $message);
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}
	}

	/**
	  * Отправляет два запроса на поиск лида и сделки по ID контакта/компании.
	*/

	public function searchEntis(){
		$sendData = $this->getDatasearch();

		$resultLaed = CSRest::call('crm.lead.list', $sendData);
		$resultDeal = CSRest::call('crm.deal.list', $sendData);

		$leadID = $this->parsDataResult($resultLaed);
		$dealID = $this->parsDataResult($resultDeal);

		if ($leadID)
			$this->leadID = $leadID;

		if ($dealID)
			$this->dealID = $dealID;
	}

	/**
	  * Получает и возвращает ID сущности из массива данных, полученных от Битрикс24.
	  *
	  * @param (array) $parsData - массив с данными.
	  * @return (int/string) $id - ID сущности или пустая строка.
	*/

	private function parsDataResult($parsData){
		$id = '';

		if (isset($parsData['result'])){
			if ($parsData['result']){				
				$id = $parsData['result'][0]['ID'];
			}
		} else {
			$textError = $this->getError($parsData, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}

		return $id;
	}

	/**
	  * Возвращает массив с данными для поиска открытых лидов в Битрикс24.
	  *
	  * @param string $fieldName - имя поля.
	  * @return array $sendData - массив с данными.
	*/

	private function getDatasearch(){
		$sendData = array();

		$sendData['order'] = array('ID' => 'ASC');
		$sendData['select'] = array('PHONE', 'STATUS_SEMANTIC_ID');
		$sendData['filter']['STATUS_SEMANTIC_ID'] = 'P';
		
		if ($this->companyID)
			$sendData['filter']['COMPANY_ID'] = $this->companyID;

		if ($this->contactID)
			$sendData['filter']['CONTACT_ID'] = $this->contactID;

		return $sendData;
	}



	/**
	  * Возвращает тип сущности.
	  *
	  * @return integer $entityType - тип сущности.
	*/

	public function getEntityType(){
		return $this->entityType;
	}

	/**
	  * получает и возвращает список не завершенных дел на входящие/исходящие звонки.
	  *
	  * @return array/false $b24Result['result'] - 
	*/

	public function getListActivityTypeCall(){
		$sendData = array();
		$entityIDs = array();

		$sendData['order']  = array('ID' => 'ASC');
		$sendData['select'] = array('STATUS');

		if ($this->nextActivity)
			$sendData['start'] = $this->nextActivity;

		$sendData['filter']['COMPLETED']        = 'N';
		$sendData['filter']['PROVIDER_TYPE_ID'] = 'CALL';
		$sendData['filter']['DIRECTION']        = 1;
		
		if ($this->companyID) 
			$entityIDs[] = $this->companyID;

		if ($this->contactID) 
			$entityIDs[] = $this->contactID;

		if ($this->leadID) 
			$entityIDs[] = $this->leadID;

		if ($this->dealID) 
			$entityIDs[] = $this->dealID;

		$sendData['filter']['OWNER_ID'] = $entityIDs;

		$b24Result = CSRest::call('crm.activity.list', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				if ($this->totalActivity === false)
					$this->totalActivity = $b24Result['total'];

				if (isset($b24Result['next']) and $b24Result['next'])
					$this->nextActivity = $b24Result['next'];

				return $b24Result['result'];
			} else {
				if ($this->nextActivity === false){
					$textError = $this->getError($sendData, __METHOD__);

					$this->writedLog('Уведомление', 'Дела для закрытия не найдены.');
				}
			}
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}

		return false;
	}

	/**
	  * Цикл для перебора всех дел, запускает метод для обновления дел.
	  *
	  * @param array $listActivity - массив с данными дел.
	*/

	public function loopUpdateActivity($listActivity){
		foreach ($listActivity as $activity){
			$this->updateStatusActivity($activity['ID']);
		}
	}

	/**
	  * Возвращает общее кол-во дел
	  * @return integer $totalActivity - общее кол-во дел.
	*/

	public function getTotalActivity(){
		return $this->totalActivity;
	}

	/*
	  * Возвращает число для получения следующей страницы дел.
	  *
	  * @return integer $nextActivity - число для следующей страницы.
	*/

	public function getNextActivity(){
		return $this->nextActivity;
	}

	/*
	  * Возвращает bool тип, найден или не найден лид.
	  *
	  * @return  bool $searchLead - число для следующей страницы.
	*/

	public function getSearchLead(){
		return $this->searchLead;
	}

	public function getLeadID($data){
		$lead = array_shift($data);
		$leadID = $lead['ID'];
		
		return $leadID;
	}

	/**
	  * Рассчитывает кол-во страниц для дел.
	  *
	  * @return integer $countPages - кол-во страниц
	*/

	public function getCountPages(){
		$countPages = ceil((int) $this->totalActivity / 50);
		
		return $countPages;
	}

	/**
	  * Обновляет статус дела, переводит дело в "завершеное".
	  *
	  * @param integer $activityID - ID дела, которое нужно обновить.
	*/

	public function updateStatusActivity($activityID){
		
		$sendData = array();

		$sendData['id'] = $activityID;
		$sendData['fields']['COMPLETED'] = 'Y';
		$sendData['fields']['STATUS'] = 2;
	$sendData['fields']['END_TIME'] = date("Y-m-dTH:i:s");

		$b24Result = CSRest::call('crm.activity.update', $sendData);

		if (isset($b24Result['result'])){
			if ($b24Result['result']){
				$this->countActivity++;
			} else {
				$textError = $this->getError($b24Result, __METHOD__);

				$this->writedLog('Ошибка', 'Дело с ID'.$activityID.' закрыть не удалось.');
			}
		} else {
			$textError = $this->getError($b24Result, __METHOD__);

			$this->writedLog('Ошибка', $textError);
		}
	}

	/* Записывает сколько дел было завершено. */

	public function saveCounttActivity(){
		$this->writedLog('Успех', 'Всего завершено дел '.$this->countActivity.' (шт.)');
	}

	/**
	  * Отправляет запрос в Битрикс24
	  *
	  * @param string $methodName - название метода
	  * @param string $webhookCode - код webhook
	  * @param array $arrParams - параметры для запроса
	  * @return array arrData - массив с результатом запроса
	*/
/*
	private function sendQuery($methodName, $arrParams=array()){
		$url = 'https://'.$this->b42Domain.'/rest/'.$this->userID.'/'.$this->inputWebhook.'/'.$methodName.'.json';

		if ($arrParams){
			$strParams = http_build_query($arrParams);
			$url .= '?'.$strParams;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);

		curl_close($ch);

		$arrData = json_decode($result, true);

		return $arrData;
	}
*/
	/**
	  * Записывает действия скрипта в лог-файл, если включена запись.
	  *
	  * @param string $status - статус действия успешно/ошибка.
	  * @param string $textMessage - описание.
	*/

	public function writedLog($status, $textMessage){
		if ($this->infoLog == true){
			$nameDomain=$this->b42Domain;
			$toDate = date('Y-m-d-H:i:s');
			//$fileName = __DIR__ . '/logs/b24_data-' . date('Y-m-d') . '.txt';
                        $fileLog =  '/var/www/www-root/data/www/brokci.cassoft.ru/pub/cassoftApp/telBeeline/logs/'.date("Y-m-d") . '-'.$nameDomain.'.txt';
			//$fileLog = __DIR__ . '/logs/log-' . date('Y-m-d') . '.txt';
			$textMessage = sprintf(
								"%s | %s: (%s) %s \r\n",
								//$nameDomain,
								$toDate,
								$status,
								$this->phoneNumber,
								$textMessage
							);

			file_put_contents($fileLog, $textMessage, FILE_APPEND);
			//file_put_contents($fileLog2, $textMessage, FILE_APPEND);
		}
	}

	/** Возвращает ошибку от Битрикс24, если есть.
	  * @param array $data - массив данных от Битрикс24.
	  * @return string $textError - текст ошибки.
	*/

	private function getError($b24Data, $methodName){
		if (isset($data['error_description'])) 
			$textError = $data['error_description'];
		else {
			$textError = 'Ответ Битрикс24 не содержет ошибок.';
			$nameDomain=$this->b42Domain;
			$toDate = date('Y-m-d-H:i:s');
			//$fileName = __DIR__ . '/logs/b24_data-' . date('Y-m-d') . '.txt';
                        $fileName =  '/var/www/www-root/data/www/brokci.cassoft.ru/pub/cassoftApp/telBeeline/logs/'.date("Y-m-d") . '-b24_data-'.$nameDomain.'.txt';
			$fileData = sprintf(
							"%s | (%s): %s\r\n",
							//$nameDomain,
							$toDate,
							$methodName,
							print_r($b24Data, true)
						);

			file_put_contents($fileName, $fileData, FILE_APPEND);
		}

		return $textError;
	}

}

?>