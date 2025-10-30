<?php

namespace Cassoft\AdverFeedback;

class CianTransformation {

    public function __construct() {

    }

    public function statusTransform(string $status = null) {
        if($status == null) {
            return '';
        }
        switch($status) {
            case 'Draft':
                $result = 'Черновик';
                break;
            case 'Published':
                $result = 'Опубликовано';
                break;
            case 'Deactivated':
                $result = 'Деактивировано';
                break;
            case 'Refused':
                $result = 'Отклонено модератором';
                break;
            case 'Deleted':
                $result = 'Удалён';
                break;
            case 'Sold':
                $result = 'Продано/Сдано';
                break;
            case 'RemovedByModerator':
                $result = 'Удалено модератором';
                break;
            case 'Blocked':
                $result = 'Санкция: "Приостановкa публикации"';
                break;
        }
        return $result;
    }

    public function errorsTransform(string $status, array $errors, array $warnings) { 
        $result = [];
        $messages = array_merge($errors, $warnings);
        if(empty($messages)) {
            if($status === 'Published') {
                $result[] = 'Успешно опубликовано';
            } else {
                $result[] = 'Дополнительную информации нужно уточнить в личном кабинете Циана';
            }
        } else {
            $result = $messages;
        }
        return $result;
    }

}
