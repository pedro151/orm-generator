<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterConfig\ZendFrameworkOne;
use Classes\Maker\AbstractMaker;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
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
    protected $overwrite     = true;

    protected $validFunc = array ();

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelation ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {
        return array (
            'parents' => $this->listParents ( $makerFile , $dbTable ) ,
            'depends' => $this->listDependence ( $makerFile , $dbTable )
        );
    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    private function listParents ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {
        $parents = array ();
        foreach ( $dbTable->getForeingkeys () as $objColumn )
        {
            $constrant = $objColumn->getFks ();
            $name =
                'Parent'
                . ZendFrameworkOne::SEPARETOR
                . AbstractMaker::getClassName ( $constrant->getTable () )
                . ZendFrameworkOne::SEPARETOR
                . 'By'
                . ZendFrameworkOne::SEPARETOR
                . $objColumn->getName ();

            $parents[] = array (
                'class'    => $makerFile->getConfig ()
                                        ->createClassNamespace ( $constrant )
                              . ZendFrameworkOne::SEPARETOR
                              . AbstractMaker::getClassName ( $constrant->getTable () ) ,
                'function' => AbstractMaker::getClassName ( $name ) ,
                'table'    => $constrant->getTable () ,
                'column'   => $objColumn->getName () ,
                'name'     => $constrant->getNameConstrant () ,
            );
            unset( $name );
        }

        return $parents;
    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    private function listDependence ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {
        $depends = array ();
        foreach ( $dbTable->getDependences () as $objColumn )
        {
            foreach ( $objColumn->getDependences () as $dependence )
            {
                $name =
                    'Depend'
                    . ZendFrameworkOne::SEPARETOR
                    . AbstractMaker::getClassName ( $dependence->getTable () )
                    . ZendFrameworkOne::SEPARETOR
                    . 'By'
                    . ZendFrameworkOne::SEPARETOR
                    . $objColumn->getName ();

                if ( ! key_exists ( $name , $this->validFunc ) )
                {
                    $this->validFunc[ $name ] = true;
                    $depends[] = array (
                        'class'    => $makerFile->getConfig ()
                                                ->createClassNamespace ( $dependence )
                                      . ZendFrameworkOne::SEPARETOR
                                      . AbstractMaker::getClassName ( $dependence->getTable () ) ,
                        'function' => AbstractMaker::getClassName ( $name ) ,
                        'table'    => $dependence->getTable () ,
                        'column'   => $dependence->getColumn () ,
                        'name'     => $dependence->getNameConstrant ()
                    );
                }
                unset( $name );
            }
        }

        return $depends;
    }

}
