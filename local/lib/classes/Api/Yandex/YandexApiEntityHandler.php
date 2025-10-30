<?php

namespace Cassoft\Api\Yandex;

require __DIR__ . '/errors_ru.php';

use YandexErrorsListRu;

class YandexApiEntityHandler
{

    public static function getLink($item)
    {
        $state = $item['state'];
        if (array_key_exists('errors', $state)) {
            return null;
        } else {
            return $item['url'];
        }
    }
    public static function getTlColor($item)
    {
        $state = $item['state'];
        if (array_key_exists('errors', $state)) {
            return 'red';
        } else {
            return 'green';
        }
    }
    public static function getErrors($item)
    {
        $result = [];
        $result['errorsList'] = self::getErrorsList($item);
        $result['errorsCount'] = count($result['errorsList']);
        return $result;
    }
    public static function getErrorsList($item)
    {
        $state = $item['state'];
        if (array_key_exists('errors', $state)) {
            $errors = [];
            foreach ($state['errors'] as $error) {
                if (YandexErrorsListRu::$errors_ru[$error['type']]) {
                    $errors[] = YandexErrorsListRu::$errors_ru[$error['type']];
                } else {
                    $errors[] = $error['type'];
                }
            }
            return $errors;
        }
    }
    public static function getStatus($item)
    {
        $state = $item['state'];
        if (array_key_exists('errors', $state)) {
            return 'Не опубликовано';
        } else {
            return 'Опубликовано';
        }
    }
    public static function getDataForEntity($report)
    {
        $result = [];
        foreach ($report as $ad) {
            $tableRow = [];
            $tableRow['status'] = self::getStatus($ad);
            $tableRow['date_end'] = null;
            $tableRow['deal_id'] = preg_replace("/[^0-9]/", '', $ad['internalId']);
            $tableRow['tl_color'] = self::getTlColor($ad);
            $tableRow['link'] = self::getLink($ad);
            $tableRow['errors'] = json_encode(self::getErrors($ad));
            $tableRow['up'] = null;
            $tableRow['site'] = 'yandex';
            $tableRow['discount'] = null;
            $tableRow['discount_info'] = null;
            $result[$tableRow['deal_id']] = $tableRow;
        }
        return $result;
    }
}
