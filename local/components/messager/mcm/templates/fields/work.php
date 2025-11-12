<style>
    .block-profile-icon{
        height:20px;
        width:20px;
    }
</style>
<link rel="stylesheet" href="/local/lib/css/new/cs-root.css"/>
<link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
<link rel="stylesheet" href="/local/lib/css/new/flex.css"/>
<div class="form-section block-flex-columns --align-start " style="">
    <div class="--mb50 --dn" style="font-size: 32px">Варианты коммуникации с клиентом</div>
    <?php
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    $log = __DIR__ . "/logWork.txt";
    //p($arParams, "start", $log);

    if ($arParams['app'] && $arParams['member_id']) {
        $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
        $profile = 'c40b7054-9f50';
        if(!empty($arResult['contactAll'])){
            foreach ($arResult['contactAll'] as $fio =>$value){?>
                <div class="block-flex-columns --align-start --w100p">
                    <div class="form-section-header block-flex-columns --m0">
                        <div class="form-section-header-title --mb15" style="font-size: 18px;"><?=$fio?></div>
                        <div class="form-section-header-subtitle " >номера телефонов</div>
                    </div>
                    <div class="cs-form-block  --mb10 --w100p" style="padding: 20px 0 20px 20px;">
                        <?php
                        foreach ($value as $phone){
                            //  pr($phone, '');
                            $regNumber = sendGetWappi('/api/sync/contact/check?profile_id='.$profile.'&phone='.$phone);
                            //    pr($regNumber, '');
                            if($regNumber['on_whatsapp'] == true) {
                                $color = 'green;';
                                $type = 'whatsappGreenAn';
                                $size= ' height:28px; width:28px;';
                            }else {
                                $color = 'gray;';
                                $type = 'whatsapp';
                                $size= 'height:20px; width:20px;';
                            }

                            ?>

                            <div class="block-flex-row --align-center --mb5">
                                <div class="cs-form-block-label --m0"><?= formatPhoneNumber($phone)?></div>
                                <a href="#" >
                                    <svg  class="block-profile-icon --ml15" style="color:<?=$color?> <?=$size?>">
                                        <use xlink:href="/local/images/svg/icon.svg#<?=$type?>"></use>
                                    </svg>
                                </a>
                            </div>

                        <?php   }?>
                    </div>
                </div>
            <?php   }  ?>


            <?php
        }
    }
    ?>
</div>

<script src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script defer src="//api.bitrix24.com/api/v1/"></script>