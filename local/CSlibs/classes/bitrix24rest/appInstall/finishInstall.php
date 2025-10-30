<?php
namespace CSlibs\B24\Install;
class finishInstall
{
    private $auth;
    private $installApp;

    public function __construct($auth, $app)
    {
        $this->auth = $auth;
        $HlInstallApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $this->installApp = $HlInstallApp->installApp($app);
    }
    public  function adminInstall($clientApp){
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logAdmin.txt";
        p("start", "start", $log);
        p($clientApp, "clientApp", $log);
        $HlUserParams = new \CSlibs\B24\HL\HlService('client_app_cassoft');
        $clientData =$HlUserParams->getByFilterList(['UF_CS_CLIENT_PORTAL_MEMBER_ID' => $clientApp['member_id']])[0];
       p($clientData, "clientData", $log);
        $userParams = $this->auth->CScore->call('user.current');
       p($userParams, "admin", $log);
        $userData = [
            'UF_CS_CLIENT_FIO' => $userParams['LAST_NAME']." ".$userParams['NAME']." ".$userParams['SECOND_NAME'],
            'UF_CS_CLIENT_PHOTO' => $userParams['PERSONAL_PHOTO'],
            'UF_CS_CLIENT_TEL' => $userParams['WORK_PHONE'],
            'UF_CS_CLIENT_TEL2' =>$userParams['PERSONAL_PHONE'],
            'UF_CS_CLIENT_MAIL' => $userParams['EMAIL'],
            'UF_DATA_UP' => date('d.m.YTH:i:s'),
        ];
        if($clientData){
                            $installApp=[];
                if($clientData['UF_INSTALL_APP']){
                    $installApp =  $clientData['UF_INSTALL_APP'];
                    if (!in_array($this->installApp['ID'], $installApp)) {
                        array_push($installApp, $this->installApp['ID']);
                    }
                }else{
                    $installApp[] =$this->installApp['ID'];
                }
                            $userData['UF_INSTALL_APP'] = $installApp;
                            p($userData, "userData", $log);
                            $upClient = $HlUserParams->hl::update($clientData['ID'], $userData);

        }else{
            $installApp[] =$this->installApp['ID'];
            $userData['UF_CS_CLIENT_PORTAL_DOMEN']= $clientApp['DOMAIN'];
            $userData['UF_CS_CLIENT_PORTAL_MEMBER_ID'] = $clientApp['member_id'];
            $userData['UF_INSTALL_APP'] = $installApp;
            $addClient = $HlUserParams->hl::add($userData);
            $res= (array)$addClient;
            p($res, "res", $log);

        }
        $clientApp['app']=$this->installApp['UF_APP_NAME'];
     $authInstall= $this->authInstall($clientApp);
     $adminSetupInstall= $this->adminSetupInstall($userParams);
        $result['admin'] ='success';
return $result;
    }

    public  function authInstall($clientApp){
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logAuth.txt";
        $folder = crypt($clientApp['member_id'], 'CASsoft');
        p("start", "start", $log);
        //caVXZwUQKIdc2

        p($folder, "folder", $log);
        //$params=json_encode($clientApp);
        $params=http_build_query($clientApp);
        p($params, "clientApp", $log);
        if($this->installApp['UF_DOMAIN']){
            $keyHook = $this->installApp['UF_DOMAIN']."/local/ajax/appAuth.php?".$params;
        }else{
            $keyHook = $this->installApp['UF_HANDLER']."/appAuth.php?".$params;
        }


        $ch = curl_init($keyHook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
//curl_setopt($ch, CURLOPT_POST, true);
        $result = curl_exec($ch);
        p($result, "result", $log);
        curl_close($ch);
        $arResult=json_decode($result, true);
        p($arResult, "arResult", $log);


        $result['auth'] ='success';
        return $result;
    }

    public  function adminSetupInstall($userParams){
        $log = "/var/www/www-root/data/www/brokci.cassoft.ru/local/a_logs/".$this->installApp['UF_APP_NAME']."/install/logAdminSetup.txt";
        p("start", "start", $log);
        p($userParams, "userParams", $log);
        $resEntity = $this->auth->CScore->call('entity.get', ['ENTITY' => 'setup']);
        p($resEntity, "resEntity", $log);
if($resEntity){
    $resItem = $this->auth->CScore->call('entity.item.get', ['ENTITY' => 'setup'])[0];
    if(empty($resItem)){
        $params=[
            'ENTITY' => 'setup',
            'NAME' => 'setupOne',
            'PROPERTY_VALUES' => [
                'UF_CS_ADMIN' => json_encode([$userParams['ID']])
            ]
        ];
        $resItemAdd = $this->auth->CScore->call('entity.item.add', $params);
        p($resItemAdd, "resItemAdd", $log);
    }
}
        $result['admin'] ='success';

        return $result;
    }

    //close class
}
