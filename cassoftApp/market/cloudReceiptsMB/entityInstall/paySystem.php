<?php
function paySystemArray($nameTypePerson, $idTypePerson, $handler,  $registryType,  $logo){
//обработчик
$paysystem=[
 // 'fields'=>[
    'NAME' => "Modulbank(".$nameTypePerson.")",
    'DESCRIPTION' => "Платежная система Модульбанк",
    'PERSON_TYPE_ID' => $idTypePerson,
    'BX_REST_HANDLER' => $handler,//"cs_modulbank_invoice",
    'ACTIVE' => "Y",
    'ENTITY_REGISTRY_TYPE' => $registryType,//"CRM_INVOICE",
    'LOGOTYPE' =>  $logo,
    'NEW_WINDOW' => "N",
//  ]
];
 /* '1' => [
    'NAME' => "Modulbank(".$idTypePerson.")",
    'DESCRIPTION' => "Платежная система Модульбанк",
    'PERSON_TYPE_ID' => $idTypePerson,
    'BX_REST_HANDLER' => "cs_modulbank_order",
    'ACTIVE' => "Y",
    'ENTITY_REGISTRY_TYPE' => "ORDER",
    'LOGOTYPE' => "",
    'NEW_WINDOW' => "N",
  ],
  ];*/
return $paysystem;
}
?>