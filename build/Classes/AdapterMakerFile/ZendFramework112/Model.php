<?php

namespace Classes\AdapterMakerFile\ZendFramework112;
use Classes\AdapterMakerFile\AbstractAdapter;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Model extends AbstractAdapter
{
    /**
     * @var void
     */
    protected $fileTpl       = "model.php";


    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
       return array();
    }
}
