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
    const FILE_EntityAbstract = "EntityAbstract.php";
    const FILE_TableAbstract  = "TableAbstract.php";
    public $path;

    private function rrmdir ( $dir )
    {
        if ( is_dir ( $dir ) )
        {
            $objects = scandir ( $dir );
            foreach ( $objects as $object )
            {
                if ( $object != "." && $object != ".." )
                {
                    if ( is_dir ( $dir . "/" . $object ) )
                    {
                        $this->rrmdir ( $dir . "/" . $object );
                    }
                    else
                    {
                        unlink ( $dir . "/" . $object );
                    }
                }
            }
            rmdir ( $dir );
        }
    }


    protected function setUp ()
    {
        //C:\Apache24\htdocs\DAO-Generator\models\"
        $this->path = realpath ( __DIR__ . '/../../' );
    }


    public function testFactory ()
    {
        $names = array (
            'DbTable',
            'Entity',
            ''
        );

        $configIni = $this->path . '/configs/config.ini';

        $maker = new MakerFile(
            new Config(
                array (
                    'schema' => array (
                        'public',
                        'quiz'
                    )
                ), $configIni
            )
        );
        foreach ( $maker->factoryMakerFile () as $key => $obj )
        {
            $this->assertTrue ( $obj->getPastName () == $names[ $key ] );
        }
    }

    public function testLocationDatabaseTrue ()
    {
        $configIni = $this->path . '/configs/config.ini';

        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => true,
                    'driver'          => 'pgsql',
                    'schema'          => array (
                        'public',
                        'quiz',
                    )
                ), $configIni
            )
        );

        foreach ( $maker->location as $index => $item )
        {
            $this->assertTrue ( $item == '\models\Pgsql\\' . ucfirst ( $index ) );
        }
    }


    public function testLocationDatabaseFalse ()
    {
        $configIni = $this->path . '/configs/config.ini';

        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => false,
                    'driver'          => 'pgsql',
                    'schema'          => array (
                        'public',
                        'quiz',
                    )
                ), $configIni
            )
        );

        foreach ( $maker->location as $index => $item )
        {
            $this->assertTrue ( $item == '\models\\' . ucfirst ( $index ) );
        }
    }

    public function testLocationSchemaOff ()
    {
        $configIni = $this->path . '/configs/config.ini';

        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => false,
                    'driver'          => 'pgsql',
                    'schema'          => array ()
                ), $configIni
            )
        );

        foreach ( $maker->location as $index => $item )
        {
            $this->assertTrue ( $item == '\models' );
        }

    }
}
