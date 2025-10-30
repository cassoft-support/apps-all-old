<?php 

      function randomColor() {
        $str = '#';
        for ($i = 0; $i < 6; $i++) {
            $randNum = rand(0, 15);
            switch ($randNum) {
                case 10: $randNum = 'A';
                    break;
                case 11: $randNum = 'B';
                    break;
                case 12: $randNum = 'C';
                    break;
                case 13: $randNum = 'D';
                    break;
                case 14: $randNum = 'E';
                    break;
                case 15: $randNum = 'F';
                    break;
            }
            $str .= $randNum;
        }
        return $str;}
        
      function array_multisort_value()
      {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
          if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row) {
              $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
          }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
      }
function firstDayOf($period, DateTime $date = null)
{
$period = strtolower($period);
$validPeriods = array('year', 'quarter', 'month', 'week');

if ( ! in_array($period, $validPeriods))
throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

$newDate = ($date === null) ? new DateTime() : clone $date;

switch ($period) {
case 'year':
$newDate->modify('first day of january ' . $newDate->format('Y'));
break;
case 'quarter':
$month = $newDate->format('n') ;

if ($month < 4) {
$newDate->modify('first day of january ' . $newDate->format('Y'));
} elseif ($month > 3 && $month < 7) {
$newDate->modify('first day of april ' . $newDate->format('Y'));
} elseif ($month > 6 && $month < 10) {
$newDate->modify('first day of july ' . $newDate->format('Y'));
} elseif ($month > 9) {
$newDate->modify('first day of october ' . $newDate->format('Y'));
}
break;
case 'month':
$newDate->modify('first day of this month');
break;
case 'week':
$newDate->modify(($newDate->format('w') === '0') ? 'monday last week' : 'monday this week');
break;
}

return $newDate;
}

function rating($rating, $params, $UserID, $Users, $domain){
  foreach( $rating as $key => $value){
    $plan = number_format(($value['profit']/$value['profit_plan']*100),  2, '.', ' ');
    $reitingPlan[$plan][]=$key;
    }
    krsort($reitingPlan);
    $i=1;
    $m=1;
        foreach( $reitingPlan as $key => $value){
          if($key>0){
           $Proc = $key;
           $keyUsers = array_search($UserID, $value);
          // d($keyUsersM);
         if(($keyUsers === 0 || $keyUsers>0) && $i>3 ){
        $arResult['ratingUser'.$params] =$i;
          if ($Proc < 100) {
           $arResult['Proc'.$params.'4'] = $Proc . "%";
         } else {
           $arResult['Proc'.$params.'4'] = "100%";
         }
         $arResult['ProcL'.$params.'4' ]['%'] = $Proc . "%";
         if ($Proc < 85) {
        //   $margin = 'style="margin-left: 0px!important;"';
           $arResult['ProcL'.$params.'4' ]['style'] = 'style="margin-left: 0px!important;"';
         }
       }
            if($m<4)
            {
              if ($Proc < 100) {
                $arResult['Proc'.$params . $m] = $Proc . "%";
              } else {
                $arResult['Proc'.$params . $m] = "100%";
              }
              $arResult['ProcL'.$params . $m]['%'] = $Proc . "%";
              if ($Proc < 85) {
                $arResult['ProcL'.$params. $m ]['style'] = 'style="margin-left: 0px!important;"';
              }
              
              foreach( $value as  $valueUsers){
              $arResult[$params. $m][$valueUsers]['UserLinc']=$Users[$valueUsers]['LINK'];
              $arResult[$params. $m][$valueUsers]['UserPhoto']=$Users[$valueUsers]['PHOTO'];
              $arResult[$params. $m][$valueUsers]['User']=$Users[$valueUsers]['FIO'];
          }                                                      
          $m++;                                                                 
          }
          $i++;
          }
        }
          if(!$arResult['ratingUser'.$params] >0){
           $arResult['ratingUserN'.$params] = "Не попал в рейтинг";
       
        }
         return $arResult;
      }
/*
      function number_of_working_days($month) {
        $from = date('Y-'.$month. '-01');
        $d = new DateTime($from);
        $to = $d - > format('Y-m-t');
    
        $workingDays = [1, 2, 3, 4, 5];#
        date format = N(1 = Monday, ...)
    
        $from = new DateTime($from);
        $to = new DateTime($to);
        $to - > modify('+1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);
    
        $days = 0;
        foreach($periods as $period) {
            if (!in_array($period - > format('N'), $workingDays)) continue;
            $days++;
        }
        return $days;
    }
    
function array_multisort_value() {
    $args = func_get_args();
    $data = array_shift($args);
    foreach($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }
    $args[] = & $data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

    */
?>