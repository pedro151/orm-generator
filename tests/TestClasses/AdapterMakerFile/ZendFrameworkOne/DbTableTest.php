<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/02/16
 * Time: 18:56
 */

namespace TestClasses\AdapterMakerFile\ZendFrameworkOne;

class DbTableTest extends \PHPUnit_Framework_TestCase
{

    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\ZendFrameworkOne\DbTable::getInstance ();
        $this->assertTrue ( $instance instanceof \Classes\AdapterMakerFile\ZendFrameworkOne\DbTable );
        $this->assertTrue ( $instance->getPastName () == "DbTable" );
        $this->assertTrue ( $instance->getFileTpl () == "dbtable.php" );
        $this->assertTrue ( $instance->getParentClass () == "TableAbstract" );
        $this->assertTrue ( $instance->getParentFileTpl () == "dbtable_abstract.php" );
    }
}
