 <?php
      define("NO_KEEP_STATISTIC", true);
      define("NOT_CHECK_PERMISSIONS", true);
      require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
      $log = __DIR__."/logHandlerDVA.txt";
      $message = file_get_contents("php://input");
      $result=json_decode($message, true);
      p($result, date('c'), $log);
      $fileInfo = pathinfo(basename(__FILE__))["filename"];
      $resName = explode("_", $fileInfo);
       $memberId = $resName[0];
       $ProfileId = $resName[1];

      p($memberId, "memberId", $log);
      if ($memberId) {
          $CloudApp = "mcm";
          $appAccess = "app_" . $CloudApp . "_access";
          $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
          $clientsApp = $HlClientApp->searchID($memberId);
          p($clientsApp, "rest", $log);
          if ($clientsApp["ID"] > 0) {
             $arParams["message"] = $result;
              $arParams["tempList"] = "sendIn";
              $arParams["app"] = $CloudApp;
              $arParams["member_id"] = $memberId;
              $arParams["profile_id"] = $ProfileId;
              $APPLICATION->IncludeComponent(
                  "messager:mcm",
                  "desctop",
                  $arParams,
                  false
              );
          }
      }
          