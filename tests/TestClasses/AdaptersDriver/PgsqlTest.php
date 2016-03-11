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
        $this->pdo = new \PDO( $GLOBALS[ 'db_dsn' ] , $GLOBALS[ 'db_username' ] , $GLOBALS[ 'db_password' ] );
        $this->pdo->setAttribute ( \PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION );
        $this->pdo->query ( "CREATE TABLE dao (test VARCHAR(50) NOT NULL)" );

        $this->objAdapterConfig = $this->getMockBuilder ( '\Classes\AdapterConfig\ZendFrameworkOne' )
                                       ->disableOriginalConstructor ( 0 )
                                       ->setMethods ( array ( 'getParams' ) )
                                       ->getMock ();

        $this->parseObj ();
        $this->objDriver = new Pgsql( $this->objAdapterConfig );
    }

    protected function tearDown ()
    {
        $this->pdo->query ( "DROP TABLE dao" );
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
                'database'  => 'dao_generator' ,
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
            $this->objDriver->getTable ( "public.dao", "public" ) instanceof
            \Classes\Db\DbTable
        );
        $arrTables = $this->objDriver->getTables ('public');
        $this->assertTrue ( $arrTables[ "public.dao" ] instanceof \Classes\Db\DbTable );
    }

    public function testTotalTables ()
    {
        $this->assertTrue ( is_int ( $this->objDriver->getTotalTables () ) );
    }
}
