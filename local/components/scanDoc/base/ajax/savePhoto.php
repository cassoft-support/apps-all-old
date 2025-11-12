<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//require_once '/home/bitrix/www/local/components/brokci_object/object/ajax/Watermark.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/local/CSlibs/tools/resize.php';
//$file_log = "/home/bitrix/www/local/components/brokci_object/object/ajax/logImg.txt";

function savePhoto($auth, $resDomain, $post, $files, $type)
{
    $log = __DIR__."/logSavePhoto.txt";
    p($post , "start", $log);
    $Resize = new Resize();
    if (strpos($resDomain, 'https://') === 0) {
        $domain =$resDomain;
    } else {
        $domain ="https://".$resDomain;
    }
    $folderId = $auth->CScore->call('disk.storage.getforapp', [])["ROOT_OBJECT_ID"];
//    $folderAppResult = $folderApp->getResponseData()->getResult()->getResultData();
//    $folderId = $folderAppResult["ROOT_OBJECT_ID"];
    $result = [];
    $arFiles = [];
    $sort = [];
    $oldPhotoInfo= [];
    if ($type === 'photo') {
        $sort = $post['sort'];
        $oldPhotoInfo = $post['oldPhotoInfo'];
        $files['file'] = $files['files'];

    } else {
        $sort = $post['sort'];
        $oldPhotoInfo = $post['oldPhotoInfo'];
       // $files['file'] = $files['files'];

    }

    if ($oldPhotoInfo) {
      //  $result['_post'] = $post;
        p($oldPhotoInfo , "oldPhotoInfo", $log);
        foreach ($oldPhotoInfo as $el) {
$val=array();
            p($val , "val1", $log);
            $val = explode(',', $el);
            p($val , "val", $log);
            $sortPosition = array_search($val['0'], $sort);
            p($sortPosition , "sortPosition", $log);
           // $arFiles[$sortPosition] = "{$val['0']}|{$val['1']}";
            $arFiles[$sortPosition] =[
                'photo_id' => $val['0'],
                'photo_link' => $val['1'],
            ];
            p($arFiles , "arFiles", $log);
        }
    }

    if ($files) {
        //   file_put_contents(__DIR__ . 'logImg.txt', date('Y-m-d H:i:s') .print_r('php2 start',true) . PHP_EOL, FILE_APPEND);
        //   $test = [];
        //    $tmp = $files['file'];
        //$files['file'] = $files['files'];
        $writable = [];
p($files['file'] , "file", $log);
        foreach ($files['file']['name'] as $key_files => $item_n_files) { // Перебор входящих файлов
            $resUpload = '';
            $resGetExtLink = '';
            $downloadLink = '';

            $tempFile = $files['file']['tmp_name'][$key_files];
            $nameFile = $files['file']['name'][$key_files];
//            $writable[] = is_writable($tempFile);
//            p($writable , "writable", $log);
            if($files['file']['type'][$key_files] !== 'application/pdf') {
                $Resize->resizePhoto($tempFile, $nameFile);
            }
            // $Watermark->create($tempFile, $tempFile, $settings);
            $base64 = base64_encode(file_get_contents($tempFile));

            //загрузка файла в папку на сервер
            // $url = $domain_full . '/rest/disk.folder.uploadfile';
            $additionalParameters = array(
                "id"                 => $folderId, //id папки на диске в которую загружается файл
                "generateUniqueName" => 'Y', //добавлять в скобках номер, если такое имя уже есть
                "fileContent"        => array($nameFile, trim($base64)),
                "data"               => array("NAME" => $nameFile)
            );
p($additionalParameters , "additionalParameters", $log);
            $uploadImageResult = $auth->CScore->call('disk.folder.uploadfile', $additionalParameters);
           p($uploadImageResult , "uploadImageResult", $log);
            if ($uploadImageResult["CONTENT_URL"]) {
                $downloadLink = $uploadImageResult["CONTENT_URL"];
                $newFileId = $uploadImageResult["ID"];
            } elseif ($newFileId = $uploadImageResult["ID"]) {

             //   $url = $domain . '/rest/disk.file.getExternalLink';
                $additionalParameters = array(
                    "id"   => $newFileId
                );
p($additionalParameters , "additionalParameters", $log);
                $ExtLinkResult = $auth->CScore->call('disk.file.getExternalLink', $additionalParameters);
p($ExtLinkResult , "ExtLinkResult", $log);

                if ($ExtLinkResult['0']) //если получена публичная ссылка
                {

                    $html = '';
                    try {
                        $html = parse($ExtLinkResult['0']);
                    } catch (Exception $e) {
                        $html = $e->getMessage();
                    }

                    preg_match("/href=.(\/[\w\/?&]*download\/[\w\/?&]*token=\w*)/", $html, $arPregRes);
                    $downloadLink = $arPregRes[1];

                    if ($downloadLink) {
                        $downloadLink = $domain . $downloadLink;
                    }
                    //else {
                    //  $downloadLink = $domain_full . $downloadLink;
                    //                    }
                }
            } else {
                $message .= ' Ошибка загрузки файла на диск' . $nameFile;
            }

            if ($downloadLink) {
                $sortPosition = array_search($nameFile, $sort);

             //   $arFiles[$sortPosition] = $newFileId . '|' . $downloadLink;
                $arFiles[$sortPosition] =[
                    'photo_id' => $newFileId,
                    'photo_link' => $downloadLink,
                ];
                //    $test[] = $downloadLink;
            } else {
                $message .= ' Ошибка загрузки файла ' . $nameFile;
            }
        }


    } else {
        $result['_file'] = 'nothing';
    }
    ksort($arFiles);

if(!empty($arFiles)) {
    $uploadResult["result"] = json_encode($arFiles);
}else{
    $uploadResult["result"] = false;
}
       // 'test' => is_readable($settings["watermark_img"]),
      //  'access' =>  $writable,
        $uploadResult["message"] = $message;

p($uploadResult , "uploadResult", $log);
    return $uploadResult;
}
function parse($url)
{
    $curlOptions = array(
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_URL => $url
    );

    $curl = curl_init();
    curl_setopt_array($curl, $curlOptions);
    $curlResult = curl_exec($curl);
    curl_close($curl);

    return $curlResult;
}
