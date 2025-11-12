<?php

namespace CSlibs\B24\Deal;

class dealClass
{
    private $auth;
    private $deal;
    private $fileLog;
    private $date;


    public function __construct($auth, $deal)
    {
        $this->auth = $auth;
        $this->deal = $deal;
        $this->fileLog = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/logDealClass.txt";
        $this->date =date("d.m.YTH:i:s");

//;

    }
    public function stageDeal(){

        $categoryId = 'DEAL_STAGE';
        if($this->deal['CATEGORY_ID'] > 0){
            $categoryId = 'DEAL_STAGE_'.$this->deal['CATEGORY_ID'];    }
        $paramStatus = [
            'order'=> [],
            'filter' => [
                "ENTITY_ID"=> $categoryId
            ]
        ];

        $resStage = $this->auth->CScore->call( "crm.status.list", $paramStatus);
        foreach ($resStage as $kStage => $vStage){
            $stage =explode(':', $vStage['STATUS_ID'])[1];
            if($stage) {
                $arStage[$stage] = $vStage['NAME'];
            }else{
                $arStage[$vStage['STATUS_ID']] = $vStage['NAME'];
            }
        }
        return $arStage;
    }

    /**
     * Получение справочника стадий текущей сделки
     * @noinspection PhpUnused не ппроверять на использование
     * @return возвращает массив имя-> код стадии
     */
    public function stageDealName(){
        $categoryId = 'DEAL_STAGE';
        if($this->deal['CATEGORY_ID'] > 0){
            $categoryId = 'DEAL_STAGE_'.$this->deal['CATEGORY_ID'];    }
        $paramStatus = [
            'order'=> [],
            'filter' => [
                "ENTITY_ID"=> $categoryId
            ]
        ];

        $resStage = $this->auth->CScore->call( "crm.status.list", $paramStatus);
        foreach ($resStage as $kStage => $vStage){
            $stage =explode(':', $vStage['STATUS_ID'])[1];
            if($stage) {
                $arStage[$vStage['NAME']] = $stage;
            }else{
                $arStage[$vStage['NAME']] = $vStage['STATUS_ID'];
            }
        }
        return $arStage;
    }
    public function stageDealNameCategory($category){
        $categoryId = 'DEAL_STAGE';
        if($category > 0){
            $categoryId = 'DEAL_STAGE_'.$category;    }
        $paramStatus = [
            'order'=> [],
            'filter' => [
                "ENTITY_ID"=> $categoryId
            ]
        ];

        $resStage = $this->auth->CScore->call( "crm.status.list", $paramStatus);
        foreach ($resStage as $kStage => $vStage){
            $arStage[$vStage['NAME']] = $vStage['STATUS_ID'];
        }
        return $arStage;
    }
    public function stageDealCategoryName($category){
        $categoryId = 'DEAL_STAGE';
        if($category > 0){
            $categoryId = 'DEAL_STAGE_'.$category;    }
        $paramStatus = [
            'order'=> [],
            'filter' => [
                "ENTITY_ID"=> $categoryId
            ]
        ];

        $resStage = $this->auth->CScore->call( "crm.status.list", $paramStatus);
        foreach ($resStage as $kStage => $vStage){
            $arStage[$vStage['STATUS_ID']] = $vStage['NAME'];
        }
        return $arStage;
    }
    public function stageDealNameReal(){
        $categoryId = 'DEAL_STAGE';
        if($this->deal['CATEGORY_ID'] > 0){
            $categoryId = 'DEAL_STAGE_'.$this->deal['CATEGORY_ID'];    }
        $paramStatus = [
            'order'=> [],
            'filter' => [
                "ENTITY_ID"=> $categoryId
            ]
        ];

        $resStage = $this->auth->CScore->call( "crm.status.list", $paramStatus);
        foreach ($resStage as $kStage => $vStage){

                $arStage[$vStage['NAME']] = $vStage['STATUS_ID'];

        }
        return $arStage;
    }
    public function stageDealNew($newStage){
        $stage =explode(':', $this->deal['STAGE_ID'])[0];
        if($stage) {
            $arStageNew = $stage.":".$newStage;
        }else{
            $arStageNew = $newStage;
        }
        return $arStageNew;
    }
    public function stageDealCode(){
        $stage =explode(':', $this->deal['STAGE_ID'])[1];
        if($stage) {
            $arStageCode = $stage;
        }else{
            $arStageCode = $this->deal['STAGE_ID'];
        }
        return $arStageCode;
    }
    public function categoryDealName(){

        $resCategory = $this->auth->CScore->call( "crm.category.list", ['entityTypeId'=>2])['categories'];
        foreach ($resCategory as $kCategory => $vCategory){
            $category[$vCategory['name']]=$vCategory['id'];
        }
        return $category;
    }
    public function categoryDeal(){

        $resCategory = $this->auth->CScore->call( "crm.category.list", ['entityTypeId'=>2])['categories'];
        foreach ($resCategory as $kCategory => $vCategory){
            $category[$vCategory['id']]=$vCategory['name'];
        }
        return $category;
    }
    public function categoryStageDealNameList(){
$category=$this->categoryDeal();
        foreach ($this->auth->batch->getTraversableList('crm.status.list', [], [], ["*"], 6000) as $arStage) {

            if (strpos($arStage['ENTITY_ID'], 'DEAL_STAGE') !== false
                //&& $arStage['CATEGORY_ID'] > -1
                ) // именно через жесткое сравнение
            {
                $stageDeal[$category[$arStage['CATEGORY_ID']]][$arStage['STATUS_ID']] = $arStage['NAME'];
            }
        }
        return $stageDeal;
    }
    public function guideEnumeration($enumeration){
        $fields = $this->auth->CScore->call('crm.deal.userfield.list',['filter'=>['USER_TYPE_ID' => 'enumeration']]);
        foreach ($fields as $kfields){
            foreach ($kfields['LIST'] as $kList){
                $guide[$kfields['FIELD_NAME']][$kList['ID']]=$kList['VALUE'];
            }
        }
        return $guide[$enumeration];
    }

    public function dealComments($comments){

        $params = [
            'UF_CRM_CS_DATE_CHANGE' => strtoTime(date("d.m.YTH:i:s")),
        ];
        $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params]);
        file_put_contents($this->fileLog, print_r($dealUp, true), FILE_APPEND);
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
        file_put_contents($this->fileLog, print_r($dealComment, true), FILE_APPEND);
        return $dealComment;
    }
    public function dealCommentsStage($comments, $stageID){

        file_put_contents($this->fileLog, print_r("\n разница времени " . $dateMin . "\n", true), FILE_APPEND);
        $params = [
            'UF_CRM_CS_DATE_CHANGE' => strtoTime(date("d.m.YTH:i:s")),
            'STAGE_ID' => $stageID
        ];
        $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params]);
        file_put_contents($this->fileLog, print_r($dealUp, true), FILE_APPEND);
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
        file_put_contents($this->fileLog, print_r($dealComment, true), FILE_APPEND);
        return $dealComment;
    }
    public function dealCommentsParams($comments, $params){
        $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params]);
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
        return $dealComment;
    }
    public function dealCommentsParamsId($comments, $params, $id){
        $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $id, 'fields' => $params]);
        $fields = ['fields' => ["ENTITY_ID" => $id, "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
        return $dealComment;
    }
    public function dealCommentsStageControl($comments, $stageID){
        $params = [
            'UF_CRM_CS_DATE_CHANGE' => strtoTime(date("d.m.YTH:i:s")),
            'STAGE_ID' => $stageID,
            'UF_CRM_CS_DATE_CHECK' => date("d.m.YTH:i:s")
        ];
        $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params]);
        file_put_contents($this->fileLog, print_r($dealUp, true), FILE_APPEND);
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
        file_put_contents($this->fileLog, print_r($dealComment, true), FILE_APPEND);
        sleep(2);
        dealStageControl();
        return $dealComment;
    }

    public function dealStageControl(){
        $stage = $this->stageDealCode();
        $guide = $this->dealSmartGuide();
        $smartEx = $guide['Экспедирование']['entityTypeId'];; // 130
        $smartExId = $guide['Экспедирование']['id'];; // 130
        $parentEx = "PARENT_ID_" . $guide['Экспедирование']['entityTypeId'];
        $stageDealName = $this->stageDealNameReal();
        if ($this->deal['STAGE_ID'] === $stageDealName['Контроль пройден'] && $this->deal['UF_CRM_CS_DATE_CHECK'] && $this->deal[$parentEx] > 1) {
            $dealUp = $this->auth->CScore->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => ['STAGE_ID' => $stageDealName['Поиск перевозчика']]]);
        }
        // Поиск перевозчика (заменить признак стадии)
//        if ($stage === $statusDealName['Поиск перевозчика'] && $this->deal['UF_CRM_CS_DATE_CHECK'] && $this->deal[$parentEx] > 1) {
//            $smart = $CSRest->call("crm.item.get", ['entityTypeId' => $smartEx, 'id' => $this->deal[$parentEx]])['result']['item'];
//            $smartStage = $smartProcess->smartStage($smartEx, $smart['categoryId']);
//            file_put_contents($file_log, print_r($smart, true), FILE_APPEND);
//            if ($smart['stageId'] !== $smartStage['Маршрут определен'] && $smart['stageId'] !== $smartStage['Перевозчик найден']) {
//                $stageID = explode(':', $this->deal['STAGE_ID'])[0] . ":" . $statusDealName['Перевозчик найден'];
//                $dealUp = $CSRest->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => ['STAGE_ID' => $stageID]]);
//            }
//
//        }
    }

    public function dealSmartGuide(){
        $params =[ 'select'=>['*'], 'order'=>[], 'filter'=>[], 'start'=> 0 ];
        $resSmart = $this->auth->CScore->call("crm.type.list", $params )['types'];
        foreach ($resSmart as $kSmart){
            $guide[$kSmart['title']]['id'] =$kSmart['id'];
            $guide[$kSmart['title']]['entityTypeId'] =$kSmart['entityTypeId'];
        }
        return $guide;
    }

}