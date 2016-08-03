<?php

namespace TestPgsql\TestClasses\AdapterConfig;


class ZendFrameworkOneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp ()
    {
        $this->pdo = new \PDO( $GLOBALS[ 'db_dsn' ], $GLOBALS[ 'db_username' ], $GLOBALS[ 'db_password' ] );
        $this->pdo->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        $this->pdo->query ( "CREATE TABLE dao (test VARCHAR(50) NOT NULL)" );
    }

    protected function tearDown ()
    {
        $this->pdo->query ( "DROP TABLE dao" );
    }

    protected static function getMethod ( $name )
    {
        $class  = new \ReflectionClass( '\Classes\AdapterConfig\ZendFrameworkOne' );
        $method = $class->getMethod ( $name );
        $method->setAccessible ( true );

        return $method;
    }

    /**
     * IsValid  populado deve retornar True
     */
    public function testIsValidTrue ()
    {
        $config = array (
            'driver'   => 'pdo_pgsql',
            'host'     => 'localhost',
            'database' => $GLOBALS[ 'dbname' ],
            'username' => $GLOBALS[ 'db_username' ],
            'password' => $GLOBALS[ 'db_password' ],
        );
        $obj    = new \Classes\AdapterConfig\None( $config );

        $valid = self::getMethod ( 'isValid' );
        $resp  = $valid->invoke ( $obj );
        $this->assertTrue ( $resp, "IsValid populado deve retornar True" );
    }

    public function testTypeConvert ()
    {
        $config = array (
            'driver'   => 'pdo_pgsql',
            'host'     => 'localhost',
            'database' => $GLOBALS[ 'dbname' ],
            'username' => $GLOBALS[ 'db_username' ],
            'password' => $GLOBALS[ 'db_password' ],
        );
        $obj    = new \Classes\AdapterConfig\Phalcon( $config );
        $this->assertTrue('integer'===$obj->convertTypeToTypeFramework('int'));
    }

    /**
     * Testa a Exception do construtor caso falte parametro
     */
    public function testException ()
    {
        $this->setExpectedException (
            'Classes\AdapterConfig\Exception'
        );

        $obj = $this->getMockBuilder ( '\Classes\AdapterConfig\ZendFrameworkOne' )
                    ->setConstructorArgs ( array ( array () ) )
                    ->setMethods (
                        array (
                            'getParams',
                            'parseFrameworkConfig'
                        )
                    )
                    ->getMock ();

        $obj->expects ( $this->any () )
            ->method ( 'getParams' )
            ->will ( $this->returnValue ( array () ) );
    }
}
