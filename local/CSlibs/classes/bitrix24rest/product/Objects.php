<?php
namespace CSlibs\B24\Product;

class Objects
{
    private $auth;


    public function __construct($auth)
    {
        $this->auth = $auth;

    }

    public function objectInstall()
    {
        $flog = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/logObject.txt";
        p("object", "start",$flog);
        $update=0;
        $add=0;
        $propertyListCatalog = $this->auth->CScore->call('crm.product.property.list');
        $propertyMapXml = array_column($propertyListCatalog, 'ID', 'XML_ID');
        $propertyMapCode = array_column($propertyListCatalog, 'ID', 'CODE');
       // p($propertyMapCode, "propertyMapCode",$flog);
        $propertyListCatalogId = [];
        foreach ($propertyListCatalog as $propertyCatalog) {
            $propertyListCatalogId[$propertyCatalog['ID']] = $propertyCatalog;
        }
        $HlPropertyList = new \CSlibs\B24\HL\HlService('product_property_list');
        $propertyList = $HlPropertyList->getByFilterList(['UF_CATALOG' => '1']);
      //  p($propertyList, "propertyList");
        $propertyList = array_chunk($propertyList, 50);
        foreach ($propertyList as $propertyGroup) {
            $addFields = [];
            $updateFields = [];
            foreach ($propertyGroup as $property) {
                $fieldId = false;
                $PropertyCatalog = new \CSlibs\B24\Product\PropertyCatalog($property);
              //  p($PropertyCatalog->fields, "PropertyCatalog",$flog);
                $fieldId = array_key_exists($PropertyCatalog->code, $propertyMapCode);

                if ($fieldId !== false) {
                    $fieldId = $propertyMapCode[$PropertyCatalog->code];
                    if ($PropertyCatalog->type === 'L') {
                        $PropertyCatalog->combineValues($propertyListCatalogId[$fieldId]['VALUES']);
                    }
                    $updateFields[] = [
                        'ID' => $fieldId,
                        'FIELDS' => $PropertyCatalog->fields
                    ];
                } else {
                    $addFields[] = [
                        'FIELDS' => $PropertyCatalog->fields
                    ];
                }
            }
          //  p($updateFields, "upFields",$flog);
            foreach ($this->auth->batch->addEntityItems('crm.product.property.update', $updateFields) as $key => $val) {
                $update++;
            }
            foreach ($this->auth->batch->addEntityItems('crm.product.property.add', $addFields) as $key => $val) {
                $add++;
            }
            p($update, "update");
        }
        $result=[];
        $result['object'] = 'success';
        $result['update'] = $update;
        $result['add'] = $add;
        p($result, "result",$flog);
       // echo json_encode($result);
        return $result;
    }

    public function objectPropertylist()
    {
        foreach ($this->auth->batch->getTraversableList('crm.product.property.list', [], [], [], 6000) as $arProperty) {

//---новый справочник
            $arGuideProperty['id_code']["PROPERTY_" . $arProperty["ID"]] = $arProperty["CODE"];
            $arGuideProperty[$arProperty["CODE"]]['NAME'] = $arProperty["NAME"];
            $arGuideProperty[$arProperty["CODE"]]['ID'] = "PROPERTY_" . $arProperty["ID"];
            $arGuidePropertyCode[$arProperty["CODE"]] = "PROPERTY_" . $arProperty["ID"];
            if ($arProperty["CODE"] === "CS_OPERATION") {
                $operation = "PROPERTY_" . $arProperty["ID"];
                foreach ($arProperty['VALUES'] as $key => $value) {
                    $operQ[$value['XML_ID']] = $key;
                }
            }
            if ($arProperty["CODE"] === "CS_OBJECT_TYPE") {
                $objectType = "PROPERTY_" . $arProperty["ID"];
                foreach ($arProperty['VALUES'] as $key => $value) {
                    $objectQ[$value['XML_ID']] = $key;
                }
            }
            if ($arProperty["PROPERTY_TYPE"] === 'L') {
                if ($arProperty["MULTIPLE"] === "Y") {
                    $arGuideProperty[$arProperty["CODE"]]['TYPE'] = "M";
                } else {
                    $arGuideProperty[$arProperty["CODE"]]['TYPE'] = "L";
                }
                foreach ($arProperty["VALUES"] as $value) {
                    $arGuideProperty[$arProperty["CODE"]]["GUIDE"][$value['ID']] = $value['VALUE'];
                    $arGuideProperty[$arProperty["CODE"]]["CODES"][$value['ID']] = $value['XML_ID'];
                    if ($arProperty["CODE"] === "CS_STUDIO") {
                        $arGuideProperty[$arProperty["CODE"]]["GUIDErev"][$value['VALUE']] = $value['ID'];
                    }
                }
            } else {
                $arGuideProperty[$arProperty["CODE"]]['TYPE'] = "S";
            }
        }
        return $arGuideProperty;
    }
//class close
}
