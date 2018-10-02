<?php

namespace Classes\AdapterMakerFile\ZendFramework112;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterConfig\ZendFramework112;
use Classes\Maker\AbstractMaker;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Mapper extends AbstractAdapter
{

    /**
     * @var void
     */
    public    $pastName      = 'Mapper';
    protected $fileTpl       = "mapper.php";
    protected $fileFixedData = array (
        'parentclass' => array (
            'name' => "MapperAbstract" ,
            'tpl'  => "mapper_abstract.php"
        )
    );

    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        return array();
    }

}
