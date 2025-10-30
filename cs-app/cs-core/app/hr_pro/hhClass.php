<?php
namespace CSlibs\App\HR;

class hhClass
{
private $auth;
private $app;
private $member_id;
private $user;
private $hhCode;
private $hhRefreshToken;
private $hhKey;
private $fileLog;
private $date;

public function __construct($auth, $app, $user){
    $this->auth = $auth;
    $this->app = $app;
    $this->user = $user;
    $this->hhCode = $this->hhCode($user);
    $this->hhRefreshToken = $this->hhRefreshToken($user);
    $this->hhKey = $this->hhKey($app);
    $this->fileLog = __DIR__ . "/logClass.txt";
    $this->date = date("d.m.YTH:i:s");

}

    /**
     * Получаем код авторизации для пользователя
     * @param string $memberId member_id портала
     * @param string $user id пользователя
     * @noinspection PhpUnused не ппроверять на использование
     *
     */
public function hhCode($user){
    $resSetup = $this->auth->CScore->call('entity.item.get', ['ENTITY' => 'setup',])[0]['PROPERTY_VALUES'];
    $setupKey = json_decode($resSetup['CS_HH_KEY'], true);
    $date = strtotime(date('c'));
    if($setupKey[$user]['date_close'] > $date) {
        $code= $setupKey[$user]['access_token'];
    }else{
        $code= $this->curlPostAuthUp();
    }
    return $code;
}

public function hhRefreshToken($user){
    $resSetup = $this->auth->CScore->call('entity.item.get', ['ENTITY' => 'setup',])[0]['PROPERTY_VALUES'];
    $setupKey = json_decode($resSetup['CS_HH_KEY'], true);
return $setupKey[$user]['refresh_token'];
}
public function hhCodeAll(){
    $resSetup = $this->auth->CScore->call('entity.item.get', ['ENTITY' => 'setup',])[0];
    $setupKey['data'] = json_decode($resSetup['PROPERTY_VALUES']['CS_HH_KEY'], true);
    $setupKey['ID'] = $resSetup['ID'];
return $setupKey;
}
public function hhKey($app){
    $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
    $appParams = $HlApp->hl::getList([
        'select' => ['*'],
        'order' => [],
        'filter' => [
            'UF_APP_NAME' => $app
        ],
        'limit' => 1
    ])->fetch();
    $resKey['ID']= $appParams['UF_HH_ID'];
    $resKey['KEY']= $appParams['UF_HH_KEY'];
return $resKey;
}

 public function curlPostAuth($grant_type){
    $log = __DIR__."/logPostAuth.txt";
    p($grant_type , "start", $log);
    $redirectUri = 'https://app.cassoft.ru/cassoftApp/market/hr/ajax/handlerHh.php';
// URL для получения токена
        $url = 'https://hh.ru/oauth/token';
// Параметры POST-запроса
        $postFields = [
            'grant_type' => $grant_type,
            'client_id' => $this->hhKey['ID'],
            'client_secret' => $this->hhKey['KEY'],
            'code' => $this->hhCode,
            'redirect_uri' => $redirectUri,
        ];
p($postFields , "postFields", $log);
// Инициализация cURL
        $ch = curl_init($url);
// Настройка параметров cURL
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
// Выполнение запроса и получение ответа
        $response = curl_exec($ch);
// Проверка на ошибки
        if (curl_errno($ch)) {
            p(curl_error($ch) , "Ошибка cURL", $log);
        } else {
            // Декодирование JSON-ответа
            $responseData = json_decode($response, true);
p($responseData , "responseData", $log);
        }
// Закрытие cURL-сессии
        curl_close($ch);
return $responseData;
    }

    public function curlPostAuthUp()
    {
        $log = __DIR__ . "/logAuthUpdate.txt";
// URL для обновления токена
        $url = 'https://hh.ru/oauth/token';
// Параметры POST-запроса
        $postFields = [
            'grant_type' => "refresh_token",
            'refresh_token' => $this->hhRefreshToken,
        ];
        p($postFields, "start", $log);
     //   $responseData=  $this->curlPost($urlType, $postFields);
        $ch = curl_init($url);
// Настройка параметров cURL
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
// Выполнение запроса и получение ответа
        $response = curl_exec($ch);
// Проверка на ошибки
        if (curl_errno($ch)) {
            p(curl_error($ch) , "Ошибка cURL", $log);
        } else {
            // Декодирование JSON-ответа
            $responseData = json_decode($response, true);
            p($responseData , "responseData", $log);
        }
// Закрытие cURL-сессии
        curl_close($ch);
        if(!empty($responseData['access_token'])) {
            $setupKey = $this->hhCodeAll();
            $setupKeyData = $setupKey['data'];
            $dateClose = strtotime(date('c')) + $responseData['expires_in'];
            $setupKeyData[$this->user] =
                [
                    'access_token' => $responseData['access_token'],
                    'refresh_token' => $responseData['refresh_token'],
                    'date_close' => $dateClose,
                ];

            $paramsUp = [
                'ENTITY' => 'setup',
                'ID' => $setupKey['ID'],
                'PROPERTY_VALUES' => [
                    'CS_HH_KEY' => json_encode($setupKeyData)
                ]
            ];
            $resSetupUp = $this->auth->CScore->call('entity.item.update', $paramsUp);
            $code = $responseData['access_token'];
        }

        return $code;
    }



    /**
     * Коментарий  и изменение в другом смарт процессе
     * @param string $elID ID элемента смартпроцесса
     * @noinspection PhpUnused не ппроверять на использование
     *
     */

    public function curlPost($urlType, $postFields=''){
    $log = __DIR__."/logCurlPost.txt";
            $url = 'https://api.hh.ru/'.$urlType;
// Инициализация cURL
    $ch = curl_init($url);

// Настройка параметров cURL
        if(!empty($postFields)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        }
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $this->hhCode",
                'Content-Type: application/json',
                'User-Agent: HR-pro'
            ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



// Выполнение запроса и получение ответа
        $response = curl_exec($ch);
// Проверка на ошибки
        if (curl_errno($ch)) {
            p(curl_error($ch) , "Ошибка cURL", $log);
        } else {
            // Декодирование JSON-ответа
            $responseData = json_decode($response, true);
p($responseData , "responseData", $log);
        }
// Закрытие cURL-сессии
        curl_close($ch);
return $responseData;
    }

    /**
     * Коментарий  и изменение в другом смарт процессе
     * @param string $elID ID элемента смартпроцесса
     * @noinspection PhpUnused не ппроверять на использование
     *
     */
    public function curlGet($urlType){
    $log = __DIR__."/logCurlPost.txt";
    p($urlType , "start", $log);
    $url = 'https://api.hh.ru/'.$urlType;
// Инициализация cURL
    $ch = curl_init($url);

// Настройка параметров cURL
        if(!empty($postFields)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $this->hhCode",
        'Content-Type: application/json',
        'User-Agent: HR-pro'
    ]);

// Выполнение запроса и получение ответа
        $response = curl_exec($ch);
// Проверка на ошибки
        if (curl_errno($ch)) {
            p(curl_error($ch) , "Ошибка cURL", $log);
        } else {
            // Декодирование JSON-ответа
            $responseData = json_decode($response, true);
p($responseData , "responseData", $log);
        }
// Закрытие cURL-сессии
        curl_close($ch);
return $responseData;
    }
}