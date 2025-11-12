<?php
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

// Подключаем модуль highloadblock
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!Loader::includeModule('highloadblock')) {
    die('Модуль highloadblock не подключен');
}

// Укажите ID вашего хайдблока
$hlblockId = 8; // Замените на ваш ID хайдблока

// Получаем информацию о хайдблоке
$hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();

if (!$hlblock) {
    die('Хайдблок не найден');
}

// Получаем сущность хайдблока
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityDataClass = $entity->getDataClass();

// Получаем список полей
$fields = $entity->getFields();

echo "Список полей хайдблока (ID: $hlblockId):<br>";

$typeDescriptions = [
    'string' => 'Строка',
    'integer' => 'Число',
    'float' => 'Дробное число',
    'datetime' => 'Дата/время',
    'date' => 'Дата',
    'boolean' => 'Логический',
    'enum' => 'Перечисление',
    'file' => 'Файл',
    'text' => 'Текст',
    // Добавьте другие типы по мере необходимости
];

foreach ($fields as $field) {
    $fieldName = $field->getName();
    $fieldTitle = $field->getTitle(); // Получаем заголовок поля
    $fieldType = $field->getDataType();
    $fieldTypeDescription = isset($typeDescriptions[$fieldType]) ? $typeDescriptions[$fieldType] : 'Неизвестный тип';
   // $hb[$field->getDataType()] = $field->getName();
    $hb[$fieldName]['TITLE'] = $fieldTitle;
    $hb[$fieldName]['TYPE'] = $fieldType;
    $hb[$fieldName]['TYPE_DESC'] = $fieldTypeDescription;
//    echo "Название поля: " . $field->getName() . "<br>";
//    echo "Тип поля: " . $field->getDataType() . "<br><br>";
}
pr($hb, '');

$jsonData = json_encode($hb, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// Указываем путь к файлу, в который нужно сохранить данные
$filePath = $_SERVER["DOCUMENT_ROOT"] . "/pub/dataHb.json";

// Сохраняем строку JSON в файл
file_put_contents($filePath, $jsonData) ;
