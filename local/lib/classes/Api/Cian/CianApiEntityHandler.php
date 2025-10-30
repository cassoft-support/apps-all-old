<?php

namespace Cassoft\Api\Cian;

class CianApiEntityHandler
{
    public static function getStatus(string $status)
    {
        switch ($status) {
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

    public static function getErrors($errors, $warnings)
    {
        $result = [];
        $errors = self::rmExcessString($errors);
        $warnings = self::rmExcessString($warnings);
        $result['errorsList'] = array_merge($errors, $warnings);
        $result['errorsCount'] = count($result['errorsList']);
        return json_encode($result);
    }

    public static function rmExcessString($errors)
    {
        $result = [];
        if (!empty($errors)) {
            foreach ($errors as $error) {
                if ($error === 'Ошибка при размещении объявления') {
                    continue;
                } else {
                    $result[] = $error;
                }
            }
        }
        return $result;
    }
    private static function getTlColor($status)
    {
        $result = '';
        switch ($status) {
            case 'Blocked':
                $result = 'red';
                break;
            case 'Deactivated':
                $result = 'red';
                break;
            case 'Deleted':
                $result = 'red';
                break;
            case 'Draft':
                $result = 'red';
                break;
            case 'Moderate':
                $result = 'red';
                break;
            case 'Published':
                $result = 'green';
                break;
            case 'Refused':
                $result = 'red';
                break;
            case 'RemovedByModerator':
                $result = 'red';
                break;
            case 'Sold':
                $result = 'red';
                break;
            default:
                $result = 'red';
                break;
        }
        return $result;
    }

    public static function getDataForEntity($report)
    {
        $result = [];
        if (!empty($report)) {
            foreach ($report as $ad) {
              echo "\n";
              print_r($ad);
                $tableRow = [];
                $tableRow['deal_id'] = preg_replace("/[^0-9]/", '', $ad['externalId']);
                $tableRow['site'] = 'cian';
                $tableRow['tl_color'] = self::getTlColor($ad['status']);
                $tableRow['status'] = self::getStatus($ad['status']);
                $tableRow['date_end'] = null;
                $tableRow['link'] = $ad['url'];
                $tableRow['errors'] = self::getErrors($ad['errors'], $ad['warnings']);
                $tableRow['up'] = null;
                $tableRow['discount'] = null;
                $tableRow['id_site'] =preg_replace("/[^0-9]/", '', $ad['offerId']); 
                $tableRow['discount_info'] = null;
                $result[$tableRow['deal_id']] = $tableRow;
            }
        }
        return $result;
    }
}