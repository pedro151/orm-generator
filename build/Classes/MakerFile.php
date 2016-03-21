<?php

namespace Classes;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\Maker\Template;

require_once 'Classes/AdapterMakerFile/AbstractAdapter.php';
require_once 'Classes/Maker/Template.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class MakerFile
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

        # pasta com nome do driver do banco
        if ( $this->config->{"folder-database"} )
        {
            $classDriver = explode ( '\\' , get_class ( $this->driver ) );
            $arrBase[] = end ( $classDriver );
        }

        $this->baseLocation = implode ( DIRECTORY_SEPARATOR , filter_var_array ( $arrBase ) );

        if ( $this->config->hasSchemas () )
        {
            $schemas = $this->config->getSchemas ();
            foreach ( $schemas as $schema )
            {
                $this->location[ $schema ] = implode (
                    DIRECTORY_SEPARATOR , array (
                        $this->baseLocation ,
                        ucfirst ( $schema )
                    )
                );
            }

        } else
        {
            $this->location = array ( $this->baseLocation );
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
                Template::makeDir ( $path );

                if ( $objMakeFile->getParentFileTpl () != '' )
                {
                    $fileAbstract = $this->baseLocation
                                    . DIRECTORY_SEPARATOR
                                    . $objMakeFile->getParentClass () . '.php';

                    $tplAbstract = $this->getParsedTplContents ( $objMakeFile->getParentFileTpl () );
                    Template::makeSourcer ( $fileAbstract , $tplAbstract );
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
                            . Template::getClassName ( $objTables->getName () )
                            . '.php';


                    $tpl = $this->getParsedTplContents (
                        $objMakeFile->getFileTpl () , $objTables , $objMakeFile ,
                        $objMakeFile->parseRelation ( $this , $objTables )
                    );
                    Template::makeSourcer ( $file , $tpl );
                }

            }
        }

        $this->reportProcess ( $cur );
        echo "\nProcess finished!\n";
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
    public function getParsedTplContents ( $tplFile , \Classes\Db\DbTable $objTables = null , $objMakeFile = null , $vars = array () )
    {
        if ( empty( $vars ) )
        {
            $vars = array ();
        }

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