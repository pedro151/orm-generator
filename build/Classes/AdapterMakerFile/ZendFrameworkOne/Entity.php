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

    protected $validFunc = array();

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
                $constrant = $objColumn->getFks ();
                $name =
                     'Parent'
                    . ZendFrameworkOne::SEPARETOR
                    . $makerFile->getClassName ( $constrant->getTable () )
                    . ZendFrameworkOne::SEPARETOR
                    . 'By'
                    . ZendFrameworkOne::SEPARETOR
                    . $constrant->getColumn ();

                if ( !key_exists ( $name, $this->validFunc ) )
                {
                    $this->validFunc[ $name ] = true;
                    $parents[] = array (
                        'class'    => $makerFile->getConfig ()->createClassNamespace ( $constrant ) . '_'
                            . $makerFile->getClassName ( $constrant->getTable () ),
                        'function' => $makerFile->getClassName ( $name ),
                        'table'    => $constrant->getTable (),
                        'column'   => $objColumn->getName (),
                        'name'     => $makerFile->getClassName ( $constrant->getNameConstrant () )
                    );
                }
                unset( $name );
            }

            foreach ( $dbTable->getDependences () as $objColumn )
            {
                foreach ( $objColumn->getDependences () as $dependence )
                {
                    $name =
                        'Depend'
                        . ZendFrameworkOne::SEPARETOR
                        . $makerFile->getClassName ( $dependence->getTable () )
                        . ZendFrameworkOne::SEPARETOR
                        . 'By'
                        . ZendFrameworkOne::SEPARETOR
                        . $dependence->getColumn ();

                    if ( !key_exists ( $name, $this->validFunc ) )
                    {
                        $this->validFunc[ $name ] = true;
                        $depends[] = array (
                            'class'    => $makerFile->getConfig ()->createClassNamespace ( $dependence )
                                . ZendFrameworkOne::SEPARETOR
                                . $makerFile->getClassName ( $dependence->getTable () ),
                            'function' => $makerFile->getClassName ( $name ),
                            'table'    => $dependence->getTable (),
                            'column'   => $dependence->getColumn (),
                            'name'     => $dependence->getNameConstrant()
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
