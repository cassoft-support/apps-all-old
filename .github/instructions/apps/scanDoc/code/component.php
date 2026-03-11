<?php
/**
 * Компонент scanDoc - основная логика
 * 
 * НАЗНАЧЕНИЕ:
 * - Определяет тип CRM сущности (deal, contact, company, smart-процесс)
 * - Загружает данные из Битрикс24 CRM через REST API
 * - Парсит JSON с загруженными документами
 * - Передает данные в шаблон для отображения
 * 
 * ПОДДЕРЖИВАЕМЫЕ СУЩНОСТИ:
 * - Сделки (deal) - $arParams['deal_id']
 * - Контакты (contact) - $arParams['contact_id']
 * - Компании (company) - $arParams['company_id']
 * - Smart-процессы - $arParams['PLACEMENT_OPTIONS'] с ENTITY_TYPE_ID
 * 
 * ФОРМАТ ХРАНЕНИЯ ДОКУМЕНТОВ:
 * Поле UF_CRM_CS_SCAN_DOC (или ufCrm{smartId}CsScanDoc) содержит JSON:
 * [
 *   {"photo_id": "12345", "photo_link": "https://..."},
 *   {"photo_id": "12346", "photo_link": "https://..."}
 * ]
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

// Подключение необходимых библиотек
require_once $_SERVER['DOCUMENT_ROOT'] . "/local/CSlibs/classes/B24/Auth/Auth.php";

/**
 * Класс компонента scanDoc
 */
class ScanDocBaseComponent extends \CBitrixComponent
{
    /**
     * Точка входа компонента
     */
    public function executeComponent()
    {
        // Логирование для отладки
        $this->logRequest();

        try {
            // Инициализация авторизации в Битрикс24
            $this->initAuth();

            // Определение типа сущности и загрузка данных
            $this->loadEntityData();

            // Подключение шаблона для отображения
            $this->includeComponentTemplate();

        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    /**
     * Инициализация авторизации в Битрикс24
     * 
     * Создает объект Auth для работы с REST API
     * Передает данные приложения и member_id портала
     */
    private function initAuth()
    {
        $app = $this->arParams['app'] ?? 'scanDoc';
        $memberId = $this->arParams['member_id'] ?? '';

        if (empty($memberId)) {
            throw new \Exception('member_id не указан');
        }

        // Создание объекта авторизации
        // CSlibs\B24\Auth\Auth автоматически:
        // - Получает токены из HighLoad блока
        // - Обновляет access_token при необходимости
        // - Предоставляет метод call() для REST API запросов
        $this->arResult['auth'] = new \CSlibs\B24\Auth\Auth(
            $app,
            [],
            $memberId
        );
    }

    /**
     * Определяет тип сущности и загружает ее данные
     * 
     * ЛОГИКА ОПРЕДЕЛЕНИЯ:
     * 1. Если есть PLACEMENT_OPTIONS с ENTITY_TYPE_ID -> Smart-процесс
     * 2. Если есть deal_id -> Сделка
     * 3. Если есть contact_id -> Контакт
     * 4. Если есть company_id -> Компания
     */
    private function loadEntityData()
    {
        $auth = $this->arResult['auth'];

        // Проверка на Smart-процесс
        if (!empty($this->arParams['PLACEMENT_OPTIONS'])) {
            $this->loadSmartProcessData($auth);
            return;
        }

        // Проверка на обычные CRM сущности
        if (!empty($this->arParams['deal_id'])) {
            $this->loadDealData($auth, $this->arParams['deal_id']);
        } elseif (!empty($this->arParams['contact_id'])) {
            $this->loadContactData($auth, $this->arParams['contact_id']);
        } elseif (!empty($this->arParams['company_id'])) {
            $this->loadCompanyData($auth, $this->arParams['company_id']);
        } else {
            throw new \Exception('Не указан ID сущности');
        }
    }

    /**
     * Загрузка данных Smart-процесса
     * 
     * Smart-процессы (динамические сущности):
     * - Имеют уникальный entityTypeId (например, 1055, 1056)
     * - Имеют динамическое имя поля: ufCrm{smartId}CsScanDoc
     * - Используют методы crm.type.list и crm.item.get
     * 
     * @param \CSlibs\B24\Auth\Auth $auth Объект авторизации
     */
    private function loadSmartProcessData($auth)
    {
        // Парсинг PLACEMENT_OPTIONS (JSON строка)
        $placementOptions = json_decode($this->arParams['PLACEMENT_OPTIONS'], true);
        
        if (empty($placementOptions['ID']) || empty($placementOptions['ENTITY_TYPE_ID'])) {
            throw new \Exception('Некорректные PLACEMENT_OPTIONS для Smart-процесса');
        }

        $smartElId = $placementOptions['ID'];
        $entityTypeId = $placementOptions['ENTITY_TYPE_ID'];

        // Сохранение параметров для шаблона
        $this->arResult['smartElId'] = $smartElId;
        $this->arResult['entityTypeId'] = $entityTypeId;

        // Получение информации о типе Smart-процесса
        // Метод: crm.type.list
        // Возвращает: массив типов с полями title, entityTypeId, customSectionId
        $smartTypeResult = $auth->CScore->call('crm.type.list', [
            'filter' => [
                'entityTypeId' => $entityTypeId
            ]
        ]);

        if (empty($smartTypeResult['types'])) {
            throw new \Exception("Smart-процесс с entityTypeId={$entityTypeId} не найден");
        }

        $smartType = $smartTypeResult['types'][0];
        $smartId = $smartType['customSectionId'] ?? $entityTypeId;

        $this->arResult['smartId'] = $smartId;

        // Получение данных элемента Smart-процесса
        // Метод: crm.item.get
        // Возвращает: объект элемента со всеми полями
        $smartItem = $auth->CScore->call('crm.item.get', [
            'entityTypeId' => $entityTypeId,
            'id' => $smartElId
        ]);

        if (empty($smartItem['item'])) {
            throw new \Exception("Элемент Smart-процесса не найден");
        }

        // Формирование имени поля для документов
        // Формат: ufCrm{smartId}CsScanDoc
        // Пример: ufCrm1055CsScanDoc
        $fieldName = 'ufCrm' . $smartId . 'CsScanDoc';
        
        // Получение JSON с документами
        $scanDoc = $smartItem['item'][$fieldName] ?? '';

        // Парсинг и сохранение массива документов
        $this->parseDocuments($scanDoc);
    }

    /**
     * Загрузка данных сделки
     * 
     * Метод: crm.deal.get
     * Поле документов: UF_CRM_CS_SCAN_DOC
     * 
     * @param \CSlibs\B24\Auth\Auth $auth Объект авторизации
     * @param int $dealId ID сделки
     */
    private function loadDealData($auth, $dealId)
    {
        // Получение данных сделки
        // Метод: crm.deal.get
        // Параметры: ID сделки
        // Возвращает: объект сделки со всеми полями
        $deal = $auth->CScore->call('crm.deal.get', [
            'ID' => $dealId
        ]);

        if (empty($deal)) {
            throw new \Exception("Сделка с ID={$dealId} не найдена");
        }

        // Сохранение ID для шаблона
        $this->arResult['deal_id'] = $dealId;

        // Получение JSON с документами из пользовательского поля
        $scanDoc = $deal['UF_CRM_CS_SCAN_DOC'] ?? '';

        // Парсинг и сохранение массива документов
        $this->parseDocuments($scanDoc);
    }

    /**
     * Загрузка данных контакта
     * 
     * Метод: crm.contact.get
     * Поле документов: UF_CRM_CS_SCAN_DOC
     * 
     * @param \CSlibs\B24\Auth\Auth $auth Объект авторизации
     * @param int $contactId ID контакта
     */
    private function loadContactData($auth, $contactId)
    {
        // Получение данных контакта
        // Метод: crm.contact.get
        $contact = $auth->CScore->call('crm.contact.get', [
            'ID' => $contactId
        ]);

        if (empty($contact)) {
            throw new \Exception("Контакт с ID={$contactId} не найден");
        }

        $this->arResult['contact_id'] = $contactId;

        // Получение документов
        $scanDoc = $contact['UF_CRM_CS_SCAN_DOC'] ?? '';
        $this->parseDocuments($scanDoc);
    }

    /**
     * Загрузка данных компании
     * 
     * Метод: crm.company.get
     * Поле документов: UF_CRM_CS_SCAN_DOC
     * 
     * @param \CSlibs\B24\Auth\Auth $auth Объект авторизации
     * @param int $companyId ID компании
     */
    private function loadCompanyData($auth, $companyId)
    {
        // Получение данных компании
        // Метод: crm.company.get
        $company = $auth->CScore->call('crm.company.get', [
            'ID' => $companyId
        ]);

        if (empty($company)) {
            throw new \Exception("Компания с ID={$companyId} не найдена");
        }

        $this->arResult['company_id'] = $companyId;

        // Получение документов
        $scanDoc = $company['UF_CRM_CS_SCAN_DOC'] ?? '';
        $this->parseDocuments($scanDoc);
    }

    /**
     * Парсинг JSON с документами
     * 
     * ФОРМАТ JSON:
     * [
     *   {
     *     "photo_id": "12345",     // ID файла в Битрикс24 Disk
     *     "photo_link": "https://..." // Публичная ссылка на файл
     *   }
     * ]
     * 
     * @param string $scanDoc JSON строка с документами
     */
    private function parseDocuments($scanDoc)
    {
        $this->arResult['scanDoc'] = $scanDoc;

        if (!empty($scanDoc)) {
            // Декодирование JSON в массив
            $documents = json_decode($scanDoc, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($documents)) {
                // Массив документов для отображения в галерее
                $this->arResult['link'] = $documents;
            } else {
                // Ошибка парсинга JSON
                $this->arResult['link'] = [];
                $this->arResult['error'] = 'Ошибка парсинга JSON с документами';
            }
        } else {
            // Документов нет
            $this->arResult['link'] = [];
        }

        // Количество документов
        $this->arResult['documentCount'] = count($this->arResult['link']);
    }

    /**
     * Логирование запроса для отладки
     */
    private function logRequest()
    {
        $logFile = __DIR__ . "/log.txt";
        
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'arParams' => $this->arParams,
            'GET' => $_GET,
            'POST' => $_POST,
        ];

        file_put_contents(
            $logFile,
            print_r($logData, true) . "\n\n",
            FILE_APPEND
        );
    }

    /**
     * Отображение ошибки
     * 
     * @param string $message Текст ошибки
     */
    private function showError($message)
    {
        $this->arResult['error'] = $message;
        
        // Логирование ошибки
        $logFile = __DIR__ . "/error.txt";
        file_put_contents(
            $logFile,
            date('Y-m-d H:i:s') . " - " . $message . "\n",
            FILE_APPEND
        );

        // Отображение шаблона с ошибкой
        $this->includeComponentTemplate('error');
    }
}

/**
 * ИСПОЛЬЗОВАНИЕ В ШАБЛОНЕ:
 * 
 * $arResult['link'] - массив документов:
 * [
 *   ["photo_id" => "12345", "photo_link" => "https://..."],
 *   ["photo_id" => "12346", "photo_link" => "https://..."]
 * ]
 * 
 * $arResult['deal_id'] - ID сделки (если сделка)
 * $arResult['contact_id'] - ID контакта (если контакт)
 * $arResult['company_id'] - ID компании (если компания)
 * $arResult['smartElId'] - ID элемента smart-процесса
 * $arResult['smartId'] - ID типа smart-процесса
 * $arResult['entityTypeId'] - entityTypeId smart-процесса
 * 
 * $arResult['auth'] - объект авторизации для REST API
 * $arResult['scanDoc'] - исходный JSON с документами
 * $arResult['documentCount'] - количество документов
 * $arResult['error'] - текст ошибки (если есть)
 */
