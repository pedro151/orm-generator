<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 26/02/16
 * Time: 17:47
 */

namespace TestClasses\AdapterMakerFile\ZendFrameworkOne;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\ZendFrameworkOne\Model::getInstance ();
        $this->assertTrue ( $instance instanceof \Classes\AdapterMakerFile\ZendFrameworkOne\Model );
        $this->assertTrue ( $instance->getPastName () == '' );
        $this->assertTrue ( $instance->getParentClass () == "" );
        $this->assertTrue ( $instance->getParentFileTpl () == "" );
        $this->assertTrue ( $instance->getFileTpl () == "model.php" );
    }
}
