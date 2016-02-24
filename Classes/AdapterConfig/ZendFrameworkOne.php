<?php

namespace Classes\AdapterConfig;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151
 */
class ZendFrameworkOne extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $framework = "zend_framework";

    const SEPARETOR = "_";

    protected function init ()
    {
    }

    /**
     * retorna os parametros da configuração do framework
     *
     * @return array
     */
    protected function getParams ()
    {

    }

    protected function parseFrameworkConfig ()
    {
        // TODO: Implement parseFrameworkConfig() method.
    }

    public function createClassNamespace ( $table )
    {
        $arrNames = array (
            $this->arrConfig[ 'namespace' ] ,
            'Model'
        );
        if ( $table->hasSchema () )
        {
            $arrNames[] = ucfirst ( $table->getSchema () );
        }

        return implode ( '_' , array_filter ( $arrNames ) );
    }

    /**
     * @param \Classes\AdapterMakerFile\AbstractAdapter $adapterFile
     * @param \Classes\MakerFile                        $makerFile
     * @param \Classes\Db\DbTable                       $dbTable
     *
     * @return string[]
     */
    public function factoryRelationTables ( \Classes\AdapterMakerFile\AbstractAdapter $adapterFile , \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {
        switch ( $adapterFile->getPastName () )
        {
            case 'DbTable':
            {
                $this->parseRelationDbTable ( $makerFile , $dbTable );
                break;
            }
            case 'Entity':
            {
                $this->parseRelationEmtity ( $makerFile , $dbTable );
                break;
            }
            case 'Model':
            {
                break;
            }
        }

        return $this->arrFunc;

    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return string[]
     */
    private function parseRelationDbTable ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {
        $referenceMap = '';
        $references = array ();
        $dependentTables = '';
        $dependents = array ();
        foreach ( $dbTable->getForeingkeys () as $fk )
        {
            $constrant = $fk->getFks ();
            $references[] = sprintf ( "
       '%s' => array (
            'columns'       => '%s' ,
            'refTableClass' => '%s',
            'refColumns'    =>'%s'
       )" ,
                                      $constrant->getNameConstrant () ,
                                      $fk->getName () ,
                                      $dbTable->getNamespace ()
                                      . '_Dbtable_'
                                      . $makerFile->getClassName ( $dbTable->getName () ) ,
                                      $constrant->getColumn ()

            );
        }

        if ( sizeof ( $references ) > 0 )
        {
            $referenceMap = "protected \$_referenceMap = array(" .
                join ( ',' , $references ) . "\n    );";
        }

        foreach ( $dbTable->getDependences () as $objColumn )
        {
            foreach ( $objColumn->getDependences () as $dependence )
            {
                $dependents[] = $this->createClassNamespace ( $dependence )
                    . '_Dbtable_'
                    . $makerFile->getClassName ( $dependence->getTable () );
            }
        }

        if ( sizeof ( $dependents ) > 0 )
        {
            $dependentTables = "protected \$_dependentTables = array(\n        '" .
                join ( "',\n        '" , $dependents ) . "'\n    );";
        }


        $this->arrFunc = array (
            'referenceMap' => $referenceMap , 'dependentTables' => $dependentTables
        );

    }

    /**
     * @param \Classes\MakerFile  $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelationEmtity ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
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
                        .  $objColumn->getName ();

                    if ( ! in_array ( $name , $this->arrFunc ) )
                    {
                        $parents[] = array (
                            'class'    => $this->createClassNamespace ( $constrant ) . '_'
                                . $makerFile->getClassName ( $constrant->getTable () ) ,
                            'function' => $makerFile->getClassName ($name) ,
                            'table'    => $constrant->getTable () ,
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
                        .  $objColumn->getName () ;

                    if ( ! in_array ( $name , $this->arrFunc ) )
                    {
                        $depends[] = array (
                            'class'    => $this->createClassNamespace ( $constrant ) . '_'
                                . $makerFile->getClassName ( $constrant->getTable () ) ,
                            'function' => $makerFile->getClassName ($name) ,
                            'table'    => $constrant->getTable () ,
                            'column'   => $objColumn->getName ()
                        );
                    }
                    unset( $name );
                }
            }
        }

        $this->arrFunc = array (
            'parents' => $parents , 'depends' => $depends
        );

    }

}
