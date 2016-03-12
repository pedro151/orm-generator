<?php

namespace Classes;

use Classes\AdapterConfig\None;
use Classes\AdapterConfig\ZendFrameworkOne;
use Classes\AdaptersDriver\Dblib;
use Classes\AdaptersDriver\Mssql;
use Classes\AdaptersDriver\Mysql;
use Classes\AdaptersDriver\Pgsql;
use Classes\AdaptersDriver\Sqlsrv;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Config
{

    /**
     * @var string
     */
    private $version = "1.0";

    /**
     * String that separates the parent section name
     *
     * @var string
     */
    protected $sectionSeparator = ':';

    /**
     * @var array
     */
    private $argv = array ();

    /**
     * @var \Classes\AdapterConfig\AbstractAdapter
     */
    private $adapterConfig;

    /**
     * @var \Classes\AdaptersDriver\AbsractAdapter
     */
    private $adapterDriver;

    public function __construct ( $argv, $configIni )
    {
        if ( array_key_exists ( 'help', $argv ) )
        {
            die ( $this->getUsage () );
        }

        $configTemp = $this->loadIniFile ( $configIni );
        $thisSection = isset( $argv[ 'framework' ] ) ? $argv[ 'framework' ] : $configTemp[ 'main' ][ 'framework' ];
        $configDefaul = $configTemp[ $thisSection ] + $configTemp[ $configTemp[ $thisSection ][ 'extends' ] ];
        unset( $configTemp );

        $this->argv = $argv + array_filter ( $configDefaul );

    }

    /**
     * @return string
     */
    public function getUsage ()
    {
        return <<<USAGE
parameters:
    --database            : database name
 *  --schema              : database schema name (one or more than one)
    --driver              : database driver name (Ex.: pgsql)
    --framework           : name framework used, which has the contents of the database configurations
                            and framework template
    --status              : show status of implementation carried out after completing the process
    --path                : specify where to create the files (default is current directory)

 example: php DAO-generator.php --framework=zend_framework --database=foo --table=foobar --status=1

Data Access Object DAO-generator By: Pedro Alarcao Version: $this->version
USAGE;
    }

    /**
     * Carregar o arquivo ini e pré-processa o separador de seção ':'
     * no nome da seção (que é usado para a extensão seção) de modo a que a
     * matriz resultante tem os nomes de seção corretos e as informações de
     * extensão é armazenado em uma sub-chave
     *
     * @param string $filename
     * @throws \Exception
     * @return array
     */
    protected function loadIniFile ( $filename )
    {
        if ( !is_file ( $filename ) )
        {
            throw new \Exception( "File does not exist: configs/config.ini \n" );
        }

        $loaded = parse_ini_file ( $filename, true );
        $iniArray = array ();
        foreach ( $loaded as $key => $data )
        {
            $pieces = explode ( $this->sectionSeparator, $key );
            $thisSection = trim ( $pieces[ 0 ] );
            switch ( count ( $pieces ) )
            {
                case 1:
                    $iniArray[ $thisSection ] = $data;
                    break;

                case 2:
                    $extendedSection = trim ( $pieces[ 1 ] );
                    $iniArray[ $thisSection ] = array_merge ( array ( 'extends' => $extendedSection ), $data );
                    break;

                default:
                    throw new \Exception( "Section '$thisSection' may not extend multiple sections in $filename" );
            }
        }

        return $iniArray;
    }

    /**
     *
     */
    private function compileListParamTables ()
    {
        // TODO: implement here
    }

    /**
     * analisa a opção e cria a instancia do Atapter do determinado framework
     *
     */
    private function factoryConfig ()
    {
        switch ( strtolower ( $this->argv[ 'framework' ] ) )
        {
            case 'none':
                $this->adapterConfig = new None( $this->argv );
                break;
            case 'zend_framework':
                $this->adapterConfig = new ZendFrameworkOne( $this->argv );
                break;
        }

    }

    /**
     * Analisa a opção e instancia o determinado banco de dados
     *
     */
    private function factoryDriver ()
    {
        switch ( $this->argv[ 'driver' ] )
        {
            case 'pgsql':
            case 'pdo_pgsql':
                $this->adapterDriver = new Pgsql( $this->getAdapterConfig () );
                break;
            case 'mysql':
            case 'pdo_mysql':
                $this->adapterDriver = new Mysql( $this->getAdapterConfig () );
                break;
            case 'mssql':
                $this->adapterDriver = new Mssql( $this->getAdapterConfig () );
                break;
            case 'dblib':
                $this->adapterDriver = new Dblib( $this->getAdapterConfig () );
                break;
            case 'sqlsrv':
                $this->adapterDriver = new Sqlsrv( $this->getAdapterConfig () );
                break;
        }

    }

    /**
     * @return AdapterConfig\AbstractAdapter
     */
    public function getAdapterConfig ()
    {
        if ( !$this->adapterConfig )
        {
            $this->factoryConfig ();
        }

        return $this->adapterConfig;
    }

    /**
     * @return AdaptersDriver\AbsractAdapter
     */
    public function getAdapterDriver ()
    {
        if ( !$this->adapterDriver )
        {
            $this->factoryDriver ();
        }

        return $this->adapterDriver;
    }

}
