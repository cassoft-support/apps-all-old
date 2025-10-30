<?php

function double_sha1($data, $signKey) {
  for ($i = 0; $i < 2; $i++) {
    $data = sha1($signKey . $data);
  }
  return $data;
  }
  
  /* Вычисляем подпись (signature). Подпись считается на основе склеенной строки из отсортированного массива параметров, исключая из расчета пустые поля и элемент "signature" */
  function get_signature(array $params, $signKey, $key = 'signature') {
  $keys = array_keys($params);
  sort($keys);
  $chunks = array();
  foreach ($keys as $k) {
    $v = (string) $params[$k];
    if (($v !== '') && ($k != 'signature')) {
        $chunks[] = $k . '=' . base64_encode($v);
    }
  }
  $data = implode('&', $chunks);
  $sig = double_sha1($data, $signKey); 
  
  return $sig;
  }

  function random_str($length = 20)
{
	$arr = array(
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
		'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 
		'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
	);
 
	$res = '';
	for ($i = 0; $i < $length; $i++) {
		$res .= $arr[random_int(0, count($arr) - 1)];
	}
	return $res;
}
 $vat=[
  '0'=>'none',
 '10'=>'vat10',
'20'=>'vat20', 
 ];
//echo random_number();
  