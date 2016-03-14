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

    /**
     * http://framework.zend.com/manual/1.12/en/zend.db.adapter.html#zend.db.adapter.example-database
     */
    protected function setUp ()
    {
        $this->pdo = new \PDO( $GLOBALS[ 'db_dsn' ], $GLOBALS[ 'db_username' ], $GLOBALS[ 'db_password' ] );
        $this->pdo->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        $this->pdo->query (
            "CREATE TABLE accounts (
      account_name      VARCHAR(100) NOT NULL PRIMARY KEY
    );"
        );

        $this->pdo->query (
            "CREATE TABLE products (
      product_id        INTEGER NOT NULL PRIMARY KEY,
      product_name      VARCHAR(100)
    );"
        );

        $this->pdo->query (
            "CREATE TABLE bugs (
      bug_id            INTEGER NOT NULL PRIMARY KEY,
      bug_description   VARCHAR(100),
      bug_status        VARCHAR(20),
      reported_by       VARCHAR(100) REFERENCES accounts(account_name),
      assigned_to       VARCHAR(100) REFERENCES accounts(account_name),
      verified_by       VARCHAR(100) REFERENCES accounts(account_name)
    );"
        );

        $this->pdo->query (
            "CREATE TABLE bugs_products (
      bug_id            INTEGER NOT NULL REFERENCES bugs,
      product_id        INTEGER NOT NULL REFERENCES products,
      PRIMARY KEY       (bug_id, product_id)
    );"
        );

        $this->objAdapterConfig = $this->getMockBuilder ( '\Classes\AdapterConfig\ZendFrameworkOne' )
            ->disableOriginalConstructor ( 0 )
            ->setMethods ( array ( 'getParams' ) )
            ->getMock ();

        $this->parseObj ();
        $this->objDriver = new Pgsql( $this->objAdapterConfig );
        $this->objDriver->runDatabase ();
    }

    protected function tearDown ()
    {
        $this->pdo->query ( "DROP TABLE bugs_products" );
        $this->pdo->query ( "DROP TABLE bugs" );
        $this->pdo->query ( "DROP TABLE products" );
        $this->pdo->query ( "DROP TABLE accounts" );
    }

    protected function parseObj ()
    {
        $class = new \ReflectionClass( '\Classes\AdapterConfig\ZendFrameworkOne' );
        $property = $class->getProperty ( 'arrConfig' );
        $property->setAccessible ( true );
        $property->setValue (
            $this->objAdapterConfig, array (
                                       'driver'    => 'pdo_pgsql',
                                       'host'      => 'localhost',
                                       'database'  => 'dao_generator',
                                       'username'  => 'postgres',
                                       'socket'    => null,
                                       'password'  => '123',
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
            $this->objDriver->getTable ( "public.accounts", "public" ) instanceof
            \Classes\Db\DbTable
        );
        $arrTables = $this->objDriver->getTables ( 'public' );
        $this->assertTrue ( $arrTables[ "public.accounts" ] instanceof \Classes\Db\DbTable );
    }

    public function testTotalTables ()
    {
        $this->assertTrue ( is_int ( $this->objDriver->getTotalTables () ) );
    }
}
