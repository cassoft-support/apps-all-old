<?php

namespace Cassoft\Adver;

class Writer
{
    public $xml;
    public $pwd;
    public $feedPwd;
    private $hash;

    function __construct($xml, $pwd)
    {
        $this->xml = $xml;
        $this->pwd = $pwd;
    }
    public function checkDirectory($folderNumber)
    {
        $this->feedPwd = $this->pwd . '/' . $folderNumber;
        if (!file_exists($this->pwd . '/' . $folderNumber)) {
            mkdir($this->pwd . '/' . $folderNumber);
        }
        $folderNameSec = crypt($folderNumber, 'ucre');
        $folderNameSec = str_replace("/", "", $folderNameSec);
        if (!file_exists($this->feedPwd . '/' . $folderNameSec)) {
            mkdir($this->feedPwd . '/' . $folderNameSec);
        }
        $this->feedPwd = $this->feedPwd . '/' . $folderNameSec;
        return $this->feedPwd;
    }

    public function writeXmlAvito(array $data, string $feedName)
    {
        $this->xml->openURI($this->feedPwd . '/' . $feedName . '.xml');
        $this->xml->setIndent(true);
        $this->xml->startDocument("1.0", "utf-8");
        $this->xml->startElement("Ads"); //Корневой элемент Ads
        $this->xml->writeAttribute("formatVersion", "3");
        $this->xml->writeAttribute("target", "Avito.ru");

        foreach ($data as $ad) {

            $this->xml->startElement("Ad");
            foreach ($ad as $key => $val) {
                switch ($key) {
                    case 'Images':
                        $this->imageXmlAvito($key, $val);
                        break;
                    case 'ViewFromWindows':
                        $this->writeViewFromWindowsAvito($key, $val);
                        break;
                    case 'LeaseMultimedia':
                        $this->avitoLease($key, $val);
                        break;
                    case 'LeaseAppliances':
                        $this->avitoLease($key, $val);
                        break;
                    case 'LeaseComfort':
                        $this->avitoLease($key, $val);
                        break;
                    case 'LeaseAdditionally':
                        $this->avitoLease($key, $val);
                        break;
                    case 'RoomType':
                        $this->avitoRoomType($key, $val);
                        break;
                    default:
                        $this->writeSimple($key, $val);
                        break;
                }
            }
            $this->xml->endElement();
        }
        $this->xml->endElement();
        $this->xml->endDocument();
    }
    public function avitoRoomType($key, $val)
    {
        $this->xml->StartElement($key);
        foreach ($val as $option) {
            $this->xml->writeElement('Option', $option);
        }
        $this->xml->endElement();
    }
    public function avitoLease($key, $val)
    {
        $this->xml->StartElement($key);
        foreach ($val as $option) {
            $this->xml->writeElement('Option', $option);
        }
        $this->xml->endElement();
    }
    public function writeXmlYandex(array $data, string $feedName)
    {
        $this->xml->openURI($this->feedPwd . '/' . $feedName . '.xml');
        $this->xml->setIndent(true);
        $this->xml->startDocument("1.0", "utf-8");
        $this->xml->startElement("realty-feed");
        $this->xml->writeAttribute("xmlns", "http://webmaster.yandex.ru/schemas/feed/realty/2010-06");
        $this->xml->writeElement("generation-date", date("c"));

        foreach ($data as $ad) {
            $this->xml->startElement("offer");
            $this->xml->writeAttribute("internal-id", $ad['internal-id']);
            unset($ad['internal-id']);
            foreach ($ad as $key => $val) {
                if ($key == 'image') {
                    $this->imageXmlYandex($key, $val);
                } else {
                    $this->writeSimple($key, $val);
                }
            }
            $this->xml->endElement();
        }
        $this->xml->endElement();
        $this->xml->endDocument();
        return $data;
    }

    public function writeXmlCian(array $data, string $feedName)
    {
        $this->xml->openURI($this->feedPwd . '/' . $feedName . '.xml');
        $this->xml->setIndent(true);
        $this->xml->startDocument("1.0", "utf-8");
        $this->xml->startElement("feed");
        $this->xml->writeElement("feed_version", 2);

        foreach ($data as $ad) {
            $this->xml->StartElement('object');
            foreach ($ad as $key => $val) {
                if ($key == 'Photos') {
                    $this->photoXmlCian($val);
                } elseif ($key == 'LayoutPhoto') {
                    $this->layoutPhotoXmlCian($val);
                } else {
                    $this->writeSimple($key, $val);
                }
            }
            $this->xml->endElement();
        }

        $this->xml->endElement();
        $this->xml->endDocument();
    }

    public function photoXmlCian($val)
    {
        $this->xml->StartElement('Photos');
        foreach ($val as $key => $photo) {
            $this->xml->StartElement('PhotoSchema');
            $this->xml->writeElement('FullUrl', $photo['FullUrl']);
            if ($key == '0') {
                $this->xml->writeElement('IsDefault', 'true');
            } else {
                $this->xml->writeElement('IsDefault', 'false');
            }
            $this->xml->endElement();
        }
        $this->xml->endElement();
    }

    public function layoutPhotoXmlCian($val)
    {
        $this->xml->StartElement('LayoutPhoto');
        $this->xml->writeElement('FullUrl', $val);
        $this->xml->writeElement('IsDefault', 'false');
        $this->xml->endElement();
    }


    public function imageXmlYandex($key, $val)
    {
        foreach ($val as $v) {
            $this->xml->writeElement($key, $v);
        }
    }
    public function writeSimple($key, $val)
    {
        $rv = is_array($val);
        if ($rv === false) {
            $this->xml->writeElement($key, $val);
        } else {
            $this->xml->startElement($key);
            foreach ($val as $k => $v) {
                $this->writeSimple($k, $v);
            }
            $this->xml->endElement();
        }
    }

    private function imageXmlAvito($key, $val)
    {
        $this->xml->startElement($key);
        foreach ($val as $v) {
            $this->xml->startElement('Image');
            $this->xml->writeAttribute('url', $v);
            $this->xml->endElement();
        }
        $this->xml->endElement();
    }

    private function writeViewFromWindowsAvito($key, $val)
    {
        $this->xml->startElement($key);
        foreach ($val as $v) {
            $this->xml->writeElement('Option', $v);
        }
        $this->xml->endElement();
    }
    private function writeSimpleAvito($key, $val)
    {
        $rv = array_filter($val, 'is_array');
        if (count($rv) == 0) {
            $this->xml->writeElement($key, $val);
        } else {
            $this->xml->startElement($key);
            foreach ($val as $k => $v) {
                $this->writeSimpleAvito($k, $v);
            }
            $this->xml->endElement;
        }
    }
    public function writeXmlDoubleGis(array $data, string $feedName, array $categories)
    {
        $this->xml->openURI($this->feedPwd . '/' . $feedName . '.xml');
        $this->xml->setIndent(true);
        $this->xml->startDocument("1.0", "utf-8");
        $this->xml->startElement("yml_catalog"); //Корневой элемент Ads
        $this->xml->writeAttribute("date", date("c"));

        $this->xml->startElement("shop");
        $this->xml->startElement("categories");
        foreach ($categories as $key => $value) {
            $this->xml->startElement('category');
            $this->xml->writeAttribute('id', $key);
            $this->xml->text($value);
            $this->xml->endElement();
        }
        $this->xml->endElement();
        $this->xml->startElement("offers");
        foreach ($data as $ad) {
            $this->xml->startElement("offer");
            $this->xml->writeAttribute("id", $ad['id']);
            unset($ad['id']);
            foreach ($ad as $key => $val) {
                switch ($key) {
                    default:
                        $this->writeSimple($key, $val);
                        break;
                }
            }
            $this->xml->endElement();
        }
        $this->xml->endElement();
        $this->xml->endElement();
        $this->xml->endElement();
        $this->xml->endDocument();
        return $data;
    }
}