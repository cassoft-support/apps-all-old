<?php

namespace CSlibs\B24\Financier;

class financierClass
{
    private $auth;
    private $fileLog;
    private $date;


    public function __construct($auth)
    {
        p($assigned, "start", __DIR__ . "/log.txt");
        $this->auth = $auth;
        $this->fileLog = __DIR__ . "/logClass.txt";
        $this->date = date("d.m.YTH:i:s");
    }
    /**
     * Коментарий  и изменение в другом смарт процессе
     * @param string $elID ID элемента смартпроцесса
     * @noinspection PhpUnused не ппроверять на использование
     *
     */
    public function finDeal()
    {
        
    }
}