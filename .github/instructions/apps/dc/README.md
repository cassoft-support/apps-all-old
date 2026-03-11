# Domclick Messenger - Документация для миграции на Vue + Nuxt

**Дата создания:** 11 марта 2026  
**Статус:** Legacy PHP приложение для миграции  
**Цель:** Перенос на Vue 3 + Nuxt 3

---

## 📋 Содержание

1. [Обзор текущего приложения](#обзор-текущего-приложения)
2. [Архитектура](#архитектура)
3. [Структура файлов](#структура-файлов)
4. [Ключевые компоненты](#ключевые-компоненты)
5. [API интеграция](#api-интеграция)
6. [Бизнес-логика](#бизнес-логика)
7. [Сравнение с CIAN](#сравнение-с-cian)
8. [План миграции](#план-миграции)
9. [Где что посмотреть](#где-что-посмотреть)

---

## Обзор текущего приложения

**Domclick Messenger** - приложение для интеграции чатов Домклик (Сбербанк) с Битрикс24 CRM.

### Основные функции:
- ✅ Прием входящих сообщений от покупателей через Domclick webhooks
- ✅ Отправка исходящих сообщений агентов в Domclick
- ✅ Интеграция с Битрикс24 Open Lines
- ✅ Определение роли отправителя (BUYER/AGENT)
- ✅ Привязка чатов к объектам недвижимости
- ✅ Разделение `display_name` на имя и фамилию

### Технологии:
- **Backend:** PHP 8.2
- **Битрикс24:** REST API, Entity, ImConnector
- **Domclick API:** public-api.domclick.ru/chats/v1/
- **Frontend:** Нет (серверная обработка)

---

## Архитектура

### Тип приложения: Webhook Handler (без UI)

```
┌─────────────────┐
│  Domclick API   │
│  (webhooks)     │
└────────┬────────┘
         │ HTTP POST
         ↓
┌─────────────────────────────────────────┐
│  cassoftApp/market/domclickMessager/    │
│         in/{member_id}.php              │  ← Точка входа вебхука
└────────────┬────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────┐
│ local/components/messager/              │
│    domclick_messager/                   │
│    templates/desctop/                   │
│    ├─ sendIn.php   (входящие)          │  ← Обработка вебхуков
│    └─ sendOut.php  (исходящие)         │  ← Отправка в Domclick
└────────────┬────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────┐
│      Bitrix24 REST API                  │
│  • imconnector.send.messages            │
│  • imconnector.send.status.delivery     │
│  • entity.item.get (настройки)          │
└─────────────────────────────────────────┘
```

### Модель данных:

```
Domclick Webhook → sendIn.php → Bitrix24 ImConnector → CRM чат
                      ↓
               Обработка:
               - Проверка роли (BUYER/AGENT)
               - Извлечение имени из display_name
               - Формирование сообщения
            
Bitrix24 Agent → sendOut.php → Domclick API → Domclick чат
                      ↓
               Обработка:
               - Очистка BB-кодов
               - Отправка в Domclick
               - Подтверждение доставки
```

---

## Структура файлов

### 1. Market Application (точка входа)
```
cassoftApp/market/domclickMessager/
├── index.php                    # Редирект на страницу установки
├── install.php                  # Инициализация установки
└── in/                          # Webhook endpoints
    ├── {member_id}.php          # Индивидуальный endpoint для клиента
    └── ... (20+ файлов)
```

### 2. Messager Component (обработка сообщений)
```
local/components/messager/domclick_messager/
├── component.php                # Роутер компонента
└── templates/desctop/
    ├── sendIn.php              # 🔥 ОБРАБОТКА ВХОДЯЩИХ (главный файл)
    └── sendOut.php             # 🔥 ОТПРАВКА ИСХОДЯЩИХ (главный файл)
```

### 3. Settings Component (настройка интеграции)
```
local/components/settings/domclick_messager/
├── component.php
└── templates/desctop/
    ├── template.php            # UI настроек
    └── ajax/
        ├── ajax.php            # 🔥 АКТИВАЦИЯ (подписка на webhooks)
        └── deactive.php        # 🔥 ДЕАКТИВАЦИЯ (отписка)
```

---

## Ключевые компоненты

### 🔥 1. sendIn.php - Обработка входящих сообщений

**Путь:** `local/components/messager/domclick_messager/templates/desctop/sendIn.php`

**Функционал:**
```php
// 1. Получение webhook от Domclick
$messages = $arParams['message']['messages'];
$users = $arParams['message']['users'];
$offers = $arParams['message']['offers'];

// 2. Определение отправителя
$senderId = $messages[0]['user_id'];

// 3. Поиск отправителя в массиве users
foreach ($users as $user) {
    if ($user['user_id'] == $senderId) {
        $sender = $user;
        break;
    }
}

// 4. Проверка роли (BUYER или AGENT)
if ($sender['roles'][0] === 'BUYER') {
    
    // 5. Разделение display_name на имя и фамилию
    $nameParts = explode(' ', trim($sender['display_name']), 2);
    $firstName = $nameParts[0];
    $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // 6. Формирование сообщения для Bitrix24
    $arMessage = [
        'user' => [
            'id' => $sender['user_id'],
            'name' => $firstName,
            'last_name' => $lastName,
            'skip_phone_validate' => 'Y'
        ],
        'message' => [
            'id' => $messages[0]['id'],
            'date' => time(),
            'text' => htmlspecialchars($messages[0]['message']),
            'disable_crm' => 'N'
        ],
        'files' => [],  // Domclick не передает файлы в webhook
        'chat' => [
            'id' => $messages[0]['chat_id'],
            'url' => $offers[0]['url']
        ]
    ];
    
    // 7. Отправка в Bitrix24
    $result = callBitrix24('imconnector.send.messages', [
        'CONNECTOR' => 'cs_domclick_connector',
        'LINE' => $lineId,
        'MESSAGES' => [$arMessage]
    ]);
}
```

**Особенности:**
- ✅ **Роли пользователей:** Обрабатываются только сообщения от BUYER
- ✅ **Разделение имени:** `display_name` → `firstName` + `lastName`
- ✅ **Без файлов:** Domclick не передает файлы в webhooks
- ✅ **Простая структура:** Без дополнительных API запросов

**Полный код:** См. [sendIn.php](./code/sendIn.php)

---

### 🔥 2. sendOut.php - Отправка исходящих сообщений

**Путь:** `local/components/messager/domclick_messager/templates/desctop/sendOut.php`

**Функционал:**
```php
// 1. Получение сообщения от Bitrix24
$message = $arParams['data']['MESSAGES'][0];

// 2. Очистка BB-кодов (аналогично CIAN)
$cleanText = preg_replace('/\[[^\]]*\]/', '', $message['message']['text']);

// 3. Формирование запроса к Domclick
$dataMes = [
    "chat_id" => $message['chat']['id'],
    "message" => $cleanText
];

// 4. Отправка в Domclick
$result = callDomclickAPI('/chats/v1/messages/', $dataMes, 'POST');

// 5. Подтверждение доставки в Bitrix24
if ($result['success']) {
    callBitrix24('imconnector.send.status.delivery', $arParams['data']);
}
```

**Отличия от CIAN:**
- ⚠️ **Структура запроса:** `chat_id` вместо `chatId`, `message` вместо `content.text`
- ⚠️ **Endpoint:** `/chats/v1/messages/` вместо `/v1/send-message`
- ✅ **Очистка BB-кодов:** Идентично CIAN

**Полный код:** См. [sendOut.php](./code/sendOut.php)

---

### 🔥 3. ajax.php - Активация интеграции

**Путь:** `local/components/settings/domclick_messager/templates/desctop/ajax/ajax.php`

**Функционал:**
```php
// 1. Создание webhook endpoint файла
$fileName = "/cassoftApp/market/domclickMessager/in/{$memberId}.php";
file_put_contents($fileAdd, $webhookHandlerCode);

// 2. Подписка на webhooks Domclick
$data = [
    "url" => "https://app.cassoft.ru" . $fileName,
    "types" => [
        "new_messages"  // Только один тип вебхука
    ],
    "description" => "CS domclick"
];

$result = callDomclickAPI('/chats/v1/webhooks/', $data, 'POST');

// 3. Активация коннектора в Bitrix24
callBitrix24('imconnector.activate', [
    'CONNECTOR' => 'cs_domclick_connector',
    'LINE' => $lineId,
    'ACTIVE' => 1
]);

// 4. Сохранение настроек
callBitrix24('entity.item.update', [
    'ENTITY' => 'setup_messager',
    'ID' => $setupId,
    'PROPERTY_VALUES' => [
        'CS_KEY_DC' => $apiKey,
        'CS_DC_LINE' => $lineId,
        'CS_DC_CONNECT' => 1
    ]
]);
```

**Отличия от CIAN:**
- ⚠️ **Один тип вебхука:** `new_messages` (у CIAN: 3 типа)
- ⚠️ **Endpoint:** `/chats/v1/webhooks/` (у CIAN: `/v2/subscribe-webhooks`)
- ✅ **Логика создания файлов:** Идентична CIAN

**Полный код:** См. [ajax.php](./code/ajax.php)

---

### 🔥 4. deactive.php - Деактивация интеграции

**Путь:** `local/components/settings/domclick_messager/templates/desctop/ajax/deactive.php`

**Функционал:**
```php
// 1. Отписка от webhooks
$data = [
    "url" => ADDRESS_SITE . $fileName
];

callDomclickAPI('/chats/v1/webhooks/unsubscribe', $data, 'POST');

// 2. Деактивация коннектора
callBitrix24('imconnector.activate', [
    'CONNECTOR' => 'cs_domclick_connector',
    'LINE' => $lineId,
    'ACTIVE' => 0
]);

// 3. Очистка настроек
callBitrix24('entity.item.update', [
    'ENTITY' => 'setup_messager',
    'PROPERTY_VALUES' => [
        'CS_DC_CONNECT' => false
    ]
]);
```

**Полный код:** См. [deactive.php](./code/deactive.php)

---

## API интеграция

### Domclick API (public-api.domclick.ru/chats/v1/)

#### 1. Подписка на webhooks
```http
POST /chats/v1/webhooks/
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "url": "https://app.cassoft.ru/cassoftApp/market/domclickMessager/in/{member_id}.php",
  "types": ["new_messages"],
  "description": "CS domclick"
}
```

**Ответ:**
```json
{
  "success": true,
  "result": {
    "webhook_id": "123",
    "url": "https://...",
    "types": ["new_messages"]
  }
}
```

---

#### 2. Список подписок
```http
GET /chats/v1/webhooks/
Authorization: Bearer {api_key}
```

**Ответ (реальный пример):**
```json
{
  "success": true,
  "result": [
    {
      "url": "https://app.cassoft.ru/cassoftApp/market/domclickMessager/in/186e23c05905c4a0d500a726c925521a.php",
      "description": "CS domclick",
      "types": ["new_messages"],
      "is_disabled": false,
      "created_at": "2026-03-04T09:48:53.502843",
      "updated_at": "2026-03-04T09:48:53.502843"
    }
  ],
  "errors": []
}
```

---

#### 3. Отписка от webhooks
```http
POST /chats/v1/webhooks/unsubscribe
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "url": "https://app.cassoft.ru/..."
}
```

**Ответ:**
```json
{
  "success": true,
  "result": {
    "url": "https://...",
    "is_disabled": true,
    "updated_at": "2026-03-11T05:51:05.179666"
  },
  "errors": []
}
```

---

#### 4. Отправка сообщения
```http
POST /chats/v1/messages/
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "chat_id": "chat_123",
  "message": "Текст сообщения"
}
```

**Ответ:**
```json
{
  "success": true,
  "result": {
    "message_id": "msg_456",
    "chat_id": "chat_123"
  }
}
```

---

### Структура входящего webhook от Domclick

```json
{
  "messages": [{
    "id": "msg_789",
    "chat_id": "chat_123",
    "user_id": 12345,
    "message": "Здравствуйте, интересует объект"
  }],
  "users": [{
    "user_id": 12345,
    "display_name": "Иван Петров",
    "roles": ["BUYER"],        // BUYER или AGENT
    "avatar": {
      "images": {
        "small": "https://..."
      }
    }
  }],
  "offers": [{
    "url": "https://domclick.ru/card/..."
  }]
}
```

**Отличия от CIAN:**
- 🔹 **Роли в массиве:** `roles: ["BUYER"]` вместо одного поля `role: "initiator"`
- 🔹 **Простая структура:** Меньше вложенности
- 🔹 **Нет `mainPhoto`:** Только URL объявления

---

### Bitrix24 REST API

Идентично CIAN, см. [CIAN документацию](../cian/README.md#bitrix24-rest-api)

---

## Бизнес-логика

### 1. Определение роли отправителя

**Код:**
```php
$arUser = [];
foreach ($arParams['message']['users'] as $user) {
    $arUser[$user['user_id']] = $user['roles'][0];
}

// Проверка роли
if ($arUser[$messages[0]['user_id']] === 'BUYER') {
    // Обработка сообщения от покупателя
}
```

**Логика:** Обрабатываются только сообщения от BUYER, сообщения от агентов игнорируются.

---

### 2. Разделение display_name

```php
$nameParts = explode(' ', trim($sender['display_name']), 2);
$firstName = $nameParts[0];               // "Иван"
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';  // "Петров"
```

**Аналогично CIAN.**

---

### 3. Skip Phone Validate

```php
'skip_phone_validate' => 'Y'
```

Domclick, как и CIAN, не предоставляет номера телефонов покупателей.

---

### 4. Очистка BB-кодов

```php
$cleanText = preg_replace('/\[[^\]]*\]/', '', $text);
```

**Аналогично CIAN.**

---

## Сравнение с CIAN

### Сходства ✅

| Параметр | CIAN | Domclick |
|----------|------|----------|
| Архитектура | Webhook Handler | Webhook Handler |
| Обработка входящих | sendIn.php | sendIn.php |
| Обработка исходящих | sendOut.php | sendOut.php |
| Очистка BB-кодов | ✅ | ✅ |
| Skip phone validation | ✅ | ✅ |
| Разделение имени | ✅ | ✅ |
| Активация/Деактивация | ajax.php / deactive.php | ajax.php / deactive.php |
| Bitrix24 Entity | setup_messager | setup_messager |

### Отличия ⚠️

| Параметр | CIAN | Domclick |
|----------|------|----------|
| **Webhook типы** | 3 типа (offers, newbuilding, readability) | 1 тип (new_messages) |
| **Бот обработка** | ✅ Есть (userId: 68084393) | ❌ Нет |
| **API запросы** | get-chat для ботов | Нет дополнительных запросов |
| **Структура сообщения** | `content.text` | `message` |
| **Структура роли** | `role: "initiator"` | `roles: ["BUYER"]` |
| **Файлы** | mainPhoto в webhook | ❌ Нет файлов |
| **API endpoint (подписка)** | `/v2/subscribe-webhooks` | `/chats/v1/webhooks/` |
| **API endpoint (отправка)** | `/v1/send-message` | `/chats/v1/messages/` |

### Сложность миграции

- **CIAN:** ⭐⭐⭐⭐ (Сложная - из-за обработки бота и API get-chat)
- **Domclick:** ⭐⭐⭐ (Средняя - простая структура, без доп. API запросов)

---

## План миграции

### Этап 1: Анализ ✅ (ВЫПОЛНЕНО)
- ✅ Документирована архитектура
- ✅ Выявлены ключевые компоненты
- ✅ Описаны API и бизнес-логика
- ✅ Сравнение с CIAN

---

### Этап 2: Проектирование Vue приложения

#### 2.1 Nuxt 3 Backend API Routes

**Структура:** Аналогично CIAN (см. [CIAN План миграции](../cian/README.md#этап-2-проектирование-vue-приложения))

**Различия:**
```typescript
// server/services/domclick.service.ts
export class DomclickService {
  private apiKey: string;
  private baseUrl = 'https://public-api.domclick.ru/chats/v1';
  
  // Простая подписка (один тип webhook)
  async subscribeWebhooks(url: string) {
    return await $fetch(`${this.baseUrl}/webhooks/`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${this.apiKey}` },
      body: {
        url,
        types: ['new_messages'],  // Только один тип
        description: 'CS domclick'
      }
    });
  }
  
  // Отправка сообщения
  async sendMessage(chatId: string, text: string) {
    return await $fetch(`${this.baseUrl}/messages/`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${this.apiKey}` },
      body: {
        chat_id: chatId,  // Отличие от CIAN
        message: text      // Отличие от CIAN
      }
    });
  }
}
```

---

**MessageProcessor:**
```typescript
// server/services/message.processor.ts
export class DomclickMessageProcessor {
  
  async handleIncoming(webhook: DomclickWebhook, memberId: string) {
    const message = webhook.messages[0];
    
    // 1. Поиск отправителя
    const sender = webhook.users.find(u => u.user_id === message.user_id);
    
    // 2. Проверка роли (только BUYER)
    if (sender.roles[0] !== 'BUYER') {
      return;  // Игнорируем сообщения от агентов
    }
    
    // 3. Разделение имени
    const [firstName, lastName] = this.splitName(sender.display_name);
    
    // 4. Формирование сообщения (БЕЗ запросов к API)
    const bitrixMessage = {
      user: {
        id: sender.user_id,
        name: firstName,
        last_name: lastName,
        skip_phone_validate: 'Y'
      },
      message: {
        id: message.id,
        date: Math.floor(Date.now() / 1000),
        text: message.message,
        disable_crm: 'N'
      },
      files: [],  // Domclick не передает файлы
      chat: {
        id: message.chat_id,
        url: webhook.offers[0]?.url
      }
    };
    
    // 5. Отправка в Bitrix24
    return await this.bitrix24Service.sendMessage(
      'cs_domclick_connector',
      await this.getLineId(memberId),
      [bitrixMessage]
    );
  }
  
  async handleOutgoing(bitrixMessage: any, memberId: string) {
    // 1. Очистка BB-кодов
    const cleanText = this.cleanBBCodes(bitrixMessage.MESSAGES[0].message.text);
    
    // 2. Отправка в Domclick
    const result = await this.domclickService.sendMessage(
      bitrixMessage.MESSAGES[0].chat.id,
      cleanText
    );
    
    // 3. Подтверждение доставки
    if (result.success) {
      await this.bitrix24Service.sendStatusDelivery(bitrixMessage);
    }
    
    return result;
  }
}
```

---

#### 2.2 Типы TypeScript

```typescript
// types/domclick.ts
export interface DomclickWebhook {
  messages: Array<{
    id: string;
    chat_id: string;
    user_id: number;
    message: string;
  }>;
  users: Array<{
    user_id: number;
    display_name: string;
    roles: ('BUYER' | 'AGENT')[];  // Массив ролей
    avatar?: {
      images: {
        small: string;
      };
    };
  }>;
  offers: Array<{
    url: string;
  }>;
}
```

---

### Этап 3: Реализация

**Приоритеты:**
1. **Высокий:** sendIn.php → TypeScript API route
2. **Высокий:** sendOut.php → TypeScript API route
3. **Средний:** Activation/Deactivation → Admin UI
4. **Низкий:** Настройки → Vue UI

**Задачи:**
- [ ] Создать DomclickService (проще чем CIAN, без get-chat)
- [ ] Реализовать MessageProcessor (без бота обработки)
- [ ] Создать API routes для webhooks
- [ ] Написать тесты
- [ ] Миграция данных настроек

**Время разработки:** ~40% меньше чем CIAN (за счет простоты)

---

### Этап 4: Тестирование

#### Unit тесты:
- splitName()
- cleanBBCodes()
- Role validation (BUYER/AGENT)

#### Integration тесты:
- Webhook обработка
- Domclick API вызовы
- Bitrix24 API вызовы

#### E2E тесты:
- Полный цикл сообщения
- Активация/деактивация
- Обработка ошибок

---

### Этап 5: Деплой

**Аналогично CIAN**, см. [CIAN План миграции](../cian/README.md#этап-5-деплой)

---

## Где что посмотреть

### Ключевые файлы для изучения:

#### ✅ Обязательно прочитать:

1. **[sendIn.php](./code/sendIn.php)** (120 строк)
   - Обработка входящих webhooks
   - Проверка роли BUYER
   - Формирование сообщения для Bitrix24

2. **[sendOut.php](./code/sendOut.php)** (40 строк)
   - Отправка исходящих сообщений
   - Очистка BB-кодов
   - Подтверждение доставки

3. **[ajax.php](./code/ajax.php)** (80 строк)
   - Активация интеграции
   - Подписка на webhooks
   - Создание endpoint файлов

4. **[deactive.php](./code/deactive.php)** (50 строк)
   - Деактивация
   - Отписка от webhooks

---

### Структура директорий для изучения:

```bash
# 1. Основные обработчики
local/components/messager/domclick_messager/templates/desctop/

# 2. Настройки
local/components/settings/domclick_messager/templates/desctop/ajax/

# 3. Webhook endpoints
cassoftApp/market/domclickMessager/in/
```

---

### Команды для быстрого доступа:

```bash
# Перейти к ключевым файлам
cd d:\projects\apps-all-old

# Основные обработчики
code local\components\messager\domclick_messager\templates\desctop\sendIn.php
code local\components\messager\domclick_messager\templates\desctop\sendOut.php

# Настройки
code local\components\settings\domclick_messager\templates\desctop\ajax\ajax.php
code local\components\settings\domclick_messager\templates\desctop\ajax\deactive.php

# Webhook endpoint (пример)
code cassoftApp\market\domclickMessager\in\186e23c05905c4a0d500a726c925521a.php
```

---

### Логи для отладки:

```bash
# sendIn логи
local/components/messager/domclick_messager/templates/desctop/logSendIn.txt

# sendOut логи
local/components/messager/domclick_messager/templates/desctop/logSendOut.txt

# Активация логи
local/components/settings/domclick_messager/templates/desctop/ajax/logAjax.txt

# Деактивация логи
local/components/settings/domclick_messager/templates/desctop/ajax/logDeactive.txt
```

---

## Дополнительные материалы

### Документация Domclick API:
- https://developers.domclick.ru/ (если доступна)

### Документация Bitrix24:
- https://dev.1c-bitrix.ru/rest_help/
- https://dev.1c-bitrix.ru/rest_help/im/

### Nuxt 3:
- https://nuxt.com/docs
- https://nuxt.com/docs/guide/directory-structure/server

### Сравнение с CIAN:
- См. [CIAN документацию](../cian/README.md)

---

## Выводы

**Domclick Messenger проще CIAN:**
- ☑️ Без обработки бота
- ☑️ Без дополнительных API запросов
- ☑️ Один тип webhook
- ☑️ Простая структура данных

**Миграция на Vue+Nuxt будет проще и быстрее.**

---

**Последнее обновление:** 11 марта 2026  
**Версия:** 1.0  
**Автор:** AI Assistant  
**Статус документа:** Готов к использованию
