<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 22/02/16
 * Time: 15:29
 */

namespace TestPgsql\TestClasses;


use Classes\Config;
use Classes\MakerFile;

class MakerFileTest extends \PHPUnit_Framework_TestCase
{
    const FILE_EntityAbstract = "EntityAbstract.php";
    const FILE_TableAbstract  = "TableAbstract.php";
    public $basePath;

    protected function setUp ()
    {
        $this->basePath = __DIR__ . '/../../';
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
            null
        );

        $maker = new MakerFile(
            new Config(
                array (
                    'schema'    => array (
                        'public' ,
                        'quiz'
                    ) ,
                    'framework' => 'zf1'
                ) ,
                $this->basePath, 2
            )
        );

        $factoryFile = $maker->factoryMakerFile ();
        $this->assertTrue ( $factoryFile[ 0 ]->getPastName () === $names[ 0 ] );
        $this->assertTrue ( $factoryFile[ 1 ]->getPastName () === $names[ 1 ] );
        $this->assertTrue ( $factoryFile[ 2 ]->getPastName () === $names[ 2 ] );
    }

    public function testLocationDatabaseTrue ()
    {
        $maker = new MakerFile(
            new Config(
                array (
                    'folder-database' => true ,
                    'driver'          => 'pgsql' ,
                    'framework'       => 'zf1' ,
                    'schema'          => array (
                        'public' ,
                        'quiz' ,
                    )
                ) ,
                $this->basePath, 4
            )
        );

        $arrBase = array (
            $this->basePath ,
            'models' ,
            'Pgsql'
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
                    'driver'          => 'pgsql' ,
                    'framework'       => 'zf1' ,
                    'schema'          => array (
                        'public' ,
                        'quiz' ,
                    )
                ) ,
                $this->basePath, 4
            )
        );

        $arrBase = array (
            $this->basePath ,
            'models'
        );

        foreach ( $maker->location as $index => $item )
        {
            $arrBaseFinal = $arrBase;
            $arrBaseFinal[] = MakerFile::getClassName ( $index );
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
                    'database'        => $GLOBALS[ 'dbname' ] ,
                    'framework'       => 'zf1' ,
                    'driver'          => 'pgsql' ,
                    'schema'          => array ()
                ) ,
                $this->basePath,5
            )
        );


        $db = MakerFile::getClassName ( $maker->getConfig ()->getDatabase () );

        $arrBase = array (
            $this->basePath ,
            'models' ,
            $db
        );

        foreach ( $maker->location as $index => $item )
        {
            $location = implode ( DIRECTORY_SEPARATOR , array_filter ( $arrBase ) );
            $this->assertTrue ( $item == $location );
        }

    }
}
