<?php
/**
 * Created by PhpStorm.
 * User: PEDRO151
 * Date: 20/02/2016
 * Time: 02:35
 */

namespace TestPgsql\TestClasses;


use Classes\AdaptersDriver\Pgsql;
use Classes\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $basePath;

    protected function setUp ()
    {
        $this->basePath = dirname ( $GLOBALS[ 'base_path' ] );

    }

    /**
     * @param $param1
     * @param $param2
     *
     * @return mixed
     */
    private function getReflectionMethodParseConfigEnv ( $configTemp , $argv )
    {
        $obj = $this->getMockBuilder ( 'Classes\Config' )
                    ->disableOriginalConstructor ()
                    ->getMock ();


        $reflectionMethod = new \ReflectionMethod( 'Classes\Config' , 'parseConfigEnv' );
        $reflectionMethod->setAccessible ( true );

        return $reflectionMethod->invokeArgs ( $obj , array ( $configTemp , $argv ) );
    }

    public function testAdapterDriver ()
    {
        $config = new Config(
            array (
                'framework' => 'none' ,
                'database'  => $GLOBALS[ 'dbname' ] ,
                'driver'    => 'pgsql'
            ) ,
            $this->basePath
        );

        $driver = $config->getAdapterDriver ();
        $table = $driver->getTables ();
        $this->assertTrue ( $driver instanceof Pgsql );
        $this->assertTrue ( is_array ( $table ) );
    }

    public function testAdapterConfig ()
    {
        $config = new Config(
            array (
                'database' => $GLOBALS[ 'dbname' ] ,
                'driver'   => 'pgsql'
            ) ,
            $this->basePath
        );
        $config = $config->getAdapterConfig ();
        $strAuthor = $config->author;
        $this->assertTrue ( $strAuthor == ucfirst ( get_current_user () ) );
        $this->assertTrue ( $config->lol == null );
    }

    public function testParseConfigEnvDefault ()
    {
        $param1 = array ( 'main' => 'configs' );
        $param2 = array ();
        $resp = $this->getReflectionMethodParseConfigEnv ( $param1 , $param2 );
        $this->assertEquals ( 'configs' , $resp );
    }

    public function testParseConfigEnvArgs ()
    {
        $param1 = array (
            'main'    => array (
                "framework"  => "zend_framework" ,
                "database"   => "main" ,
                "config-env" => "config2"
            ) ,
            'config1' => array (
                "extends"  => "main" ,
                "database" => "config1"
            ) ,
            'config2' => array (
                "extends"  => "main" ,
                "database" => "config2"
            ) ,
        );
        $param2 = array ( 'config-env' => 'config1' );

        $expectedConfig1 = array (
            "extends"    => "main" ,
            "framework"  => "zend_framework" ,
            "config-env" => "config2" ,
            "database"   => "config1"
        );
        $resp1 = $this->getReflectionMethodParseConfigEnv ( $param1 , $param2 );
        $this->assertEquals ( $expectedConfig1 , $resp1 );

        $expectedConfig2 = array (
            "extends"    => "main" ,
            "framework"  => "zend_framework" ,
            "config-env" => "config2" ,
            "database"   => "config2"
        );
        $resp2 = $this->getReflectionMethodParseConfigEnv ( $param1 , array () );
        $this->assertEquals ( $expectedConfig2 , $resp2 );

    }
}
