<?php

namespace Classes\AdapterMakerFile\Phalcon;

use Classes\AdapterConfig\Phalcon;
use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\Maker\AbstractMaker;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Entity extends AbstractAdapter
{

    public    $pastName      = 'Entity';
    protected $fileTpl       = "entity.php";
    protected $fileFixedData = array (
        'parentclass' => array (
            'name' => "AbstractEntity" ,
            'tpl'  => "entity_abstract.php"
        )
    );

    protected $overwrite     = true;

    protected $validFunc = array ();

    private $intersectDependence = false;

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        return array (
            'mapParents'    => $this->listParents ( $makerFile, $dbTable ),
            'mapDependents' => $this->listDependence ( $makerFile, $dbTable )
        );
    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    private function listParents ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        $mapParents = '';
        $references = array ();
        foreach ( $dbTable->getForeingkeys () as $objColumn ) {
            $constrant    = $objColumn->getFks ();
            $references[] = sprintf (
                '$this->belongsTo(\'%s\', \'%s\', \'%s\', array(\'alias\' => \'%s\'))',
                $objColumn->getName (),
                $makerFile->getConfig ()
                          ->createClassNamespace ( $constrant ) . Phalcon::SEPARETOR . AbstractMaker::getClassName (
                    $constrant->getTable ()
                ),
                $constrant->getColumn (),
                AbstractMaker::getClassName ( $constrant->getTable () )
            );
        }

        if ( sizeof ( $references ) > 0 ) {
            $mapParents = join ( ";\n\t\t", $references ) . ";\n";
        }


        return $mapParents;
    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    private function listDependence ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        $mapDependence = '';
        $references    = array ();
        foreach ( $dbTable->getDependences () as $objColumn ) {
            foreach ( $objColumn->getDependences () as $dependence ) {
                $references[] = sprintf (
                    '$this->hasMany(\'%s\', \'%s\', \'%s\', array(\'alias\' => \'%s\'))',
                    $objColumn->getName (),
                    $makerFile->getConfig ()
                              ->createClassNamespace ( $dependence )
                    . Phalcon::SEPARETOR
                    . AbstractMaker::getClassName ( $dependence->getTable () ),
                    $dependence->getColumn (),
                    AbstractMaker::getClassName ( $dependence->getTable () )
                );

            }
        }

        if ( sizeof ( $references ) > 0 ) {
            $mapDependence = join ( ";\n\t\t", $references ) . ";";
        }

        return $mapDependence;
    }

}
