<?php

namespace TestPgsql\TestClasses\Update\Content;

use Classes\Update\Content\GitHub;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type File
     */
    private $obj;

    protected function setUp ()
    {

    }

    protected function tearDown ()
    {
    }

    public function testGetInfoGitHub ()
    {
        var_dump ( GitHub::getInstance ()->getLastPhar () );
        exit();
    }
}
