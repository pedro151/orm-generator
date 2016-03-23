<?php

namespace Classes;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\Maker\AbstractMaker;

require_once 'Classes/AdapterMakerFile/AbstractAdapter.php';
require_once 'Classes/Maker/AbstractMaker.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class MakerFile extends AbstractMaker
{

    /**
     * @type string[]
     */
    public $location = array ();

    /**
     * caminho de pastas Base
     *
     * @type string
     */
    private $baseLocation = '';

    /**
     * @type \Classes\AdapterConfig\AbstractAdapter
     */
    private $config;

    /**
     * @type \Classes\AdaptersDriver\AbsractAdapter
     */
    private $driver;

    public function __construct ( Config $config )
    {
        $this->config = $config->getAdapterConfig ();
        $this->driver = $config->getAdapterDriver ();
        $this->parseLocation ( $config->_basePath );
    }

    /**
     * Analisa os caminhos das pastas base
     */
    public function parseLocation ( $basePath )
    {

        $arrBase = array (
            $basePath ,
            $this->config->path
        );

        $this->baseLocation = implode ( DIRECTORY_SEPARATOR , array_filter ( $arrBase ) );

        # pasta com nome do driver do banco
        $driverBase = '';
        if ( $this->config->{"folder-database"} )
        {
            $classDriver = explode ( '\\' , get_class ( $this->driver ) );
            $driverBase = end ( $classDriver );
        }

        if ( $this->config->hasSchemas () )
        {

            $schemas = $this->config->getSchemas ();
            foreach ( $schemas as $schema )
            {
                $arrUrl = array (
                    $this->baseLocation ,
                    $driverBase ,
                    $this->getClassName ( $schema )
                );

                $this->location[ $schema ] = implode ( DIRECTORY_SEPARATOR , array_filter ( $arrUrl ) );
                unset( $arrUrl );
            }


        } else
        {
            $baseLocation = implode (
                DIRECTORY_SEPARATOR , array_filter ( array (
                        $this->baseLocation ,
                        $driverBase ,
                        $this->getClassName ( $this->getConfig()->getDatabase () )
                    )
                )
            );
            $this->location = array ( $baseLocation );
        }
    }

    /**
     * @return AdapterConfig\AbstractAdapter
     */
    public function getConfig ()
    {
        return $this->config;
    }

    /* Get current time */
    public function startTime ()
    {
        echo "Starting..\n";
        $this->startTime = microtime ( true );
    }

    private function getRunTime ()
    {
        return round ( ( microtime ( true ) - $this->startTime ) , 3 );
    }

    /**
     * Executa o Make, criando arquivos e Diretorios
     */
    public function run ()
    {
        $this->startTime ();
        $this->driver->runDatabase ();
        $max = $this->driver->getTotalTables () * count ( $this->factoryMakerFile () );
        $cur = 0;


        foreach ( $this->location as $schema => $location )
        {
            foreach ( $this->factoryMakerFile () as $objMakeFile )
            {
                $path = $location . DIRECTORY_SEPARATOR . $objMakeFile->getPastName ();
                self::makeDir ( $path );

                if ( $objMakeFile->getParentFileTpl () != '' )
                {
                    $fileAbstract = $this->baseLocation
                                    . DIRECTORY_SEPARATOR
                                    . $objMakeFile->getParentClass () . '.php';

                    $tplAbstract = $this->getParsedTplContents ( $objMakeFile->getParentFileTpl () );
                    self::makeSourcer ( $fileAbstract , $tplAbstract );
                    unset( $fileAbstract , $tplAbstract );
                }

                foreach (
                    $this->driver->getTables ( $schema ) as $key => $objTables
                )
                {
                    $total = ( $cur / $max * 100 );
                    printf ( "\r Creating: %6.2f%%" , ceil ( $total ) );
                    $cur ++;

                    $file = $path
                            . DIRECTORY_SEPARATOR
                            . self::getClassName ( $objTables->getName () )
                            . '.php';


                    $tpl = $this->getParsedTplContents (
                        $objMakeFile->getFileTpl () ,
                        $objMakeFile->parseRelation ( $this , $objTables )
                        , $objTables , $objMakeFile

                    );
                    self::makeSourcer ( $file , $tpl );
                }

            }
        }

        $this->reportProcess ( $cur );
        echo "\n\033[1;32mSuccessfully process finished!\n\033[0m";
    }

    private function reportProcess ( $countFiles )
    {
        if ( $this->config->isStatusEnabled () )
        {
            $databases = count ( $this->location );
            $countDir = $this->countDiretory ();
            $totalTable = $this->driver->getTotalTables ();
            echo "\n------";
            printf ( "\n\r-Files generated:%s" , $countFiles );
            printf ( "\n\r-Diretory generated:%s" , $databases * $countDir );
            printf ( "\n\r-Scanned tables:%s" , $totalTable );
            printf ( "\n\r-Execution time: %ssec" , $this->getRunTime () );
            echo "\n------";
        }
    }

    /**
     * Instancia os Modulos de diretorios e tampletes
     *
     * @return AbstractAdapter[]
     */
    public function factoryMakerFile ()
    {
        return $this->config->getMakeFileInstances ();
    }

    /**
     * conta o numero de diretorios que serao criados
     *
     * @return int
     */
    public function countDiretory ()
    {
        $dir = 0;
        foreach ( $this->factoryMakerFile () as $abstractAdapter )
        {
            if ( $abstractAdapter->hasDiretory () )
            {
                $dir ++;
            }
        }

        return $dir;
    }

    /**
     *
     * parse a tpl file and return the result
     *
     * @param String $tplFile
     *
     * @return String
     */
    protected function getParsedTplContents ( $tplFile , $vars = array () , \Classes\Db\DbTable $objTables = null , $objMakeFile = null )
    {

        $arrUrl = array (
            __DIR__ ,
            'templates' ,
            $this->config->framework ,
            $tplFile
        );

        $filePath = implode ( DIRECTORY_SEPARATOR , filter_var_array ( $arrUrl ) );

        extract ( $vars );
        ob_start ();
        require $filePath;
        $data = ob_get_contents ();
        ob_end_clean ();

        return $data;
    }

}