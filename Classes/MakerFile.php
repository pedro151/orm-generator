<?php

namespace Classes;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterMakerFile\DbTable;
use Classes\AdapterMakerFile\Entity;
use Classes\AdapterMakerFile\Model;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class MakerFile
{
    const SEPARETOR = '_';

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
        $this->parseLocation ();
    }

    /**
     * Analisa os caminhos das pastas base
     */
    public function parseLocation ()
    {
        $arrBase = array (
            dirname ( __FILE__ ),
            '..',
            $this->config->path
        );

        # pasta com nome do driver do banco
        if ( $this->config->folder_database )
        {
            $classDriver = explode ( '\\', get_class ( $this->driver ) );
            $arrBase[] = end ( $classDriver );
        }

        $this->baseLocation = implode ( DIRECTORY_SEPARATOR, filter_var_array ( $arrBase ) );

        if ( $this->config->hasSchemas () )
        {
            $schemas = $this->config->getSchemas ();
            foreach ( $schemas as $schema )
            {
                $this->location[ $schema ] = implode (
                    DIRECTORY_SEPARATOR, array (
                                           $this->baseLocation,
                                           ucfirst ( $schema )
                                       )
                );
            }

        }
        else
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

    /**
     * Executa o Make, criando arquivos e Diretorios
     */
    public function run ()
    {
        $countDir = count ( $this->factoryMakerFile () );
        $max = $this->driver->getTotalTables () * $countDir;
        $cur = 0;
        echo "Starting..\n";
        foreach ( $this->location as $schema => $location )
        {
            foreach ( $this->factoryMakerFile () as $objMakeFile )
            {
                $path = $location . DIRECTORY_SEPARATOR . $objMakeFile->getPastName ();
                $this->makeDir ( $path );

                if ( $objMakeFile->getParentFileTpl () != '' )
                {
                    $fileAbstract = $this->baseLocation
                        . DIRECTORY_SEPARATOR
                        . $objMakeFile->getParentClass () . '.php';

                    $tplAbstract = $this->getParsedTplContents ( $objMakeFile->getParentFileTpl () );
                    $this->makeSourcer ( $fileAbstract, $tplAbstract );
                    unset( $fileAbstract, $tplAbstract );
                }

                foreach (
                    $this->driver->getTables () as $key => $objTables
                )
                {
                    printf ( "\r Creating: %6.2f%%", ceil ( $cur / $max * 100 ) );
                    $cur++;

                    $file = $path
                        . DIRECTORY_SEPARATOR
                        . $this->getClassName ( $objTables->getName () )
                        . '.php';


                    $tpl = $this->getParsedTplContents (
                        $objMakeFile->getFileTpl (), $objTables, $objMakeFile,
                        $objMakeFile->parseRelation ( $this, $objTables )
                    );
                    $this->makeSourcer ( $file, $tpl );
                }

            }
        }

        $this->reportProcess ( $cur, $countDir );
        echo "\nfinished!";
    }

    private function reportProcess ( $countFiles, $countDir )
    {
        $databases = count ( $this->location );
        $totalTable = ( $countFiles / $countDir ) * $databases;
        echo "\n------";
        printf ( "\n\r-Files generated:%s", $databases * $countFiles );
        printf ( "\n\r-Diretory generated:%s", $databases * $countDir );
        printf ( "\n\r-Scanned tables:%s", ceil ( $totalTable ) );
        echo "\n------";
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
     * verifica se ja existe e cria as pastas em cascata
     *
     * @param $dir
     */
    private function makeDir ( $dir )
    {
        if ( !is_dir ( $dir ) )
        {
            if ( !@mkdir ( $dir, 0755, true ) )
            {
                die( "error: could not create directory $dir\n" );
            }
        }
    }

    private function makeSourcer ( $modelFile, $modelData )
    {
        if ( !is_file ( $modelFile ) )
        {
            if ( !file_put_contents ( $modelFile, $modelData ) )
            {
                die( "Error: could not write model file $modelFile." );
            }
        }

    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function getClassName ( $str )
    {
        $temp = '';
        foreach ( explode ( self::SEPARETOR, $str ) as $part )
        {
            $temp .= ucfirst ( $part );
        }

        return $temp;
    }

    /**
     *
     * parse a tpl file and return the result
     *
     * @param String $tplFile
     *
     * @return String
     */
    public function getParsedTplContents ( $tplFile, \Classes\Db\DbTable $objTables = null, $objMakeFile = null, $vars = array () )
    {
        if ( empty( $vars ) )
        {
            $vars = array ();
        }

        $arrUrl = array (
            dirname ( __FILE__ ),
            'templates',
            $this->config->framework,
            $tplFile
        );

        extract ( $vars );
        ob_start ();
        require implode ( DIRECTORY_SEPARATOR, filter_var_array ( $arrUrl ) );
        $data = ob_get_contents ();
        ob_end_clean ();

        return $data;
    }

}