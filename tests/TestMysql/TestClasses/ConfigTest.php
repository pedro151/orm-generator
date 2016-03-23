<?php
/**
 * Created by PhpStorm.
 * User: PEDRO151
 * Date: 20/02/2016
 * Time: 02:35
 */

namespace TestMysql\TestClasses;


use Classes\AdaptersDriver\Pgsql;
use Classes\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp ()
    {
        $this->basePath =  dirname($GLOBALS[ 'base_path' ]);
    }


    public function testAdapterDriver ()
    {
        $config = new Config(
            array (
                'framework' => 'none',
                'database'  => 'dao_generator',
                'driver'    => 'pgsql'
            ),
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
                'database' => 'dao_generator',
                'driver'   => 'pgsql'
            ),
            $this->basePath
        );
        $config = $config->getAdapterConfig ();
        $strAuthor = $config->author;
        $this->assertTrue ( $strAuthor == ucfirst ( get_current_user () ) );
        $this->assertTrue ( $config->lol == null );
    }
}
