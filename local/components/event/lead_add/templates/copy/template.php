<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CJSCore::Init(array("jquery"));

use Bitrix\Main\Page\Asset; 
//Asset::getInstance()->addCss("/local/components/cassoft/newBuildings/templates/.default/style.css"); 
//Asset::getInstance()->addJs("/local/components/cassoft/newBuildings/templates/.default/script.js"); 
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<div class="selectors">
    <p>ЖК в вашей базе</p>

    <select id="rcBase"> 
        <option value="0">(Выбирите значение)</option>
        <?php foreach($arResult['rcBase'] as $key => $rc):?>
            <option value="<?php echo $key?>"><?php echo $rc?></option>
        <?php endforeach?>  
    </select>
    <p id="rcBaseHTMLAvito"></p>
    <p id="rcBaseHTMLYandex"></p>
</div>

<div class="selectors">
<!--<form id="select_city" method="post" class="crm-buy_form">-->

    <p>Авито</p>
    <div class="selectorAvito">
        <select class="select2" id="cityAvito"> 
            <option value="0">(Выбирите значение)</option>
            <?php foreach($arResult['cityAvito'] as $city):?>
                <option value="<?php $city?>"><?php echo $city?></option>
            <?php endforeach?>  
        </select>
        <p id="cityNameHTMLAvito"></p>
    </div>

    <div class="selectorAvito">
        <select id="residentialComplexAvito" disabled> 
            <option value="0">(Выбирите значение)</option>
        </select>
        <p id="rcNameHTMLAvito"></p>
    </div>

    <div class="selectorAvito">
        <select id="buildingsAvito" disabled> 
         <option value="0">(Выбирите значение)</option>
        </select>
        <p id="buildingsHTMLAvito"></p>
    </div>


</div>
<div class="selectors">
    <p>Яндекс</p>
    <div class="selectorYandex">
        <select class="select2" id="cityYandex"> 
            <option value="0">(Выбирите значение)</option>
            <?php foreach($arResult['cityYandex'] as $city):?>
                <option value="<?php $city?>"><?php echo $city?></option>
            <?php endforeach?>  
        </select>
        <p id="cityNameHTMLYandex"></p>
    </div>

    <div class="selectorYandex">
        <select id="residentialComplexYandex" disabled> 
            <option value="0">(Выбирите значение)</option>
        </select>
        <p id="rcNameHTMLYandex"></p>
    </div>

    <div class="selectorYandex">
        <select id="buildingsYandex" disabled> 
            <option value="0">(Выбирите значение)</option>
        </select>
        <p id="buildingsHTMLYandex"></p>
    </div>
</div>

<div class="buttonSaveToBase">
    <button id='saveToBase'>Сохранить</button>
    <p id="feedback"></p>
</div>
