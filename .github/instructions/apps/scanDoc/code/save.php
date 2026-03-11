<?php
/**
 * AJAX обработчик сохранения документов
 * 
 * НАЗНАЧЕНИЕ:
 * - Прием данных формы с загруженными файлами
 * - Вызов функции savePhoto() для обработки файлов
 * - Обновление данных в Битрикс24 CRM через REST API
 * - Возврат результата в формате JSON
 * 
 * ВХОДНЫЕ ДАННЫЕ ($_POST):
 * - authParams: JSON с токенами авторизации (access_token, refresh_token, domain, member_id)
 * - app: код приложения (scanDoc)
 * - deal_id / contact_id / company_id: ID CRM сущности
 * - smartElId, smartId, entityTypeId: для smart-процессов
 * - sort: массив порядка файлов ["file1.jpg", "12345", "file2.jpg"]
 * - oldPhotoInfo: массив "id,link" существующих фото
 * 
 * ВХОДНЫЕ ДАННЫЕ ($_FILES):
 * - files: массив загруженных файлов
 * 
 * ВЫХОДНЫЕ ДАННЫЕ (JSON):
 * - Результат обновления CRM или сообщение об ошибке
 * 
 * ПРОЦЕСС:
 * 1. Парсинг параметров авторизации
 * 2. Создание объекта Auth для REST API
 * 3. Вызов savePhoto() для обработки файлов
 * 4. Определение типа сущности (deal/contact/company/smart)
 * 5. Обновление соответствующего поля в CRM
 * 6. Возврат результата клиенту
 */

// Отключение статистики и проверок для производительности
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

// Подключение prolog Битрикс
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Подключение функции savePhoto для обработки файлов
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/scanDoc/base/ajax/savePhoto.php");

// Файл лога для отладки
$log = __DIR__ . "/logSave.txt";

// ===================================
// ШАГ 1: Логирование запроса
// ===================================
p($_POST, "start", $log);
p($_FILES, "files", $log);

// ===================================
// ШАГ 2: Парсинг параметров авторизации
// ===================================
// authParams передается как JSON строка
// Формат: {
//   "domain": "company.bitrix24.ru",
//   "member_id": "abc123",
//   "access_token": "...",
//   "refresh_token": "..."
// }
$paramAuth = json_decode($_POST['authParams'], true);

if (empty($paramAuth)) {
    echo json_encode([
        'error' => true,
        'message' => 'authParams не переданы или некорректны'
    ]);
    exit;
}

p($paramAuth, "paramAuth", $log);

// Формирование массива данных клиента для Auth класса
$clientApp = [
    'DOMAIN' => $paramAuth['domain'],
    'member_id' => $paramAuth['member_id'],
    'AUTH_ID' => $paramAuth['access_token'],
    'REFRESH_ID' => $paramAuth['refresh_token'],
];

// ===================================
// ШАГ 3: Проверка обязательных параметров
// ===================================
if (empty($clientApp['member_id']) || empty($_POST['app'])) {
    echo json_encode([
        'error' => true,
        'message' => 'Не переданы обязательные параметры (member_id или app)'
    ]);
    exit;
}

p($clientApp, "clientApp", $log);

// ===================================
// ШАГ 4: Инициализация авторизации
// ===================================
// Класс \CSlibs\B24\Auth\Auth:
// - Загружает/обновляет токены из HighLoad блока
// - Автоматически обновляет access_token при истечении
// - Предоставляет метод call() для REST API запросов
$auth = new \CSlibs\B24\Auth\Auth($_POST['app'], $clientApp, "");

if (!$auth) {
    echo json_encode([
        'error' => true,
        'message' => 'Ошибка инициализации авторизации'
    ]);
    exit;
}

// ===================================
// ШАГ 5: Обработка загруженных файлов
// ===================================
// Вызов функции savePhoto()
// Параметры:
//   - $auth: объект авторизации
//   - $paramAuth['domain']: домен портала (для формирования ссылок)
//   - $_POST: данные формы (sort, oldPhotoInfo)
//   - $_FILES: загруженные файлы
//   - $type: 'photo' если есть новые файлы, 'sort_photo' если только сортировка

if (!empty($_FILES['files'])) {
    // Есть новые файлы для загрузки
    p("Processing new files upload", "mode", $log);
    $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'photo');
} else {
    // Только изменение порядка существующих файлов
    p("Processing files reordering", "mode", $log);
    $photo = savePhoto($auth, $paramAuth['domain'], $_POST, $_FILES, 'sort_photo');
}

p($photo, "photo result", $log);

// Проверка результата savePhoto()
if (isset($photo['error'])) {
    echo json_encode([
        'error' => true,
        'message' => $photo['message'] ?? 'Ошибка при обработке файлов'
    ]);
    exit;
}

// ===================================
// ШАГ 6: Формирование параметров для обновления CRM
// ===================================
$paramsUp = [];

// Если результат пустой - очищаем поле документов
// Если есть результат - сохраняем JSON строку
if (empty($photo['result'])) {
    $crmFieldValue = '';
} else {
    $crmFieldValue = $photo['result']; // JSON: [{"photo_id":"...","photo_link":"..."},...]
}

// ===================================
// ШАГ 7: Определение типа сущности и обновление
// ===================================

// --- СДЕЛКА (Deal) ---
if (!empty($_POST['deal_id'])) {
    p("Updating deal", "entity_type", $log);
    
    // Поле для сделок: UF_CRM_CS_SCAN_DOC
    $paramsUp["UF_CRM_CS_SCAN_DOC"] = $crmFieldValue;
    
    p($paramsUp, "params for deal.update", $log);
    
    // Метод: crm.deal.update
    // Параметры:
    //   - id: ID сделки
    //   - fields: массив полей для обновления
    $dealUpdate = $auth->CScore->call("crm.deal.update", [
        'id' => $_POST['deal_id'],
        'fields' => $paramsUp
    ]);
    
    p($dealUpdate, "dealUpdate result", $log);
    
    // Возврат результата
    echo json_encode([
        'success' => true,
        'result' => $dealUpdate,
        'message' => 'Документы успешно сохранены'
    ]);
}

// --- КОНТАКТ (Contact) ---
elseif (!empty($_POST['contact_id'])) {
    p("Updating contact", "entity_type", $log);
    
    // Поле для контактов: UF_CRM_CS_SCAN_DOC
    $paramsUp["UF_CRM_CS_SCAN_DOC"] = $crmFieldValue;
    
    p($paramsUp, "params for contact.update", $log);
    
    // Метод: crm.contact.update
    $contactUpdate = $auth->CScore->call("crm.contact.update", [
        'id' => $_POST['contact_id'],
        'fields' => $paramsUp
    ]);
    
    p($contactUpdate, "contactUpdate result", $log);
    
    echo json_encode([
        'success' => true,
        'result' => $contactUpdate,
        'message' => 'Документы успешно сохранены'
    ]);
}

// --- КОМПАНИЯ (Company) ---
elseif (!empty($_POST['company_id'])) {
    p("Updating company", "entity_type", $log);
    
    // Поле для компаний: UF_CRM_CS_SCAN_DOC
    $paramsUp["UF_CRM_CS_SCAN_DOC"] = $crmFieldValue;
    
    p($paramsUp, "params for company.update", $log);
    
    // Метод: crm.company.update
    $companyUpdate = $auth->CScore->call("crm.company.update", [
        'id' => $_POST['company_id'],
        'fields' => $paramsUp
    ]);
    
    p($companyUpdate, "companyUpdate result", $log);
    
    echo json_encode([
        'success' => true,
        'result' => $companyUpdate,
        'message' => 'Документы успешно сохранены'
    ]);
}

// --- SMART-ПРОЦЕСС (Smart Process) ---
elseif (!empty($_POST['smartElId']) && !empty($_POST['smartId']) && !empty($_POST['entityTypeId'])) {
    p("Updating smart process", "entity_type", $log);
    
    // Для smart-процессов имя поля динамическое
    // Формат: ufCrm{smartId}CsScanDoc
    // Пример: ufCrm1055CsScanDoc
    $fieldName = 'ufCrm' . $_POST['smartId'] . 'CsScanDoc';
    $paramsUp[$fieldName] = $crmFieldValue;
    
    p($paramsUp, "params for crm.item.update", $log);
    
    // Метод: crm.item.update
    // Параметры:
    //   - entityTypeId: тип smart-процесса
    //   - id: ID элемента
    //   - fields: массив полей для обновления
    $smartUpdate = $auth->CScore->call("crm.item.update", [
        'entityTypeId' => $_POST['entityTypeId'],
        'id' => $_POST['smartElId'],
        'fields' => $paramsUp
    ]);
    
    p($smartUpdate, "smartUpdate result", $log);
    
    echo json_encode([
        'success' => true,
        'result' => $smartUpdate,
        'message' => 'Документы успешно сохранены'
    ]);
}

// --- ОШИБКА: Неизвестный тип сущности ---
else {
    p("Unknown entity type", "error", $log);
    
    echo json_encode([
        'error' => true,
        'message' => 'Не удалось определить тип CRM сущности. Переданные параметры: ' . 
                     json_encode($_POST)
    ]);
}

/**
 * ФОРМАТ ЗАПРОСА (JavaScript):
 * 
 * var formData = new FormData();
 * 
 * // Авторизация
 * formData.append('authParams', JSON.stringify({
 *     domain: 'company.bitrix24.ru',
 *     member_id: 'abc123',
 *     access_token: '...',
 *     refresh_token: '...'
 * }));
 * 
 * // Приложение
 * formData.append('app', 'scanDoc');
 * 
 * // ID сущности (один из вариантов)
 * formData.append('deal_id', '123');
 * // ИЛИ
 * formData.append('contact_id', '456');
 * // ИЛИ
 * formData.append('company_id', '789');
 * // ИЛИ для smart-процесса
 * formData.append('smartElId', '111');
 * formData.append('smartId', '1055');
 * formData.append('entityTypeId', '1055');
 * 
 * // Данные о файлах
 * formData.append('sort', ['file1.jpg', '12345', 'file2.jpg']); // Порядок файлов
 * formData.append('oldPhotoInfo', ['12345,https://...', '12346,https://...']); // Существующие
 * 
 * // Новые файлы (если есть)
 * myDropzone.files.forEach(function(file, index) {
 *     formData.append('files[name][]', file.name);
 *     formData.append('files[tmp_name][]', file);
 *     formData.append('files[type][]', file.type);
 *     formData.append('files[size][]', file.size);
 * });
 * 
 * // AJAX запрос
 * $.ajax({
 *     url: 'save.php',
 *     type: 'POST',
 *     data: formData,
 *     processData: false,
 *     contentType: false,
 *     success: function(response) {
 *         console.log('Success:', response);
 *         BX24.closeApplication();
 *     },
 *     error: function(xhr, status, error) {
 *         console.error('Error:', error);
 *         alert('Ошибка сохранения документов');
 *     }
 * });
 */

/**
 * ФОРМАТ ОТВЕТА (JSON):
 * 
 * УСПЕХ:
 * {
 *   "success": true,
 *   "result": { ... },  // Результат REST API вызова
 *   "message": "Документы успешно сохранены"
 * }
 * 
 * ОШИБКА:
 * {
 *   "error": true,
 *   "message": "Описание ошибки"
 * }
 */

/**
 * ПРИМЕЧАНИЯ:
 * 
 * 1. БЕЗОПАСНОСТЬ:
 *    - Проверяйте authParams перед использованием
 *    - Валидируйте ID сущностей (deal_id, contact_id и т.д.)
 *    - Ограничивайте размер файлов (upload_max_filesize в PHP)
 *    - Проверяйте типы файлов (только изображения и PDF)
 * 
 * 2. ПРОИЗВОДИТЕЛЬНОСТЬ:
 *    - savePhoto() может занимать время при большом количестве файлов
 *    - Рассмотрите асинхронную обработку для больших объемов
 *    - Используйте сжатие изображений на клиенте перед загрузкой
 * 
 * 3. ОШИБКИ:
 *    - Логируйте все ошибки в файл для отладки
 *    - Возвращайте понятные сообщения пользователю
 *    - Обрабатывайте случаи истечения токенов (Auth класс делает автоматически)
 * 
 * 4. РАСШИРЕНИЕ:
 *    - Можно добавить прогресс-бар загрузки
 *    - Добавить валидацию MIME-типов
 *    - Реализовать batch загрузку для очень больших объемов
 */
