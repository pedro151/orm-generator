<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;
use Classes\AdapterMakerFile\AbstractAdapter;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Model extends AbstractAdapter
{
    /**
     * @var void
     */
    public    $pastName      = 'Model';
    protected $parentClass   = "";
    protected $parentFileTpl = "";
    protected $fileTpl       = "model.tpl";

    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        // TODO: Implement parseRelation() method.
    }
}
