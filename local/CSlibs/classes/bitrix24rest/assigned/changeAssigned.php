<?php

namespace CSlibs\B24\Assigned;

class changeAssigned
{
    private $auth;
    private $assigned;
    private $fileLog;
    private $date;


    public function __construct($auth, $assigned)
    {
        p($assigned , "start", __DIR__."/log.txt");
        $this->auth = $auth;
        $this->assigned = $assigned;
        $this->fileLog = __DIR__ . "/logClass.txt";
        $this->date = date("d.m.YTH:i:s");
    }

    public function changeAssignedContact($filter)
    {
        $log = __DIR__ . "/logChangeContact.txt";
        p($params, "start", $log);
        foreach ($this->auth->batch->getTraversableList('crm.contact.list', [], $filter, ["*"], 6000) as $element) {
            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
                $params = [
                    "ID" => $element['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $this->assigned
                    ]
                ];
                $contactUp = $this->auth->CScore->call("crm.contact.update", $params);
            }
        }
    }

    public function changeAssignedDeal($filter)
    {
        foreach ($this->auth->batch->getTraversableList('crm.deal.list', [], $filter, ["*"], 6000) as $element) {
            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
                $params = [
                    "ID" => $element['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $this->assigned
                    ]
                ];
                $dealUp = $this->auth->CScore->call("crm.deal.update", $params);
            }
        }
    }
    public function changeAssignedLead($filter)
    {
        foreach ($this->auth->batch->getTraversableList('crm.lead.list', [], $filter, ["*"], 6000) as $element) {
            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
                $params = [
                    "ID" => $element['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $this->assigned
                    ]
                ];
                $leadUp = $this->auth->CScore->call("crm.lead.update", $params);
            }
        }
    }
public function changeAssignedQuote($filter)
    {
        foreach ($this->auth->batch->getTraversableList('crm.lead.list', [], $filter, ["*"], 6000) as $element) {
            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
                $params = [
                    "ID" => $element['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $this->assigned
                    ]
                ];
                $quoteUp = $this->auth->CScore->call("crm.quote.update", $params);
            }
        }
    }
    public function changeAssigned($filter, $type, $name='')
    {
        $log = __DIR__."/log".$type.".txt";
        $i=0;
        foreach ($this->auth->batch->getTraversableList('crm.'.$type.'.list', [], $filter, ["*"], 6000) as $element) {
            p($element , "start", $log);
            if ($element['ASSIGNED_BY_ID'] !== $this->assigned) {
                $params = [
                    "ID" => $element['ID'],
                    'fields' => [
                        'ASSIGNED_BY_ID' => $this->assigned
                    ]
                ];
                $resUp = $this->auth->CScore->call("crm.".$type.".update", $params);
                p($resUp , "resUp", $log);
                if($resUp[0]>0){
                    $i++;
                }
            }
        }
        return "Изменено ".$type."-".$i;
    }

}

?>