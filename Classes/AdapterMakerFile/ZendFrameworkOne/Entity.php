<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterMakerFile\AbstractAdapter;


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
    protected $parentFileTpl = "entity_abstract.tpl";
    protected $fileTpl       = "entity.tpl";


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

        foreach ( $dbTable->getColumns () as $objColumn )
        {
            if ( $objColumn->isForeingkey () )
            {
                foreach ( $objColumn->getFks () as $constrant )
                {
                    $name = $constrant->getTable ()
                        . self::SEPARETOR
                        . 'By'
                        . self::SEPARETOR
                        . $objColumn->getName ();

                    if ( !in_array ( $name, $this->arrFunc ) )
                    {
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
                        . self::SEPARETOR
                        . 'By'
                        . self::SEPARETOR
                        . $objColumn->getName ();

                    if ( !in_array ( $name, $this->arrFunc ) )
                    {
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

        $this->arrFunc = array (
            'parents' => $parents,
            'depends' => $depends
        );

    }


}
