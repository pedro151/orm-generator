<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterConfig\ZendFrameworkOne;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Entity extends AbstractAdapter
{

    /**
     * @var void
     */
    public    $pastName      = 'Entity';
    protected $parentClass   = "EntityAbstract";
    protected $parentFileTpl = "entity_abstract.php";
    protected $fileTpl       = "entity.php";


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
        $arrFunc=array();;
        foreach ( $dbTable->getColumns () as $objColumn )
        {
            if ( $objColumn->isForeingkey () )
            {
                foreach ( $objColumn->getFks () as $constrant )
                {
                    $name = $constrant->getTable ()
                        . ZendFrameworkOne::SEPARETOR
                        . 'By'
                        . ZendFrameworkOne::SEPARETOR
                        . $objColumn->getName ();

                    if ( !in_array ( $name, $arrFunc) )
                    {
                        $arrFunc[]=$name;
                        $parents[] = array (
                            'class'    => $makerFile->getConfig()->createClassNamespace ( $constrant ) . '_'
                                . $makerFile->getClassName ( $constrant->getTable () ),
                            'function' => $makerFile->getClassName ( $name ),
                            'table'    => $constrant->getTable (),
                            'column'   => $objColumn->getName ()
                        );
                    }
                    unset( $name );
                }
            }

            if ( $objColumn->hasDependence () )
            {
                foreach ( $objColumn->getDependences () as $constrant )
                {
                    $name = $constrant->getTable ()
                        . ZendFrameworkOne::SEPARETOR
                        . 'By'
                        . ZendFrameworkOne::SEPARETOR
                        . $objColumn->getName ();

                    if ( !in_array ( $name, $arrFunc ) )
                    {
                        $arrFunc[]=$name;
                        $depends[] = array (
                            'class'    => $makerFile->getConfig()->createClassNamespace ( $constrant ) . '_'
                                . $makerFile->getClassName ( $constrant->getTable () ),
                            'function' => $makerFile->getClassName ( $name ),
                            'table'    => $constrant->getTable (),
                            'column'   => $objColumn->getName ()
                        );
                    }
                    unset( $name );
                }
            }
        }

       return array (
            'parents' => $parents,
            'depends' => $depends
        );

    }


}
