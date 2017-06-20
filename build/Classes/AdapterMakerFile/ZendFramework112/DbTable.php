<?php

namespace Classes\AdapterMakerFile\ZendFramework112;

use Classes\AdapterConfig\ZendFramework112;
use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\Maker\AbstractMaker;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class DbTable extends AbstractAdapter
{

    public    $pastName      = 'DbTable';
    protected $fileTpl       = "dbtable.php";
    protected $fileFixedData = array (
        'parentclass' => array (
            'name' => "TableAbstract" ,
            'tpl'  => "dbtable_abstract.php"
        )
    );

    protected $overwrite     = true;

    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        $referenceMap = '';
        $references = array ();
        $dependentTables = '';
        $dependents = array ();
        foreach ( $dbTable->getForeingkeys () as $objColumn )
        {
            $constrant = $objColumn->getFks ();
            $variable =  $constrant->getNameConstrant () . ZendFramework112::SEPARETOR . $objColumn->getName ();

            $arrClass = array (
                $makerFile->getConfig ()->createClassNamespace ( $constrant ),
                'DbTable',
                AbstractMaker::getClassName ( $constrant->getTable () )
            );
            $class = implode ( ZendFramework112::SEPARETOR , array_filter ( $arrClass ) );

            $references[] = sprintf (
                "
       '%s' => array (
            'columns'       => '%s' ,
            'refTableClass' => '%s',
            'refColumns'    =>'%s'
       )",
                AbstractMaker::getClassName ( $variable ),
                $objColumn->getName (),
                $class,
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
                $arrClass = array (
                    $makerFile->getConfig ()->createClassNamespace ( $dependence ),
                    'DbTable',
                    AbstractMaker::getClassName ( $dependence->getTable () )
                );
                $class = implode ( ZendFramework112::SEPARETOR , array_filter ( $arrClass ) );

                if(!in_array($class,$dependents)){
                   $dependents[] = $class;
                }
            }
        }

        if ( sizeof ( $dependents ) > 0 )
        {
            $dependentTables = "protected \$_dependentTables = array(\n        '" .
                join ( "',\n        '", $dependents ) . "'\n    );";
        }


        return array (
            'referenceMap'    => $referenceMap,
            'dependentTables' => $dependentTables
        );

    }

}
