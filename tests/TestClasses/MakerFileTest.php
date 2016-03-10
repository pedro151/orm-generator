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
        $maker = new MakerFile( new Config( array (
            'schema' => array (
                'public' , 'quiz'
            )
        ) ) );
        foreach ( $maker->factoryMakerFile () as $key => $obj )
        {
            $this->assertTrue ( $obj->getPastName () == $names[ $key ] );
        }
    }

    public function testLocation ()
    {
        $maker = new MakerFile( new Config( array (
            'database' => 'postgres' ,
            'schema'   => array (
                'public'
            )
        ) ) );

       // $maker->run ();
    }
}
