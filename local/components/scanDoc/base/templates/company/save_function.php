<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
require_once '/home/bitrix/www/local/components/brokci_object/object/ajax/Watermark.php';
require_once '/home/bitrix/www/local/components/brokci_object/object/ajax/resize.php';
//$file_log = "/home/bitrix/www/local/components/brokci_object/object/ajax/logImg.txt";

function savePhoto($auth, $member, $post, $files, $type)
{
    $date = date("d.m.YTH:i");
    file_put_contents("logImg.txt", print_r($date . "\n", true));
    file_put_contents("logImg.txt", print_r("Img19\n", true), FILE_APPEND);
    //   $file_log = "/home/bitrix/www/local/components/brokci_object/object/ajax/logImg.txt";
    //  file_put_contents("logImg.txt", print_r($post, true), FILE_APPEND);

    $HlSettings = new \Cassoft\Services\HlService('client_marketing_settings');
    $ClientSettings = $HlSettings->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'DESC'],
        'filter' => [
            'UF_CS_CLIENT_MEMBER_ID' =>  $member,
        ]
    ])->fetchAll();
    $ClientSettings = $ClientSettings['0'];
    $watermarkInfo = CFile::GetById($ClientSettings['UF_CS_WATERMARK'])->fetch();
    $watermarkInfo['link'] = CFile::GetPath($ClientSettings['UF_CS_WATERMARK']);
    $message = '';
    // $arImage = $files['file'];
    $domain_full =  'https://' . $ClientSettings['UF_CS_CLIENT_PORTAL_DOMEN'];
    $settings = [
        'watermark_pos' =>  $ClientSettings['UF_CS_WATERMARK_LOCATION'],
        'watermark_type' => 'img',
        'watermark_status' => true,
        'watermark_caption_opacity' => '90',
        'watermark_caption_font' => 'NotoSerif-BoldItalic.ttf',
        'watermark_caption' => 'brokci.shop',
        'watermark_caption_size' => 'medium',
        'watermark_img' => '/home/bitrix/www' . $watermarkInfo['link'],
    ];
    $Watermark = new Watermark();
    $Resize = new Resize();


    $folderAppResult = $auth->core->call('disk.storage.getforapp', [])->getResponseData()->getResult()->getResultData();
    $folderId = $folderAppResult["ROOT_OBJECT_ID"];
    $result = [];
    $arFiles = [];
    $sort = [];
    if ($type === 'photo') {
        $sort = $post['sort'];
        $oldPhotoInfo = $post['oldPhotoInfo'];
        $files['file'] = $files['files'];

    } elseif ($type === 'sort_photo') {
//        $sort = $post['sort_plans'];
//        $oldPhotoInfo = $post['oldPhotoInfo_plans'];
//        $files['file'] = $files['files_plans'];
        $sort = $post['sort'];
        $oldPhotoInfo = $post['oldPhotoInfo'];
        $files['file'] = $files['files'];
    }

    if ($post) {
        $result['_post'] = $post;
        //$sort = $post['sort'];
        foreach ($oldPhotoInfo as $val) {
            $val = explode(',', $val);
            $sortPosition = array_search($val['0'], $sort);
            $arFiles[$sortPosition] = "{$val['0']}|{$val['1']}";
        }
    }

    if ($files) {
        //   file_put_contents(__DIR__ . 'logImg.txt', date('Y-m-d H:i:s') .print_r('php2 start',true) . PHP_EOL, FILE_APPEND);
        //   $test = [];
        //    $tmp = $files['file'];
        //$files['file'] = $files['files'];
        $writable = [];
        foreach ($files['file']['name'] as $key_files => $item_n_files) { // Перебор входящих файлов
            $resUpload = '';
            $resGetExtLink = '';
            $downloadLink = '';

            $tempFile = $files['file']['tmp_name'][$key_files];
            $nameFile = $files['file']['name'][$key_files];
            $writable[] = is_writable($tempFile);
            $Resize->resizePhoto($tempFile, $nameFile);
            $Watermark->create($tempFile, $tempFile, $settings);
            $base64 = base64_encode(file_get_contents($tempFile));


            //загрузка файла в папку на сервер
            $url = $domain_full . '/rest/disk.folder.uploadfile';
            $additionalParameters = array(
                "id"                 => $folderId, //id папки на диске в которую загружается файл
                "generateUniqueName" => 'Y', //добавлять в скобках номер, если такое имя уже есть
                "fileContent"        => array($nameFile, trim($base64)),
                "data"               => array("NAME" => $nameFile)
            );

            $uploadImageResult = $auth->core->call('disk.folder.uploadfile', $additionalParameters)->getResponseData()->getResult()->getResultData();
//            file_put_contents("logImg.txt", print_r($uploadImageResult, true), FILE_APPEND);

            if ($uploadImageResult["CONTENT_URL"]) {
                $downloadLink = $uploadImageResult["CONTENT_URL"];
                $newFileId = $uploadImageResult["ID"];
            } elseif ($newFileId = $uploadImageResult["ID"]) {

                $url = $domain_full . '/rest/disk.file.getExternalLink';
                $additionalParameters = array(
                    "id"   => $newFileId
                );


                $ExtLinkResult = $auth->core->call('disk.file.getExternalLink', $additionalParameters)->getResponseData()->getResult()->getResultData();

                if ($ExtLinkResult['0']) //если получена публичная ссылка
                {

                    $html = '';
                    try {
                        $html = parse($ExtLinkResult['0']);
                    } catch (Exception $e) {
                        $html = $e->getMessage();
                    }
                    file_put_contents("logImg.txt", print_r($html, true), FILE_APPEND);
//                    preg_match("/href=.(\/[\w\/?&]*download\/[\w\/?&]*token=\w*)/", $html, $arPregRes);
                    preg_match('/(\/[\w\/?&]*download\/[\w\/?&]*token=\w*)/', $html, $arPregRes);
                    file_put_contents("logImg.txt", print_r($arPregRes, true), FILE_APPEND);
                    $downloadLink = $arPregRes[1];
                    if ($downloadLink) {
                        $downloadLink = $domain_full . $downloadLink;
                    }
                    //else {
                    //  $downloadLink = $domain_full . $downloadLink;
                    //                    }
                }
            } else {
                $message .= ' Ошибка загрузки файла на диск' . $nameFile;
            }


            file_put_contents("logImg.txt", print_r($downloadLink, true), FILE_APPEND);
            if ($downloadLink) {
                $sortPosition = array_search($nameFile, $sort);

                $arFiles[$sortPosition] = $newFileId . '|' . $downloadLink;
                //    $test[] = $downloadLink;
            } else {
                $message .= ' Ошибка загрузки файла ' . $nameFile;
            }
        }


    } else {
        $result['_file'] = 'nothing';
    }
    ksort($arFiles);
    $uploadResult = array(
        "result" => $arFiles,
        'test' => is_readable($settings["watermark_img"]),
        'access' =>  $writable,
        "message" => $message
    );

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
