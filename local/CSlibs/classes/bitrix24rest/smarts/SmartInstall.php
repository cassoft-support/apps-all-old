<?php
namespace CSlibs\B24\Smarts;
class SmartInstall
{
    private $auth;
    private $installApp;
  //  private $smart;
//  private $fileLog = __DIR__ . "/logProcessAuth.txt";

    // private $date = date("d.m.YTH:i");


    public function __construct($auth, $app)
    {
        $this->auth = $auth;
        $HlInstallApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $this->installApp = $HlInstallApp->installApp($app);
      //  $this->smart = $smart;
        //  $this->log_file = "logProcess";
    }
    public function smartsInstall()
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logSmartInstall.txt";
        p('smart', 'start', $log);
        $guideCRM = [ //список сущностей срм
            '3694' => 1,
            '3695' => 2,
            '3696' => 3,
            '3697' => 4, //компания
            '3698' => 31 //счет
        ];

//        $resSolutions = $this->auth->CScore->call("crm.automatedsolution.list")['automatedSolutions'];
//        $solutions = array_column($resSolutions, 'id', 'title');
//        p($resSolutions, 'solutions', $log);
        foreach ($this->auth->batch->getTraversableList("crm.type.list", [], [], ['*'], 6000) as $value) {
            // d($value);
            foreach($value as $smartVal) {
                if ($smartVal['code']) {
                    $smartsMapCode[$smartVal['code']] = $smartVal['id'];
                }
            }
        }
       // p($this->installApp['UF_SMART'], 'smartsApp', $log);
        $HlEntities = new \CSlibs\B24\HL\HlService('install_smart');
        $smarts = $HlEntities->getByIdList($this->installApp['UF_SMART']);
        $smartsHLMapCode = array_column($smarts, 'ID', 'UF_CODE');
        $guideSmart = array_column($smarts, 'UF_ENTITY_TYPE_ID', 'ID');
        if(!empty($smartsMapCode)){
            $smartsInstall = array_diff_key($smartsHLMapCode,$smartsMapCode);
        }else{
            $smartsInstall = $smartsHLMapCode;
        }

//        p($smartsHLMapCode, 'smartsHLMapCode', $log);
//        p($smartsMapCode, 'smartsMapCode', $log);
//        p($smartsInstall, 'smartsInstall', $log);
        $addSmart=0;
        $upSmart=0;
        $check=[
            '0'=>'N',
            '1'=>'Y',
        ];
       // p($smarts, 'smarts', $log);
        $solutionsSmart=[];
        foreach ($smarts as $keyFields) {
            $params=[];

            $params['fields'] = [
                'entityTypeId' => $keyFields['UF_ENTITY_TYPE_ID'],
                'title' => $keyFields['UF_TITLE'], // Счета поставщиков
                'code' => $keyFields['UF_CODE'], // accountProvider
                'isCategoriesEnabled' => $check[$keyFields['UF_IS_CATEGORIES_ENABLED']], // 1
                'isStagesEnabled' => $check[$keyFields['UF_IS_STAGES_ENABLED']], // 1
                'isBeginCloseDatesEnabled' => $check[$keyFields['UF_IS_BEGIN_CLOSE_DATES_ENABLED']], // 1
                'isClientEnabled' => $check[$keyFields['UF_IS_CLIENT_ENABLED']], // 1
                'isUseInUserfieldEnabled' => $check[$keyFields['UF_IS_USE_IN_USERFIELD_ENABLED']], // 1
                'isLinkWithProductsEnabled' => $check[$keyFields['UF_IS_LINK_WITH_PRODUCTS_ENABLED']], // 1
                'isMycompanyEnabled' => $check[$keyFields['UF_IS_MYCOMPANY_ENABLED']], // 1
                'isDocumentsEnabled' => $check[$keyFields['UF_IS_DOCUMENTS_ENABLED']], // 1
                'isSourceEnabled' => $check[$keyFields['UF_IS_SOURCE_ENABLED']], // 1
                'isObserversEnabled' => $check[$keyFields['UF_IS_OBSERVERS_ENABLED']], // 1
                'isRecyclebinEnabled' => $check[$keyFields['UF_IS_RECYCLEBIN_ENABLED']], // 1
                'isAutomationEnabled' => $check[$keyFields['UF_IS_AUTOMATION_ENABLED']], // 1
                'isBizProcEnabled' => $check[$keyFields['UF_IS_BIZ_PROC_ENABLED']], // 1
                'isSetOpenPermissions' => $check[$keyFields['UF_IS_SET_OPEN_PERMISSIONS']], // 0
                'isPaymentsEnabled' => $check[$keyFields['UF_IS_PAYMENTS_ENABLED']], // 0
                'isCountersEnabled' => $check[$keyFields['UF_IS_COUNTERS_ENABLED']], // 0
                'linkedUserFields' => [
                    'CALENDAR_EVENT|UF_CRM_CAL_EVENT' => true,
                    'TASKS_TASK|UF_CRM_TASK' => true,
                    'TASKS_TASK_TEMPLATE|UF_CRM_TASK' => true
                ],
//                "customSections" => [
//                    [
//                        "id" => $idSection,
//                        "title" => $keyFields['UF_SECTION'],
//                        'isSelected' => 'Y'
//                    ]
//                ],
//                "customSectionId" => $idSection,

            ];
            $parent = [];
            $parentCRM = [];
            foreach ($keyFields['UF_CARD_HB'] as $kHbP => $vHbP) {
                $parent = [
                    'entityTypeId' => $guideSmart[$vHbP],
                    'isChildrenListEnabled' => 'Y',
                      'isPredefined' => 'Y',
                ];
                $params['fields']['relations']['parent'][] = $parent;
                $params['fields']['relations']['child'][] = $parent;
            }
            foreach ($keyFields['UF_CARD_CRM'] as $k => $vCrmP) {

                $parentCRM = [
                    'entityTypeId' => $guideCRM[$vCrmP],
                    'isChildrenListEnabled' => 'Y',
                     'isPredefined' => 'Y',
                ];

                $params['fields']['relations']['parent'][] = $parentCRM;
                $params['fields']['relations']['child'][] = $parentCRM;
            }
           // p($params, 'params', $log);

            if(in_array($keyFields['ID'], $smartsInstall)){
                $smartAdd = $this->auth->CScore->call("crm.type.add", $params);
                $addSmart++;
                p($smartAdd['type']['customSections'], 'smartAdd', $log);
                $smartID= $smartAdd['type']['id'];
            }else{
                $smartUp = $this->auth->CScore->call("crm.type.update", ['id' => $smartsMapCode[$keyFields['UF_CODE']], $params]);
            $upSmart++;
                $smartID= $smartsMapCode[$keyFields['UF_CODE']];
               // p($smartUp['type']['customSections'], 'smartUp', $log);
            }



            $resSolutions = $this->auth->CScore->call("crm.automatedsolution.list",['filter' => ['title' => $keyFields['UF_SECTION']]])['automatedSolutions'][0];

            if(empty($resSolutions)){
                $solutionsParams=[

                    'fields' => [
                        'title' => $keyFields['UF_SECTION'],
                        'typeIds' => [$smartID]
                    ]
                ];
                $solutionsSmartAdd = $this->auth->CScore->call('crm.automatedsolution.add',$solutionsParams);
                p($solutionsParams, 'solutionsParams', $log);
                p($solutionsSmartAdd, 'solutionsSmartAdd', $log);
            }else{
                array_unshift($resSolutions['typeIds'], $smartID);
                $typeIds =array_unique($resSolutions['typeIds']);
                $solutionsParams=[
                    'id' => $resSolutions['id'],
                    'fields' => [
                        'typeIds' => $typeIds
                    ]
                ];
                $solutionsSmartUp = $this->auth->CScore->call('crm.automatedsolution.update',$solutionsParams);
                p($solutionsParams, 'solutionsParams', $log);
                p($solutionsSmartUp, 'solutionsSmartUp', $log);
           }

            if ($keyFields['UF_CATEGORY']) {
                $smartCategoryInstall = $this->smartCategoryInstall( $keyFields['UF_CATEGORY'], $keyFields['UF_ENTITY_TYPE_ID']);
            }
            if ($keyFields['UF_FIELDS']) {
                $smartFieldsInstall = $this->smartFieldsInstall($keyFields['UF_FIELDS'], $smartID, $keyFields['UF_ENTITY_TYPE_ID']);
            }
              }


        $result=[];
        $result =$smartCategoryInstall;
        $result =$smartFieldsInstall;
        $result['smarts'] = 'success';
        $result['smartAdd'] = $addSmart;
        $result['smartUp'] = $upSmart;

        return $result;
    }

    public function smartCategoryInstall( $idCategory, $entityTypeId)
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logSmartCat.txt";
        p("start", "idCategory", $log);
        p($this->installApp, "app", $log);
        p($idCategory, "idCategory", $log);
        $resCategory = $this->auth->CScore->call("crm.category.list", ['entityTypeId' => $entityTypeId])['categories'];
        $categoryMapCode = array_column($resCategory, 'id', 'name');
        $generalId = $categoryMapCode['Общая'];
        p($categoryMapCode, "categoryMapCode", $log);
        $HlCategory = new \CSlibs\B24\HL\HlService('install_category');
        $smartCategory = $HlCategory->getByIdList($idCategory);
        p($smartCategory, "smartCategory", $log);
        $categoryHLMapCode = array_column($smartCategory, 'ID', 'UF_TITLE');
        $catInstall= array_diff_key($categoryHLMapCode, $categoryMapCode);


        $add=0;
        $up=0;
        foreach ($smartCategory as $k => $kCat) {
            $fieldsCat =[
            'entityTypeId' => $entityTypeId,
            'fields' => [
                'name' => $kCat['UF_TITLE'],
                'sort' => $kCat['UF_SORT'],
                'isDefault' => $kCat['UF_IS_DEFAULT']
            ]
            ];
if($k ==0 && $generalId>0){
    $fieldsCat['id'] = $generalId;
    $idCategory = $generalId;
    $upCategory = $this->auth->CScore->call("crm.category.update", $fieldsCat); //создание категорий в смартпроцессах
    p($upCategory, "UpCategoryGeneral", $log);
    $up++;
}else {
    if (in_array($kCat['ID'], $catInstall)) {
        p($fieldsCat, "fieldsCat", $log);
        $addCategory = $this->auth->CScore->call("crm.category.add", $fieldsCat); //создание категорий в смартпроцессах
        p($addCategory, "addCategory", $log);
        $idCategory = $addCategory['category']['id'];
        $add++;
    } else {
        $fieldsCat['id'] = $categoryMapCode[$kCat['UF_TITLE']];
        $idCategory = $categoryMapCode[$kCat['UF_TITLE']];
        $upCategory = $this->auth->CScore->call("crm.category.update", $fieldsCat); //создание категорий в смартпроцессах
        p($upCategory, "UpCategory", $log);
        $up++;
    }
}
           $stageInstall= $this->smartStageInstall($kCat['UF_CS_STAGE'], $kCat['UF_CS_STAGE_START'], $kCat['UF_STAGE_S'],$kCat['UF_STAGE_F'], $idCategory, $entityTypeId);
        }
        $result =[];
        $result =$stageInstall;
        $result['category'] = 'success';
        $result['catAdd'] = $add;
        $result['catUp'] = $up;

        return $result;
    }


    public function smartStageInstall($stageAll, $stageStart, $stageS,$stageF, $idCategory, $entityTypeId)
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logSmartStage".$entityTypeId."-".$idCategory.".txt";
      //  $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/brokci/install/logSmartStage".$entityTypeId."-".$idCategory.".txt";
        p("start", "idCategory", $log);
        p($stageAll, "stageAll", $log);
        p($stageStart, "stageStart", $log);
        $add=0;
        $up=0;
        $systemStart=[];
        $systemS=[];
        $systemF=[];
        $stageMap=[];
        foreach ($this->auth->batch->getTraversableList("crm.status.list", [], ['ENTITY_ID' => 'DYNAMIC_'.$entityTypeId.'_STAGE_'.$idCategory], ['*'], 6000) as $value) {
         //   p($value, "value", $log);
            if($value['SYSTEM'] === 'Y' && $value['SEMANTICS'] !== 'S' && $value['SEMANTICS'] !== 'F'){
                $systemStart[$value['ID']] = $value['NAME'];
            }
            elseif($value['SYSTEM'] === 'Y' && $value['SEMANTICS'] === 'S'){
                $systemS[$value['ID']] = $value['NAME'];
            }elseif($value['SYSTEM'] === 'Y' && $value['SEMANTICS'] === 'F'){
                $systemF[$value['ID']] = $value['NAME'];
            }else{
                $stageMap[$value['NAME']]=$value['ID'];
            }
        }
        p($systemS, "systemS", $log);
        p($systemF, "systemF", $log);
        p($stageMap, "stageMap", $log);
        $HlStage = new \CSlibs\B24\HL\HlService('install_stage');
        $smartStageAll=[];
        $smartStageStart=[];
        $smartStageS=[];
        $smartStageF=[];
        $smartStageAll = $HlStage->getByIdList($stageAll);
        $smartStageStart = $HlStage->getByIdList($stageStart);
        $smartStageS = $HlStage->getByIdList($stageS);
        $smartStageF = $HlStage->getByIdList($stageF);
        $stageAllHLMapCode = array_column($smartStageAll, 'ID', 'UF_TITLE');
        $stageStartHLMapCode = array_column($smartStageStart, 'UF_TITLE', 'ID');
        $stageSHLMapCode = array_column($smartStageS, 'UF_TITLE', 'ID');
        $stageFHLMapCode = array_column($smartStageF, 'UF_TITLE', 'ID');
//        foreach ($smartStageAll as $kstage => $vstage){
//            if($vstage['UF_SEMANTICS'] === 'S'){
//                $stageSHLMapCode[$vstage['ID']]=$vstage['UF_TITLE'];
//                $stageSHL=$vstage;
//            }
//            elseif($vstage['UF_SEMANTICS'] === 'F'){
//                $stageFHLMapCode[$vstage['ID']]=$vstage['UF_TITLE'];
//                $stageFHL=$vstage;
//            }elseif($vstage['UF_SEMANTICS'] === 'F'){
//                $stageFHLMapCode[$vstage['ID']]=$vstage['UF_TITLE'];
//                $stageFHL=$vstage;
//            }
//            else{
//                $stageAllHLMapCode[$vstage['ID']]=$vstage['UF_TITLE'];
//                $stageAllHL[]=$vstage;
//            }
//
//        }
        $smartStageAll = array_multisort_value($smartStageAll, 'UF_SORT', SORT_ASC);
        $stageStartInstall=[];
        $stageFInstall=[];
        $stageSInstall=[];
        $stageAllInstall=[];
        $stageAllDell=[];
        $stageStartInstall= array_diff($stageStartHLMapCode, $systemStart);
        $stageFInstall= array_diff($stageFHLMapCode, $systemF);
        $stageSInstall= array_diff($stageSHLMapCode, $systemS);
        $stageAllInstall= array_diff_key($stageAllHLMapCode, $stageMap);
        $stageAllDell= array_diff_key($stageMap, $stageAllHLMapCode);

       p($stageAllDell, "stageAllDell", $log);
//        p($systemStart, "systemStart", $log);
//        p($stageStartInstall, "stageStartInstall", $log);
//        p($stageFInstall, "stageFInstall", $log);
//        p($stageSInstall, "stageSInstall", $log);
        p($stageAllInstall, "stageAllInstall", $log);
        if($stageStartInstall) {
            $paramsStart=[];
            $paramsStart=[
       "ID"=> array_key_first($systemStart),
            'fields'=>[
   'STATUS_ID' => 'DT'.$entityTypeId.'_'.$idCategory.':'.$smartStageStart[0]['UF_CODE'],
    'NAME' => $smartStageStart[0]['UF_TITLE'],
    "SORT"=> 1,
    'COLOR' => $smartStageStart[0]['UF_COLOR'],
            ]];
               $resStatusStartUp = $this->auth->CScore->call( "crm.status.update", $paramsStart);
               $up++;
        }
        if($stageSInstall) {
            $paramsS=[];
            $paramsS=[
       "ID"=> array_key_first($systemS),
            'fields'=>[
   'STATUS_ID' => 'DT'.$entityTypeId.'_'.$idCategory.':'.$smartStageS[0]['UF_CODE'],
    'NAME' => $smartStageS[0]['UF_TITLE'],
    "SORT"=> 5,
    'COLOR' => $smartStageS[0]['UF_COLOR'],
            ]];
            p($paramsS, "paramsStart", $log);
               $resStatusSUp = $this->auth->CScore->call( "crm.status.update", $paramsS);
            p($resStatusSUp, "resStatusSUp", $log);
            $up++;
        }
        if($stageFInstall) {
            $paramsF=[];
            $paramsF=[
       "ID"=> array_key_first($systemF),
            'fields'=>[
   'STATUS_ID' => 'DT'.$entityTypeId.'_'.$idCategory.':'.$smartStageF[0]['UF_CODE'],
    'NAME' => $smartStageF[0]['UF_TITLE'],
    "SORT"=> 10,
    'COLOR' => $smartStageF[0]['UF_COLOR'],
            ]];
            p($paramsF, "paramsStart", $log);
               $resStatusFUp = $this->auth->CScore->call( "crm.status.update", $paramsF);
            p($resStatusFUp, "resStatusStartUp", $log);
            $up++;
        }
        if($stageAllInstall) {
            foreach ($smartStageAll as $kStage) {
                if (in_array($kStage['ID'], $stageAllInstall)) {
                    $paramsAll=[];
                    $paramsAll = [
                        'fields' => [
                            'ENTITY_ID' => 'DYNAMIC_' . $entityTypeId.'_STAGE_'.$idCategory,
                            'STATUS_ID' => 'DT' . $entityTypeId . '_' . $idCategory . ':' . $kStage['UF_CODE'],
                            'NAME' => $kStage['UF_TITLE'],
                            "SORT" => $kStage['UF_SORT'],
                            'COLOR' => $kStage['UF_COLOR'],
                            'SYSTEM' => 'N',
                            'SEMANTICS' => $kStage['UF_SEMANTICS'],
                        ]];
                    p($paramsAll, "paramsStart", $log);
                    $resStatusAllAdd = $this->auth->CScore->call("crm.status.add", $paramsAll);
                    p($resStatusAllAdd, "resStatusStartAdd", $log);
                    $add++;
                }
            }
        }

//        if($stageAllDell) {
//            foreach ($stageAllDell as $kStage =>$vStage) {
//                $paramsDell=[];
//                    $paramsDell = [
//                        'id'=>$vStage,
//                        'fields' => [
//                            'NAME' => $kStage."OLD",
//                            'SYSTEM' => 'N',
//                            "SORT" =>1000,
//                            'SEMANTICS' => 'F',
//                        ]];
//                    p($paramsDell, "paramsStart", $log);
//                    $resStatusDell = $this->auth->CScore->call("crm.status.update", $paramsDell);
//                    p($resStatusDell, "resStatusDell", $log);
//                    $dell++;
//                }
//            }
        if($stageAllDell) {
            foreach ($stageAllDell as $kStage =>$vStage) {
//                $paramsDell=[];
//                    $paramsDell = [
//                        'id'=>$vStage,
//                        'fields' => [
//                            'NAME' => $kStage."OLD",
//                            'SYSTEM' => 'N',
//                            "SORT" =>1000,
//                            'SEMANTICS' => 'F',
//                        ]];
//                    p($paramsDell, "paramsStart", $log);
                    $resStatusDell = $this->auth->CScore->call("crm.status.delete", ['id'=>$vStage]);
                    p($resStatusDell, "resStatusDell", $log);
                    $dell++;
                }
            }


        $result =[];
        $result['stage-'.$entityTypeId][$idCategory] = 'success';
        $result['stage-'.$entityTypeId][$idCategory.'-stageAdd'] = $add;
        $result['stage-'.$entityTypeId][$idCategory.'-stageUp'] = $up;
        return $result;
    }

    public function smartFieldsInstall($params, $smartID, $smartTypeID)
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logSmartFields".$smartTypeID.".txt";
        //  $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/brokci/install/logSmartStage".$entityTypeId."-".$idCategory.".txt";
        p("start", "idCategory", $log);
        p($smartID, "smartID", $log);
        $add = 0;
        $fieldsSmartCrmAll=$this->auth->CScore->call("userfieldconfig.list", ["moduleId"=> "crm", 'filter'=>['entityId'=> 'CRM_'.$smartID]])['fields'];
        foreach ($fieldsSmartCrmAll as $fieldsSmartCrm){

            $fieldName="UF_CRM_".$smartID."_";
            p($fieldsSmartCrm['fieldName'], "fieldName", $log);
            $fieldsSmartCrmMap[str_replace($fieldName, '', $fieldsSmartCrm['fieldName'])] = $fieldsSmartCrm['id'];
        }
        p($fieldsSmartCrmMap, "fieldsSmartCrmMap", $log);
       // $guide = $this->smartGuide();
        $HlFieldsSmart = new \CSlibs\B24\HL\HlService('install_fields_smarts');
        $smartFieldsSmartAll=[];
        $smartFieldsSmartAll = $HlFieldsSmart->getByIdList($params);
        $smartFieldsSmartAllMap =array_column($smartFieldsSmartAll, 'ID','UF_FIELD_NAME');
        if($fieldsSmartCrmMap){
            $fieldsStartInstall= array_diff_key($smartFieldsSmartAllMap, $fieldsSmartCrmMap);
        }else{
            $fieldsStartInstall= $smartFieldsSmartAllMap;
        }
        $idRest=$this->auth->CScore->call("app.info")['ID'];
        p($idRest."\n", "--------------------------------idRest", $log);
        p($fieldsStartInstall, "fieldsStartInstall", $log);
        $HlTypeFields = new \CSlibs\B24\HL\HlService('crm_userfield_types');
        $typeFields = $HlTypeFields->makeFieldToFieldRest("ID", "UF_CODE", $idRest);

        foreach ($smartFieldsSmartAll as $field) {
            $arEnum=[];
            $enumAll=[];
            $settings = [];
            if ($field["UF_SETTINGS"]) {

                foreach ($field["UF_SETTINGS"] as $set) {
                    $res = explode("|", $set);
                    $settings[$res[0]] = $res[1];
                }
            }
                if ($field["UF_ENUM"]) {

                    foreach ($field["UF_ENUM"] as $enum) {
                        $resEnum = explode(":", $enum);
                        foreach ($resEnum as $el) {
                            if ($el) {
                                $resEl = explode("|", $el);
                                $arEnum[$resEl[0]] = $resEl[1];
                            }
                        }
                        $enumAll[] = $arEnum;
                    }
                }
                $paramsFields = [
                    'moduleId' => 'crm',
                    'field'=>[
                    'entityId' => 'CRM_' . $smartID,
                    'fieldName' => "UF_CRM_" . $smartID . "_" . $field['UF_FIELD_NAME'],
                    'userTypeId' => $typeFields[$field["UF_CRM_FIELD_TYPE"]],
                    'xmlId' => $field['UF_FIELD_NAME'],
                    'sort' => $field['UF_SORT'],
                    'multiple' => $field['UF_MULTIPLE'],
                    'mandatory' => $field['UF_MANDATORY'],
                    'showFilter' => $field['UF_SHOW_FILTER'],
                    'showInList' => $field['UF_SHOW_IN_LIST'],
                    'editInList' => $field['UF_EDIT_IN_LIST'],
                    'isSearchable' => $field['UF_IS_SEARCHABLE'],
                    'editFormLabel' => [
                        "ru" => $field['UF_NAME'],
                    ],
                    'settings' => $settings,
                    'enum' => $enumAll
]
                ];

//                if ($valP['userTypeId'] === 'crm' && $valP['isDynamic'] === 'Y') {
//                    $valP['settings']['DYNAMIC_' . $smartTypeID] = 'Y';
//                }

                p($paramsFields, "paramsFields", $log);
                p($field['ID'], "ID", $log);
                p(in_array($field['ID'], $fieldsStartInstall), "in_array", $log);

                if (in_array($field['ID'], $fieldsStartInstall)) {
                    $smartfieldAdd = $this->auth->CScore->call("userfieldconfig.add", $paramsFields);
                    p($smartfieldAdd, "smartfieldAdd", $log);
                    $add++;
                }

        }

            $result =[];
            $result['fields-'.$smartTypeID] = 'success';
            $result['fieldsAdd-'.$smartTypeID] = $add;
            return $result;

    }

//end class
}