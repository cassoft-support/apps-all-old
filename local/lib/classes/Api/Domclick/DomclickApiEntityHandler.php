<?php

namespace Cassoft\Api\Domclick;

class DomclickApiEntityHandler
{
    public static function getErrors($item)
    {
        $result = [];
        $errors = self::getErrorsList($item['PublishRejectionReasons']['Reason']);
        $errorsCount = count($errors);
        $result['errorsList'] = $errors;
        $result['errorsCount'] = $errorsCount;
        return $result;
    }

    public static function getErrorsDiscount($item)
    {
        $result = [];
        $errorsDiscount = self::getErrorsList($item['DiscountStatus']['RejectionReasons']['Reason']);
        $errorsDiscountCount = count($errorsDiscount);
        $result['errorsDiscount'] = $errorsDiscount;
        $result['errorsDiscountCount'] = $errorsDiscountCount;
        return $result;
    }

    public static function getErrorsList($errors = [])
    {
        $result = [];
        if (!empty($errors)) {
            if (array_key_exists('0', $errors)) {
                foreach ($errors as $error) {
                    $result[] = $error['Descr'];
                }
            } else {
                $result[] = $errors['Descr'];
            }
        } 
        return $result;
    }

    private static function getTlColor(string $status)
    {
        $result = '';
        switch ($status) {
            case 'pending':
                $result = 'green';
                break;
            case 'sold':
                $result = 'orange';
                break;
            case 'published':
                $result = 'green';
                break;
            case 'rejected':
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
        foreach ($report as $ad) {
            $tableRow = [];
            $tableRow['deal_id'] = preg_replace("/[^0-9]/", '', $ad['ExternalId']);
            $tableRow['status'] = $ad['Status']['Descr'];
            $tableRow['date_end'] = null;
            $tableRow['link'] = null;
            $tableRow['errors'] = null;
            $tableRow['up'] = null;
            $tableRow['site'] = 'domclick';
            $tableRow['tl_color'] = self::getTlColor($ad['Status']['Code']);
            $tableRow['discount'] = null;
            $tableRow['discount_info'] = null;
            switch ($ad['Status']['Code']) {
                case 'pending': {
                        break;
                    }
                case 'sold': {
                        break;
                    }
                case 'published': {
                        $tableRow['link'] = $ad['Publication']['DomclickURL'];
                        $tableRow['up'] = $ad['Publication']['Tariff']['Descr'];
                        $tableRow['discount'] = $ad['DiscountStatus']['Descr'];
                        $tableRow['discount_info'] = json_encode(self::getErrorsDiscount($ad));
                        break;
                    }
                case 'rejected': {
                        $tableRow['errors'] = json_encode(self::getErrors($ad));
                        break;
                    }
            }
            $result[$tableRow['deal_id']] = $tableRow;
        }
        return $result;
    }
}
