<?php
namespace Cassoft\SelfProg;

class IniHelper
{
    /**
     * @var array Структура ini файла
     */
    private array $structure = [];
    /**
     * @var int Тип сканирования
     */
    private int $scannerMode;
    /**
     * @var string Путь к файлу
     */
    private string $path;

    public function __construct(string $path, int $scannerMode = INI_SCANNER_TYPED) {
        $this->path = $path;
        //file_put_contents($path, null, LOCK_EX);
        $this->scannerMode = $scannerMode;

        $this->setInitStructure();
    }

    private function setInitStructure(): void {
        $this->structure = parse_ini_file($this->path, true, $this->scannerMode);
    }

    public function getStructure(): array {
        return $this->structure;
    }

    public function getSection(string $section): array {
        return $this->structure[$section];
    }

    public function getValue(string $section, string $key) {
        return $this->getSection($section)[$key];
    }

    /**
     * @throws Exception
     */
    public function addSection(string $section): IniHelper {
        if (array_key_exists($section, $this->structure)) {
            throw new Exception("Секция $section уже существует");
        }

        $this->structure[$section] = [];

        return $this;
    }
    public function write(): void {
        $iniContent = null;

        foreach ($this->structure as $section => $data) {
            $iniContent .= "[$section]\n";

            foreach ($data as $key => $value) {
                $iniContent .= "$key=$value\n";
            }

            $iniContent .= "\n";
        }

        file_put_contents($this->path, $iniContent);
        $this->setInitStructure();
    }
}
