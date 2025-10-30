
<?

?>
<head>
<link rel="stylesheet" href="/local/lib/css/line-awesome/css/line-awesome.min.css"/>
<link rel="stylesheet" href="/local/components/install/base/css/install.css"/>
</head>

<body>
<div class="cassoft-install-header">
  <input id="reg" value='<?=$arResult['req'] ?>' style="display:none;">
  <input id="cassoftApp" value="brokciPro" style="display:none;">
  <div class="cassoft-install-header-logo">
        <img  src="https://city.brokci.ru/local/images/brokci55x55.png" alt="brokci-logo">
    </div>
    <div>
        <h4 id="title" class="cassoft-install-text">Установка </h4>
    </div>
</div>
<div class="forma-install">
    <div class="">
        <h3>Процесс может занимает значительное время.
    </div>
    </h3>
    <div class="">Дождитесь окончания процесса.</h3>

        <br>
        <br>
        
        <div class="btn_block" style="justify-content: right;">
        <a class="link-button-blue" href='javascript:install(<?= $arResult['req']?>)'></i>Установить</a>
        </div>
<div class="finishInstallBlock" style="display:none;"></div>

<script src="/local/lib/js/jquery-3.6.0.min.js" ></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script src="/local/components/install/base/js/install.js"></script>
<script>
$(document).ready(function() {
                    BX24.init(function() {
                      console.log('Инициализация завершена!', BX24.isAdmin());
                   //   BX24.installFinish();
                    });
                  });

                    </script>