<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 22/02/16
 * Time: 15:29
 */

namespace TestMysql\TestClasses;


use Classes\Config;
use Classes\MakerFile;

class MakerFileTest extends \PHPUnit_Framework_TestCase
{
    const FILE_EntityAbstract = "EntityAbstract.php";
    const FILE_TableAbstract  = "TableAbstract.php";
    public $basePath;

    protected function setUp ()
    {
        $this->basePath = dirname ( $GLOBALS[ 'base_path' ] );
    }

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
                    } else
                    {
                        unlink ( $dir . "/" . $object );
                    }
                }
            }
            rmdir ( $dir );
        }
    }

    public function testFactory ()
    {
        $names = array (
            'DbTable' ,
            'Entity' ,
            ''
        );

        $maker = new MakerFile(
            new Config(
                array (
                    'schema' => array (
                        'public' ,
                        'quiz'
                    )
                ) ,
                $this->basePath
            )
        );
        foreach ( $maker->factoryMakerFile () as $key => $obj )
        {
            $this->assertTrue ( $obj->getPastName () == $names[ $key ] );
        }
    }

    public function testLocationDatabaseTrue ()
    {
        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => true ,
                    'driver'          => 'mysql' ,
                    'schema'          => array (
                        'public' ,
                        'quiz' ,
                    )
                ) ,
                $this->basePath
            )
        );

        $arrBase = array (
            $this->basePath ,
            'models' ,
            'Mysql'
        );

        foreach ( $maker->location as $index => $item )
        {
            $arrBaseFinal = $arrBase;
            $arrBaseFinal[] = ucfirst ( $index );
            $location = implode ( DIRECTORY_SEPARATOR , filter_var_array ( $arrBaseFinal ) );
            $this->assertTrue ( $item == $location );
            unset( $arrBaseFinal );
        }
    }

    public function testLocationDatabaseFalse ()
    {
        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => false ,
                    'driver'          => 'mysql' ,
                    'schema'          => array (
                        'public' ,
                        'quiz' ,
                    )
                ) ,
                $this->basePath
            )
        );

        $arrBase = array (
            $this->basePath ,
            'models'
        );

        foreach ( $maker->location as $index => $item )
        {
            $arrBaseFinal = $arrBase;
            $arrBaseFinal[] = MakerFile::getClassName( $index );
            $location = implode ( DIRECTORY_SEPARATOR , array_filter ( $arrBaseFinal ) );
            $this->assertTrue ( $item == $location );
            unset( $arrBaseFinal );
        }
    }

    public function testLocationSchemaOff ()
    {
        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => false ,
                    'database'  => $GLOBALS[ 'dbname' ],
                    'driver'          => 'mysql' ,
                    'schema'          => array ()
                ) ,
                $this->basePath
            )
        );


        $db = MakerFile::getClassName( $maker->getConfig()->getDatabase());

        $arrBase = array (
            $this->basePath ,
            'models',
            $db
        );

        foreach ( $maker->location as $index => $item )
        {
            $location = implode ( DIRECTORY_SEPARATOR , array_filter ( $arrBase ) );
            $this->assertTrue ( $item == $location );
        }

    }


    public function testLocationDatabaseCustom ()
    {
        $maker = new MakerFile(
            new Config(
                array (
                    'folder-name' => 'db' ,
                    'driver'          => 'mysql' ,
                    'schema'          => array (
                        'public' ,
                        'quiz' ,
                    )
                ) ,
                $this->basePath
            )
        );

        $arrBase = array (
            $this->basePath ,
            'models' ,
            'Db'
        );

        foreach ( $maker->location as $index => $item )
        {
            $arrBaseFinal = $arrBase;
            $arrBaseFinal[] = ucfirst ( $index );
            $location = implode ( DIRECTORY_SEPARATOR , filter_var_array ( $arrBaseFinal ) );
            $this->assertTrue ( $item == $location );
            unset( $arrBaseFinal );
        }
    }
}
