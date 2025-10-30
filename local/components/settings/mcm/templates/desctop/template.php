<?php
$tokenWappi = tokenWappi();
//pr($tokenWappi, '');
//pr($arResult);
//
//pr(strtotime(date('d-m-Y H:i:s')));
//pr(strtotime(date('c')));
//pr(strtotime(date('23.02.2025 ')));
//pr(strtotime(date('23.02.2025 00:00:00')));
?>

<!doctype html>
<head>
    <link rel="stylesheet" href="/local/lib/css/new/cs-root.css">
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="/local/lib/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="/local/lib/css/cassoft/brokci-grid.css" rel="stylesheet">
    <link rel="stylesheet" href="/local/lib/css/cassoft/checkbox.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/forma-elastic.css"/>
    <link rel="stylesheet" href="/local/lib/css/new/flex.css">
</head>
<style>


.cs-messager{
    max-width: 100%;
    /*padding: 0 15px;*/
}
.block-profile-icon{
    height:24px;
    width:24px;
}
.block-profile-name{
    font-size: 14px;
    padding: 5px;
}
.radio-container {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.radio-input {
    display: none;
}

.radio-icon {
    width: 24px;
    height: 24px;
    color: rgba(51, 51, 51, 0.65);
    transition: fill 0.3s;
}


.radio-input:checked + .icon-telegram {
    color: #007bff;
}
.radio-input:checked + .icon-whatsapp {
    color: green;
}

.channels-grid[data-v-58632347] {
    display: flex;
    flex-wrap: wrap;
    margin: 12px 20px;
}
.channel-card[data-v-58632347] {
    background-color: #fff;
    margin: 4px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    vertical-align: middle;
    width: 172px;
    height: 124px;
    cursor: pointer;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.channel-card .type-icon[data-v-58632347] {
    margin-bottom: 12px;
}

 .base-type-icon[data-v-19d4a45e] {
     display: inline;
 }
.v-icon.v-icon {
    align-items: center;
    display: inline-flex;
    font-feature-settings: "liga";
    font-size: 24px;
    justify-content: center;
    letter-spacing: normal;
    line-height: 1;
    position: relative;
    text-indent: 0;
    transition: .3s cubic-bezier(.25,.8,.5,1), visibility 0s;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
<?php
//pr($arResult['profile'], '');
//if (empty($arResult['CS_CIAN_CONNECT'])){
//    $deactive = 'display:none;';
//    $active = '';
//}else{
//    $deactive = '';
//    $active = 'display:none;';
//}

?>
<!--<input style="display: none;" type="text" value="--><?php //= $arResult['app']?><!--" id="app" >-->
<!--<input style="display: none;" type="text" value="--><?php //= $arResult['member_id']?><!--" id="member_id" >-->
<!--<input style="display: none;" type="text" value="--><?php //= $arResult['ID']?><!--" id="ID" >-->
<input style="display: none;" type="text" value='<?= $arResult['options']?>' id="options" >
<div class="filter_conteiner" id="mainApp">
    <div class="form-section"  >
        <div class="form-section-header">
            <div class="form-section-header-title">
                <span class="form-section-header-title-text" style="">Профили MCM</span>
            </div>
            <div class="form-section-header-actions --dn" >
                <span class="form-section-header-edit-lnk" data-target="blockMain">Скрыть</span>
            </div>
        </div>

<div class="block-flex-columns cs-form-block" id="profileAll">
    <?php if(!empty($arResult['profile'])){
        foreach ($arResult['profile'] as $profile){
//pr($profile, '');
            ?>


            <div class="block-profile block-flex-row --p10">
        <div class=" block-flex-row ">
            <svg  class="block-profile-icon" style="color:<?=$profile['style']?>">
                <use xlink:href="/local/images/svg/icon.svg#<?=$profile['type']?>"></use>
            </svg>
            <div class="block-profile-name">Название профиля - </div>
            <div class="block-profile-name_val --ml10"><?= $profile['name']?></div>
        </div>
        <div class=" block-flex-row --ml20">
        <div class="block-profile-date">Дата окончания активности </div>
        <div class="block-profile-date_val --ml10" style="<?=$profile['style-date']?>"><?= $profile['date_close']?></div>
        </div>
        <?php if( $profile['authorized'] === 'N'){?>
        <div class=" block-flex-row --ml20" id="btnBlock">
            <a href="#" class="" data-profile="<?=$profile['profile']?>" id="loadQR">
                <span class="">Получить QR</span>
            </a>
            <a href="" class="--ml20">
                <span class="">Получить Код активации</span>
            </a>
        </div>
            <div class="" id="imageContainer">

            </div>

<?php }?>

    </div>
    <?php }
    }?>
</div>
        <div data-v-58632347="" class="channels-container --dn">
            <div data-v-58632347="" class="channels-grid"><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0025 2H11.9975C6.48375 2 2 6.485 2 12C2 13.8326 2.49482 15.553 3.36025 17.0309L2 22L7.04171 20.6814C8.50526 21.5225 10.1957 22 12.0025 22C17.5162 22 22 17.5137 22 12C22 11.9866 22 11.9732 21.9999 11.9598C22 11.9466 22 11.9333 22 11.92C22 9.27 20.9648 6.78 19.0854 4.91C17.206 3.03 14.7035 2 12.0402 2C12.0339 2 12.0276 2.00001 12.0214 2.00002C12.0151 2.00001 12.0088 2 12.0025 2ZM17.8212 16.1213C17.58 16.8025 16.6225 17.3675 15.8587 17.5325C15.3362 17.6438 14.6537 17.7325 12.3562 16.78C9.63729 15.6536 7.81394 13.013 7.43958 12.4709C7.40932 12.4271 7.38853 12.397 7.3775 12.3825L7.36422 12.3644C7.18088 12.1143 6.19 10.7628 6.19 9.36625C6.19 7.97243 6.87752 7.27321 7.18616 6.95932L7.2125 6.9325C7.45375 6.68625 7.8525 6.57375 8.235 6.57375C8.34761 6.57375 8.44987 6.57893 8.54272 6.58362L8.57 6.585C8.86375 6.5975 9.01125 6.615 9.205 7.07875C9.27718 7.25265 9.38035 7.50298 9.49138 7.77238L9.49195 7.77375C9.75191 8.40449 10.0547 9.13921 10.1037 9.2425C10.175 9.39 10.2462 9.59 10.1462 9.78375C10.0583 9.97132 9.9803 10.061 9.84926 10.2117L9.8225 10.2425C9.7608 10.3136 9.70041 10.3777 9.64023 10.4416L9.6402 10.4416C9.55654 10.5305 9.47329 10.6189 9.3875 10.725L9.38012 10.7337C9.24706 10.89 9.10309 11.0591 9.27 11.3475C9.44 11.635 10.0275 12.5937 10.8925 13.3637C11.9181 14.2768 12.7653 14.6189 13.1473 14.7732C13.1811 14.7869 13.2112 14.799 13.2375 14.81C13.4787 14.91 13.7662 14.8862 13.9425 14.6988C14.136 14.4902 14.3687 14.1629 14.61 13.8235L14.61 13.8235L14.7237 13.6638C14.9237 13.3813 15.1762 13.3462 15.4412 13.4462C15.7112 13.54 17.14 14.2463 17.4337 14.3925C17.4944 14.423 17.5508 14.4501 17.6027 14.4751L17.6028 14.4751C17.8023 14.5712 17.936 14.6356 17.9925 14.7338C18.0625 14.8575 18.0625 15.4387 17.8212 16.1213Z" fill="url(#paint0_linear_4777_270943)"></path> <defs><linearGradient id="paint0_linear_4777_270943" x1="12" y1="2" x2="12" y2="22" gradientUnits="userSpaceOnUse"><stop stop-color="#5ED169"></stop> <stop offset="1" stop-color="#29B640"></stop></linearGradient></defs></svg></span></div> <span data-v-58632347="" class="text-center">
            WhatsApp
          </span></div><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 17.7383 21.3889 19.2135 20.3012 20.3012C19.2135 21.3889 17.7383 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 6.26174 2.61107 4.78649 3.69878 3.69878C4.78649 2.61107 6.26174 2 7.8 2ZM7.6 4C6.64522 4 5.72955 4.37928 5.05442 5.05442C4.37928 5.72955 4 6.64522 4 7.6V16.4C4 18.39 5.61 20 7.6 20H16.4C17.3548 20 18.2705 19.6207 18.9456 18.9456C19.6207 18.2705 20 17.3548 20 16.4V7.6C20 5.61 18.39 4 16.4 4H7.6ZM17.25 5.5C17.5815 5.5 17.8995 5.6317 18.1339 5.86612C18.3683 6.10054 18.5 6.41848 18.5 6.75C18.5 7.08152 18.3683 7.39946 18.1339 7.63388C17.8995 7.8683 17.5815 8 17.25 8C16.9185 8 16.6005 7.8683 16.3661 7.63388C16.1317 7.39946 16 7.08152 16 6.75C16 6.41848 16.1317 6.10054 16.3661 5.86612C16.6005 5.6317 16.9185 5.5 17.25 5.5ZM12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7ZM12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9Z" fill="url(#paint0_linear_4828_270630)"></path> <defs><linearGradient id="paint0_linear_4828_270630" x1="3.5" y1="20.5" x2="21" y2="3" gradientUnits="userSpaceOnUse"><stop stop-color="#FEC51E"></stop> <stop offset="0.305208" stop-color="#F8450A"></stop> <stop offset="0.466667" stop-color="#F72205"></stop> <stop offset="1" stop-color="#BF00A2"></stop></linearGradient></defs></svg></span></div> <span data-v-58632347="" class="text-center">
            Instagram
          </span></div><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12.5783 9.36244C11.6057 9.767 9.66177 10.6043 6.74657 11.8744C6.27318 12.0627 6.02521 12.2469 6.00263 12.4269C5.96448 12.7313 6.34558 12.8511 6.86455 13.0143C6.93514 13.0365 7.00829 13.0595 7.08327 13.0838C7.59385 13.2498 8.28068 13.444 8.63773 13.4517C8.96161 13.4587 9.3231 13.3252 9.72219 13.0511C12.446 11.2125 13.852 10.2832 13.9402 10.2631C14.0025 10.249 14.0888 10.2312 14.1473 10.2832C14.2058 10.3352 14.2 10.4336 14.1938 10.46C14.1561 10.621 12.6601 12.0117 11.8859 12.7315C11.6446 12.9559 11.4734 13.115 11.4384 13.1514C11.36 13.2328 11.2801 13.3098 11.2033 13.3838C10.729 13.8411 10.3732 14.184 11.223 14.744C11.6314 15.0131 11.9581 15.2356 12.2841 15.4576C12.6401 15.7001 12.9952 15.9419 13.4547 16.2431C13.5717 16.3198 13.6835 16.3995 13.7924 16.4771C14.2067 16.7725 14.5789 17.0379 15.0388 16.9955C15.306 16.9709 15.582 16.7197 15.7222 15.9703C16.0535 14.1993 16.7047 10.362 16.8552 8.78081C16.8684 8.64228 16.8518 8.46498 16.8384 8.38715C16.8251 8.30932 16.7973 8.19842 16.6961 8.11633C16.5763 8.01911 16.3913 7.99861 16.3086 8.00007C15.9325 8.0067 15.3554 8.20735 12.5783 9.36244Z" fill="url(#paint0_linear_4777_271710)"></path> <defs><linearGradient id="paint0_linear_4777_271710" x1="12" y1="2" x2="12" y2="21.8517" gradientUnits="userSpaceOnUse"><stop stop-color="#2AABEE"></stop> <stop offset="1" stop-color="#229ED9"></stop></linearGradient></defs></svg></span></div> <span data-v-58632347="" class="text-center">
            Telegram
          </span></div><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.40589 3.40589C2 4.81177 2 7.0745 2 11.6V12.4C2 16.9255 2 19.1882 3.40589 20.5941C4.81177 22 7.0745 22 11.6 22H12.4C16.9255 22 19.1882 22 20.5941 20.5941C22 19.1882 22 16.9255 22 12.4V11.6C22 7.0745 22 4.81177 20.5941 3.40589C19.1882 2 16.9255 2 12.4 2H11.6C7.0745 2 4.81177 2 3.40589 3.40589ZM5.37504 8.08337C5.48337 13.2834 8.08333 16.4084 12.6417 16.4084H12.9001V13.4334C14.5751 13.6 15.8416 14.825 16.35 16.4084H18.7167C18.0667 14.0417 16.3583 12.7334 15.2916 12.2334C16.3583 11.6167 17.8583 10.1167 18.2166 8.08337H16.0666C15.5999 9.73337 14.2167 11.2334 12.9001 11.375V8.08337H10.75V13.85C9.41667 13.5167 7.73337 11.9 7.65837 8.08337H5.37504Z" fill="#0077FF"></path></svg></span></div> <span data-v-58632347="" class="text-center">
            Вконтакте
          </span></div><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path d="M7.58959 22C11.2289 22 14.1792 19.1028 14.1792 15.5289C14.1792 11.9551 11.2289 9.0579 7.58959 9.0579C3.95026 9.0579 1 11.9551 1 15.5289C1 19.1028 3.95026 22 7.58959 22Z" fill="#04E061"></path> <path d="M19.0187 21.1843C21.2175 21.1843 23 19.4339 23 17.2747C23 15.1154 21.2175 13.3649 19.0187 13.3649C16.8199 13.3649 15.0373 15.1154 15.0373 17.2747C15.0373 19.4339 16.8199 21.1843 19.0187 21.1843Z" fill="#FF4053"></path> <path d="M8.84929 8.2183C10.2102 8.2183 11.3135 7.13489 11.3135 5.79843C11.3135 4.46197 10.2102 3.37855 8.84929 3.37855C7.48836 3.37855 6.3851 4.46197 6.3851 5.79843C6.3851 7.13489 7.48836 8.2183 8.84929 8.2183Z" fill="#965EEB"></path> <path d="M17.4709 12.5222C20.4297 12.5222 22.8284 10.1668 22.8284 7.26112C22.8284 4.35548 20.4297 2 17.4709 2C14.512 2 12.1134 4.35548 12.1134 7.26112C12.1134 10.1668 14.512 12.5222 17.4709 12.5222Z" fill="#00AAFF"></path></svg></span></div> <span data-v-58632347="" class="text-center">
            Авито
          </span></div><div data-v-58632347="" class="channel-card"><div data-v-19d4a45e="" data-v-58632347="" class="base-type-icon type-icon"><span data-v-19d4a45e="" aria-hidden="true" class="v-icon notranslate theme--light" style="font-size: 40px; height: 40px; width: 40px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="v-icon__component theme--light" style="font-size: 40px; height: 40px; width: 40px;"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.8184 2.90432C19.2969 2.43633 17.0458 1.03934 12.3487 1.01911C12.3487 1.01911 6.78673 0.658182 4.08675 3.07356C2.58403 4.53897 2.07778 6.71815 2.02228 9.37745C2.02043 9.46582 2.01833 9.55687 2.01617 9.65036C1.95341 12.3703 1.84285 17.1619 6.6042 18.4683C6.6042 18.4683 6.58483 22.2398 6.58259 22.5713C6.58259 22.8031 6.61984 22.9617 6.75395 22.9944C6.85043 23.0176 6.99496 22.9683 7.11789 22.8473C7.90539 22.0662 10.4266 19.0621 10.4266 19.0621C13.8094 19.2803 16.5008 18.6206 16.7902 18.5272C16.8584 18.5055 16.9565 18.4829 17.0786 18.4548C18.1792 18.2014 21.2263 17.4997 21.7886 13.0136C22.4342 7.87635 21.5513 4.36973 19.8184 2.90432ZM11.5162 3.96692C11.5174 3.79563 11.659 3.65774 11.8324 3.65894C13.6548 3.67147 15.1952 4.27069 16.432 5.46149C17.6792 6.66225 18.2959 8.29698 18.3116 10.3273C18.3129 10.4986 18.1734 10.6385 17.9999 10.6398C17.8265 10.6411 17.6848 10.5033 17.6835 10.332C17.6689 8.42951 17.0958 6.96686 15.9936 5.90567C14.8811 4.83452 13.4998 4.29073 11.828 4.27923C11.6546 4.27804 11.515 4.13821 11.5162 3.96692ZM12.0706 5.55535C12.0832 5.38451 12.2337 5.25615 12.4067 5.26866C13.7254 5.36398 14.7869 5.80901 15.56 6.64257L15.5601 6.64269C16.3249 7.46805 16.6884 8.4948 16.662 9.69911C16.6582 9.87037 16.5146 10.0062 16.3413 10.0025C16.1679 9.99878 16.0303 9.85694 16.0341 9.68569C16.0574 8.62364 15.7411 7.75684 15.0968 7.06147C14.4544 6.36899 13.5565 5.97374 12.3608 5.88731C12.1879 5.87481 12.0579 5.72618 12.0706 5.55535ZM12.5466 7.22628C12.5556 7.05522 12.7034 6.92378 12.8766 6.93271C13.5246 6.96609 14.0637 7.15982 14.4476 7.55193C14.8302 7.94253 15.0177 8.4882 15.0506 9.14355C15.0592 9.31464 14.9257 9.46021 14.7525 9.46869C14.5793 9.47718 14.4319 9.34537 14.4233 9.17429C14.3951 8.6133 14.2393 8.23139 13.9962 7.98317C13.7546 7.73645 13.3859 7.5801 12.8438 7.55217C12.6706 7.54325 12.5376 7.39734 12.5466 7.22628ZM13.6015 13.0607C13.3638 13.3499 12.9224 13.3131 12.9224 13.3131C9.69606 12.4996 8.83257 9.27188 8.83257 9.27188C8.83257 9.27188 8.79383 8.8359 9.08812 8.60117L9.6711 8.14348C9.96017 7.92347 10.1446 7.38962 9.85028 6.86828C9.62456 6.47695 9.37914 6.09703 9.11494 5.72994C8.85828 5.38373 8.26039 4.67476 8.25816 4.67255C7.96983 4.33664 7.54591 4.25864 7.09889 4.48859C7.09754 4.48859 7.09568 4.48919 7.0939 4.48976C7.09224 4.49029 7.09065 4.4908 7.08958 4.4908C6.64498 4.7438 6.24408 5.06523 5.90201 5.44297C5.90084 5.44527 5.89967 5.44637 5.89851 5.44747C5.89744 5.44848 5.89637 5.44949 5.8953 5.45143C5.61914 5.78059 5.46107 6.10326 5.42109 6.41942C5.41279 6.46604 5.40991 6.51344 5.41252 6.5607C5.41104 6.70031 5.43255 6.83924 5.47622 6.97203L5.49149 6.98233C5.63081 7.47093 5.97949 8.28476 6.73718 9.64164C7.17057 10.4265 7.6704 11.1737 8.23133 11.8753C8.51237 12.227 8.81361 12.5624 9.13356 12.88C9.13742 12.8839 9.14124 12.8877 9.14505 12.8915C9.15263 12.899 9.16015 12.9065 9.16783 12.9139L9.20211 12.9477L9.23638 12.9816L9.27065 13.0154C9.59222 13.3314 9.93189 13.629 10.288 13.9065C10.9984 14.4606 11.7551 14.9543 12.5499 15.3823C13.9233 16.1306 14.7481 16.475 15.242 16.6126L15.2525 16.6277C15.3869 16.6709 15.5276 16.6923 15.6689 16.6909C15.7168 16.6932 15.7648 16.6903 15.812 16.6821C16.1326 16.6446 16.459 16.4887 16.7913 16.2145C16.7954 16.2123 16.7954 16.2101 16.7999 16.2079C17.1824 15.8699 17.5079 15.474 17.7643 15.0349C17.7643 15.0327 17.7662 15.0283 17.7662 15.0257C17.999 14.5842 17.92 14.1656 17.5781 13.8804C17.5776 13.8804 17.5497 13.8577 17.5025 13.8193C17.3087 13.6615 16.7893 13.2389 16.5075 13.0342C16.1362 12.7733 15.7519 12.5309 15.356 12.3079C14.8278 12.0173 14.2884 12.1994 14.0649 12.4849L13.6015 13.0607Z" fill="#7360F2"></path></svg></span></div> <span data-v-58632347="" class="text-center">
            Viber
          </span></div></div></div>
        <div class="block-flex-row --mt50" style="">
        <div class="block-flex-row " style="">

            <label class="radio-container --mr15">
                <input type="radio" id="whatsapp" name="customRadio" class="radio-input">
                <svg class="radio-icon icon-whatsapp" >
                    <use xlink:href="/local/images/svg/icon.svg#whatsapp"></use>
                    </svg>
            </label>

            <label class="radio-container --mr15">
                <input type="radio" id="telegram" name="customRadio" class="radio-input">
                <svg class="radio-icon icon-telegram" >
                    <use xlink:href="/local/images/svg/icon.svg#telegram"></use>
                    </svg>
            </label>
            <div class="cs-input-container cs-messager --mb0" >
                    <input class="cs-input-block__text cs-input-block cs-messager profile-en" type="text" placeholder=" " value="" id="profileName" >
                    <label for="profileName" class="cs-input-label">Введите наименование линии<span class="warning">*</span></label>
                </div>
        </div>


        <div class="block-flex-row --justifu-end --mb20 ">
            <a class="form-small-button form-small-button-blue" style="height: auto;" href='javascript:addProfile()'><span>Создать новый профиль</span> </a>
        </div>
        </div>
        <!--</form>-->
    </div>

    </div>



<script  src="/local/lib/js/jquery-3.6.0.min.js"></script>
<script  src="//api.bitrix24.com/api/v1/"></script>
<script type="text/javascript" src="/local/lib/bootstrap/bootstrap.js"></script>
<script src="/local/lib/chosen/chosen.jquery.js"></script>
<script defer type="text/javascript" src="/local/components/settings/mcm/templates/desctop/script.js"></script>
<script>
$(document).ready(function() {
$('#loadQR').on('click', function(event) {
event.preventDefault(); // Предотвращаем переход по ссылке
    var profileId = $(this).data('profile');
var settings = {
"url": "https://wappi.pro/api/sync/qr/get?profile_id="+profileId,
"method": "GET",
"timeout": 0,
"headers": {
"Authorization": "<?=$tokenWappi?>"
},
};

$.ajax(settings).done(function(response) {
if (response.status === "done") {
// Создаем элемент <img> с данными из qrCode
var img = $('<img>').attr('src', response.qrCode);

// Вставляем изображение в контейнер
$('#imageContainer').html(img);
} else {
console.log("Ошибка: статус не 'done'");
}
}).fail(function(jqXHR, textStatus, errorThrown) {
console.log("Ошибка запроса: " + textStatus, errorThrown);
});
});
});
</script>