<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/02/16
 * Time: 18:56
 */

namespace TestPgsql\TestClasses\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterConfig\Phalcon;
use Classes\AdaptersDriver\Pgsql;
use Classes\Db\Column;

class ModelTest extends \PHPUnit_Framework_TestCase
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

            $this->objDriver = new Pgsql( new Phalcon( $arrConfig ) );
            $this->objDriver->runDatabase ();
        }

        return $this->objDriver;
    }

    /**
     *
     */
    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\ZendFrameworkOne\DbTable::getInstance ();
        $this->assertTrue ( $instance instanceof
                            \Classes\AdapterMakerFile\ZendFrameworkOne\DbTable );
        $this->assertTrue ( $instance->getPastName () == "" );
        $this->assertTrue ( $instance->getFileTpl () == "entity.php" );
    }

    public function testColumns ()
    {
        $dbTable = $this->getDataBaseDrive ()->getTable ( 'bugs_products' , 'public' );
        $this->assertInternalType ( "array" , $dbTable->getColumns () );
        $this->assertTrue ( $dbTable->getColumn ( "bug_id" ) instanceof Column );
    }

    public function testForeingkey ()
    {
        $dbTable = $this->getDataBaseDrive ()->getTable ( 'bugs' , 'public' );
        $this->assertTrue ( count ( $dbTable->getColumns () )
                            >= count ( $dbTable->getForeingkeys () ) );
        $this->assertTrue ( 0 < count ( $dbTable->getForeingkeys () ) );
        $this->assertTrue ( $dbTable->getColumn ( 'reported_by' )->isForeingkey () );
        $this->assertFalse ( $dbTable->getColumn ( 'bug_description' )->isForeingkey () );
    }
}
