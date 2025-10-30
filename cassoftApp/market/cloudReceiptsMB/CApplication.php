<?

class CApplication
{
	public $arB24App;
	public $arAccessParams = array(); //параметры для авторизации
	public $currentUser = 0;
	private $b24_error = '';
	public $isTokenRefreshed = false; //ключ обновления кода доступа
	private $feedsDir = 'pub/feedsCloud/'; //папка с фидами
	private $serverAddress = 'https://city.brokci.ru/';
	private $arFileNames = array(
		"avito.xml" => 'Авито',
		"cian.xml" => 'ЦИАН',
		"yandex.xml" => 'Яндекс',
		"free.xml" => 'Прочие сайты',
		"domclick.xml" => 'ДомКлик'
	);
	private $logPath = '';
    private $logDirCreateDeal = '/pub/cassoftApp/brokci/tools/logs/';


	private function checkB24Auth() {
	
		// проверяем актуальность доступа
		// $isTokenRefreshed = false;

		$this->isTokenRefreshed = false;
		
		//создание специального класса для выполнения запросов (arB24App)
		$this->arB24App = getBitrix24($this->arAccessParams, $this->isTokenRefreshed, $this->b24_error);
		return $this->b24_error === true;
	}

	public function addLog($message) {
        file_put_contents($this->logPath, $message . PHP_EOL, FILE_APPEND);
	}

	
	//авторизация и установка переменных
	public function start()
	{
		if ($_REQUEST["action"])
		{
			//авторизация при ajax запросах
			$this->arAccessParams = $_REQUEST;

			
		}
		elseif ($_REQUEST["actionRel"])
		{
			//авторизация при ajax запросах
			$this->arAccessParams = $_REQUEST["authParams"];

			
		}
		elseif ($_REQUEST["event"])
		{
			//авторизация при запуске обработчиком событий после изменения сделки
			$this->arAccessParams = $_REQUEST["auth"];
			//запросы от обработчика не содержат в себе refresh_token, но если его не передать, будет ошибка
			//подставим произволный refresh_token, он все равно не используется при запросах от обработчика
			$this->arAccessParams["refresh_token"] = '123';
		}
		else
		{
			//авторизация при открытии приложения
			$this->arAccessParams = prepareFromRequest($_REQUEST);
		}

		//создание специального класса arB24App методом checkB24Auth()
		$this->checkB24Auth();

		//остановка приложения, если данные устарели (иначе при каждом запросе будет формироваться новый access_token)
		//по документации Битрикса старый refresh_token должен стать невалидным, на практике же этого не происходит
		//поэтому сделаем exit после обновления access_token
		if ($this->isTokenRefreshed) exit('Авторизация устарела. Обновите страницу');

		if ($this->b24_error === '')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getDealId()
	{
		//поиск id сделки
		$placementOptions = array();
		if(array_key_exists('PLACEMENT_OPTIONS', $_REQUEST))
		{
			$placementOptions = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
			return $placementOptions["ID"];
		}
	}

	public function getDeal($dealId)
	{
		//создание экземпляра класса Bitrix24Deal для запросов к серверу облака
		$obB24Deal = new \Bitrix24\Bitrix24Deal\Bitrix24Deal($this->arB24App);
		//получение сделки
		$arDeal = $obB24Deal->get($dealId);
		$deal = $arDeal["result"];

		return $deal;
	}

	public function getObjectId($dealId)
	{
		//создание экземпляра класса Bitrix24Deal для запросов к серверу облака
		$obB24Deal = new \Bitrix24\Bitrix24Deal\Bitrix24Deal($this->arB24App);
		//получение сделки
		$arDeal = $obB24Deal->get($dealId);
		$deal = $arDeal["result"];

		return $deal["UF_CRM_CS_DEAL_OBJECT_ID"]; //UF_CRM_1550226234
	}

	//проверка, является ли текущий пользователь администратором
	public function checkAdmin()
	{
		//создание экземпляра класса bitrix24user для запросов к серверу облака
		$obB24User = new \Bitrix24\bitrix24user\bitrix24user($this->arB24App);
		$isAdmin = $obB24User->admin();
		return $isAdmin;
	}

	public function getCurrentUser()
	{
		//создание экземпляра класса bitrix24user для запросов к серверу облака
		$obB24User = new \Bitrix24\bitrix24user\bitrix24user($this->arB24App);
		$user = $obB24User->current();
		return $user;
	}

	//функция получает настройки для облака из хайблока
	public function getSettings($member_id, $hl)
	{
	    $rowsResult = $hl::getList(array(
	        'select' => array('*'),
	        'filter' => array("UF_CS_CLIENT_PORTAL_MEMBER_ID" => $member_id),
	        'order' => array("ID" => "DESC"),
	        'limit' => 1,
	    ));
	    $res = $rowsResult->fetch();
	    return $res;
	}
	public function getSettingsM($member_id, $hl)
	{
	    $rowsResult = $hl::getList(array(
	        'select' => array('*'),
	        'filter' => array("UF_CS_CLIENT_MEMBER_ID" => $member_id),
	        'order' => array("ID" => "DESC"),
	        'limit' => 1,
	    ));
	    $res = $rowsResult->fetch();
	    return $res;
	}
	
	//функция получает id клиента из хайблока
	public function getClientId($member_id, $hl)
	{
	    $rowsResult = $hl::getList(array(
	        'select' => array('*'),
	        'filter' => array("UF_CS_CLIENT_PORTAL_MEMBER_ID" => $member_id),
	        'order' => array("ID" => "DESC"),
	        'limit' => 1,
	    ));
	    $res = $rowsResult->fetch();

	    return $res["ID"];
	}

	//функция отдает массив id направлений сделок из хайблока
	public function getCategoryIds($arSettings)
	{
		$sellCategoryId = $arSettings["UF_CS_CLIENT_SELL_CATEGORY_ID"]; //id направления по продаже вторички
		$newCategoryId = $arSettings["UF_CS_CLIENT_NEW_CATEGORY_ID"]; //id направления по продаже новостроек
		$rentCategoryId = $arSettings["UF_CS_CLIENT_RENT_CATEGORY_ID"]; //id направления по аренде
		
		$arCategoryIds = [];

		//найдем все id категорий записанные в хайблоке в том числе равные нулю
		if ($sellCategoryId || $sellCategoryId === '0') $arCategoryIds[] = $sellCategoryId;
		if ($newCategoryId || $newCategoryId === '0') $arCategoryIds[] = $newCategoryId;
		if ($rentCategoryId || $rentCategoryId === '0') $arCategoryIds[] = $rentCategoryId;

		return $arCategoryIds;
	}

	//функция получает направления сделок из облака
	public function getCategoryNames()
	{
	    $arCategoryNames = array(); //ассоциативноый массив, где ключ - id направления, а значение - его название
		//получение основного направления
		//создание экземпляра класса Bitrix24Deal для запросов к серверу облака
		$obB24Deal = new \Bitrix24\Bitrix24Deal\Bitrix24Deal($this->arB24App);
		$defaultCategory = $obB24Deal->getDefaultCategory();
		$arCategoryNames[$defaultCategory["ID"]] = $defaultCategory["NAME"];

		//получение остальных направлений
		//создание экземпляра класса obB24Batch для запросов к серверу облака
		$obB24Batch = new \Bitrix24\Bitrix24Batch\Bitrix24Batch($this->arB24App);
		$obB24Batch->addCategoryListCall(
			0, 
			array("SORT" => "ASC"), //orders
			array("ID", "NAME"), //select
			array("IS_LOCKED" => "N") //filter
		);
		$res = $obB24Batch->call();
		$arCategoryes = $res[0]["data"];
		foreach ($arCategoryes as $category)
		{
			$arCategoryNames[$category["ID"]] = $category["NAME"];
		}
		return $arCategoryNames;
	}

	//функция получает существующие поля сделки из облака
	public function getFieldNames()
	{
	    //создание экземпляра класса Bitrix24Deal для запросов к серверу облака
		$obB24Deal = new \Bitrix24\Bitrix24Deal\Bitrix24Deal($this->arB24App);
		$arFields = $obB24Deal->getFields();
		//создание ассоциативного массива, где ключ - это название поля (UF_CRM_...) значение - заголовок поля
		$arFieldNames = array();
		foreach ($arFields as $key => $field)
		{
			if ($field["type"] == 'string' && $field["isMultiple"] != true) //только поля типа строка, не множественные
			{
				if ($field["formLabel"]) //у пользовательских полей есть formLabel
				{
					$arFieldNames[$key] = $field["formLabel"];
				}
				else //у системных полей берется title
				{
					$arFieldNames[$key] = $field["title"];
				}
			}
		}
		return $arFieldNames;
	}

	//функция отдает массив ссылок на файлы с фидами
	public function getFeedLinks($id)
	{
		//поиск папки с названием, равным id клиента
	    $dir = $this->feedsDir . $id;
		$dirFull = $_SERVER["DOCUMENT_ROOT"] . '/' . $dir;

		//поиск папки с захешированным именем, которая находится в папке с названием, равным id клиента
		$hashDirName = array_shift(array_diff( scandir($dirFull), array('..', '.')));
		//поиск файлов в папке с хешированным именем
		$arFiles = array_diff( scandir($dirFull . '/' . $hashDirName), array('..', '.'));

		$links = array();
		foreach ($arFiles as $fileName)
		{
			$url = $this->serverAddress . $dir . '/' . $hashDirName . '/' . $fileName; //ссылка на фид
			$siteName = ($this->arFileNames[$fileName] ? $this->arFileNames[$fileName] : $fileName); //название рекламного сайта
			$links[] = array(
				"URL" => $url,
				"NAME" => $siteName
			);
		}

		return $links;
	}

	//функция создает объект, создает контакт, создает сделку
	public function createDeal($arParsObject, $user, $countryID, $oper)
	{
		$this->logPath = $_SERVER['DOCUMENT_ROOT'] . $this->logDirCreateDeal . date('Y-m-d') . ".txt";
		$this->addLog('-----------------------------------------------------------------------------------------------------------------------');
		$this->addLog(date('d.m.Y H:i:s') . ' Начало работы скрипта по созданию сделки из парсера для объекта с id = ' . $arParsObject['ID']);
		$this->addLog('domain клиента: ' . $this->arAccessParams["domain"]);
		$this->addLog('Процесс запустил пользователь ' . $user['NAME'] . ' ' . $user['LAST_NAME'] . ' id = ' . $user['ID']);

		$createMessage = '';
//поиск настроек облака клиента
		$hl = \Cassoft\SelfProg\HlbkForRealEst::getHlbk('brokci_settings');
		$memberId = $this->arAccessParams["member_id"];
		$arSettings = $this->getSettings($memberId, $hl);
		//$this->addLog('setting: ' . print_r($arSettings, true));
		//поиск настроек облака клиента
		$hlM = \Cassoft\SelfProg\HlbkForRealEst::getHlbk('client_marketing_settings');
		$memberIdM = $this->arAccessParams["member_id"];
		//$this->addLog('setting: ' . print_r($memberIdM, true));
		$arSettingsM = $this->getSettingsM($memberIdM, $hlM);
		//$this->addLog('setting: ' . print_r($arSettingsM, true));

		/* создание объекта недвижимости */

		$objectCreator = new \Cassoft\SelfProg\CreateObjectFromParser($arParsObject, $this->logPath, $this->arB24App, $arSettings, $countryID, $oper );

		//проверка существования объекта
		$objectId = '';
		$res = $objectCreator->checkObject();
		if ($res['result'] == true)
		{
			$objectTitle = $res['title'];
			$objectId = $res['arObject'];
			$createMessage .= 'Для данного объявления найден уже существующий объект. ID '.$objectId.'. - '.$objectTitle.'<br>';
		}

		if (!$objectId)
		{
			//создание объекта
			$res = $objectCreator->createObject();
			if ($res['result'] == true)
			{
				$objectId = $res['objectId'];
				$objectTitle = $res['title'];
			}
			else
			{
				//прерывание функции, если объект не создался
				return $res['error'];
			}
		}


		

		//создание контакта и сделки
		$dealCreator = new \Cassoft\SelfProg\CreateDealFromParser($arParsObject, $objectId, $objectTitle, $this->logPath, $user, $this->arB24App, $arSettingsM,$oper);

		//проверка существования контакта
		$contactId = '';
		$res = $dealCreator->checkContacts();
		if ($res['result'] == true)
		{
			$contactId = $res['contactId'];
			$createMessage .= 'Для данного номера телефона найден уже существующий контакт с id = <a href="https://'.$this->arAccessParams['domain']
			.'/crm/contact/details/'.$contactId.'/" target="_blank">'.$contactId.'</a><br>';
		}

		if (!$contactId)
		{
			//создание контакта
			$res = $dealCreator->createContact();
			if ($res['result'] == true)
			{
				$contactId = $res['contactId'];
				$createMessage .= 'Создан <a href="https://'.$this->arAccessParams['domain']
				.'/crm/contact/details/'.$contactId.'/" target="_blank"> контакт '.$contactId.'</a><br>';
			}
			else
			{
				$createMessage .= 'Не удалось создать контакт для данного объекта.<br>';
			}
		}
/*
		//создание фотографий
		$res = $dealCreator->getPhotos();
		if ($res['message'])
		{
			$createMessage .= $res['message'] . '<br>';
		}
		*/
		//создание сделки
		$res = $dealCreator->createDeal();
		if ($res['result'] == true)
		{
			$dealId = $res['dealId'];
			$createMessage .= 'Создана <a href="https://'.$this->arAccessParams['domain']
			.'/crm/deal/details/'.$dealId.'/" target="_blank"> сделка '.$dealId.'</a><br>';
		}
		else
		{
			$createMessage .= 'Не удалось создать сделку для данного объекта.<br>';
		}

		$this->addLog('Конец работы скрипта');
		return $createMessage;
	}

	//функция получает объект из хайблока парсера
	public function getObjectFromParser($objectId, $oper, $country)
	{
		$hlParsingObject = \Cassoft\SelfProg\HlbkForRealEst::getHlbk('cs_parsing_'.$oper.'_'.$country); //каталог парсера
		$arFilter = array("ID" => $objectId);
		$rowResult = $hlParsingObject::getList(array(
			'select' => array('*'),
			'filter' => $arFilter,
		));
		$arObject = $rowResult->fetch();
		
		return $arObject;
	}
}