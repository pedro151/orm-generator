<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;
use Classes\AdapterMakerFile\AbstractAdapter;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/ORM-Generator
 */
class Entity extends AbstractAdapter
{
    /**
     * @var void
     */
    public    $pastName      = "";
    protected $parentClass   = "";
    protected $parentFileTpl = "";
    protected $fileTpl       = "entity.php";

    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
       return array();
    }
}
