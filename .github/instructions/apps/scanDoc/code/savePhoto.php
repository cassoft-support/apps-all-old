<?php
/**
 * Функция savePhoto() - Загрузка файлов на Битрикс24 Disk
 * 
 * НАЗНАЧЕНИЕ:
 * - Обработка загруженных файлов ($_FILES)
 * - Изменение размера изображений (оптимизация)
 * - Загрузка файлов на Битрикс24 Disk через REST API
 * - Получение публичных ссылок на файлы
 * - Сохранение порядка файлов (сортировка)
 * 
 * ПРОЦЕСС:
 * 1. Восстановление существующих фотографий из oldPhotoInfo
 * 2. Обработка новых загруженных файлов
 * 3. Изменение размера изображений (не PDF)
 * 4. Конвертация в base64
 * 5. Загрузка на Disk (disk.folder.uploadfile)
 * 6. Получение публичной ссылки (disk.file.getExternalLink)
 * 7. Формирование результата с учетом сортировки
 * 
 * ИСПОЛЬЗУЕМЫЕ REST API МЕТОДЫ:
 * - disk.storage.getforapp - получение корневой папки приложения
 * - disk.folder.uploadfile - загрузка файла на Disk
 * - disk.file.getExternalLink - получение публичной ссылки
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/CSlibs/tools/resize.php';

/**
 * Основная функция загрузки фотографий
 * 
 * @param \CSlibs\B24\Auth\Auth $auth Объект авторизации для REST API
 * @param string $resDomain Домен портала (например, "company.bitrix24.ru")
 * @param array $post Данные $_POST:
 *   - sort: массив имен файлов в нужном порядке ["file1.jpg", "12345", "file2.jpg"]
 *   - oldPhotoInfo: массив строк "id,link" для существующих фото ["12345,https://...", "12346,https://..."]
 * @param array $files Данные $_FILES с загруженными файлами
 * @param string $type Тип операции:
 *   - "photo" - загрузка новых файлов
 *   - "sort_photo" - только изменение порядка (без новых файлов)
 * 
 * @return array Результат загрузки:
 *   - result: JSON строка с массивом [{"photo_id": "...", "photo_link": "..."}, ...]
 *   - message: Сообщения об ошибках (если есть)
 */
function savePhoto($auth, $resDomain, $post, $files, $type)
{
    // Файл для логирования (отладка)
    $log = __DIR__ . "/logSavePhoto.txt";
    p($post, "start", $log);

    // Класс для изменения размера изображений
    $Resize = new Resize();

    // Формирование полного URL домена
    if (strpos($resDomain, 'https://') === 0) {
        $domain = $resDomain;
    } else {
        $domain = "https://" . $resDomain;
    }

    // ===================================
    // ШАГ 1: Получение корневой папки приложения на Disk
    // ===================================
    // Метод: disk.storage.getforapp
    // Возвращает корневую папку, выделенную приложению на Битрикс24 Disk
    // Каждое приложение имеет свою изолированную папку
    $storageResult = $auth->CScore->call('disk.storage.getforapp', []);
    $folderId = $storageResult["ROOT_OBJECT_ID"];

    if (empty($folderId)) {
        throw new \Exception('Не удалось получить папку приложения на Disk');
    }

    p($folderId, "folderId", $log);

    // ===================================
    // ШАГ 2: Подготовка массивов для обработки
    // ===================================
    $result = [];
    $arFiles = [];      // Результирующий массив файлов с учетом сортировки
    $sort = [];         // Массив порядка файлов
    $oldPhotoInfo = []; // Информация о существующих фотографиях
    $message = '';      // Сообщения об ошибках

    // Получение параметров из POST
    if ($type === 'photo') {
        // Загрузка новых файлов
        $sort = $post['sort'];              // Массив порядка: ["file1.jpg", "12345", "file2.jpg"]
        $oldPhotoInfo = $post['oldPhotoInfo']; // Существующие: ["12345,https://...", ...]
        $files['file'] = $files['files'];   // Нормализация массива файлов
    } else {
        // Только изменение порядка
        $sort = $post['sort'];
        $oldPhotoInfo = $post['oldPhotoInfo'];
    }

    // ===================================
    // ШАГ 3: Восстановление существующих фотографий
    // ===================================
    // Существующие фотографии передаются в формате "photo_id,photo_link"
    // Нужно восстановить их на правильных позициях согласно массиву sort
    if ($oldPhotoInfo) {
        p($oldPhotoInfo, "oldPhotoInfo", $log);

        foreach ($oldPhotoInfo as $el) {
            // Разбор строки "12345,https://..."
            $val = explode(',', $el);
            $photoId = $val[0];      // ID файла
            $photoLink = $val[1];    // Ссылка на файл

            // Поиск позиции этого файла в массиве sort
            $sortPosition = array_search($photoId, $sort);
            p($sortPosition, "sortPosition", $log);

            if ($sortPosition !== false) {
                // Сохранение файла на правильной позиции
                $arFiles[$sortPosition] = [
                    'photo_id' => $photoId,
                    'photo_link' => $photoLink,
                ];
            }
        }

        p($arFiles, "arFiles after old photos", $log);
    }

    // ===================================
    // ШАГ 4: Обработка новых загруженных файлов
    // ===================================
    if ($files && !empty($files['file']['name'])) {
        p($files['file'], "files to upload", $log);

        // Перебор всех загруженных файлов
        foreach ($files['file']['name'] as $key_files => $nameFile) {
            $tempFile = $files['file']['tmp_name'][$key_files]; // Временный файл
            $fileType = $files['file']['type'][$key_files];     // MIME тип

            // -----------------------------------
            // Изменение размера для изображений
            // -----------------------------------
            // PDF файлы не обрабатываем, загружаем как есть
            if ($fileType !== 'application/pdf') {
                try {
                    // Класс Resize оптимизирует изображение:
                    // - Уменьшает размер если слишком большое
                    // - Сохраняет соотношение сторон
                    // - Уменьшает вес файла
                    $Resize->resizePhoto($tempFile, $nameFile);
                    p("Resized: $nameFile", "resize", $log);
                } catch (\Exception $e) {
                    p("Resize error: " . $e->getMessage(), "resize_error", $log);
                }
            }

            // -----------------------------------
            // Конвертация в base64
            // -----------------------------------
            // REST API Битрикс24 принимает файлы в base64 формате
            $fileContent = file_get_contents($tempFile);
            $base64 = base64_encode($fileContent);

            // -----------------------------------
            // Загрузка файла на Битрикс24 Disk
            // -----------------------------------
            // Метод: disk.folder.uploadfile
            // Параметры:
            //   - id: ID папки назначения
            //   - generateUniqueName: генерировать уникальное имя если файл существует
            //   - fileContent: массив [имя_файла, base64_контент]
            //   - data: дополнительные данные (NAME)
            $uploadParams = [
                "id" => $folderId,
                "generateUniqueName" => 'Y',
                "fileContent" => [$nameFile, trim($base64)],
                "data" => ["NAME" => $nameFile]
            ];

            p($uploadParams, "uploadParams", $log);

            // Выполнение загрузки
            $uploadImageResult = $auth->CScore->call('disk.folder.uploadfile', $uploadParams);
            p($uploadImageResult, "uploadImageResult", $log);

            // Проверка результата загрузки
            if (empty($uploadImageResult["ID"])) {
                $message .= " Ошибка загрузки файла на диск: {$nameFile}";
                continue;
            }

            $newFileId = $uploadImageResult["ID"];
            $downloadLink = '';

            // -----------------------------------
            // Получение публичной ссылки на файл
            // -----------------------------------
            // В зависимости от настроек портала, файл может сразу иметь CONTENT_URL
            // Если нет, нужно получить публичную ссылку через disk.file.getExternalLink
            if (!empty($uploadImageResult["CONTENT_URL"])) {
                // Ссылка уже есть в результате загрузки
                $downloadLink = $uploadImageResult["CONTENT_URL"];
            } else {
                // Нужно получить публичную ссылку
                // Метод: disk.file.getExternalLink
                // Возвращает HTML страницу с ссылкой на скачивание
                $extLinkParams = ["id" => $newFileId];
                p($extLinkParams, "extLinkParams", $log);

                $ExtLinkResult = $auth->CScore->call('disk.file.getExternalLink', $extLinkParams);
                p($ExtLinkResult, "ExtLinkResult", $log);

                if (!empty($ExtLinkResult[0])) {
                    // Парсинг HTML для извлечения ссылки на скачивание
                    $html = parse($ExtLinkResult[0]);

                    // Регулярное выражение для поиска download ссылки с token
                    // Формат: href="/disk/download/?fileId=123&token=abc..."
                    preg_match(
                        "/href=.(\/[\w\/?&]*download\/[\w\/?&]*token=\w*)/",
                        $html,
                        $arPregRes
                    );

                    if (!empty($arPregRes[1])) {
                        // Формирование полной ссылки
                        $downloadLink = $domain . $arPregRes[1];
                    }
                }
            }

            // Проверка успешности получения ссылки
            if (empty($downloadLink)) {
                $message .= " Ошибка получения ссылки для файла: {$nameFile}";
                continue;
            }

            // -----------------------------------
            // Сохранение результата с учетом позиции
            // -----------------------------------
            // Находим позицию этого файла в массиве sort
            $sortPosition = array_search($nameFile, $sort);

            if ($sortPosition !== false) {
                $arFiles[$sortPosition] = [
                    'photo_id' => $newFileId,
                    'photo_link' => $downloadLink,
                ];
                p("Added to position $sortPosition", "position", $log);
            }
        }
    } else {
        $result['_file'] = 'nothing';
        p('No files to upload', 'no_files', $log);
    }

    // ===================================
    // ШАГ 5: Формирование финального результата
    // ===================================
    // Сортировка массива по ключам (позициям)
    // Это обеспечивает правильный порядок файлов
    ksort($arFiles);

    // Формирование результата
    if (!empty($arFiles)) {
        // Кодирование массива в JSON строку
        // Формат: [{"photo_id":"12345","photo_link":"https://..."},...]
        $uploadResult["result"] = json_encode($arFiles);
    } else {
        $uploadResult["result"] = false;
    }

    $uploadResult["message"] = $message;

    p($uploadResult, "uploadResult", $log);

    return $uploadResult;
}

/**
 * Функция для получения HTML по URL
 * 
 * Используется для парсинга результата disk.file.getExternalLink
 * Извлекает HTML страницу с публичной ссылкой на файл
 * 
 * @param string $url URL для запроса
 * @return string HTML содержимое страницы
 */
function parse($url)
{
    $curlOptions = [
        CURLOPT_FOLLOWLOCATION => true,  // Следовать редиректам
        CURLOPT_RETURNTRANSFER => true,  // Вернуть результат как строку
        CURLOPT_SSL_VERIFYPEER => false, // Не проверять SSL сертификат
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,     // Таймаут подключения 5 сек
        CURLOPT_TIMEOUT => 5,            // Таймаут запроса 5 сек
        CURLOPT_URL => $url
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $curlOptions);
    $curlResult = curl_exec($curl);
    curl_close($curl);

    return $curlResult;
}

/**
 * Вспомогательная функция для логирования
 * 
 * @param mixed $data Данные для логирования
 * @param string $label Метка для лога
 * @param string $logFile Путь к файлу лога
 */
function p($data, $label, $logFile)
{
    $logMessage = date('Y-m-d H:i:s') . " [{$label}]: " . print_r($data, true) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * ПРИМЕР ИСПОЛЬЗОВАНИЯ:
 * 
 * // 1. Инициализация авторизации
 * $auth = new \CSlibs\B24\Auth\Auth('scanDoc', $clientApp, $memberId);
 * 
 * // 2. Подготовка данных
 * $post = [
 *     'sort' => ['file1.jpg', '12345', 'file2.jpg'],
 *     'oldPhotoInfo' => ['12345,https://bitrix24.ru/disk/download/...']
 * ];
 * 
 * // 3. Вызов функции
 * $result = savePhoto($auth, 'company.bitrix24.ru', $post, $_FILES, 'photo');
 * 
 * // 4. Использование результата
 * if ($result['result']) {
 *     // Сохранение JSON в CRM поле
 *     $fields = ['UF_CRM_CS_SCAN_DOC' => $result['result']];
 *     $auth->CScore->call('crm.deal.update', ['id' => $dealId, 'fields' => $fields]);
 * }
 * 
 * // Формат result['result']:
 * // '[{"photo_id":"12345","photo_link":"https://..."},{"photo_id":"12346","photo_link":"https://..."}]'
 */
