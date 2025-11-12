<?php
/**
 * Created by PhpStorm.
 * User: Deleka
 * Date: 16.02.2019
 * Time: 4:12
 */

class debug
{
  static function console2 ($var, $label = '') {
      ob_start();
      print_r($var);
      $result = json_encode(ob_get_clean());
      echo "<script>console.group('".$label."');console.log('".$result."');console.groupEnd();</script>";
  }
  static function console ($var, $label = '') {
    $str = json_encode(print_r ($var, true));
    echo "<script>console.group('".$label."');console.log('".$str."');console.groupEnd();</script>";
  }
  static function printR ($var, $label = '') {

    if (!empty($label)) {
      echo "<pre>";
      echo $label;
      echo "</pre>";
    }

    echo "<pre>";
    print_r ($var);
    echo "</pre>";
  }
}