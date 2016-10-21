<?php

namespace Classes;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class CleanTrash
{
    /**
     * @type CleanTrash
     */
    private static $instance;

    final private function __construct (){ }

    /**
     * @return \Classes\CleanTrash
     */
    public static function getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function diffFiles ( $dir , $arrFiles )
    {

    }
}