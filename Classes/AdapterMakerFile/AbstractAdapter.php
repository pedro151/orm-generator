<?php

namespace Classes\AdapterMakerFile;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
abstract class AbstractAdapter
{
    /**
     * @type AbstractAdapter[]
     */
    private static $_instance = array ();

    /**
     *
     */
    private function __construct ()
    {
    }

    /**
     * @return \Classes\AdapterMakerFile\AbstractAdapter
     */
    public static function getInstance ()
    {
        $class = get_called_class ();
        $arr = explode ( '\\' , $class );
        $classEnd = end ( $arr );
        if ( ! isset( self::$_instance[ $classEnd ] ) )
        {
            self::$_instance[ $classEnd ] = new $class();
        }

        return self::$_instance[ $classEnd ];
    }

    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return string[]
     */
    public abstract function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable );

    /**
     * @type string
     */
    protected $parentClass;

    /**
     * @type string nome do arquivo template
     */
    protected $fileTpl;

    /**
     * @type string nome do arquivo template
     */
    protected $parentFileTpl;

    /**
     * @type string
     */
    protected $pastName;

    /**
     * @return string
     */
    public function getParentClass ()
    {
        return $this->parentClass;
    }


    /**
     * @return string
     */
    public function getFileTpl ()
    {
        return $this->fileTpl;
    }

    /**
     * @return string
     */
    public function getParentFileTpl ()
    {
        return $this->parentFileTpl;
    }

    /**
     * @return string
     */
    public function getPastName ()
    {
        return $this->pastName;
    }

}
