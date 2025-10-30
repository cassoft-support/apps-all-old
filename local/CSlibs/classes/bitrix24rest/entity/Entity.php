<?php
namespace CSlibs\B24\Entity;

class Entity
{
    private $auth;


    public function __construct($auth)
    {
        $this->auth = $auth;
//        file_put_contents(__DIR__."/logCScore.txt", print_r("core", true));
//        file_put_contents(__DIR__."/logCScore.txt", print_r($core, true), FILE_APPEND);

    }

    public function entityInstall($app)
    {

        $HlPropertyType = new \CSlibs\B24\HL\HlService('product_property_type');
        $propertyType = $HlPropertyType->makeIdToField('UF_CODE');
        $HlPropertyType = new \CSlibs\B24\HL\HlService('app_auth_params');
        $installApp = $HlPropertyType->installApp($app);
        $flog = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$installApp['UF_APP_NAME']."/install/logEntity.txt";
        p("Entity", "start",$flog);
        p($installApp, "installApp");
        // установка хранилищ
        $HlEntities = new \CSlibs\B24\HL\HlService('entity_list');
        $entities = $HlEntities->getByIdList($installApp['UF_ENTITY']);
        $entityHLMapCode = array_column($entities, 'ID', 'UF_CS_ENTITY_CODE');
        p($entityHLMapCode, "entityХайблок",$flog);
        $entityGet = $this->auth->CScore->call('entity.get');

        $entityMapCode = array_column($entityGet, 'ID', 'ENTITY');
        p($entityMapCode, "entityGetПортал",$flog);
        $entityInstall = array_diff_key($entityHLMapCode,$entityMapCode);
        p($entityInstall, "entityInstall",$flog);

      //  p($entities, "entities");
        $enAdd=0;
        $enUp=0;
        $elAdd=0;
        $elUp=0;
        p($entities, "entities",$flog);
        foreach ($entities as $entity) {
            p($entity, "entity",$flog);
            p($entity['UF_CS_ENTITY_CODE'], "entityCode",$flog);
            p($entity['ID'], "entityID",$flog);
            if (in_array($entity['ID'], $entityInstall)) {
                p($entity['UF_CS_ENTITY_CODE'], "entityCode-Создаем хранилище",$flog);
                $entityParams = [
                    'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
                    'NAME' => $entity['UF_CS_ENTITY_NAME'],
                    'ACCESS' => [
                        'AU' => $entity['UF_ACCESS']//R (чтение), W (запись) или X (управление).
                    ]
                ];
                $entityAdd = $this->auth->CScore->call('entity.add', $entityParams);
                $enAdd++;
                $HlEntityProperties = new \CSlibs\B24\HL\HlService($entity['UF_CS_TABLE_NAME']);
                $entityProperties = $HlEntityProperties->hl::getList(['select' => ['*'], 'order' => [], 'filter' => []])->fetchAll();
                $entityProperties = array_chunk($entityProperties, 50);
               // p($entityProperties, "entityProperties",$flog);
                foreach ($entityProperties as $propertyGroup) {
                    $addFields = [];
                    // $updateFields = [];
                    foreach ($propertyGroup as $property) {
                            $propertyParam = [
                                'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
                                'PROPERTY' => $property['UF_CS_PROPERTY'],
                                'NAME' => $property['UF_CS_NAME'],
                                'TYPE' => $propertyType[$property['UF_CS_TYPE']],
                            ];
                            $addFields[] = $propertyParam;
                    }
                    //p($addFields, "addFields",$flog);

                    foreach ($this->auth->batch->addEntityItems('entity.item.property.add', $addFields) as $key => $val) {
                        $elAdd++;
                    }
                }
            } else {

               // $entityGet = $this->auth->CScore->call('entity.item.property.get', ["ENTITY"=>$entity['UF_CS_ENTITY_CODE']]);
               p($entity['UF_CS_ENTITY_CODE'], "entityItamUp",$flog);
                $HlEntityProperties = new \CSlibs\B24\HL\HlService($entity['UF_CS_TABLE_NAME']);
                $entityProperties = $HlEntityProperties->hl::getList(['select' => ['*'], 'order' => [], 'filter' => []])->fetchAll();
                $propertyHLMapCode = array_column($entityProperties, 'UF_CS_PROPERTY');
                // d($propertyHLMapCode);

                $propertyMapCode=[];
               $propCount = $this->auth->CScore->callCount('entity.item.property.get', ['ENTITY'=>$entity['UF_CS_ENTITY_CODE']]);
                p($propCount, "propCount",$flog);
               if($propCount>0) {
                   foreach ($this->auth->batch->getTraversableListEntity('entity.item.property.get', $entity['UF_CS_ENTITY_CODE'], [], [], 6000) as $value) {
                       $propertyMapCode[] = $value['PROPERTY'];
                       // p($value, "value",$flog);
                   }
               }
             //   p($propertyMapCode, "propertyMapCode",$flog);
                $propertyInstall = array_diff($propertyHLMapCode, $propertyMapCode);
            //    p($propertyInstall, "propertyInstall",$flog);
                if (!empty($propertyInstall)) {
                    $entityProperties = array_chunk($entityProperties, 50);
                    foreach ($entityProperties as $propertyGroup) {
                        $addFields = [];
                        // $updateFields = [];
                        foreach ($propertyGroup as $property) {
                            if (in_array($property['UF_CS_PROPERTY'], $propertyInstall)) {

                                $propertyParam = [
                                    'ENTITY' => $entity['UF_CS_ENTITY_CODE'],
                                    'PROPERTY' => $property['UF_CS_PROPERTY'],
                                    'NAME' => $property['UF_CS_NAME'],
                                    'TYPE' => $propertyType[$property['UF_CS_TYPE']],
                                ];
                                $UpFields[] = $propertyParam;
                            }

                        }
                    //    p($addFields, "addFields2",$flog);
                        foreach ($this->auth->batch->addEntityItems('entity.item.property.update', $UpFields) as $key => $val) {
                           // p($val, "valAdd",$flog);
                            $elUp++;
                        }
                    }


                }
//
            }
        }

        $result['entity'] = 'success';
$result['entityAdd'] = $enAdd;
$result['entityPropAdd'] = $elAdd;
$result['entityPropUp'] = $elUp;
//        p($result['entity'], "---------result-------------------------");
//        p(date("d.m.YTH:i:s"), "---------result-------------------------");
        return $result;

    }

}
