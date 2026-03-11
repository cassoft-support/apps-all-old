# Документация приложений для миграции на Vue + Nuxt

**Дата создания:** 11 марта 2026  
**Проект:** app.cassoft.ru  
**Цель:** Подготовка к миграции мессенджеров на Vue 3 + Nuxt 3

---

## 📚 Доступные документации

### 1. [CIAN Messenger](./cian/README.md)
**Интеграция с ЦИАН**

- ✅ Полная документация архитектуры
- ✅ Исходные коды обработчиков с комментариями
- ✅ API интеграция (3 типа webhooks)
- ✅ Обработка бота (userId: 68084393)
- ✅ План миграции на Vue+Nuxt
- ✅ TypeScript сервисы и типы

**Сложность:** ⭐⭐⭐⭐ (Сложная)

**Ключевые файлы:**
- [sendIn.php](./cian/code/sendIn.php) - Обработка входящих (160 строк)
- [sendOut.php](./cian/code/sendOut.php) - Отправка исходящих (50 строк)
- [ajax.php](./cian/code/ajax.php) - Активация (100 строк)
- [deactive.php](./cian/code/deactive.php) - Деактивация (60 строк)

---

### 2. [Domclick Messenger](./dc/README.md)
**Интеграция с Домкликом (Сбербанк)**

- ✅ Полная документация архитектуры
- ✅ Исходные коды обработчиков с комментариями
- ✅ API интеграция (1 тип webhook)
- ✅ Проверка ролей (BUYER/AGENT)
- ✅ План миграции на Vue+Nuxt
- ✅ Сравнение с CIAN

**Сложность:** ⭐⭐⭐ (Средняя, проще чем CIAN)

**Ключевые файлы:**
- [sendIn.php](./dc/code/sendIn.php) - Обработка входящих (120 строк)
- [sendOut.php](./dc/code/sendOut.php) - Отправка исходящих (40 строк)
- [ajax.php](./dc/code/ajax.php) - Активация (80 строк)
- [deactive.php](./dc/code/deactive.php) - Деактивация (50 строк)

---

## 🔍 Сравнение мессенджеров

| Параметр | CIAN | Domclick |
|----------|------|----------|
| **Webhook типы** | 3 (offers, newbuilding, readability) | 1 (new_messages) |
| **Обработка бота** | ✅ Да (68084393 + API get-chat) | ❌ Нет |
| **API запросы** | 2 (get-chat + send-message) | 1 (send-message) |
| **Роли** | initiator/responder | BUYER/AGENT |
| **Файлы в webhook** | ✅ mainPhoto | ❌ Нет |
| **Сложность** | Высокая | Средняя |
| **Время миграции** | ~100% | ~60% от CIAN |

---

## 🎯 Общая архитектура

### Текущая (PHP):
```
Webhook → cassoftApp/in/{id}.php → local/components/messager → Bitrix24
                                            ↓
                                       sendIn.php / sendOut.php
```

### Целевая (Vue+Nuxt):
```
Webhook → Nuxt API /api/webhooks/{id} → TypeScript Services → Bitrix24
                              ↓
                    CianService / DomclickService
                    MessageProcessor
                    Bitrix24Service
```

---

## 📖 Общие технологии

### Текущий стек:
- **Backend:** PHP 8.2-FPM
- **Битрикс24:** REST API, ImConnector, Entity
- **Архитектура:** Webhook handlers (без UI)

### Целевой стек:
- **Backend:** TypeScript + Nuxt 3 Server
- **Runtime:** Node.js
- **API Client:** $fetch (ohmyfetch)
- **Type Safety:** Full TypeScript
- **Testing:** Vitest

---

## 🚀 Быстрый старт

### Просмотр документации:

```bash
# Перейти в директорию
cd d:\projects\apps-all-old\.github\instructions\apps

# Открыть CIAN документацию
code cian/README.md

# Открыть Domclick документацию
code dc/README.md
```

### Изучение кода:

```bash
# CIAN обработчики
code cian/code/sendIn.php
code cian/code/sendOut.php

# Domclick обработчики
code dc/code/sendIn.php
code dc/code/sendOut.php
```

---

## 📝 План работы

### Этап 1: Изучение ✅ (ВЫПОЛНЕНО)
- ✅ Документирована архитектура CIAN
- ✅ Документирована архитектура Domclick
- ✅ Скопированы ключевые файлы с комментариями
- ✅ Описаны API и бизнес-логика

### Этап 2: Проектирование (Следующий шаг)
- [ ] Создать Nuxt 3 проект
- [ ] Спроектировать структуру сервисов
- [ ] Определить TypeScript типы
- [ ] Подготовить тестовую среду

### Этап 3: Реализация
- [ ] Реализовать CianService
- [ ] Реализовать DomclickService
- [ ] Реализовать Bitrix24Service
- [ ] Реализовать MessageProcessor
- [ ] Создать API routes

### Этап 4: Тестирование
- [ ] Unit тесты
- [ ] Integration тесты
- [ ] E2E тесты

### Этап 5: Деплой
- [ ] Обновить webhooks
- [ ] Развернуть Nuxt приложение
- [ ] Мониторинг

---

## 🔗 Полезные ссылки

### Документация API:
- [CIAN API](https://public-api.cian.ru/v1/docs)
- [Bitrix24 REST API](https://dev.1c-bitrix.ru/rest_help/)
- [Bitrix24 ImConnector](https://dev.1c-bitrix.ru/rest_help/im/)

### Фреймворки:
- [Nuxt 3](https://nuxt.com/docs)
- [Vue 3](https://vuejs.org/)
- [TypeScript](https://www.typescriptlang.org/)

### Инструменты:
- [Vitest](https://vitest.dev/) - тестирование
- [ohmyfetch](https://github.com/unjs/ofetch) - HTTP клиент

---

## 📊 Метрики

### CIAN Messenger:
- **Строк кода (PHP):** ~410
- **Компонентов:** 4 ключевых файла
- **API endpoints:** 6
- **Webhook типов:** 3

### Domclick Messenger:
- **Строк кода (PHP):** ~290
- **Компонентов:** 4 ключевых файла
- **API endpoints:** 4
- **Webhook типов:** 1

### Оценка миграции:
- **CIAN:** ~40 часов разработки
- **Domclick:** ~25 часов разработки
- **Общее тестирование:** ~20 часов
- **Итого:** ~85 часов

---

## 💡 Рекомендации

### Порядок миграции:
1. **Начать с Domclick** (проще, быстрее освоить подход)
2. **Затем CIAN** (сложнее, но опыт с Domclick поможет)
3. **Переиспользовать код:** Битрикс24Service + базовый MessageProcessor

### Риски:
- ⚠️ **CIAN:** Обработка бота требует дополнительного API запроса
- ⚠️ **Domclick:** Меньше документации API
- ⚠️ **Общее:** Необходима синхронизация webhook URLs

### Преимущества миграции:
- ✅ Type Safety (TypeScript)
- ✅ Тестируемость
- ✅ Переиспользование кода
- ✅ Современный стек
- ✅ Лучшая производительность (Node.js)

---

**Последнее обновление:** 11 марта 2026  
**Версия:** 1.0  
**Автор:** AI Assistant  
**Статус:** Готово к использованию
