<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$colorScheme = "Dark";
?>

<head>
   <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
  
   <link rel="stylesheet" href="/local/components/brokci/dashboard-deal/templates/.default/menu<?= $colorScheme ?>.css">
   <link href="/local/lib/css/cassoft/cs-root-blue.css" rel="stylesheet">
   <link rel="stylesheet" href="/local/lib/css/cassoft/brokci-panel.css">
   <link rel="stylesheet" href="/local/lib/css/cassoft/brokci-grid.css">
   <link rel="stylesheet" href="/local/lib/chosen/chosen.css"/>
   <link rel="stylesheet" href="/local/lib/css/cassoft/style.css"/>
   <link rel="stylesheet" href="/local/lib/css/cassoft/brokci-filter.css"/>
</head>


<body>

   <div class="main-block">
      <input type="text" id="apps" name="apps" style="display:none"  value='<?php echo $arResult['app'] ?>'></input>
      <input type="text" id="deal_id" name="deal_id" style="display:none"  value='<?php echo $arResult['deal_id'] ?>'></input>
   </div>

   



      <div  class="menu-panel-deal">
                                                 
              <div  class="panel-tools" >
               
               <div class="panel-tools-left" style="margin-left: 20px;">
                  <div style ="margin-right:10px;" class="">ПОДБОР ОБЪЕКТОВ </div>
                  <div ><a  class="click" value="object" ><i  class="fa fa-users"></i> <span >База компании</span></a></div>
                  <div ><a  class="click" value="objectPars"><i  class="fa fa-globe "></i><span >Парсер</span></a></div> 
                  <div ><a  class="click" value="favourites" ><i  class="fa fa-star"></i> <span >Избранные</span></a></div> 
           <!--    <div ><a  class="" id="myObject"><i  class="fa fa-user"></i> <span >Мои объекты</span></a>  </div> -->
           </div>
       </div>
        <!--  
       <div id="panel-close" class="slider-card-panel-close">
                <div class="slider-card-panel-close-icon"><i class="fa fa-times"></i> </div>
                <div class="slider-card-panel-close-label"> <span class="">ЗАКРЫТЬ</span></div>
       </div> 
        
        <div id="help" class="add-selection">
                                    <div class=""><a class=""  ></i>Справка</a></div>
                                    <div class=""><i class="fa fa-question-circle fa-2x"></div>
                                    </div>       -->

   </div>

   <div id='main' class="container-block"></div>
  
   <script src="/local/lib/js/jquery-3.6.0.min.js"></script>
   <script  src="//api.bitrix24.com/api/v1/"></script>
   <script defer src="/local/components/dashboard/main_app/templates/brokci/script.js"></script>
   <!--<script defer src="/local/components/brokci/dashboard-deal/templates/.default/jquery-latest.min.js"></script>-->
  
</body>