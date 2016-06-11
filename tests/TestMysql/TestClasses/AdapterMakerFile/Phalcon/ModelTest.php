<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/02/16
 * Time: 18:56
 */

namespace TestMysql\TestClasses\AdapterMakerFile\Phalcon;

use Classes\AdapterConfig\Phalcon;
use Classes\AdaptersDriver\Mysql;
use Classes\Db\Column;


class ModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @type Mysql
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
            "CREATE TABLE accounts (
       account_name      VARCHAR(100) NOT NULL PRIMARY KEY
);

CREATE TABLE products (
      product_id        INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
      product_name      VARCHAR(100)
);

CREATE TABLE bugs (
    bug_id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    bug_description   VARCHAR(100),
    bug_status        VARCHAR(20),
    reported_by       VARCHAR(100),
    assigned_to       VARCHAR(100),
    verified_by       VARCHAR(100),
  FOREIGN KEY (reported_by)
	REFERENCES accounts(account_name),
  FOREIGN KEY (assigned_to)
	REFERENCES accounts(account_name),
  FOREIGN KEY (verified_by)
	REFERENCES accounts(account_name)
);

CREATE TABLE bugs_products (
    bug_id            INTEGER NOT NULL ,
    product_id        INTEGER NOT NULL,
    PRIMARY KEY       (bug_id, product_id),
    FOREIGN KEY (bug_id)
		REFERENCES bugs(bug_id),
	FOREIGN KEY (product_id)
		REFERENCES products(product_id)
);"
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
     * @return \Classes\AdaptersDriver\Mysql
     */
    protected function getDataBaseDrive ()
    {
        if ( ! $this->objDriver )
        {
            $arrConfig = array (
                'driver'    => 'pdo_mysql' ,
                'host'      => 'localhost' ,
                'framework' => 'phalcon',
                'database'  => $GLOBALS[ 'dbname' ],
                'username'  => $GLOBALS[ 'db_username' ],
                'password'  => $GLOBALS[ 'db_password' ],
                'namespace' => ''
            );

            $this->objDriver = new Mysql( new Phalcon( $arrConfig ) );
            $this->objDriver->runDatabase ();
        }

        return $this->objDriver;
    }

    /**
     *
     */
    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\Phalcon\Entity::getInstance();
        $this->assertTrue ( $instance instanceof
                            \Classes\AdapterMakerFile\Phalcon\Entity );
        $this->assertTrue ( $instance->getPastName () == "entity" );
        $this->assertTrue ( $instance->getFileTpl () == "entity.php" );
    }

    public function testColumns ()
    {
        $dbTable = $this->getDataBaseDrive ()->getTable ( 'bugs_products' );
        $this->assertInternalType ( "array" , $dbTable->getColumns () );
        $this->assertTrue ( $dbTable->getColumn ( "bug_id" ) instanceof Column );
    }

    public function testForeingkey ()
    {
        $dbTable = $this->getDataBaseDrive ()->getTable ( 'bugs'  );
        $this->assertTrue ( count ( $dbTable->getColumns () )
                            >= count ( $dbTable->getForeingkeys () ) );
        $this->assertTrue ( 0 < count ( $dbTable->getForeingkeys () ) );
        $this->assertTrue ( $dbTable->getColumn ( 'reported_by' )->isForeingkey () );
        $this->assertFalse ( $dbTable->getColumn ( 'bug_description' )->isForeingkey () );
    }
}
