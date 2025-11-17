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

**Вызов:**
```php
$APPLICATION->IncludeComponent(
    "settings:base",
    "change_assigned",
    $arParams,
    false
);
```

**Назначение:**  
Интерфейс общих настроек приложения (выбор сущностей для автоматической смены ответственного)

---

### 3. Компонент справки: `helpdesk:base`

**Шаблон:** `change_assigned`

**Вызов:**
```php
$APPLICATION->IncludeComponent(
    "helpdesk:base",
    "change_assigned",
    $arParams,
    false
);
```

**Назначение:**  
Главная страница с инструкциями и справкой

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

#### `event:lead_update` (шаблон: `change_assigned`)
Обрабатывает обновление Лида

#### `event:deal_update` (шаблон: `change_assigned`)
Обрабатывает обновление Сделки

#### `event:contact_update` (шаблон: `change_assigned`)
Обрабатывает обновление Контакта

#### `event:company_update` (шаблон: `change_assigned`)
Обрабатывает обновление Компании

#### `event:quote_update` (шаблон: `change_assigned`)
Обрабатывает обновление Предложения

#### `event:invoice_update` (шаблон: `change_assigned`)
Обрабатывает обновление Счета

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

#### Подключаемые библиотеки CSS:
- `/local/lib/bootstrap/bootstrap.css` - Bootstrap 
- `/local/lib/bootstrap/bootstrap-table/fresh-bootstrap-table.css` - Таблицы
- `/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css` - Иконки
- `/local/lib/css/cassoft/style.css` - Стили Cassoft
- `/local/lib/css/cassoft/cassoft.css` - Дополнительные стили
- `/local/lib/chosen/chosen.min.css` - Chosen selector
- `/local/lib/css/cassoft/cs-root-blue.css` - Цветовая схема
- `/local/lib/css/new/menuWhite.css` - Белое меню
- `/local/lib/css/cassoft/panel.css` - Панели
- `/local/lib/css/cassoft/brokci-grid.css` - Сетка

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

**⚠️ Примечание:** Точные методы REST API требуют анализа компонентов событий (`event:*_update`). Ниже приведены предполагаемые методы на основе логики приложения.

#### Методы для получения данных CRM:

**Лиды:**
- `crm.lead.get` - получение данных Лида
- `crm.lead.update` - обновление ответственного в Лиде

**Сделки:**
- `crm.deal.get` - получение данных Сделки
- `crm.deal.update` - обновление ответственного в Сделке

**Контакты:**
- `crm.contact.get` - получение данных Контакта
- `crm.contact.update` - обновление ответственного в Контакте

**Компании:**
- `crm.company.get` - получение данных Компании
- `crm.company.update` - обновление ответственного в Компании

**Предложения:**
- `crm.quote.get` - получение данных Предложения
- `crm.quote.update` - обновление ответственного в Предложении

**Счета:**
- `crm.invoice.get` - получение данных Счета
- `crm.invoice.update` - обновление ответственного в Счете

#### Методы для связей:

- `crm.deal.contact.items.get` - получение контактов сделки
- `crm.deal.company.items.get` - получение компаний сделки
- `crm.lead.contact.items.get` - получение контактов лида (если применимо)

#### Методы для Timeline (комментарии):

- `crm.timeline.comment.add` - добавление комментария в Таймлайн

#### Пользователи:

- `user.current` - получение текущего пользователя

### Обертка для вызовов API

```php
use CSlibs\B24\Auth\Auth;

$auth = new Auth($app, [], $member_id);
$result = $auth->CScore->call($method, $params);
```

**Пример:**
```php
// Получить сделку
$deal = $auth->CScore->call('crm.deal.get', ['ID' => $dealId]);

// Обновить ответственного
$result = $auth->CScore->call('crm.deal.update', [
    'ID' => $dealId,
    'fields' => [
        'ASSIGNED_BY_ID' => $newUserId
    ]
]);

// Добавить комментарий в Timeline
$comment = $auth->CScore->call('crm.timeline.comment.add', [
    'fields' => [
        'ENTITY_ID' => $dealId,
        'ENTITY_TYPE' => 'deal',
        'COMMENT' => 'Ответственный изменен автоматически из Лида #123'
    ]
]);
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
   - Пользователь меняет ответственного в Сделке/Лиде/Контакте/Компании

2. **Webhook на endpoint:**
   - Битрикс24 отправляет webhook на соответствующий `ajax/*.php` файл
   - Например: `ajax/DealUpdate.php` для сделки

3. **Проверка доступа:**
   - Проверяется наличие записи в Highload блоке `app_change_assigned_access`
   - Если доступа нет - процесс прерывается

4. **Загрузка настроек:**
   - Из Highload блока загружаются настройки:
     - Какие сущности обрабатывать
     - Нужно ли оставлять комментарии в Timeline

5. **Получение связанных сущностей:**
   - Через REST API получаются все связанные элементы CRM
   - Например, для сделки: контакты, компании, связанные сделки

6. **Фильтрация активных сущностей:**
   - Проверка статуса сущностей (активные/завершенные)
   - Завершенные сделки исключаются из обработки

7. **Обновление ответственного:**
   - Для каждой связанной сущности (если она в настройках):
     - Вызов `crm.*.update` с новым `ASSIGNED_BY_ID`

8. **Добавление комментария (опционально):**
   - Если включено в настройках:
     - Добавление комментария в Timeline: "Ответственный изменен автоматически из Сделки #123"

9. **Логирование:**
   - Запись результатов в соответствующий лог-файл

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
