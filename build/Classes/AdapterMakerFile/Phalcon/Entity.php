<?php

namespace Classes\AdapterMakerFile\Phalcon;

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
    public    $pastName      = "entity";
    protected $fileTpl       = "entity.php";
    protected $overwrite     = true;

    protected $validFunc = array ();

    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {

        $parents = array ();
        $depends = array ();
        foreach ( $dbTable->getForeingkeys () as $objColumn )
        {}

        foreach ( $dbTable->getDependences () as $objColumn )
        {
            foreach ( $objColumn->getDependences () as $dependence )
            {}
        }

        return array (
            'parents' => $parents,
            'depends' => $depends
        );

    }

}
