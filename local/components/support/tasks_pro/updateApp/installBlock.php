<?
//require_once (__DIR__.'/brokciBlock.php');
//require_once (__DIR__.'/brokciBlock2.php');
//require_once (__DIR__.'/brokciBlock3.php');
//require_once (__DIR__.'/brokciObject.php');
//require_once (__DIR__.'/brokciBlockObject.php');//фильтр объектов
//require_once($_SERVER["DOCUMENT_ROOT"] . '/local/components/brokci_settings/block_site/installBlock.php');
//require_once ('/home/bitrix/www/pub/test/blockSite/atlas/Blockрheader.php');
//require_once ('/home/bitrix/www/pub/test/blockSite/atlas/BlockGalleryBroker.php');
require_once ('/home/bitrix/www/local/blocks/cassoft-website/21.1.catalog_tr/manifest.php'); //фильтр объектов Турция
require_once ('/home/bitrix/www/local/blocks/cassoft-website/21.2.catalog/manifest.php'); //фильтр объектов Турция
//require_once ('/home/bitrix/www/local/blocks/cassoft-website/04.5.cover_filter_tr_with_text_columns_on_bgimg/manifest.php'); //фильтр объектов Турция на главную страницу с блоками
require_once ('/home/bitrix/www/local/blocks/cassoft-website/04.6.cover_filter_tr/manifest.php'); //фильтр объектов Турция на главную страницу с блоками
require_once ('/home/bitrix/www/local/blocks/cassoft-website/04.7.cover_filter_object/manifest.php'); //фильтр объектов RU на главную страницу с блоками
require_once ('/home/bitrix/www/local/blocks/cassoft-website/12.3.object_carusel_tr/manifest.php'); 
require_once ('/home/bitrix/www/local/blocks/cassoft-website/12.4.object_carusel/manifest.php'); 
//require_once ('/home/bitrix/www/local/blocks/cassoft-website/09.2.form_light_bgimg_left_text/manifest.php'); //кнопка на форму остались вопросы +фото брокера
require_once ('/home/bitrix/www/local/blocks/cassoft-website/20.1.object_detail_tr/manifest.php');
require_once ('/home/bitrix/www/local/blocks/cassoft-website/20.2.object_detail/manifest.php');


$function =[
"blockObjectDetail",// детальная картинка TR
"blockDetailObject",// детальная картинка
"blockObject", //объекты лист
"blockObjectTR", //объекты лист турция
"blockObjectFilterCover", //фильтр на главной TR
"blockFilterCoverObject", //фильтр на главной RU
"blockObjectCarusel", //карусель горячих предложений TR
"blockCaruselObject", //карусель горячих предложений
];

$resBlock =[
	//$accordionCasImg03_2,
//	$coverWithTextButton,
	//$oneHeadingTextLeft,
//	$oneHeadingTextRight,
//	$imgLeftText_06,
//	$imgRightText_06,
	//$four_tiled_text_07,
	//$text_blocks_slider_10,
 // $form_light_bgimg_left_text_09_2,
//	$price_cols_fix_color_08,
	//$text_img_right02_1,
	//$filter_object
//	$filterOb
//$header13_1, //заголовок + бул
//$header13_2,
//$gallery06_6, //карусель брокеров
//$form_light_bgimg_left_text_09_2
	
];
