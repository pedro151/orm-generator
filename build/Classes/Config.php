<?php

namespace Classes;

use Classes\AdapterConfig\None;
use Classes\AdapterConfig\Phalcon;
use Classes\AdapterConfig\ZendFrameworkOne;
use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdaptersDriver\Dblib;
use Classes\AdaptersDriver\Mssql;
use Classes\AdaptersDriver\Mysql;
use Classes\AdaptersDriver\Pgsql;
use Classes\AdaptersDriver\Sqlsrv;

require_once 'AdapterConfig/None.php';
require_once 'AdapterConfig/Phalcon.php';
require_once 'AdapterConfig/ZendFrameworkOne.php';
require_once 'AdaptersDriver/Dblib.php';
require_once 'AdaptersDriver/Mssql.php';
require_once 'AdaptersDriver/Mysql.php';
require_once 'AdaptersDriver/Pgsql.php';
require_once 'AdaptersDriver/Sqlsrv.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Config
{

    /**
     * @var string
     */
    public static $version = "1.5.0";

    /**
     * String that separates the parent section name
     *
     * @var string
     */
    protected $sectionSeparator = ':';

    /**
     * @var string
     */
    private $configIniDefault = '/configs/config.ini';

    /**
     * @var string
     */
    public $_basePath;
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

    private $frameworkList = array (
        'none' ,
        'zf1' ,
        'phalcon'
    );

    private $parameterList = array (
        'init'        => 'Creates the necessary configuration file to start using the orm-generator.' ,
        'name-ini'  => 'reference to another .ini file configuration (relative path).' ,
        'config-env'  => 'orm-generator configuration environment.' ,
        'framework'   => 'name framework used, which has the contents of the database configurations and framework template.' ,
        'driver'      => 'database driver name (Ex.: pgsql).' ,
        'database'    => 'database name.' ,
        'schema'      => 'database schema name (one or more than one).' ,
        'tables'      => 'table name (parameter can be used more then once).' ,
        'clean-trash' => 'delete all files that do not belong to your Database due' ,
        'status'      => 'show status of implementation carried out after completing the process.' ,
        'version'     => 'shows the version of orm-generator.' ,
        'help'        => "help command explaining all the options and manner of use." ,
        'path'        => "specify where to create the files (default is current directory)." ,
    );

    public function __construct ( $argv , $basePath , $numArgs )
    {
        if ( array_key_exists ( 'help' , $argv ) or ( $numArgs > 1
                                                      && count ( $argv ) < 1 )
        )
        {
            die ( $this->getUsage () );
        }
        if ( array_key_exists ( 'version' , $argv ) )
        {
            die ( $this->getVersion () );
        }
        if ( array_key_exists ( 'status' , $argv ) )
        {
            $argv[ 'status' ] = true;
        }

        $this->argv = $this->parseConfig ( $basePath , $argv );
    }

    /**
     * Lista de ajuda quando digita 'help'
     *
     * @return string
     */
    public function getUsage ()
    {
        $version = $this->getVersion ();
        $list = $this->renderParam ();

        return <<<EOF
parameters:
$list
 example: php generate.php --framework=zf1 --database=foo --tables=foobar --status

$version
EOF;
    }

    public function renderParam ()
    {
        $return = "";
        foreach ( $this->parameterList as $param => $desc )
        {
            if ( strlen ( $param ) < 5 )
            {
                $return .= "\t--" . $param . "\t\t: " . $desc . "\n";
            } else
            {
                $return .= "\t--" . $param . "\t: " . $desc . "\n";
            }

        }

        return $return;
    }

    public function checkHasNewVersion ()
    {
        $opts = array (
            'http' => array (
                'method' => 'GET' ,
                'header' => array (
                    'User-Agent: PHP'
                )
            )
        );

        try
        {
            $context = stream_context_create ( $opts );
            $tags = json_decode ( @file_get_contents ( "https://api.github.com/repos/pedro151/orm-generator/tags" , false , $context ) );

            $lastVersion = preg_replace ( "/[^0-9.]/" , "" , $tags[ 0 ]->name );
            if ( $lastVersion > static::$version )
            {
                return "\033[0;31mThere is a new version $lastVersion available:\033[0m https://github.com/pedro151/orm-generator\n";
            }
        } catch ( \Exception $ex ){ }
    }

    public function getVersion ()
    {
        $version = static::$version;

        return "ORM Generator \nVersion: $version\ncreated by: Pedro Alarcao <https://github.com/pedro151/orm-generator>\n".$this->checkHasNewVersion();
    }

    /**
     * Analisa e estrutura a Configuracao do generate
     *
     * @param  string $basePath
     * @param array   $argv
     *
     * @return array
     * @throws \Exception
     */
    private function parseConfig ( $basePath , $argv )
    {
        $this->_basePath = $basePath;

        $configIni = isset( $argv[ 'name-ini' ] )
            ? $argv[ 'name-ini' ]
            : $this->_basePath
              . $this->configIniDefault;

        $configTemp = $this->loadIniFile ( realpath ( $configIni ) );
        $configCurrent = self::parseConfigEnv ( $configTemp , $argv );

        if ( ! isset( $configCurrent[ 'framework' ] ) )
        {
            throw new \Exception( "configure which framework you want to use! \n" );
        }

        if ( ! in_array ( $configCurrent[ 'framework' ] , $this->frameworkList ) )
        {
            $frameworks = implode ( "\n\t" , $this->frameworkList );
            throw new \Exception( "list of frameworks: \n\t\033[1;33m" . $frameworks
                                  . "\n\033[0m" );
        }

        return $argv + array_filter ( $configCurrent );
    }

    /**
     *
     * @param $configTemp
     * @param $argv
     *
     * @return string
     */
    private static function parseConfigEnv ( $configTemp , $argv )
    {
        $thisSection = isset( $configTemp[ key ( $configTemp ) ][ 'config-env' ] )
            ? $configTemp[ key (
                $configTemp
            ) ][ 'config-env' ] : null;

        $thisSection = isset( $argv[ 'config-env' ] ) ? $argv[ 'config-env' ]
            : $thisSection;

        if ( isset( $configTemp[ $thisSection ][ 'extends' ] ) )
        {
            #faz marge da config principal com a config extendida
            return $configTemp[ $thisSection ]
                   + $configTemp[ $configTemp[ $thisSection ][ 'extends' ] ];
        }

        return $configTemp[ key ( $configTemp ) ];
    }

    /**
     * Carregar o arquivo ini e pré-processa o separador de seção ':'
     * no nome da seção (que é usado para a extensão seção) de modo a que a
     * matriz resultante tem os nomes de seção corretos e as informações de
     * extensão é armazenado em uma sub-ch ve
     *
     * @param string $filename
     *
     * @throws \Exception
     * @return array
     */
    protected function loadIniFile ( $filename )
    {
        if ( ! is_file ( $filename ) )
        {
            throw new \Exception( "\033[0;31mError: configuration file does not exist! \033[0m\n" );
        }

        $loaded = parse_ini_file ( $filename , true );
        $iniArray = array ();
        foreach ( $loaded as $key => $data )
        {
            $pieces = explode ( $this->sectionSeparator , $key );
            $thisSection = trim ( $pieces[ 0 ] );
            switch ( count ( $pieces ) )
            {
                case 1:
                    $iniArray[ $thisSection ] = $data;
                    break;

                case 2:
                    $extendedSection = trim ( $pieces[ 1 ] );
                    $iniArray[ $thisSection ] = array_merge ( array ( 'extends' => $extendedSection ) , $data );
                    break;

                default:
                    throw new \Exception( "Section '$thisSection' may not extend multiple sections in $filename" );
            }
        }

        return $iniArray;
    }

    /**
     * analisa a opção e cria a instancia do Atapter do determinado framework
     *
     * @return \Classes\AdapterConfig\AbstractAdapter
     *
     */
    private function factoryConfig ()
    {
        switch ( strtolower ( $this->argv[ 'framework' ] ) )
        {
            case 'zf1':
                return new ZendFrameworkOne( $this->argv );
            case 'phalcon':
                return new Phalcon( $this->argv );
            default:
                return new None( $this->argv );
        }

    }

    /**
     * Analisa a opção e instancia o determinado banco de dados
     *
     * @param AdapterConfig\AbstractAdapter $config
     *
     * @return AdaptersDriver\AbsractAdapter
     */
    private function factoryDriver ( AdapterConfig\AbstractAdapter $config )
    {
        switch ( $this->argv[ 'driver' ] )
        {
            case 'pgsql':
            case 'pdo_pgsql':
                return new Pgsql( $config );
            case 'mysql':
            case 'pdo_mysql':
                return new Mysql( $config );
            case 'mssql':
                return new Mssql( $config );
            case 'dblib':
                return new Dblib( $config );
            case 'sqlsrv':
                return new Sqlsrv( $config );
        }
    }

    /**
     * @return AdapterConfig\AbstractAdapter
     */
    public function getAdapterConfig ()
    {
        if ( ! $this->adapterConfig instanceof AbstractAdapter )
        {
            $this->adapterConfig = $this->factoryConfig ();
        }

        return $this->adapterConfig;
    }

    /**
     * @return AdaptersDriver\AbsractAdapter
     */
    public function getAdapterDriver ( AdapterConfig\AbstractAdapter $config )
    {
        if ( ! $this->adapterDriver )
        {
            $this->adapterDriver = $this->factoryDriver ( $config );
        }

        return $this->adapterDriver;
    }

}
