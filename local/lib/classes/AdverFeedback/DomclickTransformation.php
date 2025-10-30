<?php

namespace Cassoft\AdverFeedback;

class DomclickTransformation {
    
    public function __construct() {

    }

    public function validateDomclickErrorReport(string $status, $errors = null) {
        $result = [];   
        
        if($errors) {
            if(isset($errors['Reason']['0'])) {
                foreach($errors['Reason'] as $error) {
                    $result[] = $error['Descr'];
                }
            } else {
                $message = $errors['Reason']['Descr'];
                $result[] = $message; 
            }
        } else {
            if($status === 'published') {
                $result[] = 'Опубликовано без ошибок';
            }
        }
         
        return $result;
    }

    public function validateDomclickErrorReportDiscount(string $status, $errors = null) {
        $result = [];   
        
        if($errors) {
            if(isset($errors['Reason']['0'])) {
                foreach($errors['Reason'] as $error) {
                    $result[] = $error['Descr'];
                }
            } else {
                $message = $errors['Reason']['Descr'];
                $result[] = $message; 
            }
        } else {
            if($status === 'published') {
                //$result[] = 'Опубликовано без ошибок';
            }
        }
         
        return $result;
    }


    private function getErrorsDescription(string $errorCode) {
        $result = '';
        $errors = [
            'required_field_missing' => 'Не заполнено обязательное поле',
            'no_house' => 'В адресе отсутствует дом',
            'field_bad_format' => 'Неверный формат поля',
            'field_invalid_choice' => 'Недопустимое значение поля',
            'field_less_than_minimum' => 'Значение меньше минимального',
            'field_more_than_maximum' => 'Значение больше максимального',
            'floor_more_than_max_floor' => 'Этаж квартиры больше максимального в доме',
            'geo_location_not_found' => 'Адрес не найден',
            'contacts' => '[Модерация] Контактные данные',
            'advert' => '[Модерация] Реклама',
            'obscene' => '[Модерация] Ненормативная лексика',
            'rejected_by_moderation' => '[Модерация] Снято службой модерации по другой причине',
            'not_for_mortgage' => '[Модерация] Объект недоступен в ипотеку',
            'sold' => '[Модерация] Объект продан',
            'not_for_sale' => '[Модерация] Объект не продается',
            'owner_complaint' => '[Модерация] Жалоба собственника',
            'field_incorrect_value' => '[Модерация] В объявлении указаны некорректные данные',
            'duplicated' => 'Является дубликатом',
            'zhk_not_attached_to_company' => '[Новостройки] ЖК не привязан к компании',
            'zhk_building_not_attached_to_complex' => '[Новостройки] Корпус не привязан к ЖК',
            'non_unique_id' => '[Новостройки] Неуникальный идентификатор',
            'non_unique_flat_number' => '[Новостройки] Неуникальный номер квартиры',
            'smart_moderation' => '[Модерация] Снято службой модерации',
        ];
        if(!array_key_exists($errorCode, $errors)) {
            $result = "Код ошибки - {$errorCode}. Дополнительную информацию нужно уточнить в личном кабинете Домклика";
        } else {
            $result = $errors[$errorCode];
        }
        return $result;
    }
}
