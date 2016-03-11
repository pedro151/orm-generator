<?php
/**
 * Created by PhpStorm.
 * User: PEDRO151
 * Date: 20/02/2016
 * Time: 02:35
 */

namespace TestClasses;


use Classes\AdaptersDriver\Pgsql;
use Classes\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testAdapterDriver ()
    {
        $configIni = realpath ( __DIR__ . '/../../configs/config.ini' );

        $config = new Config( array (
            'framework' => 'none' ,
            'database'  => 'dao_generator' ,
            'driver'    => 'pgsql'
        ), $configIni );

        $driver = $config->getAdapterDriver ();
        $table = $driver->getTables ();
        $this->assertTrue ( $driver instanceof Pgsql );
        $this->assertTrue ( is_array ( $table ) );
    }

    public function testAdapterConfig ()
    {
        $configIni = realpath ( __DIR__ . '/../../configs/config.ini' );

        $config = new Config( array (
            'database' => 'dao_generator' ,
            'driver'   => 'pgsql'
        ), $configIni );
        $config = $config->getAdapterConfig ();
        $strAuthor = $config->author;
        $this->assertTrue ( $strAuthor == ucfirst ( get_current_user () ) );
        $this->assertTrue ( $config->lol == null );
    }
}
