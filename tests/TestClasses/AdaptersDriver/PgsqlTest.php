<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/02/16
 * Time: 11:33
 */

namespace TestClasses\AdaptersDriver;

use Classes\AdapterConfig\None;
use Classes\AdaptersDriver\Pgsql;

/**
 * Class PgsqlTest
 *
 * @package TestClasses\AdaptersDriver
 */
class PgsqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type Pgsql
     */
    private $objDriver;

    /**
     * http://framework.zend.com/manual/1.12/en/zend.db.adapter.html#zend.db.adapter.example-database
     */
    protected function setUp ()
    {
        $this->pdo = new \PDO( $GLOBALS[ 'db_dsn' ] , $GLOBALS[ 'db_username' ] , $GLOBALS[ 'db_password' ] );
        $this->pdo->setAttribute ( \PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION );
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

    }

    /**
     *
     */
    protected function tearDown ()
    {
        $this->pdo->query ( "DROP TABLE bugs_products" );
        $this->pdo->query ( "DROP TABLE bugs" );
        $this->pdo->query ( "DROP TABLE products" );
        $this->pdo->query ( "DROP TABLE accounts" );
    }

    /**
     * @return \Classes\AdaptersDriver\Pgsql
     */
    protected function getDataBaseDrive ()
    {
        if ( ! $this->objDriver )
        {
            $this->objDriver = new Pgsql( $this->getAdapterConfig () );
            $this->objDriver->runDatabase ();
        }

        return $this->objDriver;
    }

    /**
     * @return \Classes\AdapterConfig\None
     */
    protected function getAdapterConfig ()
    {
        $arrConfig = array (
            'driver'    => 'pdo_pgsql' ,
            'host'      => 'localhost' ,
            'database'  => 'dao_generator' ,
            'username'  => 'postgres' ,
            'socket'    => null ,
            'password'  => '123' ,
            'namespace' => ''
        );

        return new None( $arrConfig );
    }

    /**
     *
     */
    public function testPDO ()
    {
        $this->assertTrue ( $this->getDataBaseDrive ()->getPDO () instanceof \PDO );
    }

    /**
     *
     */
    public function testGetListNameTable ()
    {
        $this->assertTrue ( is_array ( $this->getDataBaseDrive ()
                                            ->getListNameTable () ) );
        $this->assertTrue ( count ( $this->getDataBaseDrive ()->getListNameTable () )
                            > 0 );
    }

    /**
     *
     */
    public function testGetListColumns ()
    {
        $this->assertTrue ( is_array ( $this->getDataBaseDrive ()->getListColumns () ) );
    }

    /**
     *
     */
    public function testGetTables ()
    {
        $this->assertTrue (
            $this->getDataBaseDrive ()->getTable ( "public.accounts" , "public" )
            instanceof
            \Classes\Db\DbTable
        );
        $arrTables = $this->getDataBaseDrive ()->getTables ( 'public' );
        $this->assertTrue ( $arrTables[ "public.accounts" ] instanceof
                            \Classes\Db\DbTable );
    }

    /**
     *
     */
    public function testTotalTables ()
    {
        $this->assertTrue ( is_int ( $this->getDataBaseDrive ()->getTotalTables () ) );
    }
}
