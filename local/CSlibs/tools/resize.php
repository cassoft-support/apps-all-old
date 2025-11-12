<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

class Resize
{

   
function resizePhoto($file, $fileName){
$get=getimagesize($file);
list($width_orig, $height_orig, $type) = getimagesize($file); // Получаем размеры и тип изображения (число)
$width = 1920;
$height =  ceil($width / ($width_orig / $height_orig));  
if($width_orig>$width){
$types = array(
	"1"=> "gif", 
	"2"=>"jpeg",
	"3"=> "png",
	"18"=> "webp"); // Массив с типами изображений
     $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
     if ($ext) {
       $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
         $image_orig = $func($file); // Создаём дескриптор для работы с исходным изображением
     } else {
       echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
       return false;
     }

$image_p = imagecreatetruecolor($width, $height);
imagecopyresampled($image_p, $image_orig, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
 imagejpeg($image_p, $file, 75);
   }
   $get=getimagesize($file);
   return 'Y';
}
}
?>




}