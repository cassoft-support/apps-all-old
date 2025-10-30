<?

function eventBind($app, $eventApp, $CSRest){
  $date=date("d.m.YTH:i");
$file_log = __DIR__."/installEvent.txt";
file_put_contents($file_log, print_r($date."\n",true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
//$app=$reg['app'];
//$CSRest = new  \CSRest($app);
  $urlEvent="https://city.brokci.ru/cassoftApp/support/event/".$app.".php";
  $isEventExist = false;
  $eventGet = $CSRest->call('event.get');
 // file_put_contents($file_log, print_r($eventGet ,true), FILE_APPEND);
  foreach($eventApp as $keyEvent => $valEvent){
  foreach ($eventGet['result'] as $event) {
    if ($event['handler'] === $urlEvent && $event['event'] === $valEvent) {  
        $isEventExist = true;
        file_put_contents($file_log, print_r($isEventExist."\n" ,true), FILE_APPEND);
        file_put_contents($file_log, print_r($valEvent."\n" ,true), FILE_APPEND);
  }
}
if (!$isEventExist) {
  $eventParmas = [
    'event' => $keyEvent,
    'handler' => $urlEvent
  ];
  $addEvent = $CSRest->call('event.bind', $eventParmas);
 file_put_contents($file_log, print_r($addEvent ,true), FILE_APPEND);
}
}
$close="eventInstall+new";
return $close;
}
