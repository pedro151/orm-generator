<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/02/16
 * Time: 11:33
 */

namespace TestPgsql\TestClasses\AdaptersDriver;

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
        $this->tearDown ();

        $this->pdo->exec (
            "
            CREATE TABLE accounts (
              account_name      VARCHAR(100) NOT NULL PRIMARY KEY
            );

            CREATE TABLE products (
                  product_id        INTEGER NOT NULL PRIMARY KEY,
                  product_name      VARCHAR(100)
                );

              CREATE TABLE bugs (
                  bug_id            SERIAL NOT NULL PRIMARY KEY,
                  bug_description   VARCHAR(100),
                  bug_status        VARCHAR(20),
                  reported_by       VARCHAR(100) REFERENCES accounts(account_name),
                  assigned_to       VARCHAR(100) REFERENCES accounts(account_name),
                  verified_by       VARCHAR(100) REFERENCES accounts(account_name)
                );

            CREATE TABLE bugs_products (
              bug_id            INTEGER NOT NULL REFERENCES bugs,
              product_id        INTEGER NOT NULL REFERENCES products,
              PRIMARY KEY       (bug_id, product_id)
            );

            CREATE SEQUENCE products_product_id_seq;
            ALTER TABLE products ALTER COLUMN product_id SET DEFAULT NEXTVAL(  'public.products_product_id_seq'::regclass );"
        );
    }

    /**
     *
     */
    protected function tearDown ()
    {
        $this->pdo->exec (
            "DROP TABLE IF EXISTS bugs_products;
             DROP TABLE IF EXISTS  bugs;
             DROP TABLE IF EXISTS  products;
             DROP TABLE IF EXISTS  accounts;
             DROP SEQUENCE IF EXISTS products_product_id_seq;"
        );
    }

    /**
     * @return \Classes\AdaptersDriver\Pgsql
     */
    protected function getDataBaseDrive ()
    {
        if ( ! $this->objDriver )
        {
            $arrConfig = array (
                'driver'    => 'pdo_pgsql' ,
                'host'      => 'localhost' ,
                'database'  => $GLOBALS[ 'dbname' ],
                'username'  => $GLOBALS[ 'db_username' ],
                'password'  => $GLOBALS[ 'db_password' ],
                'namespace' => ''
            );

            $this->objDriver = new Pgsql( new None( $arrConfig ) );
            $this->objDriver->runDatabase ();
        }

        return $this->objDriver;
    }

    /**
     *
     */
    public function testPDO ()
    {
        $this->assertTrue ( $this->getDataBaseDrive ()->getPDO () instanceof \PDO );
    }

    public function testRunDatabase ()
    {
        $daoClone = clone $this->getDataBaseDrive ();
        $this->getDataBaseDrive ()->runDatabase ();
        $this->assertEquals ( $daoClone , $this->getDataBaseDrive () );
    }

    public function testSQLSequence ()
    {
        $this->assertEquals (
            'public.bugs_bug_id_seq' , $this->getDataBaseDrive ()
                                            ->getSequence ( 'public.bugs' , 'bug_id' )
        );
        $this->assertEquals (
            'products_product_id_seq' , $this->getDataBaseDrive ()
                                             ->getSequence ( 'public.products' , 'product_id' )
        );
    }

    /**
     *
     */
    public function testSQLConstrants ()
    {
        $arrConrstrants = $this->getDataBaseDrive ()->getListConstrant ();

        foreach ( $arrConrstrants as $index => $contrstrant )
        {
            if ( $contrstrant[ 'table_name' ] == 'bugs' )
            {
                switch ( $contrstrant[ 'constraint_type' ] )
                {
                    case "FOREIGN KEY":
                    {
                        $this->assertEquals ( 'accounts' , $contrstrant[ "foreign_table" ] );
                        $this->assertEquals ( 'account_name' , $contrstrant[ "foreign_column" ] );
                        break;
                    }
                    case "PRIMARY KEY":
                    {
                        $this->assertEquals ( 'bugs' , $contrstrant[ "foreign_table" ] );
                    }

                };
            }
        }

    }

    /**
     *
     */
    public function testGetListNameTable ()
    {
        $this->assertTrue (
            is_array (
                $this->getDataBaseDrive ()
                     ->getListNameTable ()
            )
        );
        $this->assertTrue (
            count ( $this->getDataBaseDrive ()->getListNameTable () )
            > 0
        );
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
            $this->getDataBaseDrive ()->getTable ( "accounts" , "public" )
            instanceof
            \Classes\Db\DbTable
        );
        $arrTables = $this->getDataBaseDrive ()->getTables ( 'public' );
        $this->assertTrue (
            $arrTables[ "accounts" ] instanceof
            \Classes\Db\DbTable
        );
    }

    /**
     *
     */
    public function testTotalTables ()
    {
        $this->assertTrue ( is_int ( $this->getDataBaseDrive ()->getTotalTables () ) );
    }
}
