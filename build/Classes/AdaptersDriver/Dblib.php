<?php

namespace Classes\AdaptersDriver;

require_once 'Classes/AdaptersDriver/Mssql.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Dblib extends Mssql
{
    public function getPDOString ()
    {
        return sprintf (
            "dblib:host=%s;dbname=%s" ,
            $this->getHost () ,
            $this->database
        );
    }
}
