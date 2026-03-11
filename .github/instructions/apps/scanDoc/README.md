# scanDoc - Приложение для загрузки документов в Битрикс24 CRM

## 📋 Обзор

**scanDoc** - приложение для Битрикс24 CRM, предназначенное для загрузки и управления отсканированными документами (фотографиями, PDF-файлами) в карточках CRM: сделках, контактах, компаниях и smart-процессах.

### Основная функциональность

- 📤 **Загрузка документов**: drag-and-drop загрузка файлов с помощью Dropzone.js
- 🖼️ **Галерея документов**: просмотр загруженных документов с Fancybox viewer
- 🔄 **Изменение изображений**: возможность изменить порядок, удалить или добавить новые
- 💾 **Хранение в CRM**: документы сохраняются в Битрикс24 Disk и ссылки в пользовательских полях
- 🎯 **Множественная поддержка**: работа с deal, contact, company, smart-процессами

### Текущий стек технологий

**Backend:**
- PHP 8.2-FPM
- Битрикс24 REST API
- Компоненты Битрикс24
- Библиотека CSlibs (Auth, HL)

**Frontend:**
- JavaScript (ES6+)
- jQuery 3.6.0
- Dropzone.js 5.x (drag-and-drop загрузка)
- Fancybox 3.x (просмотр галереи)
- jQuery UI (сортировка)

**Хранение:**
- Битрикс24 Disk (файловое хранилище)
- Пользовательские поля CRM (ссылки на файлы)

---

## 🏗️ Архитектура приложения

### Схема работы

```
┌─────────────────────────────────────────────────────────────┐
│                    Битрикс24 CRM                            │
│   (Сделка / Контакт / Компания / Smart-процесс)             │
└──────────────────────┬──────────────────────────────────────┘
                       │ Открытие приложения
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  scanDoc Component                          │
│  (component.php - получение данных из CRM)                  │
├─────────────────────────────────────────────────────────────┤
│  1. Определение типа сущности (deal/contact/company/smart)  │
│  2. Загрузка данных через crm.*.get / crm.item.get         │
│  3. Чтение UF_CRM_CS_SCAN_DOC (JSON массив ссылок)         │
│  4. Передача в template.php                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│               Template (template.php)                        │
│  ┌──────────────────────┬───────────────────────────────┐  │
│  │   Режим просмотра    │    Режим редактирования       │  │
│  │   (#galCard)         │         (#galForm)            │  │
│  ├──────────────────────┼───────────────────────────────┤  │
│  │  • Галерея Fancybox  │  • Dropzone.js drag-drop      │  │
│  │  • Превью документов │  • jQuery UI сортировка       │  │
│  │  • Кнопка "Изменить" │  • Кнопка "Сохранить"         │  │
│  └──────────────────────┴───────────────────────────────┘  │
└──────────────────────┬──────────────────────────────────────┘
                       │ Сохранение (AJAX)
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                Save Handler (save.php)                       │
├─────────────────────────────────────────────────────────────┤
│  1. Получение $_POST['authParams'] и $_FILES['files']      │
│  2. Вызов savePhoto() для обработки файлов                 │
│  3. Обновление CRM через crm.*.update / crm.item.update    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│          savePhoto() Function (savePhoto.php)                │
├─────────────────────────────────────────────────────────────┤
│  1. Изменение размера изображения (Resize class)           │
│  2. Конвертация в base64                                    │
│  3. Загрузка в Битрикс24 Disk (disk.folder.uploadfile)     │
│  4. Получение публичной ссылки (disk.file.getExternalLink)  │
│  5. Возврат JSON массива: [{photo_id, photo_link}, ...]    │
└─────────────────────────────────────────────────────────────┘
```

### Принципы работы

1. **Встраивание**: Приложение встраивается в карточки CRM через механизм placement
2. **Авторизация**: Аутентификация через Битрикс24 OAuth (access_token, refresh_token)
3. **Загрузка**: Файлы загружаются на Битрикс24 Disk через REST API
4. **Хранение**: Ссылки на файлы сохраняются в JSON формате в пользовательском поле
5. **Отображение**: Галерея отображается через Fancybox с возможностью просмотра

---

## 📂 Структура проекта

```
cassoftApp/market/scanDoc/
├── index.php                    # Редирект на документацию
├── install.php                  # Установка приложения
├── ajax/                        # AJAX обработчики
│   ├── appAuth.php             # Сохранение авторизации в HighLoad блок
│   ├── handler.php             # Обработчик событий (legacy)
│   ├── scanDocForm.php         # Обработчик формы
│   ├── function.php            # Вспомогательные функции
│   ├── OnImConnectorMessageAdd.php  # Коннектор (не используется)
│   └── OnImChatAdd.php         # Коннектор (не используется)
└── in/                          # Входящие запросы

local/components/scanDoc/base/
├── component.php                # Основной компонент (логика)
├── ajax/
│   ├── savePhoto.php           # Функция загрузки и обработки файлов
│   └── ajax.php                # Вспомогательные AJAX методы
└── templates/
    ├── deal/                    # Шаблон для сделок
    │   ├── template.php        # UI с Dropzone и Fancybox
    │   ├── save.php            # AJAX сохранение для сделок
    │   ├── save_function.php   # Вспомогательные функции
    │   ├── ajax.php            # AJAX методы
    │   └── script.js           # JavaScript логика
    ├── contact/                 # Шаблон для контактов
    │   ├── template.php
    │   ├── save.php
    │   ├── save_function.php
    │   ├── ajax.php
    │   └── script.js
    ├── company/                 # Шаблон для компаний
    │   ├── template.php
    │   ├── save.php
    │   ├── save_function.php
    │   ├── ajax.php
    │   └── script.js
    ├── smart/                   # Шаблон для smart-процессов
    │   ├── template.php
    │   ├── save.php
    │   └── script.js
    └── crm/                     # Универсальный CRM шаблон
        ├── template.php
        └── save.php
```

### Ключевые файлы

- **component.php**: Определяет тип сущности, загружает данные из CRM, парсит JSON с фото
- **savePhoto.php**: Обрабатывает загрузку файлов, изменяет размер, загружает на Disk
- **template.php**: Отображает UI с двумя режимами (просмотр/редактирование)
- **save.php**: AJAX обработчик сохранения, вызывает savePhoto() и обновляет CRM
- **script.js**: Инициализирует Dropzone, обрабатывает сортировку, отправляет AJAX

---

## 🔌 Интеграция с Битрикс24

### REST API методы

#### 1. Получение данных сущности

**Для обычных CRM сущностей:**
```php
// Сделка
$deal = $auth->CScore->call('crm.deal.get', ['ID' => $dealId]);

// Контакт  
$contact = $auth->CScore->call('crm.contact.get', ['ID' => $contactId]);

// Компания
$company = $auth->CScore->call('crm.company.get', ['ID' => $companyId]);
```

**Для smart-процессов:**
```php
// Получение типа smart-процесса
$smartType = $auth->CScore->call('crm.type.list', [
    'filter' => ['entityTypeId' => $entityTypeId]
]);

// Получение элемента smart-процесса
$smartItem = $auth->CScore->call('crm.item.get', [
    'entityTypeId' => $entityTypeId,
    'id' => $smartElId
]);
```

#### 2. Загрузка файлов на Disk

```php
// Получение корневой папки приложения
$folderId = $auth->CScore->call('disk.storage.getforapp', [])["ROOT_OBJECT_ID"];

// Загрузка файла
$result = $auth->CScore->call('disk.folder.uploadfile', [
    'id' => $folderId,
    'generateUniqueName' => 'Y',
    'fileContent' => [$filename, base64_encode($fileContent)],
    'data' => ['NAME' => $filename]
]);

// Получение публичной ссылки
$extLink = $auth->CScore->call('disk.file.getExternalLink', [
    'id' => $fileId
]);
```

#### 3. Обновление данных в CRM

**Для обычных сущностей:**
```php
// Обновление сделки
$result = $auth->CScore->call('crm.deal.update', [
    'id' => $dealId,
    'fields' => [
        'UF_CRM_CS_SCAN_DOC' => $jsonPhotos
    ]
]);
```

**Для smart-процессов:**
```php
// Имя поля динамическое: ufCrm{smartId}CsScanDoc
$fieldName = 'ufCrm' . $smartId . 'CsScanDoc';

$result = $auth->CScore->call('crm.item.update', [
    'entityTypeId' => $entityTypeId,
    'id' => $smartElId,
    'fields' => [
        $fieldName => $jsonPhotos
    ]
]);
```

### Формат данных UF_CRM_CS_SCAN_DOC

Пользовательское поле хранит JSON-массив объектов:

```json
[
  {
    "photo_id": "12345",
    "photo_link": "https://bitrix24.ru/disk/downloadFile/?token=abc123..."
  },
  {
    "photo_id": "12346",
    "photo_link": "https://bitrix24.ru/disk/downloadFile/?token=def456..."
  }
]
```

**Поля:**
- `photo_id` - ID файла в Битрикс24 Disk
- `photo_link` - Публичная ссылка для скачивания/просмотра

---

## 💻 Обзор кода

### 1. Основной компонент (component.php)

Определяет тип сущности и загружает данные:

```php
<?php
// Определение типа сущности
if (!empty($arParams['PLACEMENT_OPTIONS'])) {
    // Smart-процесс
    $smartInfo = json_decode($arParams['PLACEMENT_OPTIONS'], true);
    $smartElId = $smartInfo['ID'];
    $entityTypeId = $smartInfo['ENTITY_TYPE_ID'];
    
    // Получение данных smart-процесса
    $smartItem = $auth->CScore->call('crm.item.get', [
        'entityTypeId' => $entityTypeId,
        'id' => $smartElId
    ]);
    
    // Поле для smart-процесса динамическое
    $fieldName = 'ufCrm' . $smartId . 'CsScanDoc';
    $scanDoc = $smartItem[$fieldName];
    
} else {
    // Обычная CRM сущность (deal/contact/company)
    if ($arParams['deal_id']) {
        $deal = $auth->CScore->call('crm.deal.get', ['ID' => $arParams['deal_id']]);
        $scanDoc = $deal["UF_CRM_CS_SCAN_DOC"];
    }
    // аналогично для contact, company
}

// Парсинг JSON с фотографиями
if ($scanDoc) {
    $arResult["link"] = json_decode($scanDoc, true);
}
```

### 2. UI Template (template.php)

Два режима работы:

```php
<!-- Режим просмотра (galCard) -->
<div id="galCard" style="<?= $styleGalCard ?>">
    <div class="block-flex-row">
        <?php foreach ($arResult["link"] as $link): ?>
            <a data-fancybox="gallery" href="<?= $link['photo_link'] ?>">
                <div class="scan-doc-img">
                    <img src="<?= $link['photo_link'] ?>">
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <button onclick="scanEdit();">Изменить</button>
</div>

<!-- Режим редактирования (galForm) -->
<div id="galForm" style="<?= $styleGalForm ?>">
    <form id="scanForm">
        <!-- Dropzone drag-and-drop зона -->
        <div class="dropzone sortable" id="dropzone"></div>
        <button id="save" type="submit">Сохранить</button>
    </form>
</div>
```

### 3. Обработчик сохранения (save.php)

```php
<?php
// Получение параметров авторизации
$paramAuth = json_decode($_POST['authParams'], true);
$clientApp = [
    'DOMAIN' => $paramAuth['domain'],
    'member_id' => $paramAuth['member_id'],
    'AUTH_ID' => $paramAuth['access_token'],
    'REFRESH_ID' => $paramAuth['refresh_token'],
];

// Авторизация
$auth = new \CSlibs\B24\Auth\Auth($_POST['app'], $clientApp, "");

// Обработка файлов
if (!empty($_FILES['files'])) {
    // Новые файлы загружены
    $photo = savePhoto($auth, $paramAuth['member_id'], $_POST, $_FILES, 'photo');
} else {
    // Только изменение порядка существующих
    $photo = savePhoto($auth, $paramAuth['member_id'], $_POST, $_FILES, 'sort_photo');
}

// Обновление CRM
$paramsUp["UF_CRM_CS_SCAN_DOC"] = $photo['result'] ?: '';
$result = $auth->CScore->call("crm.deal.update", [
    'id' => $_POST['deal_id'],
    'fields' => $paramsUp
]);

echo json_encode($result);
```

### 4. Функция загрузки файлов (savePhoto.php)

```php
<?php
function savePhoto($auth, $resDomain, $post, $files, $type) {
    $Resize = new Resize();
    $domain = "https://" . $resDomain;
    
    // Получение папки приложения на Disk
    $folderId = $auth->CScore->call('disk.storage.getforapp', [])["ROOT_OBJECT_ID"];
    
    $arFiles = [];
    $sort = $post['sort']; // Массив имен файлов в нужном порядке
    $oldPhotoInfo = $post['oldPhotoInfo']; // Массив "id,link" существующих фото
    
    // Восстановление существующих фотографий
    if ($oldPhotoInfo) {
        foreach ($oldPhotoInfo as $el) {
            $val = explode(',', $el);
            $sortPosition = array_search($val[0], $sort);
            $arFiles[$sortPosition] = [
                'photo_id' => $val[0],
                'photo_link' => $val[1],
            ];
        }
    }
    
    // Обработка новых файлов
    if ($files) {
        foreach ($files['file']['name'] as $key_files => $nameFile) {
            $tempFile = $files['file']['tmp_name'][$key_files];
            
            // Изменение размера для изображений (не PDF)
            if ($files['file']['type'][$key_files] !== 'application/pdf') {
                $Resize->resizePhoto($tempFile, $nameFile);
            }
            
            // Конвертация в base64
            $base64 = base64_encode(file_get_contents($tempFile));
            
            // Загрузка на Битрикс24 Disk
            $uploadResult = $auth->CScore->call('disk.folder.uploadfile', [
                'id' => $folderId,
                'generateUniqueName' => 'Y',
                'fileContent' => [$nameFile, trim($base64)],
                'data' => ['NAME' => $nameFile]
            ]);
            
            $newFileId = $uploadResult["ID"];
            
            // Получение публичной ссылки
            if (!$uploadResult["CONTENT_URL"]) {
                $extLinkResult = $auth->CScore->call('disk.file.getExternalLink', [
                    'id' => $newFileId
                ]);
                
                // Парсинг HTML для получения ссылки на скачивание
                $html = parse($extLinkResult[0]);
                preg_match("/href=.(\/[\w\/?&]*download\/[\w\/?&]*token=\w*)/", $html, $arPregRes);
                $downloadLink = $domain . $arPregRes[1];
            } else {
                $downloadLink = $uploadResult["CONTENT_URL"];
            }
            
            // Добавление в массив результатов согласно порядку
            $sortPosition = array_search($nameFile, $sort);
            $arFiles[$sortPosition] = [
                'photo_id' => $newFileId,
                'photo_link' => $downloadLink,
            ];
        }
    }
    
    // Сортировка по ключам (позициям)
    ksort($arFiles);
    
    return [
        'result' => !empty($arFiles) ? json_encode($arFiles) : false,
        'message' => $message
    ];
}
```

### 5. JavaScript (script.js)

```javascript
// Инициализация Dropzone
var myDropzone = new Dropzone("#dropzone", {
    url: "/local/components/scanDoc/base/templates/deal/save.php",
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 100,
    maxFiles: 100,
    addRemoveLinks: true,
    dictRemoveFile: "Удалить",
    dictCancelUpload: "Отменить",
    acceptedFiles: "image/*,application/pdf",
    
    init: function() {
        var submitButton = document.querySelector("#save");
        var dzInstance = this;
        
        // Обработка отправки формы
        submitButton.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Сбор данных для сортировки
            var sortArray = [];
            var oldPhotoArray = [];
            
            // Существующие фотографии
            $(".scan-doc-img").each(function() {
                var photoId = $(this).data('photo-id');
                var photoLink = $(this).data('photo-link');
                sortArray.push(photoId);
                oldPhotoArray.push(photoId + ',' + photoLink);
            });
            
            // Новые файлы
            dzInstance.files.forEach(function(file) {
                sortArray.push(file.name);
            });
            
            // Отправка через FormData
            var formData = new FormData();
            formData.append('authParams', JSON.stringify(BX24.getAuth()));
            formData.append('app', $('#app').val());
            formData.append('deal_id', $('#deal_id').val());
            formData.append('sort', sortArray);
            formData.append('oldPhotoInfo', oldPhotoArray);
            
            // Добавление файлов
            dzInstance.files.forEach(function(file) {
                formData.append('files[name][]', file.name);
                formData.append('files[tmp_name][]', file);
            });
            
            // AJAX запрос
            $.ajax({
                url: '/local/components/scanDoc/base/templates/deal/save.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Saved:', response);
                    BX24.closeApplication(); // Закрытие приложения
                }
            });
        });
    }
});

// jQuery UI Sortable для сортировки фотографий
$(".sortable").sortable({
    placeholder: "ui-state-highlight",
    update: function(event, ui) {
        // Обновление порядка при drag-and-drop
    }
});

// Переключение между режимами
function scanEdit() {
    $("#galCard").hide();
    $("#galForm").show();
}
```

---

## 🔧 Технические детали

### Обработка изображений

**Класс Resize** (CSlibs/tools/resize.php):
- Автоматическое изменение размера больших изображений
- Оптимизация для веб (максимальная ширина/высота)
- Сохранение соотношения сторон
- Не применяется к PDF файлам

### Авторизация

**Механизм:**
1. Битрикс24 передает access_token и refresh_token при открытии приложения
2. Данные сохраняются в HighLoad блок `app_scanDoc_access` (через appAuth.php)
3. При AJAX запросах токены передаются в параметре `authParams`
4. Класс `CSlibs\B24\Auth\Auth` автоматически обновляет токены при необходимости

**Хранение токенов (HighLoad блок):**
```php
$params = [
    'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $memberId,
    'UF_CS_CLIENT_PORTAL_DOMEN' => $domain,
    'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $accessToken,
    'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $refreshToken,
    'UF_DATE_UP' => date("d.m.YTH:i:s"),
    'UF_ACTIVE' => 1,
];
```

### Поддержка Smart-процессов

**Особенности:**
- `entityTypeId` определяет тип smart-процесса (динамический)
- Имя поля пользовательское: `ufCrm{smartId}CsScanDoc` (например: `ufCrm1055CsScanDoc`)
- Используются методы `crm.type.list` и `crm.item.get/update`
- Smart-процессы передают данные через `PLACEMENT_OPTIONS`

### Производительность

**Оптимизация загрузки:**
- Изменение размера изображений перед загрузкой на Disk
- Кеширование публичных ссылок (не запрашиваются повторно)
- Parallel uploads через Dropzone (до 100 файлов одновременно)

**Ограничения:**
- Максимум 100 файлов за раз
- Поддержка: изображения (image/*) и PDF (application/pdf)
- Размер файлов ограничен настройками PHP (upload_max_filesize)

---

## 🚀 Миграция на Vue + Nuxt

### Стратегия миграции

**Этап 1: Анализ и подготовка (8 часов)**
- ✅ Документирование текущей архитектуры
- Определение зависимостей и API методов
- Выбор библиотек для Vue (vue-dropzone, vue-fancybox)
- Проектирование компонентной структуры

**Этап 2: Создание API слоя (12 часов)**
- Абстракция над Bitrix24 REST API (TypeScript)
- Сервисы для работы с CRM (DealService, ContactService, CompanyService, SmartService)
- Сервис загрузки файлов (FileUploadService)
- Управление авторизацией (AuthStore)

**Этап 3: Разработка Vue компонентов (24 часа)**
- Основной компонент приложения (ScanDocApp.vue)
- Компонент галереи (DocumentGallery.vue)
- Компонент загрузки (DocumentUploader.vue)
- Компонент просмотра (DocumentViewer.vue)

**Этап 4: Интеграция с Битрикс24 (12 часов)**
- Обработка placement параметров
- Интеграция BX24.js SDK
- Управление lifecycle приложения
- Тестирование в разных сущностях CRM

**Этап 5: Тестирование и оптимизация (8 часов)**
- Функциональное тестирование
- Тестирование производительности загрузки
- Кроссбраузерное тестирование
- Оптимизация bundle size

**Общая оценка: 64 часа (8 рабочих дней)**

### Целевая архитектура Vue

```
cs-app/scanDoc/
├── nuxt.config.ts               # Конфигурация Nuxt
├── package.json                 # Зависимости
├── tsconfig.json                # TypeScript конфиг
├── app/
│   ├── app.vue                  # Корневой компонент
│   ├── components/
│   │   ├── ScanDocApp.vue       # Главный компонент приложения
│   │   ├── DocumentGallery.vue  # Галерея документов
│   │   ├── DocumentUploader.vue # Загрузчик с drag-and-drop
│   │   ├── DocumentViewer.vue   # Просмотр документа (Fancybox)
│   │   └── DocumentCard.vue     # Карточка одного документа
│   ├── composables/
│   │   ├── useBitrix24.ts       # Composable для работы с BX24
│   │   ├── useFileUpload.ts     # Composable для загрузки файлов
│   │   ├── useCrmEntity.ts      # Composable для CRM сущностей
│   │   └── useDocumentGallery.ts # Composable для галереи
│   ├── services/
│   │   ├── bitrix24/
│   │   │   ├── BitrixApiClient.ts    # REST API клиент
│   │   │   ├── DealService.ts        # Работа со сделками
│   │   │   ├── ContactService.ts     # Работа с контактами
│   │   │   ├── CompanyService.ts     # Работа с компаниями
│   │   │   ├── SmartService.ts       # Работа со smart-процессами
│   │   │   ├── DiskService.ts        # Работа с Disk
│   │   │   └── AuthService.ts        # Авторизация
│   │   └── image/
│   │       └── ImageProcessor.ts     # Обработка изображений
│   ├── stores/
│   │   ├── auth.ts              # Store авторизации
│   │   ├── documents.ts         # Store документов
│   │   └── crmEntity.ts         # Store текущей CRM сущности
│   └── types/
│       ├── bitrix24.ts          # Типы Битрикс24
│       ├── document.ts          # Типы документов
│       └── crmEntity.ts         # Типы CRM сущностей
└── server/
    └── api/
        ├── upload.post.ts       # API endpoint для загрузки
        └── proxy.post.ts        # Proxy для Битрикс24 API
```

### TypeScript типы

```typescript
// types/document.ts
export interface Document {
  id: string;
  link: string;
  name?: string;
  type?: 'image' | 'pdf';
  size?: number;
  uploadedAt?: Date;
}

export interface DocumentSort {
  oldPosition: number;
  newPosition: number;
}

// types/crmEntity.ts
export type CrmEntityType = 'deal' | 'contact' | 'company' | 'smart';

export interface CrmEntity {
  id: number;
  type: CrmEntityType;
  entityTypeId?: number; // Для smart-процессов
  smartId?: number;      // Для smart-процессов
}

export interface CrmDocumentField {
  fieldName: string;     // UF_CRM_CS_SCAN_DOC или ufCrm{smartId}CsScanDoc
  value: string;         // JSON строка с массивом документов
}

// types/bitrix24.ts
export interface Bitrix24Auth {
  domain: string;
  memberId: string;
  accessToken: string;
  refreshToken: string;
  expiresIn: number;
}

export interface Bitrix24PlacementOptions {
  ID?: number;              // Для обычных сущностей
  ENTITY_TYPE_ID?: number;  // Для smart-процессов
}

export interface DiskUploadResponse {
  ID: string;
  NAME: string;
  CONTENT_URL?: string;
  DOWNLOAD_URL?: string;
}
```

### Vue Composables

#### useBitrix24.ts
```typescript
import { ref, onMounted } from 'vue';
import type { Bitrix24Auth, Bitrix24PlacementOptions } from '~/types/bitrix24';

export const useBitrix24 = () => {
  const auth = ref<Bitrix24Auth | null>(null);
  const placementOptions = ref<Bitrix24PlacementOptions | null>(null);
  const isReady = ref(false);

  const init = () => {
    return new Promise<void>((resolve) => {
      BX24.init(() => {
        auth.value = BX24.getAuth();
        placementOptions.value = BX24.placement.info().options;
        isReady.value = true;
        resolve();
      });
    });
  };

  const callMethod = async <T = any>(
    method: string,
    params: Record<string, any> = {}
  ): Promise<T> => {
    return new Promise((resolve, reject) => {
      BX24.callMethod(method, params, (result: any) => {
        if (result.error()) {
          reject(result.error());
        } else {
          resolve(result.data());
        }
      });
    });
  };

  const closeApp = () => {
    BX24.closeApplication();
  };

  onMounted(() => {
    init();
  });

  return {
    auth,
    placementOptions,
    isReady,
    callMethod,
    closeApp,
    init,
  };
};
```

#### useFileUpload.ts
```typescript
import { ref } from 'vue';
import type { Document } from '~/types/document';

export const useFileUpload = () => {
  const files = ref<File[]>([]);
  const documents = ref<Document[]>([]);
  const isUploading = ref(false);
  const uploadProgress = ref(0);

  const addFiles = (newFiles: File[]) => {
    files.value.push(...newFiles);
  };

  const removeFile = (index: number) => {
    files.value.splice(index, 1);
  };

  const uploadFiles = async (
    diskService: any,
    folderId: string
  ): Promise<Document[]> => {
    isUploading.value = true;
    uploadProgress.value = 0;

    const uploaded: Document[] = [];
    const total = files.value.length;

    for (let i = 0; i < total; i++) {
      const file = files.value[i];
      
      try {
        // Изменение размера изображения если нужно
        const processedFile = await resizeImageIfNeeded(file);
        
        // Загрузка на Disk
        const result = await diskService.uploadFile(folderId, processedFile);
        
        uploaded.push({
          id: result.ID,
          link: result.CONTENT_URL || result.DOWNLOAD_URL,
          name: file.name,
          type: file.type.startsWith('image/') ? 'image' : 'pdf',
          size: file.size,
        });

        uploadProgress.value = Math.round(((i + 1) / total) * 100);
      } catch (error) {
        console.error(`Error uploading file ${file.name}:`, error);
      }
    }

    isUploading.value = false;
    files.value = [];
    documents.value.push(...uploaded);

    return uploaded;
  };

  const resizeImageIfNeeded = async (file: File): Promise<File> => {
    // Если PDF, возвращаем как есть
    if (file.type === 'application/pdf') {
      return file;
    }

    // Изменение размера изображения
    // TODO: Реализовать через canvas API или библиотеку
    return file;
  };

  return {
    files,
    documents,
    isUploading,
    uploadProgress,
    addFiles,
    removeFile,
    uploadFiles,
  };
};
```

#### useCrmEntity.ts
```typescript
import { ref, computed } from 'vue';
import type { CrmEntity, CrmEntityType, CrmDocumentField } from '~/types/crmEntity';
import type { Document } from '~/types/document';

export const useCrmEntity = () => {
  const entity = ref<CrmEntity | null>(null);
  const documents = ref<Document[]>([]);

  // Определение имени поля для документов
  const documentFieldName = computed<string>(() => {
    if (!entity.value) return '';

    if (entity.value.type === 'smart') {
      // Динамическое поле для smart-процессов
      return `ufCrm${entity.value.smartId}CsScanDoc`;
    }

    // Стандартное поле для deal, contact, company
    return 'UF_CRM_CS_SCAN_DOC';
  });

  // Загрузка данных сущности
  const loadEntity = async (
    type: CrmEntityType,
    id: number,
    crmService: any,
    placementOptions?: any
  ) => {
    try {
      let data: any;

      if (type === 'smart') {
        // Smart-процесс
        const entityTypeId = placementOptions?.ENTITY_TYPE_ID;
        data = await crmService.getSmartItem(entityTypeId, id);
        
        entity.value = {
          id,
          type: 'smart',
          entityTypeId,
          smartId: extractSmartId(data),
        };
      } else {
        // Обычная CRM сущность
        data = await crmService.getEntity(type, id);
        entity.value = { id, type };
      }

      // Парсинг документов из поля
      const fieldValue = data[documentFieldName.value];
      if (fieldValue) {
        documents.value = JSON.parse(fieldValue) as Document[];
      }
    } catch (error) {
      console.error('Error loading CRM entity:', error);
      throw error;
    }
  };

  // Сохранение документов в CRM
  const saveDocuments = async (
    crmService: any,
    newDocuments: Document[]
  ) => {
    if (!entity.value) return;

    const allDocuments = [...documents.value, ...newDocuments];
    const jsonValue = JSON.stringify(allDocuments);

    const fields: Record<string, any> = {
      [documentFieldName.value]: jsonValue,
    };

    try {
      if (entity.value.type === 'smart') {
        await crmService.updateSmartItem(
          entity.value.entityTypeId!,
          entity.value.id,
          fields
        );
      } else {
        await crmService.updateEntity(
          entity.value.type,
          entity.value.id,
          fields
        );
      }

      documents.value = allDocuments;
    } catch (error) {
      console.error('Error saving documents:', error);
      throw error;
    }
  };

  // Удаление документа
  const removeDocument = async (
    crmService: any,
    documentId: string
  ) => {
    documents.value = documents.value.filter(doc => doc.id !== documentId);
    await saveDocuments(crmService, []);
  };

  // Изменение порядка документов
  const reorderDocuments = async (
    crmService: any,
    oldIndex: number,
    newIndex: number
  ) => {
    const docs = [...documents.value];
    const [removed] = docs.splice(oldIndex, 1);
    docs.splice(newIndex, 0, removed);
    documents.value = docs;
    
    await saveDocuments(crmService, []);
  };

  const extractSmartId = (data: any): number => {
    // Извлечение smartId из данных smart-процесса
    // TODO: Реализовать логику
    return 0;
  };

  return {
    entity,
    documents,
    documentFieldName,
    loadEntity,
    saveDocuments,
    removeDocument,
    reorderDocuments,
  };
};
```

### Основные Vue компоненты

#### DocumentUploader.vue
```vue
<template>
  <div class="document-uploader">
    <div
      class="dropzone"
      :class="{ 'is-dragging': isDragging }"
      @drop.prevent="onDrop"
      @dragover.prevent="onDragOver"
      @dragleave.prevent="onDragLeave"
      @click="openFileDialog"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*,application/pdf"
        style="display: none"
        @change="onFileSelect"
      />
      
      <div v-if="files.length === 0" class="dropzone-placeholder">
        <i class="las la-cloud-upload-alt"></i>
        <p>Перетащите файлы сюда или нажмите для выбора</p>
        <small>Поддерживаются изображения и PDF</small>
      </div>

      <div v-else class="file-list">
        <TransitionGroup name="file">
          <div
            v-for="(file, index) in files"
            :key="file.name + index"
            class="file-item"
          >
            <img
              v-if="isImage(file)"
              :src="getPreviewUrl(file)"
              alt="preview"
              class="file-preview"
            />
            <div v-else class="file-icon">
              <i class="las la-file-pdf"></i>
            </div>
            
            <div class="file-info">
              <div class="file-name">{{ file.name }}</div>
              <div class="file-size">{{ formatFileSize(file.size) }}</div>
            </div>

            <button
              class="file-remove"
              @click.stop="removeFile(index)"
            >
              <i class="las la-times"></i>
            </button>
          </div>
        </TransitionGroup>
      </div>
    </div>

    <div v-if="isUploading" class="upload-progress">
      <div class="progress-bar">
        <div
          class="progress-fill"
          :style="{ width: uploadProgress + '%' }"
        ></div>
      </div>
      <div class="progress-text">
        Загрузка... {{ uploadProgress }}%
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
  files: File[];
  isUploading: boolean;
  uploadProgress: number;
}>();

const emit = defineEmits<{
  'add-files': [files: File[]];
  'remove-file': [index: number];
}>();

const fileInput = ref<HTMLInputElement>();
const isDragging = ref(false);

const onDrop = (e: DragEvent) => {
  isDragging.value = false;
  const files = Array.from(e.dataTransfer?.files || []);
  emit('add-files', files);
};

const onDragOver = () => {
  isDragging.value = true;
};

const onDragLeave = () => {
  isDragging.value = false;
};

const openFileDialog = () => {
  fileInput.value?.click();
};

const onFileSelect = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const files = Array.from(target.files || []);
  emit('add-files', files);
  target.value = ''; // Reset input
};

const removeFile = (index: number) => {
  emit('remove-file', index);
};

const isImage = (file: File) => {
  return file.type.startsWith('image/');
};

const getPreviewUrl = (file: File) => {
  return URL.createObjectURL(file);
};

const formatFileSize = (bytes: number) => {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};
</script>

<style scoped>
.document-uploader {
  width: 100%;
}

.dropzone {
  border: 2px dashed #cbd5e0;
  border-radius: 8px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s ease;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dropzone.is-dragging {
  border-color: #4299e1;
  background-color: #ebf8ff;
}

.dropzone-placeholder {
  text-align: center;
  color: #718096;
}

.dropzone-placeholder i {
  font-size: 48px;
  margin-bottom: 16px;
  display: block;
}

.file-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
  width: 100%;
}

.file-item {
  position: relative;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px;
  background: white;
}

.file-preview,
.file-icon {
  width: 100%;
  height: 100px;
  object-fit: cover;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7fafc;
}

.file-icon i {
  font-size: 48px;
  color: #e53e3e;
}

.file-info {
  margin-top: 8px;
}

.file-name {
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-size {
  font-size: 10px;
  color: #a0aec0;
}

.file-remove {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-progress {
  margin-top: 16px;
}

.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4299e1, #3182ce);
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  margin-top: 8px;
  font-size: 14px;
  color: #4a5568;
}

.file-enter-active,
.file-leave-active {
  transition: all 0.3s ease;
}

.file-enter-from,
.file-leave-to {
  opacity: 0;
  transform: scale(0.8);
}
</style>
```

#### DocumentGallery.vue
```vue
<template>
  <div class="document-gallery">
    <div v-if="documents.length === 0" class="empty-state">
      <i class="las la-folder-open"></i>
      <p>Документы отсутствуют</p>
    </div>

    <draggable
      v-else
      v-model="sortedDocuments"
      class="gallery-grid"
      item-key="id"
      @end="onSortEnd"
    >
      <template #item="{ element: doc, index }">
        <div class="gallery-item">
          <a
            :href="doc.link"
            :data-fancybox="galleryId"
            :data-caption="doc.name || ''"
            class="gallery-link"
          >
            <img
              v-if="isImage(doc)"
              :src="doc.link"
              :alt="doc.name || 'Document'"
              class="gallery-image"
            />
            <div v-else class="gallery-pdf">
              <i class="las la-file-pdf"></i>
              <span>{{ doc.name }}</span>
            </div>
          </a>

          <button
            v-if="editable"
            class="gallery-remove"
            @click="removeDocument(doc.id)"
          >
            <i class="las la-times"></i>
          </button>

          <div v-if="sortable" class="gallery-drag-handle">
            <i class="las la-grip-vertical"></i>
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import draggable from 'vuedraggable';
import type { Document } from '~/types/document';

const props = withDefaults(
  defineProps<{
    documents: Document[];
    editable?: boolean;
    sortable?: boolean;
    galleryId?: string;
  }>(),
  {
    editable: false,
    sortable: false,
    galleryId: 'gallery',
  }
);

const emit = defineEmits<{
  'remove-document': [id: string];
  'reorder': [oldIndex: number, newIndex: number];
}>();

const sortedDocuments = computed({
  get: () => props.documents,
  set: (value) => {
    // Обработка изменения порядка
  },
});

const isImage = (doc: Document) => {
  return doc.type === 'image' || doc.link.match(/\.(jpg|jpeg|png|gif|webp)$/i);
};

const removeDocument = (id: string) => {
  emit('remove-document', id);
};

const onSortEnd = (evt: any) => {
  if (evt.oldIndex !== evt.newIndex) {
    emit('reorder', evt.oldIndex, evt.newIndex);
  }
};

onMounted(() => {
  // Инициализация Fancybox
  if (typeof window !== 'undefined' && (window as any).Fancybox) {
    (window as any).Fancybox.bind(`[data-fancybox="${props.galleryId}"]`, {
      Thumbs: {
        autoStart: true,
      },
    });
  }
});
</script>

<style scoped>
.document-gallery {
  width: 100%;
}

.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #a0aec0;
}

.empty-state i {
  font-size: 64px;
  margin-bottom: 16px;
  display: block;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
}

.gallery-item {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  background: white;
  transition: all 0.3s ease;
}

.gallery-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.gallery-link {
  display: block;
  width: 100%;
  height: 120px;
}

.gallery-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.gallery-pdf {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #f7fafc;
  padding: 8px;
  text-align: center;
}

.gallery-pdf i {
  font-size: 32px;
  color: #e53e3e;
  margin-bottom: 8px;
}

.gallery-pdf span {
  font-size: 10px;
  color: #4a5568;
  word-break: break-word;
}

.gallery-remove {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-remove {
  opacity: 1;
}

.gallery-drag-handle {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.6);
  color: white;
  padding: 8px;
  border-radius: 4px;
  cursor: move;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-drag-handle {
  opacity: 1;
}
</style>
```

#### ScanDocApp.vue (главный компонент)
```vue
<template>
  <div v-if="!isReady" class="loading">
    <div class="spinner"></div>
    <p>Загрузка...</p>
  </div>

  <div v-else class="scan-doc-app">
    <div v-if="viewMode === 'view'" class="view-mode">
      <DocumentGallery
        :documents="documents"
        :gallery-id="'scandoc-gallery'"
      />
      
      <div class="actions">
        <button
          class="btn btn-primary"
          @click="switchToEditMode"
        >
          Изменить
        </button>
      </div>
    </div>

    <div v-else class="edit-mode">
      <DocumentUploader
        :files="files"
        :is-uploading="isUploading"
        :upload-progress="uploadProgress"
        @add-files="addFiles"
        @remove-file="removeFile"
      />

      <div v-if="documents.length > 0" class="existing-documents">
        <h3>Загруженные документы</h3>
        <DocumentGallery
          :documents="documents"
          :editable="true"
          :sortable="true"
          gallery-id="scandoc-edit-gallery"
          @remove-document="removeDocument"
          @reorder="reorderDocuments"
        />
      </div>

      <div class="actions">
        <button
          class="btn btn-primary"
          :disabled="isUploading"
          @click="saveDocuments"
        >
          {{ isUploading ? 'Сохранение...' : 'Сохранить' }}
        </button>
        <button
          class="btn btn-secondary"
          :disabled="isUploading"
          @click="cancel"
        >
          Отмена
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useBitrix24 } from '~/composables/useBitrix24';
import { useCrmEntity } from '~/composables/useCrmEntity';
import { useFileUpload } from '~/composables/useFileUpload';
import DocumentGallery from './DocumentGallery.vue';
import DocumentUploader from './DocumentUploader.vue';

const { auth, placementOptions, isReady, closeApp } = useBitrix24();

const {
  entity,
  documents,
  loadEntity,
  saveDocuments: saveCrmDocuments,
  removeDocument: removeCrmDocument,
  reorderDocuments: reorderCrmDocuments,
} = useCrmEntity();

const {
  files,
  isUploading,
  uploadProgress,
  addFiles,
  removeFile,
  uploadFiles,
} = useFileUpload();

const viewMode = ref<'view' | 'edit'>('view');

const switchToEditMode = () => {
  viewMode.value = 'edit';
};

const saveDocuments = async () => {
  try {
    // TODO: Получить сервисы через DI
    const diskService = null; // DiskService
    const crmService = null;  // CrmService
    const folderId = ''; // Из disk.storage.getforapp

    // Загрузка новых файлов
    if (files.value.length > 0) {
      const uploaded = await uploadFiles(diskService, folderId);
      await saveCrmDocuments(crmService, uploaded);
    }

    // Закрытие приложения
    closeApp();
  } catch (error) {
    console.error('Error saving documents:', error);
    alert('Ошибка при сохранении документов');
  }
};

const removeDocument = async (id: string) => {
  try {
    const crmService = null; // TODO: Получить через DI
    await removeCrmDocument(crmService, id);
  } catch (error) {
    console.error('Error removing document:', error);
  }
};

const reorderDocuments = async (oldIndex: number, newIndex: number) => {
  try {
    const crmService = null; // TODO: Получить через DI
    await reorderCrmDocuments(crmService, oldIndex, newIndex);
  } catch (error) {
    console.error('Error reordering documents:', error);
  }
};

const cancel = () => {
  if (documents.value.length > 0) {
    viewMode.value = 'view';
  } else {
    closeApp();
  }
};

onMounted(async () => {
  try {
    // Определение типа сущности и ID
    let entityType = 'deal'; // TODO: Определить из query параметров
    let entityId = 0;        // TODO: Получить из query параметров

    if (placementOptions.value?.ENTITY_TYPE_ID) {
      entityType = 'smart';
      entityId = placementOptions.value.ID!;
    }

    // Загрузка данных сущности
    const crmService = null; // TODO: Создать сервис
    await loadEntity(entityType as any, entityId, crmService, placementOptions.value);

    // Режим просмотра если есть документы, иначе сразу редактирование
    viewMode.value = documents.value.length > 0 ? 'view' : 'edit';
  } catch (error) {
    console.error('Error initializing app:', error);
  }
});
</script>

<style scoped>
.scan-doc-app {
  padding: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #4299e1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.existing-documents {
  margin-top: 32px;
}

.existing-documents h3 {
  margin-bottom: 16px;
  font-size: 18px;
  font-weight: 600;
}

.actions {
  margin-top: 24px;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.btn {
  padding: 10px 24px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: #4299e1;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #3182ce;
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.btn-secondary:hover:not(:disabled) {
  background: #cbd5e0;
}
</style>
```

### Сервисы для работы с Битрикс24

#### DiskService.ts
```typescript
import type { DiskUploadResponse } from '~/types/bitrix24';

export class DiskService {
  constructor(private apiClient: any) {}

  async getAppFolder(): Promise<string> {
    const result = await this.apiClient.call('disk.storage.getforapp', {});
    return result.ROOT_OBJECT_ID;
  }

  async uploadFile(
    folderId: string,
    file: File
  ): Promise<DiskUploadResponse> {
    const base64 = await this.fileToBase64(file);

    const result = await this.apiClient.call('disk.folder.uploadfile', {
      id: folderId,
      generateUniqueName: 'Y',
      fileContent: [file.name, base64],
      data: { NAME: file.name },
    });

    // Если нет CONTENT_URL, получаем публичную ссылку
    if (!result.CONTENT_URL) {
      const extLink = await this.getExternalLink(result.ID);
      result.DOWNLOAD_URL = extLink;
    }

    return result;
  }

  async getExternalLink(fileId: string): Promise<string> {
    const result = await this.apiClient.call('disk.file.getExternalLink', {
      id: fileId,
    });

    // Парсинг HTML для получения download ссылки
    const parser = new DOMParser();
    const doc = parser.parseFromString(result[0], 'text/html');
    const link = doc.querySelector('a[href*="download"]');
    
    if (link) {
      return link.getAttribute('href') || '';
    }

    return '';
  }

  private fileToBase64(file: File): Promise<string> {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => {
        const base64 = (reader.result as string).split(',')[1];
        resolve(base64);
      };
      reader.onerror = reject;
    });
  }
}
```

#### CrmService.ts
```typescript
export class CrmService {
  constructor(private apiClient: any) {}

  async getEntity(type: 'deal' | 'contact' | 'company', id: number) {
    const method = `crm.${type}.get`;
    return await this.apiClient.call(method, { ID: id });
  }

  async updateEntity(
    type: 'deal' | 'contact' | 'company',
    id: number,
    fields: Record<string, any>
  ) {
    const method = `crm.${type}.update`;
    return await this.apiClient.call(method, { id, fields });
  }

  async getSmartItem(entityTypeId: number, id: number) {
    return await this.apiClient.call('crm.item.get', {
      entityTypeId,
      id,
    });
  }

  async updateSmartItem(
    entityTypeId: number,
    id: number,
    fields: Record<string, any>
  ) {
    return await this.apiClient.call('crm.item.update', {
      entityTypeId,
      id,
      fields,
    });
  }

  async getSmartType(entityTypeId: number) {
    return await this.apiClient.call('crm.type.list', {
      filter: { entityTypeId },
    });
  }
}
```

---

## 📊 Сравнение: Текущая vs Целевая архитектура

| Аспект | Текущая (PHP + jQuery) | Целевая (Vue + Nuxt) |
|--------|------------------------|----------------------|
| **Фреймворк** | jQuery 3.6 + Dropzone.js | Vue 3 + Nuxt 3 |
| **Язык** | JavaScript ES6 | TypeScript |
| **Загрузка файлов** | Dropzone.js (плагин) | vue-dropzone / Custom composable |
| **Галерея** | Fancybox 3.x | @fancyapps/ui (Vue wrapper) |
| **Состояние** | Глобальные переменные | Pinia Store |
| **Типизация** | Нет | TypeScript (полная) |
| **Реактивность** | DOM манипуляции | Реактивные ref/computed |
| **Компонентность** | Монолитный template | Модульные Vue компоненты |
| **API запросы** | BX24.callMethod callback | BX24.callMethod Promise wrapper |
| **Сортировка** | jQuery UI Sortable | vuedraggable |
| **Обработка изображений** | PHP Resize class | Client-side canvas API |
| **Bundle size** | ~300KB (все библиотеки) | ~150KB (tree-shaking) |

---

## 🎯 Преимущества миграции

### Для разработки
- ✅ **TypeScript**: Полная типизация, меньше ошибок
- ✅ **Модульность**: Переиспользуемые компоненты и composables
- ✅ **Композиция**: Логика разделена на composables
- ✅ **Тестирование**: Unit тесты для компонентов и логики
- ✅ **DevTools**: Vue DevTools для отладки

### Для производительности
- ✅ **Меньший bundle**: Tree-shaking и code splitting
- ✅ **Lazy loading**: Загрузка компонентов по требованию
- ✅ **Оптимизация рендеринга**: Virtual DOM
- ✅ **Client-side resize**: Обработка изображений на клиенте

### Для пользователя
- ✅ **Быстрее загрузка**: Оптимизированный bundle
- ✅ **Плавная анимация**: Transitions и animations
- ✅ **Лучший UX**: Интерактивные компоненты
- ✅ **Drag-and-drop**: Плавная сортировка

---

## 📝 Чек-лист миграции

### Подготовка
- [ ] Анализ текущего кода ✅
- [ ] Документирование API ✅
- [ ] Выбор библиотек для Vue ✅
- [ ] Настройка проекта Nuxt
- [ ] Настройка TypeScript конфигурации

### Backend/API
- [ ] Создание BitrixApiClient (REST wrapper)
- [ ] Реализация DiskService (загрузка файлов)
- [ ] Реализация CrmService (работа с CRM)
- [ ] Реализация AuthService (токены)
- [ ] Тестирование API слоя

### Frontend Core
- [ ] Создание Pinia stores (auth, documents, crmEntity)
- [ ] Реализация useBitrix24 composable
- [ ] Реализация useCrmEntity composable
- [ ] Реализация useFileUpload composable
- [ ] Реализация useDocumentGallery composable

### Components
- [ ] DocumentUploader.vue (drag-and-drop)
- [ ] DocumentGallery.vue (просмотр)
- [ ] DocumentViewer.vue (Fancybox)
- [ ] DocumentCard.vue (карточка файла)
- [ ] ScanDocApp.vue (главный компонент)

### Интеграция
- [ ] Обработка placement параметров
- [ ] Определение типа CRM сущности
- [ ] Загрузка данных из CRM
- [ ] Сохранение данных в CRM
- [ ] Закрытие приложения

### UI/UX
- [ ] Стилизация компонентов (CSS)
- [ ] Анимации (transitions)
- [ ] Responsive design
- [ ] Темная тема (опционально)
- [ ] Accessibility (a11y)

### Обработка изображений
- [ ] Client-side resize (canvas API)
- [ ] Генерация превью
- [ ] Оптимизация качества
- [ ] Прогресс загрузки

### Тестирование
- [ ] Unit тесты (Vitest)
- [ ] E2E тесты (Playwright)
- [ ] Тестирование в Битрикс24
- [ ] Тестирование на разных сущностях
- [ ] Performance тесты

### Оптимизация
- [ ] Code splitting
- [ ] Lazy loading компонентов
- [ ] Image optimization
- [ ] Bundle size анализ
- [ ] Lighthouse audit

### Развертывание
- [ ] Build для production
- [ ] Загрузка на сервер app.cassoft.ru
- [ ] Настройка reverse proxy (если нужен)
- [ ] SSL сертификаты
- [ ] Мониторинг ошибок (Sentry)

---

## 🔗 Полезные ресурсы

### Битрикс24 API
- [REST API документация](https://dev.1c-bitrix.ru/rest_help/)
- [CRM API](https://dev.1c-bitrix.ru/rest_help/crm/index.php)
- [Disk API](https://dev.1c-bitrix.ru/rest_help/disk/)
- [Smart-процессы API](https://dev.1c-bitrix.ru/rest_help/crm/dynamic/)

### Vue & Nuxt
- [Vue 3 документация](https://vuejs.org/)
- [Nuxt 3 документация](https://nuxt.com/)
- [Pinia документация](https://pinia.vuejs.org/)
- [TypeScript with Vue](https://vuejs.org/guide/typescript/overview.html)

### Библиотеки
- [vue-dropzone](https://github.com/rowanwins/vue-dropzone) - Drag-and-drop загрузка
- [@fancyapps/ui](https://fancyapps.com/docs/ui/quick-start) - Fancybox для Vue
- [vuedraggable](https://github.com/SortableJS/vue.draggable.next) - Drag-and-drop сортировка
- [browser-image-compression](https://github.com/Donaldcwl/browser-image-compression) - Сжатие изображений

### Инструменты
- [Vitest](https://vitest.dev/) - Unit тестирование
- [Playwright](https://playwright.dev/) - E2E тестирование
- [Vite Bundle Visualizer](https://github.com/btd/rollup-plugin-visualizer) - Анализ bundle

---

## 📧 Контакты и поддержка

**Проект:** scanDoc  
**Репозиторий:** cassoft-support/apps-all-old  
**Текущая версия:** PHP + jQuery  
**Целевая версия:** Vue 3 + Nuxt 3  

**Документация создана:** {{ now }}  
**Автор документации:** AI Assistant (GitHub Copilot)  
**Статус:** Готова к миграции

---

**Следующий шаг:** Создание Nuxt проекта и начало миграции (см. чек-лист выше)
