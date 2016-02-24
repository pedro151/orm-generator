<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/02/16
 * Time: 11:33
 */

namespace TestClasses\AdaptersDriver;


use Classes\AdaptersDriver\Pgsql;

class PgsqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type \Classes\AdapterConfig\ZendFrameworkOne
     */
    private $objAdapterConfig;
    /**
     * @type Pgsql
     */
    private $objDriver;

    protected function setUp ()
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
                'driver'    => 'pdo_pgsql' ,
                'host'      => 'localhost' ,
                'port'      => 5432 ,
                'schema'    => array ( 'bds' ) ,
                'database'  => 'sabido' ,
                'username'  => 'postgres' ,
                'socket'    => null ,
                'password'  => '123' ,
                'namespace' => ''
            )
        );

        return $property;
    }

    public function testPDO ()
    {
        $this->assertTrue ( $this->objDriver->getPDO () instanceof \PDO );
    }

    public function testGetListNameTable ()
    {
        $this->assertTrue ( is_array ( $this->objDriver->getListNameTable () ) );
        $this->assertTrue ( count ( $this->objDriver->getListNameTable () ) > 0 );
    }

    public function testGetListColumns ()
    {
        $this->assertTrue ( is_array ( $this->objDriver->getListColumns () ) );
    }

    public function testGetTables ()
    {
        $this->assertTrue (
            $this->objDriver->getTable ( "bds.bds_pessoa" ) instanceof
            \Classes\Db\DbTable
        );
        $arrTables = $this->objDriver->getTables ();
        $this->assertTrue ( $arrTables[ "bds.bds_pessoa" ] instanceof
                            \Classes\Db\DbTable );
    }

    public function testTotalTables ()
    {
        $this->assertTrue ( is_int ( $this->objDriver->getTotalTables () ) );
    }
}
