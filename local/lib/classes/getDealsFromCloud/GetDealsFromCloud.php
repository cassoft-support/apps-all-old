<?
namespace Cassoft\SelfProg;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");
// \CModule::IncludeModule('highloadblock');
require_once("tools/tools.php");

class GetDealsFromCloud
{
	public $arB24App;
	public $arAccessParams = array(); //параметры для авторизации
	// public $currentUser = 0;
	private $b24_error = '';
	// public $portal = '';
	public $isTokenRefreshed = false; //ключ обновления кода доступа
	private $arDeals = array();
	private $arUsersIds = array(); //массив id сотрудников, ответственных за сделки
	private $arClientSettings = array(); //массив с настройками для облака


	private function checkB24Auth() {
	
		// проверяем актуальность доступа
		// $isTokenRefreshed = false;

		$this->$isTokenRefreshed = false;
		
		//создание специального класса для выполнения запросов (arB24App)
		$this->arB24App = getBitrix24($this->arAccessParams, $this->isTokenRefreshed, $this->b24_error);
		return $this->b24_error === true;
	}	
	
	public function saveAuth($hlElementId)
	{
		$hl = \Cassoft\SelfProg\Hlbk::getHlbk('app_real_estate_accesses');
		$data = array(
			'UF_CS_CLIENT_PORTAL_DOMEN'         => $this->arB24App->getDomain(),
			'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'   => $this->arB24App->getAccessToken(),
			'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'  => $this->arB24App->getRefreshToken(),
			'UF_CS_CLIENT_PORTAL_MEMBER_ID'      => $this->arB24App->getMemberId()
		);

		$res = $hl::update($hlElementId, $data);
		// $res = $hl::add($data);
		return $res;
	}

	//получение сделок
	public function getDeals()
	{
		$this->arDeals = array();

		//получение настроек для облака
		$this->getClientSettings();

		//получение направлений сделок из хайблока
		//получение мэппинга для перевода id направления сделки в код направления
		$mapCategoryIdToCategoryCode = $this->getCategoryesMap($this->arClientSettings);
		//id направлений содержатся в ключах массива $mapCategoryIdToCategoryCode 
		$categoryIds = array_keys($mapCategoryIdToCategoryCode);

		//массив id стадий $this->arClientSettings
		$sellStages = ($this->arClientSettings["UF_CS_CLIENT_SELL_STAGES_ID"] ? json_decode($this->arClientSettings["UF_CS_CLIENT_SELL_STAGES_ID"]) : []);
		$newStages = ($this->arClientSettings["UF_CS_CLIENT_NEW_STAGES_ID"] ? json_decode($this->arClientSettings["UF_CS_CLIENT_NEW_STAGES_ID"]) : []);
		$rentStages = ($this->arClientSettings["UF_CS_CLIENT_RENT_STAGES_ID"] ? json_decode($this->arClientSettings["UF_CS_CLIENT_RENT_STAGES_ID"]) : []);


		$stagesIds = array_merge($sellStages, $newStages, $rentStages);

		if (!$stagesIds) return array(); //прерывание функции, если пустой массив стадий
		if (!$categoryIds) return array(); //прерывание функции, если пустой массив направлений

		$obB24Batch = new \Bitrix24\Bitrix24Batch\Bitrix24Batch($this->arB24App);

		$arDealFilter = array(
			"CLOSED" => "N",
			"!UF_CRM_CS_DEAL_IMPORT_SITES" => false,
			"!UF_CRM_CS_DEAL_OBJECT_ID" => false,
			"CATEGORY_ID" => $categoryIds,
			"STAGE_ID" => $stagesIds
		);

		$descriptionFieldName = $this->arClientSettings["UF_CS_CLIENT_DESCRIPTION_FILD"]; //поле сделки с описанием объекта

		$obB24Batch->addDealListCall(
			0, 
			array("DATE_CREATE" => "ASC"),
			array("ID", "TITLE", "STAGE_ID", "CATEGORY_ID", "UF_CRM_PRICE", "ASSIGNED_BY_ID", "UF_CRM_CS_DEAL_OBJECT_ID", "UF_CRM_CS_DEAL_IMPORT_SITES",
			"UF_CRM_CS_DEAL_PHOTOS", "UF_CRM_CS_DEAL_PLANS", "UF_CRM_CS_DEAL_OBJECT_VIDEO", $descriptionFieldName), 
			$arDealFilter
		);
		$res = $obB24Batch->call();
		$this->arDeals = $res[0]["data"];

		//добавление данных в массив со сделками
		$this->addDataToArrayDeals();

		//получение массива пользователей
		$arUsers = $this->getUsers($this->arUsersIds , $obB24Batch);

		//добавление в сделки имен и телефонов ответственных сотрудников
		$this->addFilds($arUsers);

		return $this->arDeals;
	}

	//функция добавляет в сделки поля с данными ответственного
	function addFilds($arUsers)
	{
		foreach ($this->arDeals as $key => $deal)
		{
			$this->arDeals[$key]["ASSIGNED_NAME"] = $arUsers[$deal["ASSIGNED_BY_ID"]]["NAME"];
			$this->arDeals[$key]["ASSIGNED_LAST_NAME"] = $arUsers[$deal["ASSIGNED_BY_ID"]]["LAST_NAME"];
			$this->arDeals[$key]["ASSIGNED_EMAIL"] = $arUsers[$deal["ASSIGNED_BY_ID"]]["EMAIL"];
			$this->arDeals[$key]["ASSIGNED_AVATAR"] = $arUsers[$deal["ASSIGNED_BY_ID"]]["PERSONAL_PHOTO"];
			$this->arDeals[$key]["ASSIGNED_PHONE"] = $arUsers[$deal["ASSIGNED_BY_ID"]]["WORK_PHONE"];
		}
	}

	//функция получает массив id пользователей
	//функция отдает масив имен и телефонов пользователей
	function getUsers($arUsersIds, $obB24Batch)
	{
		if (!$arUsersIds) return false;

		//добавление запроса в пакет запросов к облаку
		$obB24Batch->addUserListCall(
			0,
			array("ID" => $arUsersIds)
		);
		//выполнение запроса
		$arRes = $obB24Batch->call();
		$users = $arRes[0]["data"];

		$arUsers = [];
		foreach ($users as $user)
		{
			$arUsers[$user["ID"]] = $user;
		}
		return $arUsers;
	}

	//функция добавляет массиву со сделками дополнительные данные
	private function addDataToArrayDeals()
	{
		$this->arUsersIds = []; //обнуление массива id сотрудников, ответственных за сделки

		//получение мэппинга направлений сделок
		$advertisingSitesMap = $this->getAdvertisingSitesMap();
		//получение мэппинга значений поля "Импорт на сайты"
		$importSitesMap = $this->getImportSitesMap();

		//получение мэппинга для перевода id значения поля "Импорт на сайты" в код рекламного сайта
		$mapValueIdToSiteCode = [];
		foreach ($importSitesMap as $siteName => $valueId)
		{
			if ($advertisingSitesMap[$siteName])
			{
				$mapValueIdToSiteCode[$valueId] = $advertisingSitesMap[$siteName];
			}
		}

		$arClientSettings = $this->arClientSettings;

		//получение мэппинга для перевода id направления сделки в код направления
		$mapCategoryIdToCategoryCode = $this->getCategoryesMap($arClientSettings);

		//перебор сделок и добавление данных
		foreach ($this->arDeals as $key => $deal)
		{
			//добавление кода категории
			$this->arDeals[$key]["CATEGORY_CODE"] = $mapCategoryIdToCategoryCode[$deal["CATEGORY_ID"]];
			//добавление кодов выбранных сайтов для рекламы
			foreach ($deal["UF_CRM_CS_DEAL_IMPORT_SITES"] as $valueId)
			{
				$this->arDeals[$key]["IMPORT_SITES"][] = $mapValueIdToSiteCode[$valueId];
			}
			//добавление id ответственного в массив ответственных сотрудников
			if (!in_array($deal["ASSIGNED_BY_ID"], $this->arUsersIds))
			{
				$this->arUsersIds[] = $deal["ASSIGNED_BY_ID"];
			}
			//добавление фотографий
			foreach ($deal["UF_CRM_CS_DEAL_PHOTOS"] as $file)
			{
				//ссылка на фотографию находится в строке после символа "|"
				$arFile = explode('|', $file);
				$this->arDeals[$key]["PHOTOS"][] = $arFile[1];
			}
			//добавление планировок
			foreach ($deal["UF_CRM_CS_DEAL_PLANS"] as $file)
			{
				//ссылка на планировку находится в строке после символа "|"
				$arFile = explode('|', $file);
				$this->arDeals[$key]["PLANS"][] = $arFile[1];
			}
			//добавление названия компании
			$this->arDeals[$key]["COMPANY_NAME"] = $arClientSettings["UF_CS_CLIENT_COMPANY_NAME"];

			//добавление описания объекта
			$descriptionFieldName = $arClientSettings["UF_CS_CLIENT_DESCRIPTION_FILD"];
			$this->arDeals[$key]["DESCRIPTION"] = $deal[$descriptionFieldName];
		}
	}

	//функция получает мэппинг поля "Импорт на сайты"
	private function getImportSitesMap()
	{
		//получение возможных значений поля сделки "Импорт на сайты"
		$obB24Deal = new \Bitrix24\Bitrix24Deal\Bitrix24Deal($this->arB24App);
		$arValues = $obB24Deal->getEnumList('UF_CRM_CS_DEAL_IMPORT_SITES');
		$importSitesMap = [];
		foreach ($arValues as $value)
		{
			$importSitesMap[$value["VALUE"]] = $value["ID"];
		}
		return $importSitesMap;
	}

	private function getClientSettings()
	{
		$this->arClientSettings = array();

		//создание объекта хайлоад блока с помощью метода getHlbk класса Hlbk
		$hl = \Cassoft\SelfProg\Hlbk::getHlbk('client_portals_settings');
		//получение настроек портала клиента из хайблока
		$rowsResult = $hl::getList(array(
			'select' => array('*'),
			'filter' => array("UF_CS_CLIENT_DOMAIN" => $this->arAccessParams["domain"]),
			'order' => array('ID' => 'DESC'),
			'limit' => 1,
		));
		$res = $rowsResult->fetch();
		$this->arClientSettings = $res;
	}

	//функция создает мэппинг рекламных площадок из хайблока
	private function getAdvertisingSitesMap()
	{
		//создание объекта хайлоад блока с помощью метода getHlbk класса Hlbk
		$hl = \Cassoft\SelfProg\Hlbk::getHlbk('advertising_sites');
		$rowsResult = $hl::getList(array(
			'select' => array('*'),
			'filter' => array(),
			'order' => array(),
		));
		$advertisingSitesMap = [];
		while ($site = $rowsResult->fetch())
		{
			$advertisingSitesMap[$site["UF_CS_ADVERT_SITE_NAME"]] = $site["UF_CS_ADVERT_SITE_CODE"];
		}
		return $advertisingSitesMap;
	}

	//функция создает мэппинг направлений сделок
	private function getCategoryesMap($arClientSettings)
	{
		$mapCategoryIdToCategoryCode = array();

		$sellCategoryId = $arClientSettings["UF_CS_CLIENT_SELL_CATEGORY_ID"];
		$newCategoryId = $arClientSettings["UF_CS_CLIENT_NEW_CATEGORY_ID"];
		$rentCategoryId = $arClientSettings["UF_CS_CLIENT_RENT_CATEGORY_ID"];

		if ($sellCategoryId || $sellCategoryId === '0') $mapCategoryIdToCategoryCode[$sellCategoryId] = 'sell';
		if ($newCategoryId || $newCategoryId === '0')   $mapCategoryIdToCategoryCode[$newCategoryId] = 'new';
		if ($rentCategoryId || $rentCategoryId === '0') $mapCategoryIdToCategoryCode[$rentCategoryId] = 'rent';

		return $mapCategoryIdToCategoryCode;
	}

	//функция получает код рекламного сайта и возвращает его название
	private function getSiteName($siteCode)
	{
		//создание объекта хайлоад блока с помощью метода getHlbk класса Hlbk
		$hl = \Cassoft\SelfProg\Hlbk::getHlbk('advertising_sites');

		//получение названия рекламного сайта
		$rowsResult = $hl::getList(array(
			'select' => array('*'),
			'filter' => array("UF_CS_ADVERT_SITE_CODE" => $siteCode),
			'order' => array(),
			'limit' => 1,
		));
		$res = $rowsResult->fetch();
		return $res["UF_CS_ADVERT_SITE_NAME"];
	}

	//авторизация из хайблока
	public function getAuthFromDB($domen)
	{
		$this->isTokenRefreshed = false;
		$this->arAccessParams = array();
		$this->b24_error = '';
		$hlElementId = ''; //id элемента хайблока, который содержит авторизационные данные для данного портала

		//создание объекта хайлоад блока с помощью метода getHlbk класса Hlbk (файл Hlbk.php)
		$hl = \Cassoft\SelfProg\Hlbk::getHlbk('app_real_estate_accesses');
		//получение данных для авторизации из хайблока
		$rowsResult = $hl::getList(array(
			'select' => array('*'),
			'filter' => array("UF_CS_CLIENT_PORTAL_DOMEN" => $domen),
			'order' => array('ID' => 'DESC'),
			'limit' => 1,
		));
		$res = $rowsResult->fetch();

		$this->arAccessParams = prepareFromHiBlock($res); //tools.php
		$hlElementId = $res["ID"]; //id элемента хайблока, который содержит авторизационные данные для данного портала

		//авторизация
		// $this->b24_error = $this->checkB24Auth();
		$this->checkB24Auth();

		//проверка был ли во время авторизации обновлен refresh_token
		if ($this->isTokenRefreshed)
		{
			// echo "рефреш токен был обновлен<br>";
			//если был то сохраним новые данные для авторизации в хайблок
			$res = $this->saveAuth($hlElementId);
		}
		
		if ($this->b24_error == '') return true;
		else return false;
	}

	public function getError()
	{
		$error = $this->b24_error;
		return $error;
	}
	
}	


?>
