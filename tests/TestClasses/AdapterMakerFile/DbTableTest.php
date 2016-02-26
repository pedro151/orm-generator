<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/02/16
 * Time: 18:56
 */

namespace TestClasses\AdapterMakerFile;


use Classes\AdaptersDriver\Pgsql;

class DbTableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @type \Classes\AdapterConfig\ZendFrameworkOne
     */
    private $objAdapterConfig;
    /**
     * @type Pgsql
     */
    private $objDriver;

    public function setUp ()
    {




        $this->objAdapterConfig = $this->getMockBuilder ( '\Classes\AdapterConfig\ZendFrameworkOne' )
                                       ->disableOriginalConstructor ( 0 )
                                       ->setMethods ( array ( 'getParams' ) )
                                       ->getMock ();

        $this->parseObj ();
        $this->objDriver = new Pgsql( $this->objAdapterConfig );
    }

    protected function parseObj ()
    {
        $class = new \ReflectionClass( '\Classes\AdapterConfig\ZendFrameworkOne' );
        $property = $class->getProperty ( 'arrConfig' );
        $property->setAccessible ( true );
        $property->setValue (
            $this->objAdapterConfig , array (
                'framework' => "zend_framework" ,
                'driver'    => 'pdo_pgsql' ,
                'host'      => 'localhost' ,
                'port'      => 5432 ,
                'schema'    => array ( 'teste' ) ,
                'database'  => 'postgres' ,
                'username'  => 'postgres' ,
                'socket'    => null ,
                'password'  => '123' ,
                'namespace' => ''
            )
        );

        return $property;
    }

    public function testGetRelationName ()
    {
        $this->assertTrue( \Classes\AdapterMakerFile\DbTable::getInstance () instanceof \Classes\AdapterMakerFile\DbTable );
    }
}
