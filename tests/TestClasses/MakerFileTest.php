<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 22/02/16
 * Time: 15:29
 */

namespace TestClasses;


use Classes\Config;
use Classes\MakerFile;

class MakerFileTest extends \PHPUnit_Framework_TestCase
{

    public function testFactory ()
    {
        $names = array (
            'DbTable' ,
            'Entity' ,
            ''
        );

        $configIni = realpath ( __DIR__ . '/../../configs/config.ini' );

        $maker = new MakerFile( new Config( array (
            'schema' => array (
                'public' , 'quiz'
            )
        ) , $configIni ) );
        foreach ( $maker->factoryMakerFile () as $key => $obj )
        {
            $this->assertTrue ( $obj->getPastName () == $names[ $key ] );
        }
    }

    public function testLocation ()
    {
        $configIni = realpath ( __DIR__ . '/../../configs/config.ini' );

        $maker = new MakerFile( new Config( array (
            'database' => 'postgres' ,
            'schema'   => array (
                'public'
            )
        ) , $configIni ) );

        // $maker->run ();
    }
}
