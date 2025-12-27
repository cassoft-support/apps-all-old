# Смена ответственного (Change Assigned)

> **Статус:** ✅ Активно, опубликовано  
> **Дата документирования:** 17 ноября 2025 г.

---

## 📋 Основная информация

### Название
**Смена ответственного** (Change Assigned)

### Статус
✅ Активно, опубликовано в Битрикс24 Marketplace

### Ссылки
- **Приложение:** https://app.cassoft.ru/cassoftApp/market/changeAssigned/index.php
- **Установка:** https://city.brokci.ru/cassoftApp/market/changeAssigned/install.php
- **Код приложения:** `change_assigned`
- **Highload блок:** `app_change_assigned_access`

### Описание

Часто при смене ответственного в Сделке, Лиде, Контакте или Компании сотрудники забывают обновить ответственного в связанных элементах CRM.

Приложение **"Смена ответственного"** решает эту проблему. Оно автоматически находит все связанные элементы и изменяет в них ответственного в соответствии с вашими настройками. Вы сами определяете, где необходимо менять ответственного, а где — нет.

По результатам, если приложение меняет ответственного в любой сущности CRM, оставляется комментарий в Таймлайне о том из какой сущности был запущен процесс (по умолчанию отключено).

### Поддерживаемые сущности CRM

Приложение работает с:
- ✅ Лидами
- ✅ Сделками  
- ✅ Контактами
- ✅ Компаниями
- ✅ Предложениями
- ✅ Счетами

**⚠️ Важно:** Приложение изменяет ответственного только в **активных** сущностях CRM. Если сделка завершена (успешно или неудачно), смена ответственного не произойдет, даже если такие сделки отмечены для обработки.

### Ключевые слова

лид, сделка, контакт, компания, предложения, счет, час софт, cassoft, brokci, смена ответственного

---

## 📁 Структура файлов и директорий

```
cassoftApp/market/changeAssigned/
│
├── index.php                    # Точка входа приложения
├── install.php                  # Скрипт установки приложения
│
├── ajax/                        # AJAX обработчики для событий CRM
│   ├── appAuth.php             # Авторизация приложения
│   ├── LeadUpdate.php          # Обработчик обновления Лида
│   ├── DealUpdate.php          # Обработчик обновления Сделки
│   ├── ContactUpdate.php       # Обработчик обновления Контакта
│   ├── CompanyUpdate.php       # Обработчик обновления Компании
│   ├── QuoteUpdate.php         # Обработчик обновления Предложения
│   ├── InvoiceUpdate.php       # Обработчик обновления Счета
│   └── sup.php                 # Вспомогательный файл
│
├── in/                          # Входящие webhooks (если используются)
│
└── logs/                        # Логи (создаются автоматически)
    ├── logContactUpdate.txt    # Лог обновлений
    ├── logLeadUpdate.txt       # Лог обновлений Лидов
    ├── logDealUpdate.txt       # Лог обновлений Сделок
    ├── logInstall.txt          # Лог установки
    └── logAjax.txt             # Лог AJAX запросов
```

### Компоненты в `/local/components/`

```
local/components/dashboard/main_app/templates/change_assigned/
│
├── template.php                 # Главный шаблон интерфейса
├── vertical.php                 # Вертикальный layout (если используется)
├── company.php                  # Шаблон для компаний (если используется)
│
├── script.js                    # JavaScript логика
├── ajax.php                     # AJAX роутер для переключения разделов
│
└── CSS стили:
    ├── menuDark.css            # Темная тема меню
    ├── menuLight.css           # Светлая тема меню
    └── menuMob.css             # Мобильное меню
```

---

## 🧩 Компоненты Битрикс24

### 1. Главный компонент: `dashboard:main_app`

**Путь:** `/local/components/dashboard/main_app/`  
**Шаблон:** `change_assigned`

**Параметры:**
```php
$arParams = [
    'app' => 'change_assigned',           // Код приложения
    'member_id' => $_REQUEST['member_id'], // ID установки Битрикс24
    'auth' => [
        'member_id' => $memberId
    ]
];
```

**Назначение:**  
Универсальный компонент для отображения интерфейса приложения

---

### 2. Компонент настроек: `settings:base`

**Шаблон:** `change_assigned`  
**Путь:** `/local/components/settings/base/templates/change_assigned/`

**Назначение:** Интерфейс общих настроек приложения - выбор сущностей для автоматической смены ответственного

**Вызов:**
```php
$APPLICATION->IncludeComponent(
    "settings:base",
    "change_assigned",
    $arParams,
    false
);
```

**Шаблон (template.php) - Полная структура настроек:**

**Подключаемые стили:**
```html
<link href="/local/lib/bootstrap/bootstrap.css" rel="stylesheet"/>
<link rel="stylesheet" href="/local/lib/bootstrap/font/bootstrap-icons.css">
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css">
<link rel="stylesheet" href="/local/lib/css/new/flex.css">
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css">
```

**Структура настроек:**

**1. Настройки для Лида (При смене ответственного в Лиде):**
```php
$lead = json_decode($arResult['PROP']['CS_LEAD'], true);
```
Чекбоксы:
- ☑️ Контакт
- ☑️ Компания
- ☑️ Предложение

**2. Настройки для Сделки (При смене ответственного в Сделке):**
```php
$deal = json_decode($arResult['PROP']['CS_DEAL'], true);
```
Чекбоксы:
- ☑️ Контакт
- ☑️ Компания
- ☑️ Счет
- ☑️ Предложение

**3. Настройки для Контакта:**
```php
$contact = json_decode($arResult['PROP']['CS_CONTACT'], true);
```
Чекбоксы:
- ☑️ Лид
- ☑️ Сделка
- ☑️ Компания
- ☑️ Счет
- ☑️ Предложение

**4. Настройки для Компании:**
```php
$company = json_decode($arResult['PROP']['CS_COMPANY'], true);
```
Чекбоксы:
- ☑️ Лид
- ☑️ Сделка
- ☑️ Контакт
- ☑️ Счет
- ☑️ Предложение

**5. Настройки для Предложения:**
```php
$quote = json_decode($arResult['PROP']['CS_QUOTE'], true);
```
Чекбоксы:
- ☑️ Лид
- ☑️ Сделка
- ☑️ Контакт
- ☑️ Компания
- ☑️ Счет

**6. Публикация в Таймлайн:**
```php
$commentAdd = $arResult['PROP']['CS_COMMENT_ADD'];
```
Чекбокс:
- ☑️ Публиковать комментарии об изменении в Таймлайн (по умолчанию выключено)

**Пример чекбокса:**
```html
<input name="contact" type="checkbox" class="cs-switch deal" 
    <?php echo ($deal['contact'] == 1) ? 'checked' : '';?> >
<span for="contact" class="add-label-check">Контакт</span>
```

**Кнопка сохранения:**
```html
<button class="form-small-button form-small-button-blue" type="submit" 
    onclick="entityUpdate(<?=$arResult['ID'] ?>);">Сохранить</button>
```

**JavaScript для сохранения:**
- Файл: `/local/components/settings/base/templates/change_assigned/script.js`
- Функция `entityUpdate()` отправляет AJAX запрос на сохранение настроек в Highload блок

**Используемые файлы:**
- `template.php` - HTML интерфейс настроек
- `script.js` - сохранение настроек через AJAX
- `style_my.css` - кастомные стили

---

### 3. Компонент справки: `helpdesk:base`

**Шаблон:** `change_assigned`  
**Путь:** `/local/components/helpdesk/base/templates/change_assigned/`

**Назначение:** Главная страница с инструкциями и справкой (iframe с документацией)

**Вызов:**
```php
$APPLICATION->IncludeComponent(
    "helpdesk:base",
    "change_assigned",
    $arParams,
    false
);
```

**Шаблон (template.php):**
```html
<style>
    #content-frame {
        width: 100%;
        height: 80vh;
        border: none;
    }
</style>

<iframe id="content-frame" 
    src="https://apps-doc.cassoft.ru/changeassigned/app_change_assigned/">
</iframe>
```

**Особенности:**
- Отображает внешнюю документацию в iframe
- Ссылка на документацию: `https://apps-doc.cassoft.ru/changeassigned/app_change_assigned/`
- Высота iframe: 80vh (80% высоты экрана)
- Без рамки для бесшовной интеграции

**Используемые файлы:**
- `template.php` - HTML с iframe
- `script.js` - JavaScript (если есть дополнительная логика)
- `style_my.css` - кастомные стили

---

### 4. Компонент поддержки: `support:change_assigned`

**Шаблон:** `admin`

**Вызов:**
```php
$APPLICATION->IncludeComponent(
    "support:change_assigned",
    "admin",
    $arParams,
    false
);
```

**Назначение:**  
Административная панель поддержки (только для разработчиков)

---

### 5. Компоненты событий CRM

Обрабатывают изменения ответственного в различных сущностях:

#### `event:deal_update` (шаблон: `change_assigned`)

**Путь компонента:** `/local/components/event/deal_update/`

**Назначение:** Обработка обновления Сделки и смена ответственного в связанных элементах

**Компонент (component.php):**
```php
// Логирование запроса
$log = __DIR__ . "/component.txt";
file_put_contents($log, print_r($_REQUEST, true), FILE_APPEND);

// Передача данных в шаблон
$arResult['req'] = json_encode($_REQUEST);
$this->IncludeComponentTemplate();
```

**Шаблон (template.php) - ПОЛНАЯ БИЗНЕС-ЛОГИКА:**

**1. Инициализация:**
```php
$id = $arParams['data']['FIELDS']['ID'];  // ID измененной сделки
$auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
```

**2. Получение настроек из Highload:**
```php
$resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0]['PROPERTY_VALUES'];
$setup = json_decode($resSetup['CS_DEAL'], true);  // Настройки для сделки
$commentAdd = $resSetup['CS_COMMENT_ADD'];          // Публиковать комментарии?
```

**3. Получение данных сделки:**
```php
$resElement = $auth->CScore->call('crm.item.get', [
    'entityTypeId' => 2,  // Deal
    'id' => $id
])['item'];
```

**4. Защита от слишком быстрого выполнения (race condition):**
```php
$timeCreate = strtotime($resElement['updatedTime']);
$date = strtotime(date('c'));
$dateMin = $date - $timeCreate;

if($dateMin < 10) {
    sleep(2);  // Пауза 2 секунды если обновление было меньше 10 секунд назад
}
```

**5. Получение данных пользователя:**
```php
$user = $auth->CScore->call('user.get', [
    'filter' => ['ID' => $arParams['auth']['user_id']]
])[0];

$assigned = $resElement['assignedById'];  // Новый ответственный
$changeAssigned = new \CSlibs\App\Assigned\changeAssigned($auth, $assigned);
```

**6. Формирование текста для комментария:**
```php
$name = "[B]сделки[/B] ID" . $resElement['ID'] . ": " .
        "[URL=/crm/deal/details/" . $resElement['id'] . "/]" . 
        $resElement['title'] . "[/URL], " .
        "пользователем " . $user['NAME'] . " " . $user['LAST_NAME'];
```

**7. Обработка связанных элементов:**

**Компания (если настройка включена и компания указана):**
```php
if (!empty($setup['company']) && !empty($resElement['companyId'])) {
    $filterCompany["id"] = $resElement['companyId'];
    $company = $changeAssigned->changeAssigned(
        $filterCompany, 
        'company',      // тип
        4,              // entityTypeId
        $user['ID'],    // автор комментария
        $name,          // текст
        $commentAdd     // публиковать ли
    );
}
```

**Контакт:**
```php
if (!empty($setup['contact']) && !empty($resElement['contactId'])) {
    $filterContact["id"] = $resElement['contactId'];
    $contact = $changeAssigned->changeAssigned($filterContact, 'contact', 3, $user['ID'], $name, $commentAdd);
}
```

**Счет (Invoice) - только активные:**
```php
// Получение активных статусов счетов
$statusInvoice = [];
foreach ($auth->CScore->call('crm.status.list') as $status) {
    if ($status['ENTITY_ID'] === 'SMART_INVOICE_STAGE_2' && 
        $status['SEMANTICS'] !== 'F' &&  // Не Failed
        $status['SEMANTICS'] !== 'S') {  // Не Success
        $statusInvoice[] = $status['STATUS_ID'];
    }
}

if (!empty($setup['invoice'])) {
    $filterInvoice['parentId2'] = $id;               // Привязка к сделке
    $filterInvoice["stageId"] = $statusInvoice;      // Только активные
    $invoice = $changeAssigned->changeAssigned($filterInvoice, 'invoice', 31, $user['ID'], $name, $commentAdd);
}
```

**Предложение (Quote) - только открытые:**
```php
if (!empty($setup['quote']) && !empty($resElement['quoteId'])) {
    $filterQuote['id'] = $resElement['quoteId'];
    $filterQuote["closed"] = 'N';  // Только открытые
    $quote = $changeAssigned->changeAssigned($filterQuote, 'quote', 7, $user['ID'], $name, $commentAdd);
}
```

**Используемые файлы компонента:**
- `/local/components/event/deal_update/component.php` - логирование
- `/local/components/event/deal_update/templates/change_assigned/template.php` - бизнес-логика
- `/local/components/event/deal_update/templates/change_assigned/script.js` - установка (не используется в событии)
- `/local/components/event/deal_update/templates/change_assigned/style.css` - стили (минимальные)

**Логи:**
- `/local/components/event/deal_update/component.txt` - лог входящих запросов
- `/local/components/event/deal_update/templates/change_assigned/logUpdate.txt` - детальный лог обработки
- `/local/components/event/deal_update/templates/change_assigned/logUpdateTime.txt` - лог тайминга (race condition)

#### `event:contact_update` (шаблон: `change_assigned`)

Аналогичная логика для Контакта:
- entityTypeId: 3
- Обрабатывает: Лиды, Сделки, Компании, Счета, Предложения

#### `event:company_update` (шаблон: `change_assigned`)

Аналогичная логика для Компании:
- entityTypeId: 4
- Обрабатывает: Лиды, Сделки, Контакты, Счета, Предложения

#### `event:lead_update` (шаблон: `change_assigned`)

Аналогичная логика для Лида:
- entityTypeId: 1
- Обрабатывает: Контакты, Компании, Предложения

#### `event:quote_update` (шаблон: `change_assigned`)

Аналогичная логика для Предложения:
- entityTypeId: 7
- Обрабатывает: Лиды, Сделки, Контакты, Компании, Счета

#### `event:invoice_update` (шаблон: `change_assigned`)

Аналогичная логика для Счета:
- entityTypeId: 31
- Обрабатывает связанные элементы

---

## 💻 Frontend (JavaScript/CSS)

### JavaScript: `script.js`

**Расположение:** `/local/components/dashboard/main_app/templates/change_assigned/script.js`

#### Основная логика

**1. Инициализация при загрузке:**
```javascript
$(document).ready(function () {
    let Width = window.screen.width
    if (Width > 400) {
        switchTemplate('helpdesk');  // Десктоп: показать справку
        BX24.init(function () {
            // Инициализация Битрикс24 JS API
        });
    } else {
        switchTemplate('chartsMob');  // Мобильная версия
    }
});
```

**2. Переключение разделов меню:**
```javascript
$('.click').on('click', function () {
    let type = $(this).attr('value')  // helpdesk, general_settings, support
    switchTemplate(type)
})
```

**3. Функция переключения шаблонов (AJAX):**
```javascript
function switchTemplate(type) {
    BX24.init(function () {
        auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/dashboard/main_app/templates/change_assigned/ajax.php",
            data: {
                auth: auth,
                member_id: $('#member_id').val(),
                app: $('#appCode').val(),
                type: type,                      // helpdesk | general_settings | support
                user_id: $('#user_id').val(),
            },
            dataType: "html",
            success: function (response) {
                $('#main').empty()
                $('#main').html(response)
            },
            error: function (data) {
                console.log(data)
            },
        })
    })
}
```

#### Используемые библиотеки

- **jQuery** - DOM манипуляции и AJAX
- **BX24 JS API** - интеграция с Битрикс24

---

### CSS стили

#### Подключаемые библиотеки CSS (из template.php):

**Bootstrap и таблицы:**
```html
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap.css"/>
<link rel="stylesheet" href="/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css"/>
```

**Иконки и шрифты:**
```html
<link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
```

**Cassoft библиотеки:**
```html
<link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cassoft.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/cs-root-blue.css">
<link rel="stylesheet" href="/local/lib/css/cassoft/panel.css"/>
<link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css?020223"/>
```

**Селекторы и UI:**
```html
<link rel="stylesheet" href="/local/lib/chosen/chosen.min.css"/>
<link rel="stylesheet" href="/local/lib/css/new/menuWhite.css"/>
<link rel="stylesheet" href="/local/components/dashboard/main_app/templates/accountant/menuMob.css"/>
```

#### 1. `menuDark.css` - Темная тема меню
Стили для темного интерфейса меню приложения

#### 2. `menuLight.css` - Светлая тема меню
Стили для светлого интерфейса меню приложения

#### 3. `menuMob.css` - Мобильное меню
Адаптивные стили для мобильной версии

#### Общие стили в `template.php`:
```css
/* Скрытие на десктопе */
.mobile {
    display: none;
}

/* Мобильная адаптация (до 400px) */
@media (max-width: 400px) {
    .mobile {
        display: block;
    }
    .menu-mob {
        display: block !important;
    }
    .menu-box {
        display: none !important;
    }
    .dashboard-element {
        min-width: 100% !important;
        max-width: 500px;
    }
}

/* Основной контейнер */
.main-block {
    width: 99%;
    overflow: hidden;
}
.container-block {
    padding: 10px;
    width: auto;
}
```

#### Подключаемые JavaScript библиотеки (из template.php):

**jQuery и UI:**
```html
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
```

**Битрикс24 и утилиты:**
```html
<script defer src="//api.bitrix24.com/api/v1/"></script>
<script defer src="/local/lib/js/jquery.maskedinput.js"></script>
<script src="/local/lib/js/cleave.min.js"></script>
<script src="/local/lib/js/moment.min.js"></script>
```

**Селекторы и UI:**
```html
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
```

**Приложение:**
```html
<script defer src="/local/components/dashboard/main_app/templates/change_assigned/script.js"></script>
```

**Встроенный скрипт:**
```javascript
function resizeFrame() {
    var currentSize = BX24.getScrollSize();
    minHeight = currentSize.scrollHeight;
    var FrameWidth = document.getElementById("workarea").offsetWidth;
    
    if (minHeight < 300) {
        frameHeight = 300;
    } else {
        frameHeight = minHeight + 100;
    }
    
    BX24.resizeWindow(FrameWidth, frameHeight);
}

$(document).ready(function () {
    BX24.init(function(){
        resizeFrame();
    });
});
```

---

## ⚙️ Backend (PHP)

### 1. Точка входа: `index.php`

**Путь:** `cassoftApp/market/changeAssigned/index.php`

**Логика:**
```php
<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Логирование запроса
$log = __DIR__."/logContactUpdate.txt";
p($_REQUEST, date('c'), $log);

$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = "change_assigned";
    $appAccess = 'app_' . $CloudApp . '_access';
    
    // Проверка доступа через Highload блок
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    
    if ($clientsApp["ID"] > 0) {
        // Доступ разрешен - показать приложение
        $arParams = $_REQUEST;
        $arParams['app'] = $CloudApp;
        $arParams['auth']['member_id'] = $memberId;
        
        $APPLICATION->IncludeComponent(
            "dashboard:main_app",
            "change_assigned",
            $arParams,
            false
        );
    } else {
        // Доступ запрещен - показать сообщение о переустановке
        ?>
        <div class="no-app">
           <div class="no-app-title">
               Приложение надо переустановить, обратитесь к вашему Администратору.
           </div>
            <div class="no-app-subtitle">
                или напишите нам в чат в этом окне
            </div>
        </div>
        <script>
        (function(w,d,u){
            var s=d.createElement('script');s.async=true;
            s.src=u+'?'+(Date.now()/60000|0);
            var h=d.getElementsByTagName('script')[0];
            h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn-ru.bitrix24.ru/b9950371/crm/site_button/loader_5_9bynjt.js');
        </script>
        <?php
    }
}
```

---

### 2. Установка: `install.php`

**Путь:** `cassoftApp/market/changeAssigned/install.php`

**Логика:**
```php
<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logInstall.txt";
p($_REQUEST, "start", $log);

if($_REQUEST['member_id']) {
    $arParams = $_REQUEST;
    $arParams['app'] = 'change_assigned';  // Здесь ошибка в коде: написано 'cian_messager'
    $arParams['appType'] = 'change_assigned';
    
    $APPLICATION->IncludeComponent(
        "install:base",
        "cianMessager",  // Шаблон установки (возможно универсальный)
        $arParams,
        false
    );
}
```

**⚠️ Замечание:** В коде обнаружена ошибка - `$arParams['app']` указан как `'cian_messager'` вместо `'change_assigned'`. Это может быть копипаста из другого приложения.

---

### 3. AJAX роутер: `ajax.php`

**Путь:** `/local/components/dashboard/main_app/templates/change_assigned/ajax.php`

**Назначение:** Обработка AJAX запросов и переключение между разделами приложения

**Логика:**
```php
<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logAjax.txt';
p($_POST, "start", $log);

if (!empty($_REQUEST)) {
    $arParams = $_REQUEST;
    session_start();
    $_SESSION['request'] = serialize($_REQUEST['auth']);

    switch ($arParams['type']) {
        
        case 'support':
            // Админ-панель поддержки
            $APPLICATION->IncludeComponent(
                "support:change_assigned",
                "admin",
                $arParams,
                false
            );
            break;

        case 'general_settings':
            // Общие настройки приложения
            $APPLICATION->IncludeComponent(
                "settings:base",
                "change_assigned",
                $arParams,
                false
            );
            break;
            
        case 'helpdesk':
            // Справка и главная страница
            $APPLICATION->IncludeComponent(
                "helpdesk:base",
                "change_assigned",
                $arParams,
                false
            );
            break;

        default:
            break;
    }
}
```

---

### 4. AJAX обработчики событий CRM

**Общая структура всех обработчиков:**

#### `ajax/LeadUpdate.php` - Обновление Лида
```php
<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__."/logLeadUpdate.txt";
p($_REQUEST, "start", $log);

$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $CloudApp = "change_assigned";
    $appAccess = 'app_' . $CloudApp . '_access';
    
    // Проверка доступа
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    
    if ($clientsApp["ID"] > 0) {
        $arParams = $_REQUEST;
        $arParams['app'] = $CloudApp;
        
        // Вызов компонента обработки события
        $APPLICATION->IncludeComponent(
            "event:lead_update",
            "change_assigned",
            $arParams,
            false
        );
    }
}
```

#### Аналогичные файлы:
- `ajax/DealUpdate.php` → компонент `event:deal_update`
- `ajax/ContactUpdate.php` → компонент `event:contact_update`
- `ajax/CompanyUpdate.php` → компонент `event:company_update`
- `ajax/QuoteUpdate.php` → компонент `event:quote_update`
- `ajax/InvoiceUpdate.php` → компонент `event:invoice_update`

---

## 🔌 Endpoints (точки входа)

### Публичные endpoints

| Endpoint | Метод | Назначение |
|----------|-------|-----------|
| `/cassoftApp/market/changeAssigned/index.php` | GET/POST | Главная страница приложения |
| `/cassoftApp/market/changeAssigned/install.php` | GET/POST | Установка приложения |

### AJAX endpoints (требуют авторизации)

| Endpoint | Метод | Параметры | Назначение |
|----------|-------|-----------|-----------|
| `/cassoftApp/market/changeAssigned/ajax/LeadUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Лида |
| `/cassoftApp/market/changeAssigned/ajax/DealUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Сделки |
| `/cassoftApp/market/changeAssigned/ajax/ContactUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Контакта |
| `/cassoftApp/market/changeAssigned/ajax/CompanyUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Компании |
| `/cassoftApp/market/changeAssigned/ajax/QuoteUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Предложения |
| `/cassoftApp/market/changeAssigned/ajax/InvoiceUpdate.php` | POST | `auth[member_id]`, `data` | Обработка обновления Счета |
| `/cassoftApp/market/changeAssigned/ajax/appAuth.php` | POST | - | Авторизация приложения |
| `/cassoftApp/market/changeAssigned/ajax/sup.php` | POST | - | Вспомогательный endpoint |

### Внутренние AJAX (компонента)

| Endpoint | Метод | Параметры | Назначение |
|----------|-------|-----------|-----------|
| `/local/components/dashboard/main_app/templates/change_assigned/ajax.php` | POST | `type`, `member_id`, `app`, `user_id`, `auth` | Роутер переключения разделов |

**Параметр `type` может быть:**
- `helpdesk` - справка/главная
- `general_settings` - общие настройки
- `support` - панель поддержки (только для разработчиков)

---

## 🔗 REST API интеграция

### Используемые методы Битрикс24 REST API

#### Универсальные методы CRM (новый API):

**Получение элемента:**
```php
$auth->CScore->call('crm.item.get', [
    'entityTypeId' => 2,  // 1=Lead, 2=Deal, 3=Contact, 4=Company, 7=Quote, 31=Invoice
    'id' => $id
]);
```

**Список элементов:**
```php
$auth->CScore->call('crm.item.list', [
    'entityTypeId' => $typeId,
    'filter' => $filter
]);
```

**Обновление элемента:**
```php
$auth->CScore->call('crm.item.update', [
    'entityTypeId' => $typeId,
    'id' => $id,
    'fields' => [
        'assignedById' => $newUserId
    ]
]);
```

#### Соответствие entityTypeId:
- `1` = Лид (Lead) → тип `l`
- `2` = Сделка (Deal) → тип `d`
- `3` = Контакт (Contact) → тип `c`
- `4` = Компания (Company) → тип `com`
- `7` = Предложение (Quote) → тип `q`
- `31` = Счет (Invoice) → тип `i`, entityType `dynamic_31`

#### Настройки приложения:

**Получение настроек из Highload:**
```php
$resSetup = $auth->CScore->call('entity.item.get', [
    'ENTITY' => 'setup'
])[0]['PROPERTY_VALUES'];

// Настройки для сделки (JSON)
$setup = json_decode($resSetup['CS_DEAL'], true);

// Публиковать комментарии (bool)
$commentAdd = $resSetup['CS_COMMENT_ADD'];
```

**Структура настроек (JSON):**
```json
{
    "contact": true,     // Менять ответственного в контактах
    "company": true,     // Менять ответственного в компаниях
    "invoice": true,     // Менять ответственного в счетах
    "quote": false       // Не менять в предложениях
}
```

#### Статусы (для фильтрации):

**Получение списка статусов:**
```php
$statuses = $auth->CScore->call('crm.status.list');

// Фильтр активных статусов лидов
foreach ($statuses as $status) {
    if ($status['ENTITY_ID'] === 'STATUS' && 
        $status['SEMANTICS'] !== 'F' &&  // Не проваленные
        $status['SEMANTICS'] !== 'S') {  // Не успешные
        $leadStatus[] = $status['STATUS_ID'];
    }
}

// Фильтр активных статусов счетов
foreach ($statuses as $status) {
    if ($status['ENTITY_ID'] === 'SMART_INVOICE_STAGE_2' && 
        $status['SEMANTICS'] !== 'F' && 
        $status['SEMANTICS'] !== 'S') {
        $statusInvoice[] = $status['STATUS_ID'];
    }
}
```

#### Timeline комментарии:

**Добавление комментария:**
```php
$comments = "🟢 Ответственный изменен автоматически из " . $name . 
            "[p](" . $typeGuide[$typeId] . "-" . $element['id'] . ")[/p]";

$auth->CScore->call("crm.timeline.comment.add", [
    'fields' => [
        "ENTITY_ID" => $element['id'],
        "ENTITY_TYPE" => $entityType,  // 'deal', 'contact', 'dynamic_31' и т.д.
        "COMMENT" => $comments,
        "AUTHOR_ID" => $user['ID']
    ]
]);
```

#### Пользователи:

**Получение пользователя:**
```php
$user = $auth->CScore->call('user.get', [
    'filter' => ['ID' => $userId]
])[0];
```

**Получение текущего пользователя:**
```php
$user = $auth->CScore->call('user.current');
```

### Класс changeAssigned

**Путь:** `/local/CSlibs/classes/app/assigned/changeAssigned.php`

**Namespace:** `CSlibs\App\Assigned`

**Конструктор:**
```php
public function __construct($auth, $assigned, $member='')
{
    $this->auth = $auth;           // Объект авторизации
    $this->assigned = $assigned;   // ID нового ответственного
    $this->member = $member;       // member_id (опционально)
}
```

**Метод changeAssigned:**
```php
public function changeAssigned($filter, $type, $typeId, $user, $name='', $commentAdd='')
{
    // $filter - фильтр для поиска элементов
    // $type - тип сущности ('deal', 'contact', 'company', и т.д.)
    // $typeId - ID типа сущности (1-7, 31)
    // $user - ID пользователя для комментария
    // $name - текст для комментария (откуда изменение)
    // $commentAdd - публиковать ли комментарий (0/1)
}
```

**Логика метода:**
1. Получить список элементов по фильтру: `crm.item.list`
2. Для каждого элемента проверить `assignedById !== $this->assigned`
3. Если отличается - обновить: `crm.item.update`
4. Если `$commentAdd == 1` - добавить комментарий в Timeline
5. Вернуть количество измененных элементов

**Пример вызова:**
```php
$changeAssigned = new \CSlibs\App\Assigned\changeAssigned($auth, $assigned);

// Изменить ответственного в контактах сделки
$filterContact["id"] = $resElement['contactId'];
$contact = $changeAssigned->changeAssigned(
    $filterContact, 
    'contact',      // тип
    3,              // entityTypeId для контакта
    $user['ID'],    // автор комментария
    $name,          // текст комментария
    $commentAdd     // публиковать ли
);
```

---

## 📦 Установка и настройка

### Процесс установки

**Для пользователя:**

1. Перейти по ссылке установки: https://city.brokci.ru/cassoftApp/market/changeAssigned/install.php
2. Ознакомиться с Лицензионным соглашением и Политикой конфиденциальности
3. Нажать «Установить»
4. В открывшейся панели нажать «Установить», дождаться окончания установки
5. Перейти в раздел "Общие настройки"
6. Установить правила замены ответственного для каждой сущности CRM
7. При необходимости включить публикацию комментариев в Таймлайн (по умолчанию отключено)

### Процесс установки (технический)

1. **Создание записи в Highload блоке:**
   - Блок: `app_change_assigned_access`
   - Запись с `member_id` установки

2. **Регистрация событий Битрикс24:**
   - Регистрация обработчиков событий CRM для всех сущностей
   - События типа `OnCrmLeadUpdate`, `OnCrmDealUpdate` и т.д.

3. **Настройка webhook'ов:**
   - Регистрация endpoint'ов для обработки изменений ответственного

### Highload блок: `app_change_assigned_access`

**Структура:**
```php
[
    'ID' => int,                    // ID записи
    'UF_MEMBER_ID' => string,       // member_id установки Битрикс24
    'UF_ACTIVE' => bool,            // Активность приложения
    'UF_SETTINGS' => string         // JSON с настройками (какие сущности обрабатывать)
]
```

**Пример настроек:**
```json
{
    "lead": true,
    "deal": true,
    "contact": true,
    "company": true,
    "quote": false,
    "invoice": false,
    "timeline_comment": false
}
```

---

## 📝 Логирование

### Файлы логов

| Файл | Назначение |
|------|-----------|
| `logContactUpdate.txt` | Основной лог запросов к `index.php` |
| `logInstall.txt` | Лог процесса установки |
| `logAjax.txt` | Лог AJAX запросов к `ajax.php` |
| `logLeadUpdate.txt` | Лог обновлений Лидов |
| `logDealUpdate.txt` | Лог обновлений Сделок |
| `logContactUpdate.txt` | Лог обновлений Контактов |
| `logCompanyUpdate.txt` | Лог обновлений Компаний (предположительно) |
| `logQuoteUpdate.txt` | Лог обновлений Предложений (предположительно) |
| `logInvoiceUpdate.txt` | Лог обновлений Счетов (предположительно) |

### Функция логирования

```php
$log = __DIR__ . "/logContactUpdate.txt";
p($_REQUEST, "start", $log);
p($clientsApp, "rest", $log);
```

**Функция `p()`** - кастомная функция логирования из библиотеки Cassoft.

---

## 🎯 Бизнес-логика

### Основной сценарий работы

1. **Событие в CRM:**
   - Пользователь меняет ответственного в Сделке/Лиде/Контакте/Компании/Предложении
   - Битрикс24 генерирует событие (webhook)

2. **Webhook на endpoint:**
   - Битрикс24 отправляет POST запрос на соответствующий `ajax/*.php` файл
   - Например: `ajax/DealUpdate.php` для сделки
   - Параметры: `auth[member_id]`, `data[FIELDS][ID]`

3. **Проверка доступа (в ajax/*.php):**
   - Проверяется наличие записи в Highload блоке `app_change_assigned_access`
   - Код: `$HlClientApp->searchID($memberId)`
   - Если `$clientsApp["ID"] <= 0` - процесс прерывается

4. **Вызов компонента события:**
   - Подключается компонент `event:deal_update` (или другой)
   - Передаются параметры: `app`, `auth`, `data`

5. **Загрузка настроек (в template.php компонента):**
   - Из Highload блока загружаются настройки через `entity.item.get`
   - `CS_DEAL` - JSON с настройками для сделки (какие сущности обрабатывать)
   - `CS_COMMENT_ADD` - публиковать ли комментарии в Timeline

6. **Получение данных элемента:**
   - Вызов `crm.item.get` для получения полных данных измененного элемента
   - Получение: `contactId`, `companyId`, `quoteId`, `assignedById`, и т.д.

7. **Защита от race condition:**
   - Проверка времени последнего обновления: `$dateMin = $date - $timeCreate`
   - Если обновление было менее 10 секунд назад: `sleep(2)`
   - Предотвращает конфликты при множественных быстрых изменениях

8. **Получение активных статусов (для фильтрации):**
   - Вызов `crm.status.list`
   - Фильтр: `SEMANTICS !== 'F'` (не проваленные) и `!== 'S'` (не успешные)
   - Формирование массива `$leadStatus`, `$statusInvoice`

9. **Инициализация класса changeAssigned:**
   - `new \CSlibs\App\Assigned\changeAssigned($auth, $assigned)`
   - `$assigned` - ID нового ответственного из измененного элемента

10. **Обработка связанных элементов (по очереди):**

**Для Сделки (DealUpdate):**

**a) Компания (если настройка включена):**
```php
if (!empty($setup['company']) && !empty($resElement['companyId'])) {
    $filterCompany["id"] = $resElement['companyId'];
    $changeAssigned->changeAssigned($filterCompany, 'company', 4, $user['ID'], $name, $commentAdd);
}
```

**b) Контакт:**
```php
if (!empty($setup['contact']) && !empty($resElement['contactId'])) {
    $filterContact["id"] = $resElement['contactId'];
    $changeAssigned->changeAssigned($filterContact, 'contact', 3, $user['ID'], $name, $commentAdd);
}
```

**c) Счет (только активные):**
```php
if (!empty($setup['invoice'])) {
    $filterInvoice['parentId2'] = $id;               // Связь с родительской сделкой
    $filterInvoice["stageId"] = $statusInvoice;      // Только активные статусы
    $changeAssigned->changeAssigned($filterInvoice, 'invoice', 31, $user['ID'], $name, $commentAdd);
}
```

**d) Предложение (только открытые):**
```php
if (!empty($setup['quote']) && !empty($resElement['quoteId'])) {
    $filterQuote['id'] = $resElement['quoteId'];
    $filterQuote["closed"] = 'N';
    $changeAssigned->changeAssigned($filterQuote, 'quote', 7, $user['ID'], $name, $commentAdd);
}
```

11. **Логика метода changeAssigned (в классе):**

**a) Получение списка элементов:**
```php
$resElement = $auth->CScore->call('crm.item.list', [
    'entityTypeId' => $typeId,
    'filter' => $filter
]);
```

**b) Обход и обновление:**
```php
foreach ($resElement['items'] as $element) {
    // Проверка: нужно ли менять ответственного
    if ((int)$element['assignedById'] !== (int)$this->assigned) {
        
        // Обновление
        $resUp = $auth->CScore->call("crm.item.update", [
            "entityTypeId" => $typeId,
            "id" => $element['id'],
            'fields' => ['assignedById' => $this->assigned]
        ]);
        
        // Добавление комментария (если включено)
        if ($resUp['item']['id'] > 0 && $commentAdd == 1) {
            $comments = "🟢 Ответственный изменен автоматически из " . $name . 
                        "[p](" . $typeGuide[$typeId] . "-" . $element['id'] . ")[/p]";
            
            $auth->CScore->call("crm.timeline.comment.add", [
                'fields' => [
                    "ENTITY_ID" => $element['id'],
                    "ENTITY_TYPE" => $entityType,
                    "COMMENT" => $comments,
                    "AUTHOR_ID" => $user
                ]
            ]);
        }
        $i++;  // Счетчик измененных элементов
    }
}
```

**c) Возврат результата:**
```php
return "Изменено " . $type . "-" . $i;
```

12. **Логирование всех операций:**
   - `logUpdate.txt` - детальный лог всех операций
   - `logUpdateTime.txt` - лог тайминга для race condition
   - `logClass.txt` - лог класса changeAssigned
   - `log{type}.txt` - логи для каждого типа сущности (logdeal.txt, logcontact.txt и т.д.)
   - `log{type}Com{member}.txt` - комментарии для конкретной установки

### Схема потока данных

```
Пользователь меняет ответственного в Сделке #123
           ↓
Битрикс24 webhook → /ajax/DealUpdate.php
           ↓
Проверка Highload: app_change_assigned_access
           ↓
Компонент: event:deal_update (template.php)
           ↓
┌──────────────────────────────────────────┐
│ 1. Получить настройки (entity.item.get) │
│ 2. Получить данные сделки (crm.item.get)│
│ 3. Защита от race condition (sleep)     │
│ 4. Получить активные статусы            │
│ 5. Инициализация changeAssigned         │
└──────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────┐
│ Обработка связанных элементов:         │
│                                         │
│ IF настройка 'company' включена →      │
│   changeAssigned->changeAssigned()     │
│     → crm.item.list (компания)         │
│     → crm.item.update (assignedById)   │
│     → crm.timeline.comment.add         │
│                                         │
│ IF настройка 'contact' включена →      │
│   changeAssigned->changeAssigned()     │
│     → crm.item.list (контакт)          │
│     → crm.item.update (assignedById)   │
│     → crm.timeline.comment.add         │
│                                         │
│ IF настройка 'invoice' включена →      │
│   changeAssigned->changeAssigned()     │
│     → crm.item.list (активные счета)   │
│     → crm.item.update (assignedById)   │
│     → crm.timeline.comment.add         │
│                                         │
│ IF настройка 'quote' включена →        │
│   changeAssigned->changeAssigned()     │
│     → crm.item.list (открытые КП)      │
│     → crm.item.update (assignedById)   │
│     → crm.timeline.comment.add         │
└─────────────────────────────────────────┘
           ↓
Логирование результатов в logUpdate.txt
           ↓
Завершение обработки
```

### Особенности реализации

**1. Фильтрация активных элементов:**
- Счета: только со статусами `SEMANTICS !== 'F'` и `!== 'S'`
- Предложения: только с `closed = 'N'`
- Сделки: (если бы обрабатывались) только `closed = 'N'`

**2. Формирование комментария:**
```
🟢 Ответственный изменен автоматически из сделки ID123: [URL]Название сделки[/URL], пользователем Иван Иванов
[p](d-456)[/p]
```
Где `(d-456)` - ссылка на элемент (d=deal, c=contact, com=company, q=quote, i=invoice, l=lead)

**3. Защита от дублирования:**
- Проверка времени обновления (race condition)
- Проверка `assignedById !== $this->assigned` перед обновлением
- Логирование для отладки конфликтов

**4. Гибкость настроек:**
- Каждая сущность имеет свой набор настроек (JSON)
- Включение/выключение комментариев глобально
- Настройки хранятся в Highload блоке для быстрого доступа

---

## 🐛 Известные проблемы и замечания

### 1. Ошибка в install.php
**Проблема:** В файле `install.php` неправильно указан код приложения:
```php
$arParams['app'] = 'cian_messager';  // Должно быть 'change_assigned'
```

**Влияние:** Возможна некорректная установка приложения

**Решение:** Исправить на `'change_assigned'`

---

### 2. Шаблон установки
**Замечание:** Используется шаблон `cianMessager` для компонента `install:base`:
```php
$APPLICATION->IncludeComponent(
    "install:base",
    "cianMessager",  // Возможно универсальный шаблон
    $arParams,
    false
);
```

**Требует уточнения:** Является ли это универсальным шаблоном или ошибкой копипасты?

---

### 3. Логирование в разные файлы
**Замечание:** В `index.php` логирование идет в `logContactUpdate.txt`, хотя это общая точка входа.

**Рекомендация:** Переименовать в `logIndex.txt` для ясности.

---

## 🚀 Возможности для улучшения

### Функциональные улучшения

1. **Расширенные настройки:**
   - Правила смены ответственного по направлениям/категориям
   - Условия смены (например, только для определенных стадий сделки)
   - Исключения (не менять для конкретных пользователей)

2. **Уведомления:**
   - Уведомления старому и новому ответственному о смене
   - Email/Push-уведомления

3. **История изменений:**
   - Ведение истории всех автоматических смен ответственного
   - Возможность отмены последнего изменения

4. **Аналитика:**
   - Статистика смен ответственного
   - Отчеты по активности

### Технические улучшения

1. **Рефакторинг:**
   - Исправить ошибки в `install.php`
   - Унифицировать имена лог-файлов
   - Вынести общую логику в отдельные классы

2. **Тестирование:**
   - Unit-тесты для бизнес-логики
   - Интеграционные тесты с Битрикс24 API

3. **Документация кода:**
   - Добавить PHPDoc комментарии
   - Документировать все методы и классы

4. **Оптимизация:**
   - Кеширование настроек приложения
   - Батч-обработка множественных обновлений
   - Асинхронная обработка для больших объемов данных

---

## 📚 Связанная документация

- [APPS_LIST.md](../APPS_LIST.md) - Список всех приложений
- [TECH_SPEC.md](../TECH_SPEC.md) - Общая техническая спецификация
- [REST_API.md](../REST_API.md) - REST API документация
- [php-backend.instructions.md](../php-backend.instructions.md) - PHP стандарты

---

**Документ создан:** 17 ноября 2025 г.  
**Последнее обновление:** 17 ноября 2025 г.  
**Статус документации:** ✅ Полная (требуется уточнение REST API методов в компонентах событий)

---

## ✅ Чеклист для полного документирования

- [x] Основная информация
- [x] Структура файлов
- [x] Компоненты Битрикс24
- [x] Frontend (JS/CSS)
- [x] Backend (PHP)
- [x] Endpoints
- [ ] REST API методы (требуется анализ компонентов `event:*_update`)
- [x] Установка и настройка
- [x] Логирование
- [x] Бизнес-логика
- [x] Известные проблемы
- [x] Возможности для улучшения
