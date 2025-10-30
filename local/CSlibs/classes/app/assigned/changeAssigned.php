<?php

namespace CSlibs\App\Assigned;

class changeAssigned
{
    private $auth;
    private $assigned;
    private $fileLog;
    private $date;
    private $member;

    public function __construct($auth, $assigned, $member='')
    {
        p($assigned , "start", __DIR__."/log.txt");
        $this->auth = $auth;
        $this->assigned = $assigned;
        $this->member = $member;
        $this->fileLog = __DIR__ . "/logClass.txt";
        $this->date = date("d.m.YTH:i:s");

    }

//    public function changeAssignedContact($filter)
//    {
//        $log = __DIR__ . "/logChangeContact.txt";
//        p($params, "start", $log);
//        foreach ($this->auth->batch->getTraversableList('crm.contact.list', [], $filter, ["*"], 6000) as $element) {
//            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
//                $params = [
//                    "ID" => $element['ID'],
//                    'fields' => [
//                        'ASSIGNED_BY_ID' => $this->assigned
//                    ]
//                ];
//                $contactUp = $this->auth->CScore->call("crm.contact.update", $params);
//            }
//        }
//    }
//
//    public function changeAssignedDeal($filter)
//    {
//        foreach ($this->auth->batch->getTraversableList('crm.deal.list', [], $filter, ["*"], 6000) as $element) {
//            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
//                $params = [
//                    "ID" => $element['ID'],
//                    'fields' => [
//                        'ASSIGNED_BY_ID' => $this->assigned
//                    ]
//                ];
//                $dealUp = $this->auth->CScore->call("crm.deal.update", $params);
//            }
//        }
//    }
//    public function changeAssignedLead($filter)
//    {
//        foreach ($this->auth->batch->getTraversableList('crm.lead.list', [], $filter, ["*"], 6000) as $element) {
//            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
//                $params = [
//                    "ID" => $element['ID'],
//                    'fields' => [
//                        'ASSIGNED_BY_ID' => $this->assigned
//                    ]
//                ];
//                $leadUp = $this->auth->CScore->call("crm.lead.update", $params);
//            }
//        }
//    }
//public function changeAssignedQuote($filter)
//    {
//        foreach ($this->auth->batch->getTraversableList('crm.lead.list', [], $filter, ["*"], 6000) as $element) {
//            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
//                $params = [
//                    "ID" => $element['ID'],
//                    'fields' => [
//                        'ASSIGNED_BY_ID' => $this->assigned
//                    ]
//                ];
//                $quoteUp = $this->auth->CScore->call("crm.quote.update", $params);
//            }
//        }
//    }
    public function changeAssigned($filter, $type, $typeId, $user, $name='', $commentAdd='')
    {
        $typeGuide=[
            '1'=>'l',
            '2'=>'d',
            '3'=>'c',
            '4'=>'com',
            '7'=>'q',
            '31'=>'i',
        ];
        $log = __DIR__."/log".$type.".txt";
        $log2 = __DIR__."/log".$type."Com".$this->member.".txt";
        p($type , 'start', $log);
       // p($type , date('d.m.Y H:i:s'), $log);
        p($filter , "filter", $log);
        p($typeId , "typeId", $log);
        p($name , "name", $log);
        p($commentAdd , "commentAdd", $log);
        $i=0;
//        $options = $this->auth->CScore->call('user.option.get');
//        p($options , date('c'), __DIR__."/option.txt");
     //   foreach ($this->auth->batch->getTraversableList('crm.'.$type.'.list', [], $filter, ["*"], 6000) as $element) {
        $resElement = $this->auth->CScore->call('crm.item.list', ['entityTypeId' => $typeId, 'filter'=>$filter]);
        p(count($resElement) , "start", $log2);
        p($resElement , "resElement", $log);
        if(!empty($resElement)){

      //  foreach ($this->auth->CScore->batch('crm.item.list', ['entityTypeId' => $typeId, 'filter'=>$filter])['items'] as $element) {
        foreach ($resElement['items'] as $element) {
            p($element['id'] , "element", $log);
            if ((int)$element['assignedById'] !== (int)$this->assigned) {
                if($element['id'] ==112) {
                    p($element['assignedById'], "assignedById", $log2);
                    p($this->assigned, "assignedById2", $log2);
                }
                $params = [
                  //  "entityTypeId" =>$typeId,
                    "entityTypeId" =>$typeId,
                    "id" => $element['id'],
                    'fields' => [
                        'assignedById' => $this->assigned
                    ]
                ];
              //  $resUp = $this->auth->CScore->call("crm.".$type.".update", $params);
                $resUp = $this->auth->CScore->call("crm.item.update", $params);
                p($params , "params", $log2);
                p($resUp['item']['assignedById'] , "assignedById", $log2);
                if(!empty($resUp['item']['id']) && $resUp['item']['id']>0 && $commentAdd == 1){
                    if($typeId == 31){
                     //   $entityType = "dynamic_" ."T" . dechex($type);
                        $entityType = "dynamic_" .$typeId;
                    }else{
                        $entityType = $type;
                    }
                    p($entityType , "entityType", $log);
                    $comments = "🟢 Ответственный изменен автоматически из ".$name . "[p](".$typeGuide[$typeId]."-".$element['id'].")[/p]";
                    p($comments , "comments", $log2);
                    $fields = ['fields' => ["ENTITY_ID" => $element['id'], "ENTITY_TYPE" => $entityType, "COMMENT" => $comments, "AUTHOR_ID" => $user]];
                    $addComment = $this->auth->CScore->call("crm.timeline.comment.add", $fields);
p($addComment , "addComment", $log);
                    $i++;
                }
            }
        }
        }
        return "Изменено ".$type."-".$i;

    }

}

?>