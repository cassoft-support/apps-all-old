<?php
//require($_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/guide/financier.php');

namespace CSlibs\B24\Smarts;
//$smartClass = new \CSlibs\B24\Smarts\SmartProcess($auth, $smart);

class SmartProcess
{
    private $auth;
    private $smart;
    private $fileLog = __DIR__ . "/logProcessAuth.txt";

    // private $date = date("d.m.YTH:i");


    public function __construct($auth, $smart = array())
    {
        $this->auth = $auth;
        $this->smart = $smart;
        //  $this->log_file = "logProcess";
    }

    public function smartFieldsAdd($params)
    {
        $y = 0;
        $up = 0;
        $i = 0;

        $guide = $this->smartGuide();
        foreach ($params as $kSmart => $vSmart) {
            $smartID = $guide[$kSmart]['id'];
            $smartTypeID = $guide[$kSmart]['entityTypeId'];
            foreach ($vSmart as $keyP => $valP) {
                //   d($valP['xmlId']);
                $valP['entityId'] = 'CRM_' . $smartID;
                $valP['fieldName'] = "UF_CRM_" . $smartID . "_" . $valP['xmlId'];
                if ($valP['userTypeId'] === 'crm' && $valP['isDynamic'] === 'Y') {
                    $valP['settings']['DYNAMIC_' . $smartTypeID] = 'Y';
                }
                $paramsList = [
                    'moduleId' => "crm",
                    'select' => ['*'],
                    'order' => [],
                    "filter" => [
                        'entityId' => 'CRM_' . $smartID,
                        'fieldName' => "UF_CRM_" . $smartID . "_" . $valP['xmlId'],
                    ],
                    'start' => 0
                ];

                $paramsAdd = [
                    'moduleId' => "crm",
                    "field" => $valP];
                $y++;
                file_put_contents(__DIR__ . "/addFields.txt", print_r($paramsList, true), FILE_APPEND);
                $userfieldList = $this->auth->core->call("userfieldconfig.list", $paramsList)->getResponseData()->getResult()->getResultData()['fields'];
                file_put_contents(__DIR__ . "/addFields.txt", print_r($userfieldList, true), FILE_APPEND);
                if (empty($userfieldList)) {
                    $userfieldAdd = $this->auth->core->call("userfieldconfig.add", $paramsAdd)->getResponseData()->getResult()->getResultData();
                    file_put_contents(__DIR__ . "/addFields.txt", print_r($userfieldAdd, true), FILE_APPEND);
                    $i++;
                }
            }
        }

        $res = "Всего - " . $y . ", обновлено - " . $up . ", создано - " . $i;
        return $userfieldAdd;

    }

    public function smartGuide()
    {
        $params = ['select' => ['*'], 'order' => [], 'filter' => [], 'start' => 0];
        $resSmart = $this->auth->core->call("crm.type.list", $params)->getResponseData()->getResult()->getResultData()['types'];
        foreach ($resSmart as $kSmart) {
            $guide[$kSmart['title']]['id'] = $kSmart['id'];
            $guide[$kSmart['title']]['entityTypeId'] = $kSmart['entityTypeId'];
        }
        return $guide;
    }

    public function smartId($entityTypeId)
    {
        $params = ['select' => ['*'], 'order' => [], 'filter' => [], 'start' => 0];
        $resSmart = $this->auth->core->call("crm.type.list", $params)->getResponseData()->getResult()->getResultData()['types'];
        foreach ($resSmart as $kSmart) {
            if ($kSmart['entityTypeId'] === $entityTypeId) {
                $smartId = $kSmart['id'];
            }
        }
        return $smartId;
    }

    public function smartGuideIdType()
    {
        $params = ['select' => ['*'], 'order' => [], 'filter' => [], 'start' => 0];
        $resSmart = $this->auth->core->call("crm.type.list", $params)->getResponseData()->getResult()->getResultData()['types'];
        foreach ($resSmart as $kSmart) {
            $guide[$kSmart['entityTypeId']] = $kSmart['id'];
        }
        return $guide;
    }

    public function smartGuideName()
    {
        $params = ['select' => ['*'], 'order' => [], 'filter' => [], 'start' => 0];
        $resSmart = $this->auth->core->call("crm.type.list", $params)->getResponseData()->getResult()->getResultData()['types'];
        foreach ($resSmart as $kSmart) {
            $guide[$kSmart['entityTypeId']] = $kSmart['title'];
        }
        return $guide;
    }

    public function smartCategory($entityTypeId)
    {
        $smartCategory = $this->auth->core->call("crm.category.list", ["entityTypeId" => $entityTypeId])->getResponseData()->getResult()->getResultData()['categories'];
        foreach ($smartCategory as $kSmart) {
            $guide[$kSmart['name']] = $kSmart['id'];
        }
        return $guide;
    }

    public function smartCategoryIn($entityTypeId)
    {
        $smartCategory = $this->auth->core->call("crm.category.list", ["entityTypeId" => $entityTypeId])->getResponseData()->getResult()->getResultData()['categories'];
        foreach ($smartCategory as $kSmart) {
            $guide[$kSmart['id']] = $kSmart['name'];
        }
        return $guide;
    }

    public function smartCategoryName($entityTypeId)
    {
        $smartCategory = $this->auth->core->call("crm.category.list", ["entityTypeId" => $entityTypeId])->getResponseData()->getResult()->getResultData()['categories'];
        foreach ($smartCategory as $kSmart) {
            $guide[$kSmart['name']] = $kSmart['id'];
        }
        return $guide;
    }

    public function smartStage($entityTypeId, $category)
    {
        $smartStatus = $this->auth->core->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $category]])->getResponseData()->getResult()->getResultData();
        foreach ($smartStatus as $kStage) {
            $guide[$kStage['NAME']] = $kStage['STATUS_ID'];
        }
        return $guide;

    }
    public function smartCategoryStageName($entityTypeId)
    {
        $smartCategory = $this->auth->CScore->call("crm.category.list", ["entityTypeId" => $entityTypeId])['categories'];
        foreach ($smartCategory as $catSmart) {
            $smartStatus = $this->auth->CScore->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $catSmart['id']]]);
            foreach ($smartStatus as $kStage) {
                $guide[$catSmart['name']][$kStage['STATUS_ID']] = $kStage['NAME'];
            }
        }
        return $guide;

    }

    public function smartStageIdStatus($entityTypeId, $category)
    {
        $smartStatus = $this->auth->core->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $category]])->getResponseData()->getResult()->getResultData();
        foreach ($smartStatus as $kStage) {
            $guide[$kStage['STATUS_ID']] = $kStage['SEMANTICS'];
        }
        return $guide;

    }

    public function smartStageIdStatusActiveDog($entityTypeId, $category)
    {
        $smartStatus = $this->auth->core->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $category]])->getResponseData()->getResult()->getResultData();
        foreach ($smartStatus as $kStage) {
            if (!$kStage['SEMANTICS']) {
                if ($kStage['NAME'] === 'Договор подписан' || $kStage['NAME'] === 'Договор в работе' || $kStage['NAME'] === 'Заканчивается срок договора') {
                    $guide[$kStage['STATUS_ID']] = 'Y'; //
                } else {
                    $guide[$kStage['STATUS_ID']] = 'N';
                }
            }

        }
        return $guide;

    }
    public function smartStageArrayStatusActiveDog($entityTypeId, $category)
    {
        $smartStatus = $this->auth->core->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $category]])->getResponseData()->getResult()->getResultData();
        foreach ($smartStatus as $kStage) {
            if (!$kStage['SEMANTICS']) {

                    $guide[] = $kStage['STATUS_ID']; //

            }

        }
        return $guide;

    }
    public function smartStageName($entityName, $categoryName)
    {

        $entityTypeId = $this->smartGuide()[$entityName]['entityTypeId'];

        // $entityTypeId = 130;
        $category = $this->smartCategoryName($entityTypeId)[$categoryName];
        // $category =  34;
        $smartStatus = $this->auth->core->call('crm.status.list', ['filter' => ['ENTITY_ID' => 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $category]])->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($smartStatus, true), FILE_APPEND);
        foreach ($smartStatus as $kStage) {
            $guide[$kStage['NAME']] = $kStage['STATUS_ID'];
        }
        return $guide;

    }

    public function smartComments($smartID, $comments)
    {
        $params = [
            'ufCrm' . $smartID . 'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
        ];
        file_put_contents($this->fileLog, print_r($params, true), FILE_APPEND);
        $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        $fields = ['fields' => ["ENTITY_ID" => $this->smart['id'], "ENTITY_TYPE" => "dynamic_" . $this->smart['entityTypeId'], "COMMENT" => $comments]];
        $smartComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        return $smartUp;
    }
    public function smartCommentsPreviousStage($comments)
    {
        $entityId = $this->smartId($this->smart['entityTypeId']);
        file_put_contents(__DIR__."/commentPrev.txt", print_r(date('c'), true));
        $params = [
            'ufCrm' . $entityId . 'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
           'stageId' => $this->smart['previousStageId']
        ];
        file_put_contents(__DIR__."/commentPrev.txt", print_r($params, true), FILE_APPEND);
        $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        file_put_contents(__DIR__."/commentPrev.txt", print_r($smartUp, true), FILE_APPEND);
        $fields = ['fields' => ["ENTITY_ID" => $this->smart['id'], "ENTITY_TYPE" => "dynamic_" . $this->smart['entityTypeId'], "COMMENT" => $comments]];
        $smartComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        file_put_contents(__DIR__."/commentPrev.txt", print_r($smartComment, true), FILE_APPEND);
        return $smartUp;
    }
    // коментарий и изменение процесса
    public function smartCommentsFields($comments, $fields)
    {
        $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $fields])->getResponseData()->getResult()->getResultData();
        $fieldsComment = ['fields' => ["ENTITY_ID" => $this->smart['id'], "ENTITY_TYPE" => "dynamic_" . $this->smart['entityTypeId'], "COMMENT" => $comments]];
        $smartComment = $this->auth->core->call("crm.timeline.comment.add", $fieldsComment)->getResponseData()->getResult()->getResultData();
        return $smartUp;
    }

    //комментарий в другом смарт-процессе
    public function smartCommentsOther($smartID, $comments, $smartOther)
    {
        $params = [
            'ufCrm' . $smartID . 'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
        ];
        file_put_contents($this->fileLog, print_r($params, true), FILE_APPEND);
        $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $smartOther['entityTypeId'], 'id' => $smartOther['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        $fields = ['fields' => ["ENTITY_ID" => $smartOther['id'], "ENTITY_TYPE" => "dynamic_" . $smartOther['entityTypeId'], "COMMENT" => $comments]];
        $smartComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        return $comments;
    }

    public function securitySmartSmart($entity, $security, $company, $categoryId, $limit, $driversAll, $carCheckDate)
    {
        $guide = $this->smartGuide(); //справочник смартпроцессов
        $entityId = $guide['Проверка контрагентов']['id'];
        $entityExId = $guide['Экспедирование']['id'];
        file_put_contents($this->fileLog, print_r($limit, true), FILE_APPEND);
        file_put_contents($this->fileLog, print_r($entity."-entity\n", true), FILE_APPEND);
        file_put_contents($this->fileLog, print_r($this->smart['id'], true), FILE_APPEND);
        $parent = "PARENT_ID_" . $entity;
        $parentSmart = "parentId" . $entity;
        $decimal = dechex($entity);
        if ($carCheckDate) {
            $carCheck = 'Y';
        }
        if ($company['UF_CRM_CS_DATE_CHECK']) {
            $companyCheck = 'Y';
        }
        if ($driversAll[0]['UF_CRM_CS_DATE_CHECK']) {
            $driverOne = 'Y';
        } else {
            $driverOne = 'N';
        }

        if ($driversAll[1]['UF_CRM_CS_DATE_CHECK']) {
            $driverTwo = 'Y';
        } else {
            $driverTwo = 'N';
        }

        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => "Проверка компании " . $company['TITLE'] . "/ №" . $this->smart['companyId'],
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['companyId'],
                'companyId' => $this->smart['companyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $security,//ответственный
                'categoryId' => $categoryId,//категория
                'ufCrm' . $entityId . 'CsCar' => $this->smart['ufCrm'.$entityExId.'CsCar'],
                'ufCrm' . $entityId . 'CsDrivers' => $this->smart['ufCrm'.$entityExId.'CsDrivers'],
                'ufCrm' . $entityId . 'CsLimit' => $limit . "|RUB",//ответственный
                'opportunity' => $limit,//ответственный
                "isManualOpportunity" => "Y",
                'ufCrm' . $entityId . 'CsProviderCheck' => $companyCheck,
                'ufCrm' . $entityId . 'CsProviderDateCheck' => $company['UF_CRM_CS_DATE_CHECK'],
                'ufCrm' . $entityId . 'CsCarCheck' => $carCheck,
                'ufCrm' . $entityId . 'CsCarDateCheck' => $carCheckDate,
                'ufCrm' . $entityId . 'CsDriverCheckOne' => $driverOne,
                'ufCrm' . $entityId . 'CsDriverDateCheckOne' => $driversAll[0]['UF_CRM_CS_DATE_CHECK'],
                'ufCrm' . $entityId . 'CsDriverCheckTwo' => $driverTwo,
                'ufCrm' . $entityId . 'CsDriverDateCheckTwo' => $driversAll[1]['UF_CRM_CS_DATE_CHECK'],

            ]
        ];

        $i = 0;
        if ($driversAll) {
            foreach ($driversAll as $kdriver) {
                $i++;
                if ($i == 1) {
                    $code = "On";
                } else {
                    $code = "Two";
                }
                $paramSmart["fields"]["ufCrm'.$entityId.'CsDrivers"][] = $kdriver['ID'];
                $paramSmart["fields"]["ufCrm'.$entityId.'CsDriversDateCheck" . $code][] = $kdriver['UF_CRM_CS_DATE_CHECK'];
                if ($kdriver['UF_CRM_CS_DATE_CHECK']) {
                    $paramSmart["fields"]["ufCrm'.$entityId.'CsDriversCheck" . $code][] = 'Y';
                }
            }
        }
        file_put_contents($this->fileLog, print_r($paramSmart, true), FILE_APPEND);
        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        file_put_contents($this->fileLog, print_r("smartId\n", true), FILE_APPEND);
        file_put_contents($this->fileLog, print_r($smartId, true), FILE_APPEND);
        if ($smartId>0) {
            $paramsEx = [
                $parentSmart => $smartId
            ];
            file_put_contents($this->fileLog, print_r("smartExUp\n", true), FILE_APPEND);
            file_put_contents($this->fileLog, print_r($paramsEx, true), FILE_APPEND);
            file_put_contents($this->fileLog, print_r("driversAll\n", true), FILE_APPEND);
            file_put_contents($this->fileLog, print_r($driversAll, true), FILE_APPEND);
foreach ($driversAll as $keyDriver){
    $driverUp = $this->auth->core->call("crm.contact.update", ['id' => $keyDriver['ID'], 'fields' => [$parent => $smartId]]);
    file_put_contents($this->fileLog, print_r("driverUp\n", true), FILE_APPEND);
    file_put_contents($this->fileLog, print_r($driverUp, true), FILE_APPEND);
}
            $companyUp = $this->auth->core->call("crm.company.update", ['id' => $this->smart['companyId'], 'fields' => [$parent => $smartId]]);
if($this->smart['contactId']>0) {
    $contactUp = $this->auth->core->call("crm.contact.update", ['id' => $this->smart['contactId'], 'fields' => [$parent => $smartId]]);
}
$smartExUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $paramsEx]);

            file_put_contents($this->fileLog, print_r($smartExUp, true), FILE_APPEND);
            $comments = "По Компании " . $company['TITLE'] . ", Запущен процесс проверки №" . $smartId;
            $commentAdd = $this->smartComments($this->smart['entityTypeId'], $comments);
            file_put_contents($this->fileLog, print_r($commentAdd, true), FILE_APPEND);
        }

        return $smartId;
    }

    public function securitySmartSmartIndividual($entity, $security, $individual, $categoryId, $limit, $driversAll, $carCheckDate)
    {
        $guide = $this->smartGuide(); //справочник смартпроцессов
        $entityId = $guide['Проверка контрагентов']['id'];


        $parent = "PARENT_ID_" . $entity;
        $parentSmart = "parentId" . $entity;
        $decimal = dechex($entity);
        if ($carCheckDate) {
            $carCheck = 'Y';
        }
        if ($individual['UF_CRM_CS_DATE_CHECK']) {
            $companyCheck = 'Y';
        }
        if ($driversAll[0]['UF_CRM_CS_DATE_CHECK']) {
            $driverOne = 'Y';
        } else {
            $driverOne = 'N';
        }

        if ($driversAll[1]['UF_CRM_CS_DATE_CHECK']) {
            $driverTwo = 'Y';
        } else {
            $driverTwo = 'N';
        }
        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => "Проверка физ. лица " . $individual['NAME'] . " " . $individual['SECOND_NAME'] . " " . $individual['LAST_NAME'] . "/ №" . $this->smart['contactId'],
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['contactId'],
                'companyId' => $this->smart['companyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $security,//ответственный
                'categoryId' => $categoryId,//категория
                'ufCrm' . $entityId . 'CsCar' => $this->smart['ufCrm18CsCar'],
                'ufCrm' . $entityId . 'CsDrivers' => $this->smart['ufCrm18CsDrivers'],
                'ufCrm' . $entityId . 'CsLimit' => $limit . "|RUB",//ответственный
                'opportunity' => $limit,//ответственный
                "isManualOpportunity" => "Y",
                'ufCrm' . $entityId . 'CsProviderCheck' => $companyCheck,
                'ufCrm' . $entityId . 'CsProviderDateCheck' => $individual['UF_CRM_CS_DATE_CHECK'],
                'ufCrm' . $entityId . 'CsCarCheck' => $carCheck,
                'ufCrm' . $entityId . 'CsCarDateCheck' => $carCheckDate,
                'ufCrm' . $entityId . 'CsDriverCheckOne' => $driverOne,
                'ufCrm' . $entityId . 'CsDriverDateCheckOne' => $driversAll[0]['UF_CRM_CS_DATE_CHECK'],
                'ufCrm' . $entityId . 'CsDriverCheckTwo' => $driverTwo,
                'ufCrm' . $entityId . 'CsDriverDateCheckTwo' => $driversAll[1]['UF_CRM_CS_DATE_CHECK'],

            ]
        ];

        $i = 0;
        if ($driversAll) {
            foreach ($driversAll as $kdriver) {
                $i++;
                if ($i == 1) {
                    $code = "On";
                } else {
                    $code = "Two";
                }
                $paramSmart["fields"]["ufCrm'.$entityId.'CsDrivers"][] = $kdriver['ID'];
                $paramSmart["fields"]["ufCrm'.$entityId.'CsDriversDateCheck" . $code][] = $kdriver['UF_CRM_CS_DATE_CHECK'];
                if ($kdriver['UF_CRM_CS_DATE_CHECK']) {
                    $paramSmart["fields"]["ufCrm'.$entityId.'CsDriversCheck" . $code][] = 'Y';
                }
            }
        }


        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        if ($smartId) {
            $params = [
                $parentSmart => $smartId
            ];

            $individualUp = $this->auth->core->call("crm.contact.update", ['id' => $this->smart['contactId'], 'fields' => [$parent => $smartId]])->getResponseData()->getResult()->getResultData();
            $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();


            $comments = "По Физ. лицу " . $individual['NAME'] . " " . $individual['SECOND_NAME'] . " " . $individual['LAST_NAME'] . ", Запущен процесс проверки №" . $smartId;
            $commentAdd = $this->smartComments($this->smart['entityTypeId'], $comments);
//            file_put_contents($this->fileLog, print_r($commentAdd, true), FILE_APPEND);


            $testItem = $this->auth->core->call("crm.item.get", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id']])->getResponseData()->getResult()->getResultData();

        }

        return $smartId;
    }

//генерация документа
    function smartDocGenerator($values, $template, $stamps)
    {
        file_put_contents($this->fileLog, print_r("Генерация договора", true), FILE_APPEND);
        $smart = $this->smart;
        $resTemplate = $this->auth->core->call('crm.documentgenerator.template.list', ['select' => ['*'], 'filter' => [], 'order ' => [], 'start' => 0])->getResponseData()->getResult()->getResultData()['templates'];
        foreach ($resTemplate as $kTemp) {
            $guide[$kTemp['name']] = $kTemp['id'];
        }


        $resTemplateAdd = $this->auth->core->call('crm.documentgenerator.document.add', ['templateId' => $guide[$template], 'entityTypeId' => $this->smart['entityTypeId'], 'entityId' => $this->smart['id'], 'values' => $values, 'stampsEnabled' => $stamps])->getResponseData()->getResult()->getResultData()['document']['id'];
        file_put_contents($this->fileLog, print_r($resTemplateAdd, true), FILE_APPEND);
        $resTemplateLink = $this->auth->core->call('crm.documentgenerator.document.enablepublicurl', ['id' => $resTemplateAdd, 'status' => 1])->getResponseData()->getResult()->getResultData()['publicUrl'];
        file_put_contents($this->fileLog, print_r($resTemplateLink, true), FILE_APPEND);
        return $resTemplateLink;

    }

    // изменение стадии сделки из смарт-процесса
    function dealStageUp($stage)
    {
        $smart = $this->smart;
        $deal = $this->auth->core->call("crm.deal.get", array('id' => $this->smart['parentId2']))->getResponseData()->getResult()->getResultData();
        $stageID = explode(':', $deal['STAGE_ID'])[0] . ":" . $stage;
        $params = [
            'STAGE_ID' => $stageID,
        ];
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->smart['parentId2'], 'fields' => $params]);
        return $dealUp;

    }

    // Возврат на предыдущую стадию смарт-процесса
    function stepBack($comments)
    {
        $guide = $this->smartGuideIdType();
        $date = date("d.m.YTH:i:s");
        $entityId = $guide[$this->smart['entityTypeId']];
        $dateCheck = "ufCrm" . $entityId . "CsDateChange";
        $params = [
            $dateCheck => strtoTime($date),
            'stageId' => $this->smart["previousStageId"]
        ];
        $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        $commentAdd = $this->smartComments($entityId, $comments);
        return $comments;
    }

    // создание смарт-процесса из другого смарт-процесса
    public function docSmartSmart($entity, $assigned, $categoryId)
    {
        $guide = $this->smartGuideIdType();
        $smartIDOne = $guide[$this->smart['entityTypeId']];
        $entityId = $guide[$entity];
        $parentSmart = "parentId" . $entity;
        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => "Документ №" . $this->smart['id'] . "/ " . $this->smart['title'],
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['companyId'],
                'companyId' => $this->smart['companyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $assigned,//ответственный
                'categoryId' => $categoryId,//категория
                'opportunity' => $this->smart['opportunity'],
                "isManualOpportunity" => "Y",
                'ufCrm' . $entityId . 'CsTypeDt' => $this->smart['ufCrm' . $smartIDOne . 'CsTypeDt'],
                'ufCrm' . $entityId . 'CsIdDoc' => $this->smart['parentId2'],
                'ufCrm' . $entityId . 'CsTypePay' => $this->smart['ufCrm' . $smartIDOne . 'CsTypePay'],
                'ufCrm' . $entityId . 'CsVat' => $this->smart['ufCrm' . $smartIDOne . 'CsVat'],
                'ufCrm' . $entityId . 'CsTemplateClient' => $this->smart['ufCrm' . $smartIDOne . 'CsTemplateClient'],
                'ufCrm' . $entityId . 'CsTemplateInternational' => $this->smart['ufCrm' . $smartIDOne . 'CsTemplateInternational'],

            ]
        ];
        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        if ($smartId) {
            $date = date("d.m.YTH:i");
            $params = [
                $parentSmart => $smartId,
                'ufCrm' . $smartIDOne . 'CsDateChange' => strtoTime($date),
            ];
            $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        }
        return $smartId;
    }

    public function docLongSmartSmart($entity, $categoryId, $nameDoc)
    {
        // require($_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/guide/financier.php');
        $guide = $this->smartGuideIdType();
        $entityIdEx = $guide[$this->smart['entityTypeId']];
        $entityId = $guide[$entity];
        if ($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo']) {
            $resDAteClose = date("c", strtotime($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo'] . ' +366 day'));
        } else {
            $resDAteClose = date("c", strtotime($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloading'] . ' +366 day'));
        }

        $company = $this->auth->core->call("crm.company.get", ['id' => $this->smart['companyId']])->getResponseData()->getResult()->getResultData()['TITLE'];
        $parentSmart = "parentId" . $entity;
        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => $nameDoc . $this->smart['companyId'] . " - " . $company,
                'closedate' => $resDAteClose, //дата закрытия договора
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['companyId'],
                'companyId' => $this->smart['companyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $this->smart['assignedById'],
                'categoryId' => $categoryId,//категория
                'mycompanyId' => $this->smart['mycompanyId'],
                'ufCrm' . $entityId . 'CsTypeDt' => $this->smart['ufCrm' . $entityIdEx . 'CsTypeDt'],
                'ufCrm' . $entityId . 'CsIdDoc' => $this->smart['companyId'],
                'ufCrm' . $entityId . 'CsTypePay' => $this->smart['ufCrm' . $entityIdEx . 'CsTypePay'],
                'ufCrm' . $entityId . 'CsVat' => $this->smart['ufCrm' . $entityIdEx . 'CsVat'],
                'ufCrm' . $entityId . 'CsTemplateClient' => $this->smart['ufCrm' . $entityIdEx . 'CsTemplateClient'],
                'ufCrm' . $entityId . 'CsTemplateInternational' => $this->smart['ufCrm' . $entityIdEx . 'CsTemplateInternational'],

            ]
        ];
        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        if ($smartId) {
//            $date = date("d.m.YTH:i");
//            $params = [
//                $parentSmart =>  $smartId,
//                'ufCrm'.$entityIdEx.'CsDateChange' => strtoTime($date),
//            ];
//            $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId'=> $this->smart['entityTypeId'],'id' => $this->smart['id'], 'fields' => $params]);
        }
        return $smartId;
    }

    // создание смарт-процесса по документам из экспедирования
    public function docSmartSmartEx($entity, $categoryId, $nameDoc, $idDoc = "", $newDoc = "N")
    {
        $date = date("d.m.YTH:i:s");
        $guide = $this->smartGuideIdType();
        $entityIdEx = $guide[$this->smart['entityTypeId']];
        $entityId = $guide[$entity];
        \CBitrixComponent::includeComponentClass('logistics:applications');
        require($_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/guide/financier.php');

        $arCarBodyTypesID = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_car_body_type', [], [], ['ID', 'UF_NAME']); // типы кузова
        $arCarBodyLoadingTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_loading_types', [], [], ['UF_ID_ATI', 'UF_NAME']); // варианты загрузки
        $arCarBodyUnloadingTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_unloading_types', [], [], ['UF_ID_ATI', 'UF_NAME']); // варианты разгрузки
        $arTruckBrands = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_truckbrands', [], [], ['ID', 'UF_NAME']); // типы валюты
        $arTruckModels = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_truckmodels', [], [], ['ID', 'UF_MODEL']); // типы валюты
        $arTruckTypes = \CassoftLogisticsApp::getAllElementsFromHiBlock('ati_su_type_auto', [], [], ['ID', 'UF_NAME']); // типы валюты

        foreach ($arCarBodyTypesID as $key) {
            $carBodyTypesID[$key['ID']] = $key['UF_NAME'];
        }
        foreach ($arTruckBrands as $key) {
            $truckBrands[$key['ID']] = $key['UF_NAME'];
        }
        foreach ($arTruckModels as $key) {
            $truckModels[$key['ID']] = $key['UF_MODEL'];
        }
        foreach ($arTruckTypes as $key) {
            $truckTypes[$key['ID']] = $key['UF_NAME'];
        }
        foreach ($arCarBodyLoadingTypes as $key) {
            $carBodyLoadingTypes[$key['UF_ID_ATI']] = $key['UF_NAME'];
        }
        foreach ($arCarBodyUnloadingTypes as $key) {
            $carBodyUnloadingTypes[$key['UF_ID_ATI']] = $key['UF_NAME'];
        }
        //запрос получение данных по авто

        $deal = $this->auth->core->call("crm.deal.get", ['id' => $this->smart['parentId2']])->getResponseData()->getResult()->getResultData();
        file_put_contents(__DIR__ . "/logDocSmart.txt", print_r($deal, true));
        $avtoId = $this->auth->core->call("entity.item.get", ['ENTITY' => "car_base", 'sort' => [], 'filter' => ['ID' => $this->smart['ufCrm' . $entityIdEx . 'CsCar'],]])->getResponseData()->getResult()->getResultData()[0]['PROPERTY_VALUES'];
        $carInfo = $truckTypes[$avtoId['truck_type']] . " " . $carBodyTypesID[$avtoId['truck_body_type']] . ", " . $truckBrands[$avtoId['truck_brands']] . " - " . $truckModels[$avtoId['truck_models']] . ", г/н:" . $avtoId['reg_number'] . " г/п:" . $avtoId['load_capacity'] . " т. объем: " . $avtoId['body_volume'] . " м3";

        $application = $this->auth->core->call("entity.item.get", ['ENTITY' => "application", 'sort' => [], 'filter' => ['PROPERTY_UF_CS_EX' => $this->smart['id']]])->getResponseData()->getResult()->getResultData()[0];
        $appProp = $application['PROPERTY_VALUES'];

        //запрос получение данных по грузу
        $cargo = $this->auth->core->call("entity.item.get", ['ENTITY' => "cargo", 'sort' => [], 'filter' => ['ID' => $appProp['UF_CS_LOADING']]])->getResponseData()->getResult()->getResultData()[0]['PROPERTY_VALUES'];
        if ($cargo['UF_CS_TYPE_WEIGHT'] === 'tons') {
            $typeWeight = "т.";
        } else {
            $typeWeight = "кг.";
        }
        $type_dt_S = [
            'EDO' => "с использованием ЭДО",
            'BDO' => "Оригиналы документов",
            'SCAN' => "Сканкопии документов",
        ];

        $workdays = [
            'W' => "Рабочих дней",
            'C' => "Календарных дней",
        ];
        $ConditionsPay = [
            "BEFORE_LOAD" => "до загрузки",
            "BEFORE" => "до разгрузки",
            "AFTER" => "после разгрузки",
        ];
        $vatDesc = [
//            "NOVAT" => "Сумма, подлежащая оплате Экспедитору за оказанные им услуги (ставка за перевозку), не облагается НДС в связи с применением Экспедитором упрощенной системы налогообложения на основании Главы 26.2 Налогового кодекса РФ",
//            "VAT0" =>"Согласованная Сторонами стоимость услуг по перевозке груза включает в себя НДС 0% по ставке, о чем указывается в Заявке/Поручении экспедитору",
//            "VAT10" =>  "Согласованная Сторонами стоимость услуг по перевозке груза включает в себя НДС по ставке, предусмотренной действующим законодательством Российской Федерации, о чем указывается в Заявке/Поручении экспедитору. Если информация о наличии НДС в Заявке/Поручении экспедитору на перевозку груза не указана, согласованная Сторонами стоимость услуг подлежит увеличению на соответствующую сумму НДС по действующей на момент заключения Заявки/Поручения экспедитору налоговой ставке.",
//            "VAT20" =>  "Согласованная Сторонами стоимость услуг по перевозке груза включает в себя НДС по ставке, предусмотренной действующим законодательством Российской Федерации, о чем указывается в Заявке/Поручении экспедитору. Если информация о наличии НДС в Заявке/Поручении экспедитору на перевозку груза не указана, согласованная Сторонами стоимость услуг подлежит увеличению на соответствующую сумму НДС по действующей на момент заключения Заявки/Поручения экспедитору налоговой ставке.",
            "NOVAT" => " Без НДС",
            "VAT0" => "в том числе НДС 0%",
            "VAT10" => "в том числе НДС 10%",
            "VAT20" => "в том числе НДС 20%",

        ];
        $type_pay = [
            'BANK_PAY' => "на расчетный счет",
            'CARD_PAY' => "на карта",
            'CASH_PAY' => "наличными",
        ];
        if ($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo']) {
            $resDAteClose = date("c", strtotime($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo'] . ' +1 day'));
        } else {
            $resDAteClose = date("c", strtotime($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloading'] . ' +1 day'));
        }
        if ($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanLoadingTo']) {
            $DatePlanLoadingTo = " по " . $this->smart['ufCrm' . $entityIdEx . 'CsDatePlanLoadingTo'];
        } else {
            $DatePlanLoadingTo = "";
        }
        if ($this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo']) {
            $DatePlanUnloadingTo = " по " . $this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloadingTo'];
        } else {
            $DatePlanUnloadingTo = "";
        }
        if ($appProp['UF_CS_LOCALITY_UNLOAD'] === 'г Москва' || $appProp['UF_CS_LOCALITY_UNLOAD'] === 'г Санкт-Петербург') {
            $unloadAddressShort = $appProp['UF_CS_LOCALITY_UNLOAD'];
        } else {
            $unloadAddressShort = $appProp['UF_CS_REGION_UNLOAD'] . ", " . $appProp['UF_CS_LOCALITY_UNLOAD'];
        }
        if ($appProp['UF_CS_LOCALITY_LOAD'] === 'г Москва' || $appProp['UF_CS_LOCALITY_LOAD'] === 'г Санкт-Петербург') {
            $loadAddressShort = $appProp['UF_CS_LOCALITY_LOAD'];
        } else {
            $loadAddressShort = $appProp['UF_CS_REGION_LOAD'] . ", " . $appProp['UF_CS_LOCALITY_LOAD'];
        }

        $parentSmart = "parentId" . $entity;
        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => $nameDoc . $this->smart['parentId2'] . "/ " . $application["NAME"],
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['companyId'],
                'companyId' => $this->smart['companyId'],
                'mycompanyId' => $this->smart['mycompanyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $this->smart['assignedById'],//ответственный
                'categoryId' => $categoryId,//категория
                'opportunity' => $this->smart['opportunity'],
                "isManualOpportunity" => "Y",
                'ufCrm' . $entityId . 'CsTypeDt' => $type_dt_S[$this->smart['ufCrm' . $entityIdEx . 'CsTypeDt']],
                'ufCrm' . $entityId . 'CsTypePay' => $type_pay[$this->smart['ufCrm' . $entityIdEx . 'CsTypePay']],
                'ufCrm' . $entityId . 'CsVat' => $vatDesc[$this->smart['ufCrm' . $entityIdEx . 'CsVat']],
                'ufCrm' . $entityId . 'CsCar' => $carInfo,
                'ufCrm' . $entityId . 'CsDriversOne' => $this->smart['ufCrm' . $entityIdEx . 'CsDrivers'][0],
                'ufCrm' . $entityId . 'CsDriversTwo' => $this->smart['ufCrm' . $entityIdEx . 'CsDrivers'][1],
                'ufCrm' . $entityId . 'CsPath' => $application["NAME"],
                'ufCrm' . $entityId . 'CsPrepayment' => $this->smart['ufCrm' . $entityIdEx . 'CsPrepayment'],
                'ufCrm' . $entityId . 'CsPostponement' => $this->smart['ufCrm' . $entityIdEx . 'CsPostponement'],
                'ufCrm' . $entityId . 'CsWorkday' => $workdays[$this->smart['ufCrm' . $entityIdEx . 'CsWorkday']],// кол-во дней
                'ufCrm' . $entityId . "CsConditionsPay" => $ConditionsPay[$this->smart['ufCrm' . $entityIdEx . 'ConditionsPay']],
                "ufCrm" . $entityId . "CsCardNumber" => $this->smart['ufCrm' . $entityIdEx . 'CardNumber'],
                "ufCrm" . $entityId . "CsCardOwner" => $this->smart['ufCrm' . $entityIdEx . 'CardOwner'],
                'ufCrm' . $entityId . 'CsDatePlanLoading' => $this->smart['ufCrm' . $entityIdEx . 'CsDatePlanLoading'],
                'ufCrm' . $entityId . 'CsDatePlanLoadingTo' => $DatePlanLoadingTo,
                'ufCrm' . $entityId . 'CsDatePlanUnloading' => $this->smart['ufCrm' . $entityIdEx . 'CsDatePlanUnloading'],
                'ufCrm' . $entityId . 'CsDatePlanUnloadingTo' => $DatePlanUnloadingTo,
                'closedate' => $resDAteClose, //дата закрытия договора
                'ufCrm' . $entityId . 'CsCommentProvider' => $this->smart['ufCrm' . $entityIdEx . 'CsComment'],
                'ufCrm' . $entityId . 'CsContactClient' => $deal['CONTACT_ID'],
                'ufCrm' . $entityId . 'CsCompanyClient' => $deal['COMPANY_ID'],
                'ufCrm' . $entityId . 'CsCommentClient' => $deal['COMMENTS'],
                'ufCrm' . $entityId . 'CsBasisDoc' => $idDoc,
                'ufCrm' . $entityId . 'CsTypeLoading' => $carBodyLoadingTypes[$appProp['UF_CS_TYPE_LOADING']],

                'ufCrm' . $entityId . 'CsUnloadAddressShort' => $unloadAddressShort,
                'ufCrm' . $entityId . 'CsloadAddressShort' => $loadAddressShort,
                'ufCrm' . $entityId . 'CsTypeUnload' => $carBodyUnloadingTypes[$appProp['UF_CS_TYPE_UNLOAD']],
                'ufCrm' . $entityId . 'CsDownloadAddress' => json_decode($cargo['UF_CS_DOWNLOAD_ADDRESS'], true)['unrestricted_value'],
                'ufCrm' . $entityId . 'CsUnloadingAddress' => json_decode($cargo['UF_CS_UNLOADING_ADDRESS'], true)['unrestricted_value'],
                'ufCrm' . $entityId . 'CsNameCargo' => $cargo['UF_CS_NAME'],
                'ufCrm' . $entityId . 'CsWeight' => $cargo['UF_CS_WEIGHT'] . $typeWeight,
                'ufCrm' . $entityId . 'CsVolume' => $cargo['UF_CS_VOLUME'] . "м3",
                'ufCrm' . $entityId . 'CsIdDoc' => $this->smart['companyId'] . "/" . $this->smart['parentId2'],
                'ufCrm' . $entityId . 'CsTemplateClient' => $this->smart['ufCrm' . $entityIdEx . 'CsTemplateClient'],
                'ufCrm' . $entityId . 'CsTemplateInternational' => $this->smart['ufCrm' . $entityIdEx . 'CsTemplateInternational'],


            ]
        ];
        file_put_contents(__DIR__ . "/logDocSmart.txt", print_r($paramSmart, true), FILE_APPEND);

        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        if ($smartId) {

            $date = date("d.m.YTH:i");
            $params = [
                $parentSmart => $smartId,
                'ufCrm' . $entityId . 'CsDateChange' => strtoTime($date),
            ];
            $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params]);
        }
        return $smartId;
    }

    //Функция добавления продукта из одного Смарт-процесса в другой
    public function productsAdd($ownerId, $ownerType)
    {
        $smartDecimalOne = "T" . dechex($this->smart['entityTypeId']);
        $paramsProd = [
            'order' => [],
            'filter' => [
                "=ownerType" => $smartDecimalOne,
                "=ownerId" => $this->smart['id']
            ],
            'start' => 0
        ];
        $arProd = $this->auth->core->call("crm.item.productrow.list", $paramsProd)->getResponseData()->getResult()->getResultData()['productRows'];
        $resAdd = $this->auth->core->call("crm.item.productrow.set", ['ownerType' => $ownerType, 'ownerId' => $ownerId, 'productRows' => $arProd]);
        return $resAdd;
    }

    public function docClientCategorySingle()
    {
        $guide = $this->smartGuide(); //справочник смартпроцессов
        $entityIdDoc = $guide['Договора (Заказчики)']['id'];
        $smartStage = $this->smartStage($this->smart['entityTypeId'], $this->smart['categoryId']);

        if ($this->smart['stageId'] === $smartStage['Условия согласованы'] && $this->smart['ufCrm' . $entityIdDoc . 'CsTemplateClient'] !== '1' && $this->smart['ufCrm' . $entityIdDoc . 'CsGenerateDoc'] !== '1') {
            if ($this->smart['ufCrm' . $entityIdDoc . 'CsTemplateInternational'] !== '1') {
                $template = 'Договор-Заявка с Клиентом ООО-ООО';
            }
            $stamps = 1;
            $values = [
                'DocumentNumber' => $this->smart['parentId2'],
                'title' => $this->smart['title'],
            ];
            $link = $this->smartDocGenerator($values, $template, $stamps);

            if ($link) {
                $params = [
                    'stageId' => $smartStage['Проект договора получен'],
                    'ufCrm' . $entityIdDoc . 'CsGenerateDoc' => 1,
                    'ufCrm' . $entityIdDoc . 'CsDateChange' => strtotime(date("d.m.YTH:i:s"))
                ];
                $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
            }
        }
        if (!empty($this->smart['ufCrm' . $entityIdDoc . 'CsScanDoc']) && $this->smart['stageId'] === $smartStage['Договор подписан']) {
            $dealStageUp = $this->dealStageUp("DOCUMENTS");
        }
        if(empty($this->smart['ufCrm' . $entityIdDoc . 'CsScanDoc']) && ($this->smart['stageId'] === $smartStage['Договор подписан'] ||  $this->smart['stageId'] === $smartStage['Договор закрыт (Обязательства выполнены)'] )){
            $comments = "[color=red][b]ВНИМАНИЕ![/color][/b] не загружен скан подписанного договора, смена стадии не возможна";
            $fields =[
                'ufCrm'.$entityIdDoc.'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
                'stageId' => $this->smart['previousStageId']
            ];
            $smartUp = $this->smartCommentsFields($comments, $fields);
        }
    }

    public function docClientCategoryLong()
    {
        $guide = $this->smartGuide(); //справочник смартпроцессов
        $entityIdDoc = $guide['Договора (Заказчики)']['id'];
        $smartStage = $this->smartStage($this->smart['entityTypeId'], $this->smart['categoryId']);
        if ($this->smart['stageId'] === $smartStage['Условия согласованы'] && $this->smart['ufCrm' . $entityIdDoc . 'CsTemplateClient'] !== '1' && $this->smart['ufCrm' . $entityIdDoc . 'CsGenerateDoc'] !== '1') {
            // file_put_contents($file_log, print_r($smartCategoryDoc, true), FILE_APPEND);
            if ($this->smart['ufCrm' . $entityIdDoc . 'CsTemplateInternational'] !== '1') {
                $template = 'Договор с Заказчиком ооо-ооо';
            }
            $stamps = 1;
            $values = [
                'DocumentNumber' => $this->smart['parentId2'],
                'title' => $this->smart['title'],
            ];
            $link = $this->smartDocGenerator($values, $template, $stamps);
            if ($link) {
                $params = [
                    'stageId' => $smartStage['Проект договора получен'],
                    'ufCrm' . $entityIdDoc . 'CsGenerateDoc' => 1,
                    'ufCrm' . $entityIdDoc . 'CsDateChange' => strtotime(date("d.m.YTH:i:s"))
                ];
                $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
            }
        }
        if(empty($this->smart['ufCrm' . $entityIdDoc . 'CsScanDoc']) && ($this->smart['stageId'] === $smartStage['Договор подписан'] ||  $this->smart['stageId'] === $smartStage['Договор закрыт (Обязательства выполнены)'] ||  $this->smart['stageId'] === $smartStage['Договор в работе'] )){
            $comments = "[color=red][b]ВНИМАНИЕ![/color][/b] не загружен скан подписанного договора, смена стадии не возможна";
            $fields =[
                'ufCrm'.$entityIdDoc.'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
                'stageId' => $this->smart['previousStageId']
            ];
            $smartUp = $this->smartCommentsFields($comments, $fields);
        }
    }


    public function docClientCategoryApp()
    {
        file_put_contents(__DIR__ . "/logDocSmartApp.txt", print_r(date('c')."\n", true));
        $guide = $this->smartGuide(); //справочник смартпроцессов
        $entityIdDoc = $guide['Договора (Заказчики)']['id'];
        $smartStage = $this->smartStage($this->smart['entityTypeId'], $this->smart['categoryId']);
        if ($this->smart['stageId'] === $smartStage['Условия согласованы'] && $this->smart['ufCrm' . $entityIdDoc . 'CsTemplateClient'] !== '1' && $this->smart['ufCrm' . $entityIdDoc . 'CsGenerateDoc'] !== '1') {
            // file_put_contents($file_log, print_r($smartCategoryDoc, true), FILE_APPEND);
            if ($this->smart['ufCrm' . $entityIdDoc . 'CsTemplateInternational'] !== '1') {
                $template = 'Приложение к основному договору (заказчик)';
            }
            $stamps = 1;
            $values = [
                'DocumentNumber' => $this->smart['parentId2'],
                'title' => $this->smart['title'],
            ];
            $link = $this->smartDocGenerator($values, $template, $stamps);
            if ($link) {
                $params = [
                    'stageId' => $smartStage['Проект договора получен'],
                    'ufCrm' . $entityIdDoc . 'CsGenerateDoc' => 1,
                    'ufCrm' . $entityIdDoc . 'CsDateChange' => strtotime(date("d.m.YTH:i:s"))
                ];
                $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
            }
        }
        if (!empty($this->smart['ufCrm' . $entityIdDoc . 'CsScanDoc']) && $this->smart['stageId'] === $smartStage['Приложение подписано']) {
            $dealStageUp = $this->dealStageUp("DOCUMENTS");
        }
        if(empty($this->smart['ufCrm' . $entityIdDoc . 'CsScanDoc']) && ($this->smart['stageId'] === $smartStage['Приложение подписано'] ||  $this->smart['stageId'] === $smartStage['Приложение закрыто (Обязательства выполнены)'] )){
            file_put_contents(__DIR__ . "/logDocSmartApp.txt", print_r("не загружен скан\n", true), FILE_APPEND);
            $comments = "[color=red][b]ВНИМАНИЕ![/color][/b] не загружен скан подписанного приложения, смена стадии не возможна";
            $fields =[
                'ufCrm'.$entityIdDoc.'CsDateChange' => strtoTime(date("d.m.YTH:i:s")),
                'stageId' => $this->smart['previousStageId']
            ];
            $smartUp = $this->smartCommentsFields($comments, $fields);
            file_put_contents(__DIR__ . "/logDocSmartApp.txt", print_r($smartUp, true), FILE_APPEND);
        }
    }

    public function smartAddLead($entity, $assigned, $categoryId)
    {
        $guide = $this->smartGuideIdType();
        $smartIDOne = $guide[$this->smart['entityTypeId']];
        $entityId = $guide[$entity];
        $parentSmart = "parentId" . $entity;
        $paramSmart = [
            "entityTypeId" => $entity,
            "fields" => [
                'title' => "Документ №" . $this->smart['id'] . "/ " . $this->smart['title'],
                'parentId2' => $this->smart['parentId2'],
                'parentId' . $this->smart['entityTypeId'] => $this->smart['id'],
                'parentId7' => $this->smart['companyId'],
                'companyId' => $this->smart['companyId'],
                'contactId' => $this->smart['contactId'],
                'assignedById' => $assigned,//ответственный
                'categoryId' => $categoryId,//категория
                'opportunity' => $this->smart['opportunity'],
                "isManualOpportunity" => "Y",
                'ufCrm' . $entityId . 'CsTypeDt' => $this->smart['ufCrm' . $smartIDOne . 'CsTypeDt'],
                'ufCrm' . $entityId . 'CsIdDoc' => $this->smart['parentId2'],
                'ufCrm' . $entityId . 'CsTypePay' => $this->smart['ufCrm' . $smartIDOne . 'CsTypePay'],
                'ufCrm' . $entityId . 'CsVat' => $this->smart['ufCrm' . $smartIDOne . 'CsVat'],
                'ufCrm' . $entityId . 'CsTemplateClient' => $this->smart['ufCrm' . $smartIDOne . 'CsTemplateClient'],
                'ufCrm' . $entityId . 'CsTemplateInternational' => $this->smart['ufCrm' . $smartIDOne . 'CsTemplateInternational'],

            ]
        ];
        $smartId = $this->auth->core->call("crm.item.add", $paramSmart)->getResponseData()->getResult()->getResultData()['item']['id'];
        if ($smartId) {
            $date = date("d.m.YTH:i");
            $params = [
                $parentSmart => $smartId,
                'ufCrm' . $smartIDOne . 'CsDateChange' => strtoTime($date),
            ];
            $smartUp = $this->auth->core->call("crm.item.update", ['entityTypeId' => $this->smart['entityTypeId'], 'id' => $this->smart['id'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        }
        return $smartId;
    }


}

