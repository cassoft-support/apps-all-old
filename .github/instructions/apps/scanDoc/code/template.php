<?php
/**
 * Шаблон scanDoc - Пользовательский интерфейс
 * 
 * НАЗНАЧЕНИЕ:
 * - Отображение двух режимов работы: просмотр (galCard) и редактирование (galForm)
 * - Галерея загруженных документов с Fancybox viewer
 * - Dropzone.js для drag-and-drop загрузки файлов
 * - jQuery UI Sortable для изменения порядка документов
 * 
 * РЕЖИМЫ РАБОТЫ:
 * 1. Режим ПРОСМОТРА (#galCard):
 *    - Отображается если есть загруженные документы
 *    - Галерея Fancybox с превью
 *    - Кнопка "Изменить" для перехода в режим редактирования
 * 
 * 2. Режим РЕДАКТИРОВАНИЯ (#galForm):
 *    - Dropzone для загрузки новых файлов
 *    - Возможность удалить/пересортировать существующие
 *    - Кнопка "Сохранить" для отправки на сервер
 * 
 * ИСПОЛЬЗУЕМЫЕ БИБЛИОТЕКИ:
 * - jQuery 3.6.0 - DOM манипуляции
 * - jQuery UI - Sortable (сортировка drag-and-drop)
 * - Dropzone.js 5.x - Drag-and-drop загрузка файлов
 * - Fancybox 3.x - Просмотр галереи
 * - Line Awesome - Иконки
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Определение начальных стилей для режимов
// Если есть загруженные документы -> показываем режим просмотра
// Если нет документов -> сразу показываем форму загрузки
$styleGalCard = "display:none;";
$styleGalForm = "display:flex;";

if ($arResult["link"] && count($arResult["link"]) > 0) {
    $styleGalCard = "display:flex;";
    $styleGalForm = "display:none;";
}
?>

<!-- ===================================
     СТИЛИ КОМПОНЕНТА
     =================================== -->
<style>
    /* Общий фон приложения */
    body {
        background: #f9fafb;
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Контейнер блока информации */
    .block-info {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Карточка документа в галерее */
    .scan-doc-img {
        width: 100px;
        height: 100px;
        overflow: hidden;
        border-radius: 10px;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .scan-doc-img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Изображение внутри карточки */
    .scan-doc-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Настройка Fancybox галереи */
    .fancybox-thumbs__list a {
        max-width: calc(100% - 4px) !important;
    }

    .fancybox-thumbs__list {
        display: flex;
        flex-direction: column;
    }

    .fancybox-thumbs {
        width: 110px !important;
    }

    .fancybox-show-thumbs .fancybox-inner {
        right: 110px !important;
    }

    /* Dropzone стилизация */
    .dropzone {
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        padding: 24px;
        min-height: 150px;
        background: #f7fafc;
        transition: all 0.3s ease;
    }

    .dropzone.dz-drag-hover {
        border-color: #4299e1;
        background: #ebf8ff;
    }

    .dropzone .dz-message {
        text-align: center;
        color: #718096;
        font-size: 16px;
    }

    /* Кнопки действий */
    .form-small-button {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-small-button-blue {
        background: #4299e1;
        color: white;
    }

    .form-small-button-blue:hover {
        background: #3182ce;
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
    }

    /* Информационное сообщение */
    .info {
        padding: 12px 16px;
        background: #bee3f8;
        border-left: 4px solid #4299e1;
        border-radius: 4px;
        margin-bottom: 16px;
        color: #2c5282;
    }

    /* Блок кнопок */
    .btn_block {
        margin-top: 20px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    /* Утилитные классы для flex */
    .block-flex-columns {
        display: flex;
        flex-direction: column;
    }

    .block-flex-row {
        display: flex;
        flex-direction: row;
    }

    .--justify-between {
        justify-content: space-between;
    }

    .--justify-start {
        justify-content: flex-start;
    }

    .--wrap {
        flex-wrap: wrap;
    }

    .--nowrap {
        flex-wrap: nowrap;
    }

    .--w100p {
        width: 100%;
    }

    /* Скрытые элементы */
    [hidden] {
        display: none !important;
    }
</style>

<!-- ===================================
     ПОДКЛЮЧЕНИЕ CSS БИБЛИОТЕК
     =================================== -->
<!-- Line Awesome - иконки -->
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<!-- Dropzone - стили для drag-and-drop загрузки -->
<link href="/local/lib/css/cassoft/dropzone.css" rel="stylesheet" />

<!-- Fancybox - галерея изображений -->
<link rel="stylesheet" href="/local/lib/gallery/fancybox/jquery.fancybox.min.css"/>

<!-- Кастомные стили CassoftApp -->
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css"/>
<link rel="stylesheet" href="/local/lib/css/new/select.css"/>
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css">


<!-- ===================================
     ОСНОВНОЙ КОНТЕЙНЕР ПРИЛОЖЕНИЯ
     =================================== -->
<div class="" id="scanFormBlock">

    <!-- Скрытые поля с данными для JavaScript -->
    <!-- Для smart-процессов -->
    <input type="hidden" id="smartElId" value="<?= $arResult['smartElId'] ?? '' ?>">
    <input type="hidden" id="smartId" value="<?= $arResult['smartId'] ?? '' ?>">
    <input type="hidden" id="entityTypeId" value="<?= $arResult['entityTypeId'] ?? '' ?>">
    
    <!-- Для обычных CRM сущностей -->
    <input type="hidden" id="deal_id" value="<?= $arResult['deal_id'] ?? '' ?>">
    <input type="hidden" id="contact_id" value="<?= $arResult['contact_id'] ?? '' ?>">
    <input type="hidden" id="company_id" value="<?= $arResult['company_id'] ?? '' ?>">
    
    <!-- Код приложения -->
    <input type="hidden" id="app" value='<?= htmlspecialchars(json_encode($arResult['app'] ?? 'scanDoc')) ?>'>

    <div class="block-info">

        <!-- ===================================
             РЕЖИМ ПРОСМОТРА (galCard)
             =================================== -->
        <div class="block-info-container" id="galCard" style="<?= $styleGalCard ?>">
            <div class="block-flex-columns --w100p">
                
                <!-- Заголовок (скрыт) -->
                <div class="block-flex-row --justify-between --wrap" style="display: none;">
                    <div class="block-info-title">Копии документов</div>
                </div>
                
                <!-- Галерея документов -->
                <div class="block-flex-row --justify-start --wrap">
                    <?php if ($arResult["link"] && count($arResult["link"]) > 0): ?>
                        <?php foreach ($arResult["link"] as $link): ?>
                            <!-- Ссылка для Fancybox галереи
                                 data-fancybox="gallery" - группировка в одну галерею
                                 href - полная ссылка на файл
                            -->
                            <a class="scan-doc" 
                               data-caption="<?= htmlspecialchars($link['photo_id'] ?? '') ?>" 
                               data-fancybox="gallery" 
                               href="<?= htmlspecialchars($link['photo_link'] ?? '') ?>">
                                
                                <!-- Превью изображения -->
                                <div class="scan-doc-img" 
                                     data-photo-id="<?= htmlspecialchars($link['photo_id'] ?? '') ?>"
                                     data-photo-link="<?= htmlspecialchars($link['photo_link'] ?? '') ?>">
                                    <img src="<?= htmlspecialchars($link['photo_link'] ?? '') ?>" 
                                         alt="Document">
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Документы отсутствуют</p>
                    <?php endif; ?>
                </div>
                
                <!-- Кнопка "Изменить" -->
                <div class="btn_block">
                    <button class="form-small-button form-small-button-blue" 
                            type="button" 
                            onclick="scanEdit();">
                        <i class="las la-edit"></i> Изменить
                    </button>
                </div>
            </div>
        </div>

        <!-- ===================================
             РЕЖИМ РЕДАКТИРОВАНИЯ (galForm)
             =================================== -->
        <div class="" id="galForm" style="margin-top: 20px; <?= $styleGalForm ?>">
            <form id="scanForm" class="block-flex-columns">
                
                <!-- Dropzone зона для загрузки файлов -->
                <div class="block-flex-row --justify-start --nowrap">
                    <div class="cs-input-container">
                        
                        <!-- Скрытое поле с JSON существующих документов -->
                        <div id="gal" hidden><?= htmlspecialchars($arResult['scanDoc'] ?? '') ?></div>
                        
                        <!-- Dropzone контейнер
                             .dropzone - класс для инициализации Dropzone.js
                             .sortable - класс для jQuery UI Sortable
                             id="dropzone" - для привязки в JavaScript
                        -->
                        <div class="dropzone mt20 sortable" id="dropzone">
                            <!-- Отображение существующих документов для сортировки -->
                            <?php if ($arResult["link"] && count($arResult["link"]) > 0): ?>
                                <?php foreach ($arResult["link"] as $link): ?>
                                    <div class="dz-preview dz-file-preview" 
                                         data-photo-id="<?= htmlspecialchars($link['photo_id'] ?? '') ?>"
                                         data-photo-link="<?= htmlspecialchars($link['photo_link'] ?? '') ?>">
                                        <div class="dz-image">
                                            <img src="<?= htmlspecialchars($link['photo_link'] ?? '') ?>" 
                                                 alt="Document">
                                        </div>
                                        <div class="dz-details">
                                            <div class="dz-filename">
                                                <span><?= htmlspecialchars($link['photo_id'] ?? '') ?></span>
                                            </div>
                                        </div>
                                        <a class="dz-remove" href="javascript:void(0);" 
                                           onclick="removeExistingPhoto(this);">
                                            Удалить
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Метка для Dropzone -->
                        <label for="dropzone" class="cs-input-label cs-input-label-select" 
                               style="min-width: 350px!important;">
                            📎 Добавьте скан документа
                        </label>
                        
                        <!-- Контейнер для ошибок валидации -->
                        <div class="msg-error" data-name="gallery"></div>
                    </div>
                </div>

                <!-- Информационное сообщение при загрузке -->
                <div class="info" id="uploadInfo" style="display: none;">
                    ⏳ Начинаем сохранение файлов, процесс может занять некоторое время
                </div>

                <!-- Кнопки действий -->
                <div class="btn_block" style="margin-top: 30px;">
                    <button class="form-small-button form-small-button-blue" 
                            id="save" 
                            type="submit">
                        <i class="las la-save"></i> Сохранить
                    </button>
                    <button class="form-small-button" 
                            type="button" 
                            onclick="scanCancel();" 
                            style="background: #e2e8f0; color: #4a5568;">
                        <i class="las la-times"></i> Отмена
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>


<!-- ===================================
     ПОДКЛЮЧЕНИЕ JAVASCRIPT БИБЛИОТЕК
     =================================== -->
<!-- jQuery - основная библиотека -->
<script src="/local/lib/js/jquery-3.6.0.min.js"></script>

<!-- Битрикс24 JS SDK -->
<script src="//api.bitrix24.com/api/v1/"></script>

<!-- jQuery UI - для Sortable сортировки -->
<script src="/local/lib/js/jquery-ui.min.js"></script>

<!-- Fancybox - галерея изображений -->
<script src="/local/lib/gallery/fancybox/jquery.fancybox.min.js"></script>

<!-- Dropzone - drag-and-drop загрузка -->
<script src="/local/lib/js/dropzone.min.js"></script>

<!-- jQuery Masked Input - маски ввода -->
<script src="/local/lib/js/jquery.maskedinput.js"></script>

<!-- Основной скрипт компонента -->
<script defer type="text/javascript" src="/local/components/scanDoc/base/templates/deal/script.js?v=<?= time() ?>"></script>


<!-- ===================================
     INLINE JAVASCRIPT
     =================================== -->
<script>
/**
 * Переключение в режим редактирования
 * Вызывается при клике на кнопку "Изменить"
 */
function scanEdit() {
    $("#galCard").hide();
    $("#galForm").show();
}

/**
 * Отмена редактирования
 * Возвращает в режим просмотра или закрывает приложение если нет документов
 */
function scanCancel() {
    var hasDocuments = $("#galCard .scan-doc-img").length > 0;
    
    if (hasDocuments) {
        $("#galForm").hide();
        $("#galCard").show();
    } else {
        // Закрытие приложения Битрикс24
        BX24.closeApplication();
    }
}

/**
 * Удаление существующей фотографии
 * Вызывается при клике на "Удалить" в превью
 * 
 * @param {HTMLElement} element - элемент кнопки удаления
 */
function removeExistingPhoto(element) {
    var preview = $(element).closest('.dz-preview');
    preview.fadeOut(300, function() {
        $(this).remove();
    });
}

/**
 * Инициализация при загрузке страницы
 */
$(document).ready(function() {
    console.log('scanDoc template loaded');
    
    // Инициализация Bitrix24 SDK
    BX24.init(function() {
        console.log('BX24 initialized');
        console.log('Auth:', BX24.getAuth());
    });

    // Инициализация Fancybox для галереи
    if (typeof $.fancybox !== 'undefined') {
        $('[data-fancybox="gallery"]').fancybox({
            loop: true,
            buttons: [
                'zoom',
                'slideShow',
                'fullScreen',
                'thumbs',
                'close'
            ],
            thumbs: {
                autoStart: true
            }
        });
    }

    // jQuery UI Sortable для сортировки drag-and-drop
    if ($.fn.sortable) {
        $(".sortable").sortable({
            placeholder: "ui-state-highlight",
            cursor: "move",
            tolerance: "pointer",
            update: function(event, ui) {
                console.log('Order changed');
            }
        });
    }
});
</script>

<?php
/**
 * ОПИСАНИЕ РАБОТЫ ШАБЛОНА:
 * 
 * 1. РЕЖИМ ПРОСМОТРА (galCard):
 *    - Отображается если $arResult["link"] не пуст
 *    - Показывает галерею с Fancybox
 *    - Превью документов 100x100px
 *    - Кнопка "Изменить" переключает в режим редактирования
 * 
 * 2. РЕЖИМ РЕДАКТИРОВАНИЯ (galForm):
 *    - Dropzone для загрузки новых файлов
 *    - Существующие документы отображаются как превью
 *    - jQuery UI Sortable для изменения порядка
 *    - Кнопка "Сохранить" отправляет AJAX в save.php
 *    - Кнопка "Отмена" возвращает в просмотр или закрывает
 * 
 * 3. DROPZONE НАСТРОЙКИ (в script.js):
 *    - autoProcessQueue: false (ручная отправка)
 *    - uploadMultiple: true (множественная загрузка)
 *    - acceptedFiles: "image/*,application/pdf"
 *    - maxFiles: 100
 * 
 * 4. AJAX ОТПРАВКА (в script.js):
 *    - URL: save.php текущего шаблона
 *    - Метод: POST
 *    - FormData с:
 *      - authParams: JSON с токенами Битрикс24
 *      - app: код приложения
 *      - deal_id/contact_id/company_id: ID сущности
 *      - smartElId, smartId, entityTypeId: для smart-процессов
 *      - sort: массив порядка файлов
 *      - oldPhotoInfo: массив "id,link" существующих
 *      - files: загруженные файлы
 * 
 * 5. СКРЫТЫЕ ПОЛЯ:
 *    - Содержат данные для JavaScript
 *    - Заполняются из $arResult компонента
 *    - Используются при отправке на сервер
 */
