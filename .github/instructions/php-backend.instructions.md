---
applyTo: "**/*.php"
description: "PHP и Битрикс24 backend стандарты для app.cassoft.ru"
---

# PHP Backend Standards для app.cassoft.ru

## Версия PHP
- Используйте PHP 8.2-FPM
- Используйте современные возможности PHP 8.2

## Битрикс24 REST API
```php
// Всегда используйте класс CRest для вызовов API
CRest::call('disk.folder.uploadfile', $params);
CRest::call('crm.deal.get', ['ID' => $dealId]);
CRest::call('crm.deal.update', ['ID' => $dealId, 'fields' => $fields]);
```

## Обработка данных
- Всегда проверяйте `$_POST` и `$_FILES` перед обработкой
- Валидируйте размеры файлов (макс. 50MB на файл)
- Проверяйте MIME-типы файлов
- Используйте `file_put_contents($logFile, $message, FILE_APPEND)` для логирования

## Безопасность
- Проверяйте авторизацию через Битрикс24
- Валидируйте все входные данные
- Ограничивайте размеры загружаемых файлов
- Проверяйте типы файлов (MIME-types)

## Именование
- Файлы компонентов: `snake_case` (например: `card_object`, `save_function`)
- Классы PHP: `PascalCase` (например: `ObjectManager`, `FileUploader`)
- Переменные: `$snake_case` или `$camelCase`
- Функции: `camelCase` или `snake_case` (будьте консистентны)

## Комментарии
- Пишите комментарии на русском языке
- Документируйте все публичные методы и функции
- Объясняйте сложную бизнес-логику

## Структура компонентов
```
/local/components/
  /[vendor]/
    /[component]/
      /templates/
        /[template_name]/
          template.php      - HTML шаблон
          style.css         - стили
          script.js         - скрипты
      /ajax/
        ajax.php            - AJAX обработчики
        save.php            - сохранение данных
      component.php         - логика компонента
      .description.php      - описание
```

## Обработка ошибок
```php
// Логирование ошибок
$logFile = __DIR__ . '/logs/error.log';
$message = date('Y-m-d H:i:s') . " - Error: " . $errorMessage . "\n";
file_put_contents($logFile, $message, FILE_APPEND);

// Проверка результата API
if (isset($result['error'])) {
    error_log("Bitrix24 API Error: " . $result['error_description']);
    return false;
}
```

## PHP Лимиты
Текущие настройки для проекта:
```ini
upload_max_filesize = 50M
post_max_size = 100M
max_file_uploads = 100
max_input_vars = 10000
memory_limit = 256M
```

## Git коммиты
- Коммиты пишите на русском языке
- Формат: "Добавлен функционал загрузки файлов"
- Будьте описательными
