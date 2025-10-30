<?php
set_time_limit(6000);


$arrIdVacancy = [];

$arrDataKont = [];

//require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Получение токена
function getRequest()
{

    if (empty($_GET['code']))
    {

       $newURL = "https://api.rabota.ru/oauth/authorize.html?app_id=634&scope=profile,vacancies&display=page&redirect_uri=https://city.brokci.ru/cassoftApp/market/hr/ajax/rb/";

        header('Location: ' . $newURL);

    }
    else
    {

        $sec = $_GET['code'];

        $ttt = time();

        $params = array(
            "app_id" => "634",
            "time" => $ttt,
            "code" => ""
        );
        $params["code"] = $sec;

        $secret = "2b3svOVVkVQ3Eno04lLMeK3LUYZG8GkJ";

        function getSignature(array $params, string $secret)
        {

            foreach ($params as $k => $v)
            {
                $params[$k] = (string)$v;

            }

            $sort = function ($array) use (&$sort)
            {
                if (!is_array($array)) return $array;
                ksort($array);
                $sort5 = array_map($sort, $array);

                return array_map($sort, $array);
            };

            return hash('sha256', json_encode($sort($params)) . $secret, false);
        }

        $outt = getSignature($params, $secret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.rabota.ru/oauth/token.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['content-type: application/x-www-form-urlencoded', ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "app_id=634&time=$ttt&code=$sec&signature=$outt");

        $response = curl_exec($ch);

        curl_close($ch);

        $data7 = json_decode($response, true);

        
        $access_token = $data7['access_token'];

        return $access_token;

    }
}


//$access_token = getRequest();


// Получение откликов по id вакансии
function getResponses($access_token, $vacancyId)
{
    echo 'Вакансия        ';
    echo $vacancyId;
    echo '<br>';

    $vacIn = '{
  "request": {
    "fields": [
      "phone",
      "email",
      "full_name",
      "birth_year_at",
      "id"
    ],
    "filter": {
      "vacancy_ids": [
        48771249
      ]
    },
    "sort": {
      "field": "response_date",
      "direction": "desc"
    },
    "offset": 0,
    "limit": 20
  },
  "user_tags": [
    {
      "id": 50425019,
      "name": "",
      "add_date": "2024-03-01",
      "campaign_key": "50425019"
    }
  ],
  "rabota_ru_id": "",
  "cache_control_max_age": 0
}';

    $array = json_decode($vacIn, true);

    $currentDate = date('Y-m-d');
    $previousDate = date('Y-m-d', strtotime('-3 days'));


    $array['request']['filter']['vacancy_ids']['0'] = $vacancyId;
    $array['user_tags']['0']['add_date'] = $currentDate;
    
/* echo '<pre>';
     print_r($array);
    $vac2 = json_encode($array);
 */


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vac2);
    curl_setopt($ch, CURLOPT_URL, 'https://api.rabota.ru/v4/me/company/responses.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Token:' . $access_token, ]);

    $response = curl_exec($ch);

    curl_close($ch);

    $data7 = json_decode($response, true);

    /* echo '<pre>';
     print_r($data7); */

    $id = $data7['response']['responses'][0]['vacancy_title'];
    $fullName = $data7['response']['responses'][0]['resume']['full_name'];
    $phone = $data7['response']['responses'][0]['resume']['phone'];
    $email = $data7['response']['responses'][0]['resume']['email'];

    return $dataKont = array(
        $id,
        $fullName,
        $phone,
        $email
    );

}   // end getResponses


// Получение токена
$access_token = getRequest();
echo $access_token;
        echo '<br>';


// Получение вакансий по id рекрутера

$vacIn3 = '{
    "request": {
        "fields": [
            "id",
            "title"
        ],
        "filter": {
            "is_blocked": false,
            "is_only_auto_renewal": false,
            "statuses": [
                "publish",
                "unpublish",
                "archived"
            ],
            "recruiter": {
                "setting": "my",
                "ids": [
                    50425019
                ]
            },
           
            "folder.recruiter": 2024,
            "query": "",
            "statistics": {
                "period_from_date": "2024-02-01",
                "period_to_date": "2024-03-01"
            }
        },
        "allow_show_deleted_vacancy": false,
        "return_totals": [
            "published_total"
        ],
        "sort": {
            "field": "responses_count",
            "direction": "desc"
        },
        "offset": 0,
        "limit": 20
    },
    "user_tags": [
        {
            "id": null,
            "name": "",
            "add_date": "2024-03-01",
            "campaign_key": null
        }
    ],
    "rabota_ru_id": "",
    "cache_control_max_age": 0
}';

    $array3 = json_decode($vacIn3, true);

    $currentDate = date('Y-m-d');
    $previousDate = date('Y-m-d', strtotime('-20 days'));


    $array3['request']['filter']['statistics']['period_from_date'] = $previousDate;
    $array3['request']['filter']['statistics']['period_to_date'] = $currentDate;
    
    $array3['user_tags']['0']['add_date'] = $currentDate;
    
/* echo '<pre>';
     print_r($array3);
      */
    $vac3 = json_encode($array3);


$ch = curl_init();
curl_setopt($ch, CURLOPT_POSTFIELDS, $vac3);
curl_setopt($ch, CURLOPT_URL, 'https://api.rabota.ru/v4/me/vacancies.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Token:' . $access_token, ]);



$response = curl_exec($ch);

curl_close($ch);

$vacancy = json_decode($response, true);

echo '<pre>';
     print_r($vacancy);


$c = $vacancy['response']['total'];


echo 'Число вакансий    ';
echo $c;
echo '<br>';

for ($k = 0;$k < $c;$k++)
{

    $vacancyId[$k] = $vacancy['response']['vacancies'][$k]['id'];

    $dataKont[$k] = getResponses($access_token, $vacancyId[$k]);
    if (isset($dataKont[$k][1]))
    {

        echo $dataKont[$k][0];
        echo '<br>';

        $parts[$k] = explode(" ", $dataKont[$k][1]);
        $name[$k] = $parts[$k][0];
        $nameLast[$k] = $parts[$k][1];

        echo $name[$k];
        echo '<br>';
        echo $nameLast[$k];
        echo '<br>';

        echo $dataKont[$k][2];
        echo '<br>';
        echo $dataKont[$k][3];
        echo '<br>';


        
    } 
    else
    {
        echo "Нет откликов";
        echo '<br>';
    }

}  // for

// Получение вакансий по id рекрутера



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.rabota.ru/v4/me/company.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Token:' . $access_token, ]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"request":{"fields":["id","name","created_date","is_spam","vacancies_count"]},"user_tags":[{"id":null,"name":"","add_date":"2024-03-01","campaign_key":null}],"rabota_ru_id":"","cache_control_max_age":0}');

$response = curl_exec($ch);

curl_close($ch);

$comp = json_decode($response, true);

echo '<pre>';
     print_r($comp);







?>
