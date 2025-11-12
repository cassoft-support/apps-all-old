<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/install/base/installEvents.php"); 
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/install/base/setupSave.php"); 

$change_user_eventBind=[
  "onCrmLeadUpdate"=>"ONCRMLEADUPDATE",
  "onCrmDealUpdate"=>"ONCRMDEALUPDATE",
  "onCrmContactUpdate"=>"ONCRMCONTACTUPDATE",
  "onCrmCompanyUpdate"=>"ONCRMCOMPANYUPDATE",
  "onCrmQuoteUpdate"=>"ONCRMQUOTEUPDATE", 
  "onCrmDynamicItemUpdate"=>"ONCRMDYNAMICITEMUPDATE"
];
$arrayInstall=[
  "change_user"=>[
    "eventBind"=>[
      "onCrmLeadUpdate"=>"ONCRMLEADUPDATE",
      "onCrmDealUpdate"=>"ONCRMDEALUPDATE",
      "onCrmContactUpdate"=>"ONCRMCONTACTUPDATE",
      "onCrmCompanyUpdate"=>"ONCRMCOMPANYUPDATE",
      "onCrmQuoteUpdate"=>"ONCRMQUOTEUPDATE", 
      "onCrmDynamicItemUpdate"=>"ONCRMDYNAMICITEMUPDATE"
    ],
    "setupSave"=>[],

  ],
];