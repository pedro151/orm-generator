<?php

namespace Classes\AdapterMakerFile;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
abstract class AbstractAdapter
{
    /**
     * @type AbstractAdapter[]
     */
    private static $_instance = array ();

    /**
     * @type FilesFixeds[]
     */
    private $instanceFixedFile = array ();

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    abstract public function parseRelation ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable );

    /**
     * @type string nome do arquivo template
     */
    protected $fileTpl;

    /**
     * @type string
     */
    protected $pastName;

    /**
     * nome do arquivo template e nome das classes fixas
     *
     * @type string[][]
     */
    protected $fileFixedData = array ();

    /**
     * @var bool
     */
    protected $overwrite = false;

    /**
     *
     */
    final private function __construct ()
    {
    }

    /**
     * @return \Classes\AdapterMakerFile\AbstractAdapter
     */
    public static function getInstance ()
    {
        $class = get_called_class ();
        if ( ! isset( self::$_instance[ $class ] ) )
        {
            self::$_instance[ $class ] = new $class();
        }

        return self::$_instance[ $class ];
    }

    /**
     * verifica se existe diretorio nesta makeFile
     *
     * @return bool
     */
    public function hasDiretory ()
    {
        return ! empty( $this->pastName );
    }

    /**
     * @return \Classes\AdapterMakerFile\FilesFixeds
     */
    public function getFilesFixeds ( $key )
    {
        $key = strtolower($key);
        if ( ! isset( $this->fileFixedData[ $key ] ) )
        {
            throw new \Exception( 'Não existe dados para popular o FilesFixeds ' . $key );
        }

        if ( !isset($this->instanceFixedFile[ $key ]) or ! $this->instanceFixedFile[ $key ] instanceof FilesFixeds )
        {
            $this->instanceFixedFile[ $key ] = FilesFixeds::getInstance ( $this->fileFixedData[ $key ] );
        }

        return $this->instanceFixedFile[ $key ];
    }

    /**
     * verifica se existe arquivo de exeção
     *
     * @return bool
     */
    public function hasFilesFixeds ( $key )
    {
        return $this->getFilesFixeds ( strtolower($key) )->hasData ();
    }

    /**
     * retorna a lista de possiveis objetos
     *
     * @return array
     */
    public function getListFilesFixed ()
    {
        return \array_keys ( $this->fileFixedData );
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
    public function getPastName ()
    {
        return $this->pastName;
    }

    /**
     * @return bool
     */
    public function isOverwrite ()
    {
        return $this->overwrite;
    }

}
