<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterConfig\ZendFrameworkOne;
/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class DbTable extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $pastName      = 'DbTable';
    protected $parentClass   = "TableAbstract";
    protected $parentFileTpl = "dbtable_abstract.tpl";
    protected $fileTpl       = "dbtable.tpl";


    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return string[]
     */
    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        $referenceMap = '';
        $references = array ();
        $dependentTables = '';
        $dependents = array ();
        foreach ( $dbTable->getForeingkeys () as $fk )
        {
            $constrant = $fk->getFks ();
            $references[] = sprintf (
                "
       '%s' => array (
            'columns'       => '%s' ,
            'refTableClass' => '%s',
            'refColumns'    =>'%s'
       )",
                $constrant->getNameConstrant (),
                $fk->getName (),
                $dbTable->getNamespace ()
                . ZendFrameworkOne::SEPARETOR
                . 'Dbtable'
                . ZendFrameworkOne::SEPARETOR
                . $makerFile->getClassName ( $dbTable->getName () ),
                $constrant->getColumn ()

            );
        }

        if ( sizeof ( $references ) > 0 )
        {
            $referenceMap = "protected \$_referenceMap = array(" .
                join ( ',', $references ) . "\n    );";
        }

        foreach ( $dbTable->getDependences () as $objColumn )
        {
            foreach ( $objColumn->getDependences () as $dependence )
            {
                $dependents[] = $makerFile->getConfig()->createClassNamespace ( $dependence )
                    . ZendFrameworkOne::SEPARETOR
                    . 'Dbtable'
                    . ZendFrameworkOne::SEPARETOR
                    . $makerFile->getClassName ( $dependence->getTable () );
            }
        }

        if ( sizeof ( $dependents ) > 0 )
        {
            $dependentTables = "protected \$_dependentTables = array(\n        '" .
                join ( "',\n        '", $dependents ) . "'\n    );";
        }


        $this->arrFunc = array (
            'referenceMap'    => $referenceMap,
            'dependentTables' => $dependentTables
        );

    }

}
