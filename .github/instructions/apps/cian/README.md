# CIAN Messenger - Документация для миграции на Vue + Nuxt

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
7. [План миграции](#план-миграции)
8. [Где что посмотреть](#где-что-посмотреть)

---

## Обзор текущего приложения

**CIAN Messenger** - приложение для интеграции чатов ЦИАН с Битрикс24 CRM.

### Основные функции:
- ✅ Прием входящих сообщений от покупателей через CIAN webhooks
- ✅ Отправка исходящих сообщений агентов в CIAN
- ✅ Обработка сообщений от CIAN бота (рассылки)
- ✅ Интеграция с Битрикс24 Open Lines
- ✅ Привязка чатов к объектам недвижимости
- ✅ Передача фотографий объектов в чат

### Технологии:
- **Backend:** PHP 8.2
- **Битрикс24:** REST API, Entity, ImConnector
- **CIAN API:** public-api.cian.ru/v1/
- **Frontend:** Нет (серверная обработка)

---

## Архитектура

### Тип приложения: Webhook Handler (без UI)

```
┌─────────────────┐
│   CIAN API      │
│  (webhooks)     │
└────────┬────────┘
         │ HTTP POST
         ↓
┌─────────────────────────────────────┐
│  cassoftApp/market/cianMessager/    │
│         in/{member_id}.php          │  ← Точка входа вебхука
└────────────┬────────────────────────┘
             │
             ↓
┌─────────────────────────────────────┐
│ local/components/messager/          │
│    cian_messager/                   │
│    templates/desctop/               │
│    ├─ sendIn.php   (входящие)      │  ← Обработка вебхуков
│    └─ sendOut.php  (исходящие)     │  ← Отправка в CIAN
└────────────┬────────────────────────┘
             │
             ↓
┌─────────────────────────────────────┐
│      Bitrix24 REST API              │
│  • imconnector.send.messages        │
│  • imconnector.send.status.delivery │
│  • entity.item.get (настройки)      │
└─────────────────────────────────────┘
```

### Модель данных:

```
CIAN Webhook → sendIn.php → Bitrix24 ImConnector → CRM чат
                   ↓
            Обработка:
            - Проверка бота (userId: 68084393)
            - Запрос get-chat API (для ботов)
            - Извлечение данных покупателя
            - Формирование сообщения
            
Bitrix24 Agent → sendOut.php → CIAN API send-message → CIAN чат
                   ↓
            Обработка:
            - Очистка BB-кодов
            - Отправка в CIAN
            - Подтверждение доставки
```

---

## Структура файлов

### 1. Market Application (точка входа)
```
cassoftApp/market/cianMessager/
├── index.php                    # Редирект на страницу установки
├── install.php                  # Инициализация установки
├── install_connector.php        # Установка коннектора
└── in/                          # Webhook endpoints
    ├── {member_id}.php          # Индивидуальный endpoint для клиента
    ├── {member_id}_1.php        # Дополнительные endpoints (версии)
    └── ... (100+ файлов)
```

### 2. Messager Component (обработка сообщений)
```
local/components/messager/cian_messager/
├── component.php                # Роутер компонента
└── templates/desctop/
    ├── sendIn.php              # 🔥 ОБРАБОТКА ВХОДЯЩИХ (главный файл)
    └── sendOut.php             # 🔥 ОТПРАВКА ИСХОДЯЩИХ (главный файл)
```

### 3. Settings Component (настройка интеграции)
```
local/components/settings/cian_messager/
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

**Путь:** `local/components/messager/cian_messager/templates/desctop/sendIn.php`

**Функционал:**
```php
// 1. Получение webhook от CIAN
$message = $arParams['message'];

// 2. Проверка направления
if ($message['chats'][0]['messages'][0]['direction'] === 'in') {
    
    // 3. Определение отправителя
    $senderUserId = $message['chats'][0]['messages'][0]['userId'];
    
    // 4. Обработка бота CIAN (userId: 68084393)
    if ($senderUserId == 68084393) {
        // Запрос к API get-chat для получения реального покупателя
        $chatData = callCianAPI("/v1/get-chat?chatId={$chatId}");
        
        // Поиск initiator (покупателя)
        foreach ($chatData['result']['chat']['users'] as $user) {
            if ($user['role'] === 'initiator') {
                $senderUserId = $user['userId'];
                $senderName = $user['name'];
            }
        }
    }
    
    // 5. Формирование сообщения для Bitrix24
    $arMessage = [
        'user' => [
            'id' => $senderUserId,
            'name' => $firstName,
            'last_name' => $lastName,
            'skip_phone_validate' => 'Y'
        ],
        'message' => [
            'id' => $messageId,
            'date' => time(),
            'text' => htmlspecialchars($text),
            'disable_crm' => 'N'
        ],
        'files' => [
            ['url' => $mainPhotoUrl, 'name' => 'Объект']
        ],
        'chat' => [
            'id' => $chatId,
            'name' => $offerId,
            'url' => $offerUrl
        ]
    ];
    
    // 6. Отправка в Bitrix24
    $result = callBitrix24('imconnector.send.messages', [
        'CONNECTOR' => 'cs_cian_connector',
        'LINE' => $lineId,
        'MESSAGES' => [$arMessage]
    ]);
}
```

**Особенности:**
- ✅ **Обработка бота:** Если сообщение от бота (ID: 68084393), запрашивает get-chat API
- ✅ **Извлечение покупателя:** Ищет пользователя с ролью `initiator`
- ✅ **Разделение имени:** Разбивает `display_name` на имя и фамилию
- ✅ **Фото объекта:** Добавляет главное фото как вложение

**Полный код:** См. [sendIn.php](./code/sendIn.php)

---

### 🔥 2. sendOut.php - Отправка исходящих сообщений

**Путь:** `local/components/messager/cian_messager/templates/desctop/sendOut.php`

**Функционал:**
```php
// 1. Получение сообщения от Bitrix24
$message = $arParams['data']['MESSAGES'][0];
$chatId = $message['chat']['id'];
$text = $message['message']['text'];

// 2. Очистка BB-кодов Bitrix24
$cleanText = preg_replace('/\[[^\]]*\]/', '', $text);

// 3. Формирование запроса к CIAN
$dataMes = [
    "chatId" => $chatId,
    "content" => [
        "text" => $cleanText
    ]
];

// 4. Отправка в CIAN
$result = callCianAPI('/v1/send-message', $dataMes, 'POST');

// 5. Подтверждение доставки в Bitrix24
if ($result['result']['messageId']) {
    callBitrix24('imconnector.send.status.delivery', $arParams['data']);
}
```

**Особенности:**
- ✅ **Очистка форматирования:** Удаляет BB-коды `[b]`, `[url]` и т.д.
- ✅ **Подтверждение доставки:** Отправляет статус в Bitrix24
- ✅ **Простая структура:** Только текст (без файлов)

**Полный код:** См. [sendOut.php](./code/sendOut.php)

---

### 🔥 3. ajax.php - Активация интеграции

**Путь:** `local/components/settings/cian_messager/templates/desctop/ajax/ajax.php`

**Функционал:**
```php
// 1. Создание webhook endpoint файла
$fileName = "/cassoftApp/market/cianMessager/in/{$memberId}.php";
file_put_contents($fileAdd, $webhookHandlerCode);

// 2. Подписка на webhooks CIAN
$data = [
    "url" => "https://app.cassoft.ru" . $fileName,
    "webhookTypes" => [
        "offersMessagesIncoming",       // Сообщения по объявлениям
        "newbuildingMessagesIncoming",  // Сообщения по новостройкам
        "chatsReadability"              // Статусы прочтения
    ]
];

$result = callCianAPI('/v2/subscribe-webhooks', $data, 'POST');

// 3. Активация коннектора в Bitrix24
callBitrix24('imconnector.activate', [
    'CONNECTOR' => 'cs_cian_connector',
    'LINE' => $lineId,
    'ACTIVE' => 1
]);

// 4. Сохранение настроек
callBitrix24('entity.item.update', [
    'ENTITY' => 'setup_messager',
    'ID' => $setupId,
    'PROPERTY_VALUES' => [
        'CS_KEY_CIAN' => $apiKey,
        'CS_CIAN_LINE' => $lineId,
        'CS_CIAN_CONNECT' => 1
    ]
]);
```

**Особенности:**
- ✅ **Динамическое создание файлов:** Генерирует webhook endpoint для каждого клиента
- ✅ **Три типа вебхуков:** Поддержка объявлений, новостроек и статусов
- ✅ **Хранилище Entity:** Использует Битрикс24 Entity для настроек

**Полный код:** См. [ajax.php](./code/ajax.php)

---

### 🔥 4. deactive.php - Деактивация интеграции

**Путь:** `local/components/settings/cian_messager/templates/desctop/ajax/deactive.php`

**Функционал:**
```php
// 1. Отписка от webhooks
$data = [
    "url" => ADDRESS_SITE . $fileName,
    "webhookTypes" => [
        "offersMessagesIncoming",
        "newbuildingMessagesIncoming",
        "chatsReadability"
    ]
];

callCianAPI('/v2/unsubscribe-webhooks', $data, 'POST');

// 2. Деактивация коннектора
callBitrix24('imconnector.activate', [
    'CONNECTOR' => 'cs_cian_connector',
    'LINE' => $lineId,
    'ACTIVE' => 0
]);

// 3. Очистка настроек
callBitrix24('entity.item.update', [
    'ENTITY' => 'setup_messager',
    'PROPERTY_VALUES' => [
        'CS_CIAN_CONNECT' => false
    ]
]);
```

**Полный код:** См. [deactive.php](./code/deactive.php)

---

## API интеграция

### CIAN API (public-api.cian.ru)

#### 1. Получение информации о чате
```http
GET /v1/get-chat?chatId={chatId}
Authorization: Bearer {api_key}
```

**Ответ:**
```json
{
  "result": {
    "chat": {
      "chatId": "123456",
      "users": [
        {
          "userId": 789,
          "role": "initiator",   // покупатель
          "name": "Иван Петров"
        },
        {
          "userId": 456,
          "role": "responder",   // агент
          "name": "Агент"
        }
      ]
    }
  }
}
```

**Использование:** Определение реального покупателя при сообщениях от бота

---

#### 2. Отправка сообщения
```http
POST /v1/send-message
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "chatId": "123456",
  "content": {
    "text": "Текст сообщения"
  }
}
```

**Ответ:**
```json
{
  "result": {
    "messageId": "msg_789",
    "chatId": "123456"
  }
}
```

---

#### 3. Подписка на webhooks
```http
POST /v2/subscribe-webhooks
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "url": "https://app.cassoft.ru/cassoftApp/market/cianMessager/in/{member_id}.php",
  "webhookTypes": [
    "offersMessagesIncoming",
    "newbuildingMessagesIncoming",
    "chatsReadability"
  ]
}
```

---

#### 4. Отписка от webhooks
```http
POST /v2/unsubscribe-webhooks
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "url": "https://app.cassoft.ru/...",
  "webhookTypes": ["offersMessagesIncoming", ...]
}
```

---

#### 5. Список подписок
```http
GET /v1/get-webhooks-subscriptions
Authorization: Bearer {api_key}
```

**Ответ:**
```json
{
  "result": {
    "webhooks": [
      {
        "url": "https://...",
        "webhookTypes": ["offersMessagesIncoming"],
        "active": true
      }
    ]
  }
}
```

---

### Структура входящего webhook от CIAN

```json
{
  "chats": [{
    "chatId": "chat_123",
    "offerId": "offer_456",
    "messages": [{
      "messageId": "msg_789",
      "userId": 68084393,        // 68084393 = CIAN бот
      "direction": "in",
      "content": {
        "text": "Здравствуйте, интересует объект"
      }
    }]
  }],
  "users": [{
    "userId": 12345,
    "name": "Иван Петров",
    "avatar": {
      "images": {
        "small": "https://..."
      }
    }
  }],
  "offers": [{
    "externalId": "obj_123",
    "title": "3-комнатная квартира",
    "address": "Москва, ул. Ленина, 1",
    "url": "https://cian.ru/...",
    "mainPhoto": {
      "url": "https://..."
    }
  }]
}
```

---

### Bitrix24 REST API

#### 1. Отправка сообщения в Open Line
```php
imconnector.send.messages
```

**Параметры:**
```php
[
  'CONNECTOR' => 'cs_cian_connector',
  'LINE' => 123,
  'MESSAGES' => [[
    'user' => [
      'id' => 'user_123',
      'name' => 'Имя',
      'last_name' => 'Фамилия',
      'skip_phone_validate' => 'Y'
    ],
    'message' => [
      'id' => 'msg_456',
      'date' => 1234567890,
      'text' => 'Текст сообщения',
      'disable_crm' => 'N'
    ],
    'files' => [[
      'url' => 'https://...',
      'name' => 'Объект'
    ]],
    'chat' => [
      'id' => 'chat_789',
      'name' => 'Чат название',
      'url' => 'https://...'
    ]
  ]]
]
```

---

#### 2. Подтверждение доставки
```php
imconnector.send.status.delivery
```

---

#### 3. Активация коннектора
```php
imconnector.activate
```

**Параметры:**
```php
[
  'CONNECTOR' => 'cs_cian_connector',
  'LINE' => 123,
  'ACTIVE' => 1  // 0 = деактивация
]
```

---

#### 4. Работа с Entity (настройки)
```php
entity.item.get
entity.item.update
```

**Entity:** `setup_messager`

**Поля:**
- `CS_KEY_CIAN` - API ключ CIAN
- `CS_CIAN_LINE` - ID линии Open Lines
- `CS_CIAN_CONNECT` - Статус подключения

---

## Бизнес-логика

### 1. Обработка сообщений от CIAN бота

**Проблема:** CIAN отправляет автоматические сообщения от имени бота (userId: 68084393), что создавало одного контакта для всех покупателей.

**Решение:**
```php
if ($senderUserId == 68084393) {
    // Запрос к get-chat API
    $chatData = callCianAPI("/v1/get-chat?chatId={$chatId}");
    
    // Поиск initiator (покупателя)
    foreach ($chatData['result']['chat']['users'] as $user) {
        if ($user['role'] === 'initiator') {
            $senderUserId = $user['userId'];
            $senderName = $user['name'];
            break;
        }
    }
}
```

**Результат:** Каждый покупатель создается отдельным контактом в CRM.

---

### 2. Разделение имени на имя и фамилию

```php
$nameParts = explode(' ', trim($buyerName), 2);
$firstName = $nameParts[0];               // "Иван"
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';  // "Петров"
```

---

### 3. Очистка BB-кодов при отправке

Битрикс24 форматирует сообщения BB-кодами `[b]текст[/b]`, `[url]ссылка[/url]`. CIAN их не понимает.

```php
$cleanText = preg_replace('/\[[^\]]*\]/', '', $text);
```

**Пример:**
- Вход: `Здравствуйте! [url=https://...]Ссылка[/url]`
- Выход: `Здравствуйте! Ссылка`

---

### 4. Skip Phone Validate

```php
'skip_phone_validate' => 'Y'
```

CIAN не предоставляет номера телефонов покупателей. Флаг отключает валидацию.

---

### 5. Привязка к объектам недвижимости

```php
'chat' => [
    'id' => $chatId,
    'name' => $offerId,        // ID объявления
    'url' => $offerUrl         // Ссылка на CIAN
]
```

Чат привязывается к конкретному объявлению через `offerId`.

---

## План миграции

### Этап 1: Анализ ✅ (ВЫПОЛНЕНО)
- ✅ Документирована архитектура
- ✅ Выявлены ключевые компоненты
- ✅ Описаны API и бизнес-логика

---

### Этап 2: Проектирование Vue приложения

#### 2.1 Nuxt 3 Backend API Routes

**Цель:** Заменить PHP обработчики на TypeScript API routes

**Структура:**
```
nuxt-app/
├── server/
│   ├── api/
│   │   └── webhooks/
│   │       └── cian/
│   │           └── [memberId].post.ts   # Входящий webhook
│   ├── services/
│   │   ├── cian.service.ts              # CIAN API client
│   │   ├── bitrix24.service.ts          # Bitrix24 REST client
│   │   └── message.processor.ts         # Бизнес-логика
│   └── utils/
│       ├── bot-detector.ts               # Определение бота
│       └── text-cleaner.ts               # Очистка форматирования
```

**Пример API route:**
```typescript
// server/api/webhooks/cian/[memberId].post.ts
export default defineEventHandler(async (event) => {
  const memberId = getRouterParam(event, 'memberId');
  const webhook = await readBody(event);
  
  // Обработка webhook
  const processor = new CianMessageProcessor();
  const result = await processor.handleIncoming(webhook, memberId);
  
  return { success: true, result };
});
```

---

#### 2.2 TypeScript Services

**CianService:**
```typescript
// server/services/cian.service.ts
export class CianService {
  private apiKey: string;
  private baseUrl = 'https://public-api.cian.ru';
  
  async getChat(chatId: string) {
    return await $fetch(`${this.baseUrl}/v1/get-chat`, {
      params: { chatId },
      headers: { Authorization: `Bearer ${this.apiKey}` }
    });
  }
  
  async sendMessage(chatId: string, text: string) {
    return await $fetch(`${this.baseUrl}/v1/send-message`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${this.apiKey}` },
      body: { chatId, content: { text } }
    });
  }
  
  async subscribeWebhooks(url: string, types: string[]) {
    return await $fetch(`${this.baseUrl}/v2/subscribe-webhooks`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${this.apiKey}` },
      body: { url, webhookTypes: types }
    });
  }
}
```

---

**Bitrix24Service:**
```typescript
// server/services/bitrix24.service.ts
export class Bitrix24Service {
  private domain: string;
  private accessToken: string;
  
  async sendMessage(connector: string, line: number, messages: any[]) {
    return await this.call('imconnector.send.messages', {
      CONNECTOR: connector,
      LINE: line,
      MESSAGES: messages
    });
  }
  
  async activateConnector(connector: string, line: number, active: boolean) {
    return await this.call('imconnector.activate', {
      CONNECTOR: connector,
      LINE: line,
      ACTIVE: active ? 1 : 0
    });
  }
  
  private async call(method: string, params: any) {
    return await $fetch(`https://${this.domain}/rest/${method}`, {
      method: 'POST',
      body: { ...params, auth: this.accessToken }
    });
  }
}
```

---

**MessageProcessor:**
```typescript
// server/services/message.processor.ts
export class CianMessageProcessor {
  private cianService: CianService;
  private bitrix24Service: Bitrix24Service;
  
  async handleIncoming(webhook: CianWebhook, memberId: string) {
    const message = webhook.chats[0].messages[0];
    
    // 1. Определение отправителя
    let senderId = message.userId;
    let senderName = webhook.users[0].name;
    
    // 2. Обработка бота
    if (senderId === 68084393) {
      const chatData = await this.cianService.getChat(webhook.chats[0].chatId);
      const buyer = chatData.result.chat.users.find(u => u.role === 'initiator');
      
      if (buyer) {
        senderId = buyer.userId;
        senderName = buyer.name;
      }
    }
    
    // 3. Разделение имени
    const [firstName, lastName] = this.splitName(senderName);
    
    // 4. Формирование сообщения для Bitrix24
    const bitrixMessage = {
      user: {
        id: senderId,
        name: firstName,
        last_name: lastName,
        skip_phone_validate: 'Y'
      },
      message: {
        id: message.messageId,
        date: Math.floor(Date.now() / 1000),
        text: message.content.text,
        disable_crm: 'N'
      },
      files: webhook.offers[0] ? [{
        url: webhook.offers[0].mainPhoto.url,
        name: 'Объект'
      }] : [],
      chat: {
        id: webhook.chats[0].chatId,
        name: webhook.chats[0].offerId,
        url: webhook.offers[0]?.url
      }
    };
    
    // 5. Отправка в Bitrix24
    return await this.bitrix24Service.sendMessage(
      'cs_cian_connector',
      await this.getLineId(memberId),
      [bitrixMessage]
    );
  }
  
  async handleOutgoing(bitrixMessage: any, memberId: string) {
    // 1. Очистка BB-кодов
    const cleanText = this.cleanBBCodes(bitrixMessage.MESSAGES[0].message.text);
    
    // 2. Отправка в CIAN
    const result = await this.cianService.sendMessage(
      bitrixMessage.MESSAGES[0].chat.id,
      cleanText
    );
    
    // 3. Подтверждение доставки
    if (result.result.messageId) {
      await this.bitrix24Service.sendStatusDelivery(bitrixMessage);
    }
    
    return result;
  }
  
  private splitName(fullName: string): [string, string] {
    const parts = fullName.trim().split(' ', 2);
    return [parts[0], parts[1] || ''];
  }
  
  private cleanBBCodes(text: string): string {
    return text.replace(/\[[^\]]*\]/g, '');
  }
}
```

---

#### 2.3 Типы TypeScript

```typescript
// types/cian.ts
export interface CianWebhook {
  chats: Array<{
    chatId: string;
    offerId: string;
    messages: Array<{
      messageId: string;
      userId: number;
      direction: 'in' | 'out';
      content: {
        text: string;
      };
    }>;
  }>;
  users: Array<{
    userId: number;
    name: string;
    avatar?: {
      images: {
        small: string;
      };
    };
  }>;
  offers: Array<{
    externalId: string;
    title: string;
    address: string;
    url: string;
    mainPhoto: {
      url: string;
    };
  }>;
}

export interface CianChatData {
  result: {
    chat: {
      chatId: string;
      users: Array<{
        userId: number;
        role: 'initiator' | 'responder';
        name: string;
      }>;
    };
  };
}
```

---

### Этап 3: Реализация

#### Приоритеты:
1. **Высокий:** sendIn.php → TypeScript API route
2. **Высокий:** sendOut.php → TypeScript API route
3. **Средний:** Activation/Deactivation → Admin UI
4. **Низкий:** Настройки → Vue UI

#### Задачи:
- [ ] Создать Nuxt проект
- [ ] Настроить TypeScript
- [ ] Реализовать CianService
- [ ] Реализовать Bitrix24Service
- [ ] Реализовать MessageProcessor
- [ ] Создать API routes для webhooks
- [ ] Написать тесты
- [ ] Миграция данных настроек

---

### Этап 4: Тестирование

#### Unit тесты:
- splitName()
- cleanBBCodes()
- Bot detection (userId: 68084393)

#### Integration тесты:
- Webhook обработка
- CIAN API вызовы
- Bitrix24 API вызовы

#### E2E тесты:
- Полный цикл сообщения
- Активация/деактивация
- Обработка ошибок

---

### Этап 5: Деплой

1. **Подготовка:**
   - Обновить webhook URLs в CIAN
   - Сохранить старые PHP файлы как backup

2. **Миграция:**
   - Развернуть Nuxt приложение
   - Обновить DNS/прокси
   - Переключить webhooks

3. **Мониторинг:**
   - Логи ошибок
   - Метрики производительности
   - Сравнение с legacy

---

## Где что посмотреть

### Ключевые файлы для изучения:

#### ✅ Обязательно прочитать:

1. **[sendIn.php](./code/sendIn.php)** (160 строк)
   - Обработка входящих webhooks
   - Логика определения бота
   - Формирование сообщения для Bitrix24

2. **[sendOut.php](./code/sendOut.php)** (50 строк)
   - Отправка исходящих сообщений
   - Очистка BB-кодов
   - Подтверждение доставки

3. **[ajax.php](./code/ajax.php)** (100 строк)
   - Активация интеграции
   - Подписка на webhooks
   - Создание endpoint файлов

4. **[deactive.php](./code/deactive.php)** (60 строк)
   - Деактивация
   - Отписка от webhooks

#### 📋 Опционально:

5. **[component.php](./code/component.php)**
   - Роутинг компонента

6. **[install.php](./code/install.php)**
   - Инициализация установки

---

### Структура директорий для изучения:

```bash
# 1. Основные обработчики
local/components/messager/cian_messager/templates/desctop/

# 2. Настройки
local/components/settings/cian_messager/templates/desctop/ajax/

# 3. Webhook endpoints
cassoftApp/market/cianMessager/in/
```

---

### Команды для быстрого доступа:

```bash
# Перейти к ключевым файлам
cd d:\projects\apps-all-old

# Основные обработчики
code local\components\messager\cian_messager\templates\desctop\sendIn.php
code local\components\messager\cian_messager\templates\desctop\sendOut.php

# Настройки
code local\components\settings\cian_messager\templates\desctop\ajax\ajax.php
code local\components\settings\cian_messager\templates\desctop\ajax\deactive.php

# Webhook endpoint (пример)
code cassoftApp\market\cianMessager\in\d17d1d15669be52925cf091ae22002d4.php
```

---

### Логи для отладки:

```bash
# sendIn логи
local/components/messager/cian_messager/templates/desctop/logSendIn.txt

# sendOut логи
local/components/messager/cian_messager/templates/desctop/logSendOut.txt

# Активация логи
local/components/settings/cian_messager/templates/desctop/ajax/logAjax.txt

# Деактивация логи
local/components/settings/cian_messager/templates/desctop/ajax/logDeactive.txt
```

---

## Дополнительные материалы

### Документация CIAN API:
- https://public-api.cian.ru/v1/docs

### Документация Bitrix24:
- https://dev.1c-bitrix.ru/rest_help/
- https://dev.1c-bitrix.ru/rest_help/im/

### Nuxt 3:
- https://nuxt.com/docs
- https://nuxt.com/docs/guide/directory-structure/server

---

**Последнее обновление:** 11 марта 2026  
**Версия:** 1.0  
**Автор:** AI Assistant  
**Статус документа:** Готов к использованию
