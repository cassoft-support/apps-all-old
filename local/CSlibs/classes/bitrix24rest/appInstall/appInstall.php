<?php
namespace CSlibs\B24\Install;
class appInstall
{
    private $auth;
    private $installApp;

    public function __construct($auth, $app)
    {
        $this->auth = $auth;
        $HlInstallApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $this->installApp = $HlInstallApp->installApp($app);
    }
    public  function placementInstall(){
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logPlacement.txt";
        p("start", "start", $log);

        $add = 0;
        $HlPlacement = new \CSlibs\B24\HL\HlService('install_placement');
        $placementAll=[];
        $placementAll = $HlPlacement->getByIdList($this->installApp['UF_PLACEMENT']);
//---------------- встройки

        foreach ($placementAll as $placement) {
            $placementParams = [
                'PLACEMENT' => $placement['UF_PLACEMENT'],
                'HANDLER' => $this->installApp['UF_HANDLER'] . $placement['UF_FILE'],
                'TITLE' => $this->installApp['UF_NAME'],
                'DESCRIPTION' => $placement['UF_DESC']
            ];
            $addPlacement = $this->auth->CScore->call('placement.bind', $placementParams);
            p($placementParams, "placementParams", $log);
            p($addPlacement, "addPlacement", $log);
            $add++;
        }
        $getPlacement = $this->auth->CScore->call('placement.get');
        p($getPlacement, "getPlacement", $log);
        $addPlacementSmart = $this->placementSmartInstall($this->installApp['UF_SMART']);

        $result['placement'] = 'success';
        $result['placementAdd'] = $add;
        $result['placementSmartAdd'] = $addPlacementSmart;
        return $result;
    }

    public  function placementSmartInstall($smarts) {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logPlacementSmart.txt";
        p("start", "start", $log);
        p($smarts, "smartID", $log);
        $result =[];
//---------------- встройка в смарте

        $HlPlacement = new \CSlibs\B24\HL\HlService('install_placement_smarts');
        $placementAll=[];
        $placementsHl = $HlPlacement->getVocabulary();
        foreach ($placementsHl as $placemenHl){
            $placementAll[$placemenHl['ID']] =$placemenHl;
        }
        $HlSmarts = new \CSlibs\B24\HL\HlService('install_smart');
        $smartAll = $HlSmarts->getByIdList($smarts);
        p($smartAll, "smartAll", $log);

        foreach ($smartAll as $kSmart => $vSmart) {
            if (!empty($vSmart['UF_PLACEMENT'])) {

                $idSmart = $vSmart['UF_ENTITY_TYPE_ID'];
                $add = 0;
                foreach ($vSmart['UF_PLACEMENT'] as $idPlacements) {

                    $placementParamsSmart = [
                        'PLACEMENT' => 'CRM_DYNAMIC_' . $idSmart ."_". $placementAll[$idPlacements]['UF_PLACEMENT'],
                        'HANDLER' => $this->installApp['UF_HANDLER'] . $placementAll[$idPlacements]['UF_FILE'],
                        'TITLE' => $this->installApp['UF_NAME'],
                        'DESCRIPTION' => $placementAll[$idPlacements]['UF_DESC']
                    ];
                    $addPlacementSmart = $this->auth->CScore->call('placement.bind', $placementParamsSmart);
                    p($placementParamsSmart, "placementParamsSmart", $log);
                    p($addPlacementSmart, "addPlacementSmart", $log);
                    $add++;
                }
                $result['smartPlacementAdd-' . $idSmart] = $add;
            }
        }

        $result['smartPlacementAdd'] = 'success';
        return $result;
    }

    public function eventInstall()
    {
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logEvent.txt";
        p("start", "start", $log);

        $add = 0;
        $HlEvent = new \CSlibs\B24\HL\HlService('install_event');
        $eventAll=[];
        $eventAll = $HlEvent->getByIdList($this->installApp['UF_EVENT']);
        p($eventAll, "eventAll", $log);
        $eventGet = $this->auth->CScore->call('event.get');
        $eventGetAllMap =array_column($eventGet, 'handler');
        p($eventGetAllMap, "eventGetAllMap", $log);
        foreach ($eventAll as $keyEvent => $valEvent) {
            p($valEvent, "valEvent", $log);
            $handler=$this->installApp['UF_HANDLER'] . $valEvent['UF_FILE'];
            p($handler, "handler", $log);
            if(!in_array($handler, $eventGetAllMap) || empty($eventGetAllMap)){
            $eventParams = [];
            $eventParams = [
                'event' => $valEvent['UF_EVENT'],
                'handler' => $handler
            ];

                $addEvent = $this->auth->CScore->call('event.bind', $eventParams);
                $add++;
                p($eventParams, "eventParams", $log);
                p($addEvent, "addEvent", $log);

            }
        }

        $result['event'] = "success" ;
        $result['eventAdd'] = $add;
        return $result;

    }




    //close class

    }
