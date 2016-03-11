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

    public function __construct ( $argv , $configIni )
    {
        if ( array_key_exists ( 'help' , $argv ) )
        {
            die ( $this->getUsage () );
        }

        $configDefaul = parse_ini_file ( $configIni , true );
        $this->argv = $argv + array_filter ( $configDefaul[ 'main' ] );

        if ( strtolower ( $this->argv[ 'framework' ] ) == 'none' )
        {
            $this->argv += $configDefaul[ 'none' ];
        } else
        {
            global $_path;
            require $_path . '/vendor/autoload.php';
        }
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
        if ( ! $this->adapterConfig )
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
        if ( ! $this->adapterDriver )
        {
            $this->factoryDriver ();
        }

        return $this->adapterDriver;
    }

}
