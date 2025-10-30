<?php

function quoteComments($CSRest, $quote, $comments){
    $date = date("d.m.YTH:i:s");
    file_put_contents(__DIR__."/function.txt", print_r("\n разница времени " . $date . "\n", true), );
    $params = [
        'UF_CRM_CS_DATE_CHANGE' => strtoTime($date),
    ];
    $quoteUp = $CSRest->call("crm.quote.update", ['id' => $quote['ID'], 'fields' => $params]);
    file_put_contents(__DIR__."/function.txt", print_r($quote['ID'], true) , FILE_APPEND);
    //$fields = ['fields' => ["ENTITY_ID" => $quote['ID'], "ENTITY_TYPE" => "quote", "COMMENT" => $comments]];
    $fields = ['fields' => ["ENTITY_ID" => $quote['ID'], "ENTITY_TYPE" => "quote", "COMMENT" => $comments]];
    $quoteComment = $CSRest->call("crm.timeline.comment.add", $fields)['result'];
    file_put_contents(__DIR__."/function.txt", print_r($quoteComment, true), FILE_APPEND );
    return $quoteComment;
}
