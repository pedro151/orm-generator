<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 26/02/16
 * Time: 17:45
 */

namespace TestClasses\AdapterMakerFile;


class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstace ()
    {
        $instance = \Classes\AdapterMakerFile\Entity::getInstance ();
        $this->assertTrue ( $instance instanceof \Classes\AdapterMakerFile\Entity );
        $this->assertTrue ( $instance->getPastName () == 'Entity' );
        $this->assertTrue ( $instance->getParentClass () == "EntityAbstract" );
        $this->assertTrue ( $instance->getParentFileTpl () == "entity_abstract.tpl" );
        $this->assertTrue ( $instance->getFileTpl () == "entity.tpl" );
    }
}
