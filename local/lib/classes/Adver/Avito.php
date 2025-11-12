<?php

namespace Cassoft\Adver;

class Avito
{
    public $data;
    private $livingPremisesIds = [1, 2, 3, 4, 5];
    private $categoryGloss = [
        '1' => 'Квартиры',
        '2' => 'Комнаты',
        '3' => 'Дома, дачи, коттеджи',
        '4' => 'Дома, дачи, коттеджи',
        '5' => 'Дома, дачи, коттеджи',
        '6' => 'Земельные участки',
        '7' => 'Коммерческая недвижимость',
        '8' => 'Коммерческая недвижимость',
        '9' => 'Коммерческая недвижимость',
        '10' => 'Коммерческая недвижимость',
    ];
    /*
    private $renovationGloss = [
        '1' => 'требует ремонта',
        '2' => 'требует ремонта',
        '3' => 'требует ремонта',
        '4' => 'требует ремонта',
        '5' => 'дизайнерский',
        '6' => 'евроремонт',
        '7' => 'косметический',
    ];*/
    private $renovationGloss = [
        '1' => 'Требуется',
        '2' => 'Требуется',
        '3' => 'Требуется',
        '4' => 'Требуется',
        '5' => 'Дизайнерский',
        '6' => 'Евро',
        '7' => 'Косметический',
    ];




    private $windowDirGloss = [
        '1' => 'Во двор',
        '2' => 'На улицу',
        '3' => [
            'Во двор',
            'На улицу',
        ],
    ];

    private $houseType = [
        '1' => 'Кирпичный',
        '2' => 'Панельный',
        '3' => 'Блочный',
        '4' => 'Блочный',
        '5' => 'Деревянный',
        '6' => 'Монолитный',
        '7' => 'Блочный',
        '8' => 'Блочный',
        '9' => 'Монолитный',
        '10' => 'Монолитный',
        '11' => 'Панельный',
        '12' => 'Панельный',
        '13' => 'Панельный',
        '14' => 'Блочный',
        '15' => 'Панельный',
    ];
    private $wallsType = [
        '1' => 'Кирпич',
        '2' => 'Сэндвич-панели',
        '3' => 'Газоблоки',
        '4' => 'Газоблоки',
        '5' => 'Брус',
        '6' => 'Экспериментальные материалы',
        '7' => 'Газоблоки',
        '8' => 'Газоблоки',
        '9' => 'Экспериментальные материалы',
        '10' => 'Экспериментальные материалы',
        '11' => 'Сэндвич-панели',
        '12' => 'Сэндвич-панели',
        '13' => 'Сэндвич-панели',
        '14' => 'Газоблоки',
        '15' => 'Сэндвич-панели',
    ];
    private $balkonies = [
        '1' => 'Балкон',
        '2' => 'Лоджия'
    ];


    private $objectClass = [
        '1' => 'A',
        '2' => 'A',
        '3' => 'B',
        '4' => 'B',
        '5' => 'C',
        '6' => 'C',
        '7' => 'D',
    ];
    private $plotStatus = [
        '1' => 'Садовое некоммерческое товарищество (СНТ)',
        '2' => 'Индивидуальное жилищное строительство (ИЖС)',
        '3' => 'Промназначения',
        '4' => 'Промназначения',
        '5' => 'Промназначения',
        '6' => 'Промназначения',
        '7' => 'Промназначения',
    ];
    private $plotHomeStatus = [
        '1' => 'Индивидуальное жилищное строительство (ИЖС)',
        '2' => 'Сельхозназначения',
        '3' => 'Промназначения',
        '4' => 'Садовое некоммерческое товарищество (СНТ)',
        '5' => 'Дачное не коммерческое партнерство (ДНП)',
        '6' => 'Фермерское хозяйство',

    ];

    private $plotType = [
        '1' => "Поселений (ИЖС)",
        '2' => "Сельхозназначения (СНТ, ДНП)",
        '3' => "Промназначения",
        '4' => "Сельхозназначения (СНТ, ДНП)",
        '5' => "Сельхозназначения (СНТ, ДНП)",
        '6' => "Сельхозназначения (СНТ, ДНП)",
    ];


    function __construct(array $data)
    {
        $this->data = $data;
    }

    public function makeFeed()
    {
        $result = [];
        foreach ($this->data as $rs) {
            $ad = $this->validate($rs);
            if ($ad !== false) {
                $result[] = $ad;
            }
        }
        return $result;
    }

    public function validate($rs)
    {
        $result = [];
        $general = $this->general($rs);
        $contactInfo = $this->contactInfo($rs);
        $location = $this->location($rs);
        $description = $this->description($rs);
        $rsParams = $this->rsParams($rs);
        $media = $this->media($rs);
        $result = array_merge(
            (array) $result,
            (array) $general,
            (array) $contactInfo,
            (array) $location,
            (array) $description,
            (array) $rsParams,
            (array) $media,
        );

        return $result;
    }

    private function media($rs)
    {
        $media = [];
        if (!isset($rs['PHOTOS'])) {
            $rs['PLANS'] = [];
        }
        if (!isset($rs['PLANS'])) {
            $rs['PLANS'] = [];
        }
        $media['Images'] = array_filter(array_merge(array_values($rs['PHOTOS']), array_values($rs['PLANS'])));
        $media['VideoURL'] = $rs['UF_CRM_CS_DEAL_OBJECT_VIDEO'];

        return array_filter($media);
    }

    private function rsParams($rs)
    {
        $rsParams = [];
        $type = $rs['UF_CS_RS_PREMISES_TYPE']['ID'];
        $build = $rs['UF_CS_RS_BUILD'];
        $premises = $rs['UF_CS_RS_PREMISES_ID'];
        $plot = $rs['UF_CS_RS_PLOT_ID'];

        $rsParams['Category'] = $this->categoryGloss[$type];
        //   if ($rs['CATEGORY_CODE'] === 'sell') {
        if ($rs['CATEGORY_CODE'] !== 'rent') {
            $rsParams['OperationType'] = 'Продам';
        }

        if ($rs['CATEGORY_CODE'] === 'rent') {
            $rsParams['OperationType'] = 'Сдам';
            $rsParams['LeaseType'] = $rs['UF_CRM_CS_DEAL_TYPE_RENT'];
            $rsParams['LeaseDeposit'] = $rs['UF_CRM_CS_DEAL_DEPOSIT'];
            $leaseMultimedia =  $this->leaseMultimedia($rs);
            if (!empty($leaseMultimedia)) {
                $rsParams['LeaseMultimedia'] = $leaseMultimedia;
            }
            $leaseAppliances =  $this->leaseAppliances($rs);
            if (!empty($leaseAppliances)) {
                $rsParams['LeaseAppliances'] = $leaseAppliances;
            }
            $leaseComfort =  $this->leaseComfort($rs);
            if (!empty($leaseComfort)) {
                $rsParams['LeaseComfort'] = $leaseComfort;
            }
            $leaseAdditionally =  $this->leaseAdditionally($rs);
            if (!empty($leaseAdditionally)) {
                $rsParams['LeaseAdditionally'] = $leaseAdditionally;
            }
        }



        $rsParams['Price'] = preg_replace("/[^0-9]/", '', $rs['UF_CRM_PRICE']);

        if ($type === '1' || $type === '2') {
            $rsParams['Rooms'] = $premises['UF_CS_P_ROOMS'];
            $rsParams['Floor'] = $premises['UF_CS_P_FLOOR'];
            $rsParams['HouseType'] = $this->houseType[$build['UF_CS_B_MATERIAL_WALL']];
        }
        if ($type === '7' || $type === '8' || $type === '9' || $type === '10') {
            $rsParams['Floor'] = $premises['UF_CS_P_FLOOR'];
            $rsParams['HouseType'] = $this->houseType[$build['UF_CS_B_MATERIAL_WALL']];
        }
        if ($type === '7' || $type === '8') {
            $rsParams['BuildingClass'] = $this->objectClass[$premises['UF_CS_P_FLOOR']];
        }

        $rsParams['Square'] = $premises['UF_CS_P_TOTAL_AREA'];

        if ($type === '1') {
            $rsParams['KitchenSpace'] = $premises['UF_CS_P_KITCHEN_AREA'];
            $rsParams['LivingSpace'] = $premises['UF_CS_P_LIVING_AREA'];
            $rsParams['Status'] = 'Квартира';
            $rsParams['ViewFromWindows'] = $this->windowDirGloss[$premises['UF_CS_P_WINDOW_DIRECTION']];
            $rsParams['DealType'] = 'Прямая продажа';
            $rsParams['RoomType'] = [
                'Смежные',
                'Изолированные'
            ];
            if (!empty($rs['balcony'])) {
                $rsParams['BalconyOrLoggia'] = $this->balkonies[$rs['balcony']['UF_CS_PP_BALCON_TYPE']];
            } else {
                $rsParams['BalconyOrLoggia'] = 'Нет';
            }
        }

        if ($type === '3' || $type === '4' || $type === '5') {
            $rsParams['Rooms'] = $premises['UF_CS_P_ROOMS'];
            $rsParams['LandArea'] = (float) $plot['UF_CS_PS_PLOT_AREA'] / 100;
            $rsParams['LandStatus'] = $this->plotHomeStatus[$plot['UF_CS_PL_PLOT_TYPE']];

            $rsParams['WallsType'] = $this->wallsType[$build['UF_CS_B_MATERIAL_WALL']];
        }
        if ($type === '6') {
            $rsParams['LandArea'] = (float) $plot['UF_CS_PS_PLOT_AREA'] / 100;
            $rsParams['LandStatus'] = $this->plotStatus[$plot['UF_CS_PL_PLOT_TYPE']];
        }
        $rsParams['Floors'] = $build['UF_CS_B_FLOOR_MAX'];
        if ($type === '1' && $rs['CATEGORY_CODE'] === 'new') {
            $rsParams['MarketType'] = 'Новостройка';
            if (!empty($build['UF_CS_AVITO_BUILD_ID'])) {
                $rsParams['NewDevelopmentId'] = $build['UF_CS_AVITO_BUILD_ID'];
            } elseif (!empty($build['UF_CS_AVITO_GK_ID'])) {
                $rsParams['NewDevelopmentId'] = $build['UF_CS_AVITO_GK_ID'];
            }
        }
        if ($type === '1' && $rs['CATEGORY_CODE'] === 'sell') {
            $rsParams['MarketType'] = 'Вторичка';
            $rsParams['BuiltYear'] = $build['UF_CS_B_BUILT_YEAR'];
            $rsParams['CeilingHeight'] = $premises['UF_CS_P_HEIGHT'];
            //$rsParams['Renovation'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];
        }
        $rsParams['Renovation'] = $this->renovationGloss[$premises['UF_CS_P_RENOVATION']];

        $rsParams['PropertyRights'] = 'Собственник';
        switch ($type) {
            case 3:
                if (!empty($build['UF_CS_B_BUILT_YEAR']) && $build['UF_CS_B_BUILT_YEAR'] >= 1970) {
                    $rsParams['ObjectType'] = 'Коттедж';
                } else {
                    $rsParams['ObjectType'] = 'Дом';
                }
                break;
            case 4:
                $rsParams['ObjectType'] = 'Таунхаус';
                break;
                case 5:
                $rsParams['ObjectType'] = 'Дача';
                break;
            case 6:
                $rsParams['ObjectType'] = $this->plotType[$plot['UF_CS_PL_PLOT_TYPE']];
                break;
            case 7:
                $rsParams['ObjectType'] = 'Офисное помещение';
                break;
            case 8:
                $rsParams['ObjectType'] = 'Складское помещение';
                break;
            case 9:
                $rsParams['ObjectType'] = 'Торговое помещение';
                break;
            case 10:
                $rsParams['ObjectType'] = 'Производственное помещение';
                break;
        }


        return array_filter($rsParams);
    }
    private function leaseAdditionally($rs)
    {
        $result = [];
        if ($rs['UF_CRM_CS_DEAL_PETS_ALLOWER'] == '1') {
            $result[] = 'Можно с питомцами';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_CHILDREN_ALLOWED'] == '1') {
            $result[] = 'Можно с детьми';
        }
        return $result;
    }
    private function leaseMultimedia($rs)
    {
        $result = [];
        if ($rs['UF_CRM_CS_DEAL_HAS_TV'] == '1') {
            $result[] = 'Телевизор';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_INTERNET'] == '1') {
            $result[] = 'Wi-Fi';
        }
        return $result;
    }
    private function leaseAppliances($rs)
    {
        $result = [];
        if ($rs['UF_CRM_CS_DEAL_HAS_FRIDGE'] == '1') {
            $result[] = 'Холодильник';
        }
        if ($rs['UF_CRM_CS_DEAL_HAS_WASHER'] == '1') {
            $result[] = 'Стиральная машина';
        }
        return $result;
    }
    private function leaseComfort($rs)
    {
        $result = [];
        if ($rs['UF_CRM_CS_DEAL_HAS_CONDITIONER'] == '1') {
            $result[] = 'Кондиционер';
        }
        return $result;
    }
    private function description($rs)
    {
        $description = [];
        $description['Description'] = $rs['DESCRIPTION'];
        if (iconv_strlen($description['Description']) <= 5000) {
            return $description;
        } else {
            return mb_substr($description['Description'], 0, 5000);
        }
    }

    private function location($rs)
    {
        if (!empty($rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE'])) {
            $addressMass = explode('|', $rs['UF_CRM_CS_DEAL_ADDRESS_SUBSTITUTE']);
            $address = $addressMass['0'];
            $coormass = explode(';', $addressMass['1']);
            $lat = $coormass['0'];
            $lon = $coormass['1'];
            $location = [];
            $location['Address'] = $address;
            $location['Latitude'] = $lat;
            $location['Longitude'] = $lon;
        } else {
            $location = [];
            if ($rs['UF_CS_RS_PREMISES_TYPE']['ID'] == 6) {
                $plot = $rs['UF_CS_RS_PLOT_ID'];
                $location['Address'] = $plot['UF_CS_PL_ADDRESS'];
                $location['Latitude'] = $plot['UF_CS_PL_LAT'];
                $location['Longitude'] = $plot['UF_CS_PL_LONG'];
            } else {
                $build = $rs['UF_CS_RS_BUILD'];
                $location['Address'] = $build['UF_CS_B_ADDRESS'];
                $location['Latitude'] = $build['UF_CS_B_LAT'];
                $location['Longitude'] = $build['UF_CS_B_LONG'];
            }
        }

        return array_filter($location);
    }

    private function general($rs)
    {
        $general = [];
        $general['Id'] = $rs['ID'];

        return array_filter($general);
    }

    private function contactInfo($rs)
    {
        $contactInfo = [];
        $contactInfo['AllowEmail'] = 'Нет';
        $contactInfo['ManagerName'] = $this->getManagerName($rs);
        $contactInfo['ContactPhone'] = substr(preg_replace("/[^0-9]/", '',  $rs['ASSIGNED_PHONE']), -10);

        return array_filter($contactInfo);
    }

    private function getManagerName($rs)
    {
        $managerName = trim("{$rs['ASSIGNED_NAME']} {$rs['ASSIGNED_LAST_NAME']}");
        if (iconv_strlen($managerName) <= 40) {
            return $managerName;
        } else {
            return trim("{$rs['ASSIGNED_NAME']}");
        }
    }
}