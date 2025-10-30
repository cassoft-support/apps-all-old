<?php

namespace Cassoft\Api\Avito;

class AvitoApiEntityHandler
{
    public static function getError($item)
    {
        $result = [];
        $result['errorsList'] = self::getErrorList($item['messages']);
        $result['errorsCount'] = count($result['errorsList']);
        return $result;
    }

    public static function getErrorList($messages)
    {
        $result = [];
        foreach ($messages as $message) {
            if ($message['type'] === 'error' || $message['type'] === 'alarm') {
                if (!empty($message['description'])) {
                    $message['description'] = strip_tags($message['description']);
                    $result[] = $message['description'];
                }
            }
        }
        return $result;
    }

    public static function getDate($date)
    {
        $newDate = null;
        if (!empty($date)) {
            $timestamp = strtotime($date);
            $newDate = date('d.m.Y', $timestamp);
        }
        return $newDate;
    }

    private static function getTlColor($status)
    {
        $result = null;
        if (!empty($status)) {
            switch ($status) {
                case 'success':
                    $result = 'green';
                    break;
                case 'problem':
                    $result = 'orange';
                    break;
                case 'not_publish':
                    $result = 'red';
                    break;
                case 'error':
                    $result = 'red';
                    break;
                default:
                    $result = 'red';
            }
        }
        return $result;
    }

    public static function getDataForEntity($report)
    {
        $result = [];
        if (!empty($report)) {
            foreach ($report as $ad) {
                $tableRow = [];
                $tableRow['deal_id'] = preg_replace("/[^0-9]/", '', $ad['ad_id']);
                $tableRow['site'] = 'avito';
                $tableRow['tl_color'] = self::getTlColor($ad['statuses']['general']['value']);
                $tableRow['status'] = $ad['statuses']['general']['help'];
                $tableRow['date_end'] = self::getDate($ad['avito_date_end']);
                $tableRow['link'] = $ad['url'];
                $tableRow['errors'] = json_encode(self::getError($ad));
                $tableRow['up'] = null;
                $tableRow['discount'] = null;
                $tableRow['discount_info'] = null;
                $result[$tableRow['deal_id']] = $tableRow;
            }
        }
        return $result;
    }
}
