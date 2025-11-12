---
applyTo: "**/*.js"
description: "JavaScript frontend стандарты для app.cassoft.ru"
---

# JavaScript Frontend Standards для app.cassoft.ru

## Библиотеки
- **jQuery 3.6.0** - DOM манипуляции и AJAX
- **Dropzone.js** - загрузка файлов drag-and-drop
- **CKEditor 5** - WYSIWYG редактор
- **Select2** - улучшенные select-элементы
- **FormData API** - отправка файлов и форм

## Стиль кода
- Используйте ES6+ синтаксис где возможно
- Именование: `camelCase` для переменных и функций
- Константы: `UPPER_SNAKE_CASE`
- Комментарии на русском языке

## Dropzone.js паттерн
```javascript
// Инициализация Dropzone для множественной загрузки
var myDropzone = new Dropzone("#photo-dropzone", {
    url: "/upload-endpoint",
    maxFilesize: 10,              // MB
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    maxFiles: 100,
    parallelUploads: 5
});

// Обработка событий
myDropzone.on("success", function(file, response) {
    console.log("Файл загружен:", file.name);
});

myDropzone.on("error", function(file, errorMessage) {
    console.error("Ошибка загрузки:", errorMessage);
});
```

## FormData для AJAX
```javascript
// Отправка формы с файлами
var formData = new FormData($('#myForm')[0]);

$.ajax({
    url: 'save.php',
    type: 'POST',
    data: formData,
    processData: false,      // Важно!
    contentType: false,      // Важно!
    success: function(response) {
        console.log('Успех:', response);
    },
    error: function(xhr, status, error) {
        console.error('Ошибка:', error);
    }
});
```

## CKEditor 5 инициализация
```javascript
// Проверка существования перед созданием
if (!window.myEditor) {
    ClassicEditor.create(document.querySelector('#editor'))
        .then(editor => {
            window.myEditor = editor;
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });
}
```

## Обработка ошибок
```javascript
// Всегда логируйте ошибки
try {
    // Ваш код
} catch (error) {
    console.error('Ошибка:', error);
    // Показать пользователю
    alert('Произошла ошибка: ' + error.message);
}

// AJAX error handler
$.ajax({
    // ...
    error: function(xhr, status, error) {
        console.error('AJAX Error:', {
            status: status,
            error: error,
            response: xhr.responseText
        });
    }
});
```

## Валидация на клиенте
```javascript
// Проверка размера файла
function validateFileSize(file, maxSizeMB) {
    var maxBytes = maxSizeMB * 1024 * 1024;
    if (file.size > maxBytes) {
        alert(`Файл слишком большой. Максимум ${maxSizeMB}MB`);
        return false;
    }
    return true;
}

// Проверка типа файла
function validateFileType(file, allowedTypes) {
    if (!allowedTypes.includes(file.type)) {
        alert('Недопустимый тип файла');
        return false;
    }
    return true;
}
```

## jQuery Best Practices
```javascript
// Используйте делегирование событий для динамических элементов
$(document).on('click', '.dynamic-button', function() {
    // обработчик
});

// Кешируйте jQuery селекторы
var $form = $('#myForm');
var $submitBtn = $form.find('.submit-btn');

// Используйте data-атрибуты
var dealId = $(this).data('deal-id');
```

## CSS Классы
- Используйте `kebab-case` для CSS классов
- Примеры: `.object-card`, `.photo-gallery`, `.upload-zone`

## Комментарии
```javascript
/**
 * Загружает фотографии объекта через Dropzone
 * @param {number} objectId - ID объекта недвижимости
 * @param {string} folderPath - Путь к папке в Битрикс24 Disk
 */
function uploadPhotos(objectId, folderPath) {
    // Реализация
}
```
