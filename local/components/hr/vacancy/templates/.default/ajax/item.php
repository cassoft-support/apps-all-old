<?php
//require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


function itemData($auth, $ID)
{
    $log = __DIR__ . "/logItem.txt";

    p($ID, "start", $log);

    $perams = [
        'ENTITY' => 'vacancy',
        'filter'=>[
            'ID'=>$ID
        ]
    ];
    $item = $auth->CScore->call('entity.item.get', $perams);
   p($item , "item", $log);
//    console($item, 'item');
  $result['item'] = $item[0];
  $guideHl = new \CSlibs\B24\HL\HlService('guide_hr_pro');
  $resGuide = $guideHl->getVocabulary();
  foreach ($resGuide as $elGuide){
      $result['guide'][$elGuide['UF_CATEGORY_CODE']][]= ['key' =>$elGuide['UF_CS_CODE'], 'name' =>$elGuide['UF_NAME']];
  }
 p($result['guide'], "guide", $log);
return $result;
}