<?php 
//Общие функции
function d($well) {
    echo '<pre>';
    print_r($well);
    echo '</pre>';
}
function console ($var, $label = '') {
    $str = json_encode(print_r ($var, true));
    echo "<script>console.group('".$label."');console.log('".$str."');console.groupEnd();</script>";
}
function dlabel ($var, $label = '') {
    if (!empty($label)) {
        echo "<pre>";
        echo $label;
        echo "</pre>";
    }
    echo "<pre>";
    print_r ($var);
    echo "</pre>";
}
function pr ($var, $label = '') {
    if (!empty($label)) {
        echo "<pre>";
        echo $label;
        echo "</pre>";
    }
    echo "<pre>";
    print_r ($var);
    echo "</pre>";
}
//if(!function_exists('getAllElem')) {
//    function getAllElem($object, $filter)
//    {
//        $result = [];
//        $res = $object::getList([
//            'select' => [
//                '*'
//            ],
//            'filter' => $filter
//        ]);
//        while ($row = $res->fetch()) {
//            $result[] = $row;
//        }
//        return $result;
//    }
//}

function callCurl($key, $method, $params){
$date=date("d.m.YTH:i:s");
$log = __DIR__."/logCall.txt";

  $arParams=http_build_query($params);
  $ch = curl_init($key.$method.'.json?'.$arParams);
  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $result = curl_exec($ch);
//file_put_contents($file_log, print_r($result,true), FILE_APPEND);
  curl_close($ch);
  $arResult=json_decode($result, true);
  return $arResult;
}

function p($print, $label="", $file_log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/log.txt"){
    if($label === 'старт' || $label === 'start' || $print === 'start' || $print === 'старт'){
        file_put_contents($file_log, "---------------------".date("d.m.YTH:i:s").PHP_EOL .print_r($print,true). PHP_EOL,true);
    } elseif($label === 'time' || $label === 'время'){
        file_put_contents($file_log, "---------------------".date("d.m.YTH:i:s").PHP_EOL .print_r($print,true). PHP_EOL, FILE_APPEND);
    }
    else{
        file_put_contents($file_log, $label.PHP_EOL .print_r($print,true). PHP_EOL, FILE_APPEND);
    }

}

function array_multisort_value()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
function translit($value)
{
    $converter = array(
        'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
        'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
        'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
        'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
        'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
        'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
        'э' => 'e',    'ю' => 'yu',   'я' => 'ya',   ',' => '_',    '.' => '_',

        'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
        'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
        'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
        'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
        'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
        'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
        'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',   ' ' => '_',    '  ' => '_',
    );

    $value = strtr($value, $converter);
    return $value;
}
function random_code($length = 6, $type='')
{
    if($type === 'letter') {
        $arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        );
    } else if($type === 'number'){
        $arr = array(
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
        );
    } else{
        $arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
        );
    }
    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $arr[random_int(0, count($arr) - 1)];
    }
    return $res;
}
function generateUniqueCode($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateCodeCS($length = 16) {
    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomCode = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, $charactersLength - 1)];
    }
    $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');
    $resProfileCsCode= $profileHb->getCSCode();
    do {
        $randomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }
    } while (in_array($randomCode, $resProfileCsCode));
    return $randomCode;
}

function formatPhoneNumber($phoneNumber)
{
    // Удаляем все символы, кроме цифр
    $digits = preg_replace('/\D/', '', $phoneNumber);

    // Проверяем, что номер начинается с 7 или 8 и содержит 11 цифр
    if (strlen($digits) == 11 && ($digits[0] == '7' || $digits[0] == '8')) {
        // Убираем первую цифру, если это 8
        if ($digits[0] == '8') {
            $digits = '7' . substr($digits, 1);
        }

        // Форматируем номер
        $formatted = sprintf('+%s %s %s-%s-%s',
            substr($digits, 0, 1), // +7
            substr($digits, 1, 3), // 969
            substr($digits, 4, 3), // 748
            substr($digits, 7, 2), // 66
            substr($digits, 9, 2) // 93
        );

        return $formatted;
    }
}
function utf8ToBase64($str) {
    return base64_encode(utf8_encode($str));
}

function verifySignature(array $response, string $secretKey): bool {
    $log = __DIR__."/logVerifySignature.txt";
   // p($response, "start", $log);
    // 1. Отфильтровываем пустые поля и убираем signature
    $filteredParams = [];
    foreach ($response as $key => $value) {
        if ($key !== 'signature' && !is_null($value) && $value !== '') {
            $filteredParams[$key] = $value;
        }
    }

    // 2. Сортируем ключи
    ksort($filteredParams);

    // 3. Формируем строку
    $chunks = [];
    foreach ($filteredParams as $key => $value) {
        $base64 = utf8ToBase64($value);
        $chunks[] = "$key=$base64";
    }

    $dataStr = implode('&', $chunks);

    // 4. SHA1 хэширование
    $firstHash = hash('sha1', $secretKey . hash('sha1', $secretKey . $dataStr));

    // 5. Сравниваем с полученной подписью
    $expectedSignature = strtolower($firstHash);
    $receivedSignature = strtolower($response['signature'] ?? '');

    return $expectedSignature === $receivedSignature;
}