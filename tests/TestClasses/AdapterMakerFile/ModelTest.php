<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 26/02/16
 * Time: 17:47
 */

namespace TestClasses\AdapterMakerFile;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\Model::getInstance ();
        $this->assertTrue ( $instance instanceof \Classes\AdapterMakerFile\Model );
        $this->assertTrue ( $instance->getPastName () == 'Model' );
        $this->assertTrue ( $instance->getParentClass () == "" );
        $this->assertTrue ( $instance->getParentFileTpl () == "" );
        $this->assertTrue ( $instance->getFileTpl () == "model.tpl" );
    }
}
