<?php

namespace Classes\AdapterMakerFile\ZendFrameworkOne;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\AdapterConfig\ZendFrameworkOne;
use Classes\Maker\AbstractMaker;

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
        foreach ( $dbTable->getForeingkeys () as $fks )
        {
            $constrant = $fks->getFks ();
            $name =
                'Parent'
                . ZendFrameworkOne::SEPARETOR
                . AbstractMaker::getClassName ( $constrant->getTable () )
                . ZendFrameworkOne::SEPARETOR
                . 'By'
                . ZendFrameworkOne::SEPARETOR
                . $constrant->getColumn ();

            $parents[] = array (
                'class'    => $makerFile->getConfig ()
                        ->createClassNamespace ( $constrant )
                    . ZendFrameworkOne::SEPARETOR
                    . AbstractMaker::getClassName ( $constrant->getTable () ),
                'function' => AbstractMaker::getClassName ( $name ),
                'table'    => $constrant->getTable (),
                'column'   => $fks->getName (),
                'name'     => $constrant->getNameConstrant (),
            );
            unset( $name );
        }

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
                    . $dependence->getColumn ();

                if ( !key_exists ( $name, $this->validFunc ) )
                {
                    $this->validFunc[ $name ] = true;
                    $depends[] = array (
                        'class'    => $makerFile->getConfig ()
                                ->createClassNamespace ( $dependence )
                            . ZendFrameworkOne::SEPARETOR
                            . AbstractMaker::getClassName ( $dependence->getTable () ),
                        'function' => AbstractMaker::getClassName ( $name ),
                        'table'    => $dependence->getTable (),
                        'column'   => $dependence->getColumn (),
                        'name'     => $dependence->getNameConstrant ()
                    );
                }
                unset( $name );
            }
        }

        return array (
            'parents' => $parents,
            'depends' => $depends
        );

    }

}
