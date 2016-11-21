<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/11/16
 * Time: 14:01
 */

namespace Classes\AdapterMakerFile;


class FilesFixeds
{
    /**
     * @type string
     */
    private $tpl;
    /**
     * @type string
     */
    private $fileName;

    private function __construct (){ }

    public static function getInstance ( $args = array () )
    {
        $obj = new FilesFixeds();
        if ( key_exists ( 'tpl' , $args ) )
        {
            $obj->setTpl ( $args[ 'tpl' ] );
        }

        if ( key_exists ( 'name' , $args ) )
        {
            $obj->setFileName ( $args[ 'name' ] );
        }

        return $obj;
    }

    public function hasData ()
    {
        return $this->hasTpl () && $this->hasFileName ();
    }

    /**
     * @return bool
     */
    public function hasTpl ()
    {
        return ! empty( $this->tpl );
    }

    /**
     * @return bool
     */
    public function hasFileName ()
    {
        return ! empty( $this->fileName );
    }

    /**
     * @return string
     */
    public function getTpl ()
    {
        return $this->tpl;
    }

    /**
     * @param string $tpl
     */
    public function setTpl ( $tpl )
    {
        $this->tpl = $tpl;
    }

    /**
     * @return string
     */
    public function getFileName ()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName ( $fileName )
    {
        $this->fileName = $fileName;
    }

}