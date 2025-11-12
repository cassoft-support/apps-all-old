<?php

namespace Cassoft\Deal;

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

//->getResponseData()->getResult()->getResultData();

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

        $resStage = $this->auth->core->call( "crm.status.list", $paramStatus)->getResponseData()->getResult()->getResultData();
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

        $resStage = $this->auth->core->call( "crm.status.list", $paramStatus)->getResponseData()->getResult()->getResultData();
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

        $resStage = $this->auth->core->call( "crm.status.list", $paramStatus)->getResponseData()->getResult()->getResultData();
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

        $resCategory = $this->auth->core->call( "crm.category.list", ['entityTypeId'=>2])->getResponseData()->getResult()->getResultData()['categories'];
        foreach ($resCategory as $kCategory => $vCategory){
            $category[$vCategory['name']]=$vCategory['id'];
        }
        return $category;
    }
    public function categoryDeal(){

        $resCategory = $this->auth->core->call( "crm.category.list", ['entityTypeId'=>2])->getResponseData()->getResult()->getResultData()['categories'];
        foreach ($resCategory as $kCategory => $vCategory){
            $category[$vCategory['id']]=$vCategory['name'];
        }
        return $category;
    }
    public function guideEnumeration($enumeration){
        $fields = $this->auth->core->call('crm.deal.userfield.list',['filter'=>['USER_TYPE_ID' => 'enumeration']])->getResponseData()->getResult()->getResultData();
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
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealUp, true), );
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealComment, true), );
        return $dealComment;
    }
    public function dealCommentsStage($comments, $stageID){

        file_put_contents($this->fileLog, print_r("\n разница времени " . $dateMin . "\n", true), );
        $params = [
            'UF_CRM_CS_DATE_CHANGE' => strtoTime(date("d.m.YTH:i:s")),
            'STAGE_ID' => $stageID
        ];
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealUp, true), );
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealComment, true), );
        return $dealComment;
    }
    public function dealCommentsParams($comments, $params){
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        return $dealComment;
    }
    public function dealOtherCommentsParams($dealId, $comments, $params){
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $dealId, 'fields' => $params])->getResponseData()->getResult()->getResultData();
        $fields = ['fields' => ["ENTITY_ID" => $dealId, "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        return $dealComment;
    }
    public function dealCommentsStageControl($comments, $stageID){
        $params = [
            'UF_CRM_CS_DATE_CHANGE' => strtoTime(date("d.m.YTH:i:s")),
            'STAGE_ID' => $stageID,
            'UF_CRM_CS_DATE_CHECK' => date("d.m.YTH:i:s")
        ];
        $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => $params])->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealUp, true), );
        $fields = ['fields' => ["ENTITY_ID" => $this->deal['ID'], "ENTITY_TYPE" => "deal", "COMMENT" => $comments]];
        $dealComment = $this->auth->core->call("crm.timeline.comment.add", $fields)->getResponseData()->getResult()->getResultData();
        file_put_contents($this->fileLog, print_r($dealComment, true), );
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
            $dealUp = $this->auth->core->call("crm.deal.update", ['id' => $this->deal['ID'], 'fields' => ['STAGE_ID' => $stageDealName['Поиск перевозчика']]]);
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
        $resSmart = $this->auth->core->call("crm.type.list", $params )->getResponseData()->getResult()->getResultData()['types'];
        foreach ($resSmart as $kSmart){
            $guide[$kSmart['title']]['id'] =$kSmart['id'];
            $guide[$kSmart['title']]['entityTypeId'] =$kSmart['entityTypeId'];
        }
        return $guide;
    }

}