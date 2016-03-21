<?php
namespace Classes;

use Classes\Maker\Template;

require_once 'Maker/Template.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class MakerConfigFile
{

    private $_basePath;

    private $configs = array (
        'name'       => 'config' ,
        'framework'  => 'none' ,
        'driver'     => 'pgsql' ,
        'enviroment' => 'dev' ,
        'host'       => 'localhost' ,
        'database'   => null ,
        'schema'     => null ,
        'username'   => null ,
        'password'   => null
    );

    public function __construct ( $argv , $basePath )
    {
        if ( array_key_exists ( 'help' , $argv ) )
        {
            die ( $this->getUsage () );
        }
        if ( array_key_exists ( 'status' , $argv ) )
        {
            $argv[ 'status' ] = true;
        }

        $this->argv = $this->parseConfig ( $basePath , $argv );
    }

    /**
     * Analisa e estrutura a Configuracao do generate
     *
     * @param string $_path
     * @param array  $argv
     *
     * @return array
     * @throws \Exception
     */
    private function parseConfig ( $basePath , $argv )
    {
        $this->_basePath = dirname ( $basePath );

        if ( ! isset( $argv[ 'framework' ] )
        )
        {
            echo "configure which framework you want to use! \n";
        }


        return $argv + array_filter ( $this->configs );
    }

    public function init ()
    {
        Template::makeDir ( $this->_basePath . DIRECTORY_SEPARATOR . "configs" );
        Template::makeSourcer ();

    }
}