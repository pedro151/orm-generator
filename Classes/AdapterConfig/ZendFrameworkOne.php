<?php

namespace Classes\AdapterConfig;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151
 */
class ZendFrameworkOne extends AbstractAdapter
{

    /**
     * @type array
     */
    private $config;

    /**
     * @var string
     */
    protected $framework = "zend_framework";

    const SEPARETOR = "_";

    protected function init ()
    {
    }

    protected function parseFrameworkConfig ()
    {
        if ( file_exists ( 'Zend/Config/Ini.php' ) )
        {
            require_once 'Zend/Config/Ini.php';
            $this->config = new Zend_Config_Ini(
                APPLICATION_PATH
                . '/configs/application.ini' , APPLICATION_ENV
            );

            $this->config = $this->config->toArray ();
        }

    }

    /**
     * retorna os parametros da configuração do framework
     *
     * @return array
     */
    protected function getParams ()
    {
        $arr1 = array (
            'driver'    => $this->config[ 'resources' ][ 'db' ][ 'adapter' ] ,
            'namespace' => $this->config[ 'appnamespace' ]
        );
        $this->config[ 'resources' ][ 'db' ][ 'params' ];

        return $arr1 + $this->config[ 'resources' ][ 'db' ][ 'params' ];
    }

    /**
     * @param \Classes\Db\DbTable|\Classes\Db\Constrant $table
     *
     * @return string
     */
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
                $this->parseRelationEmtity ( $makerFile , $dbTable);
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
        foreach ( $dbTable->getForeingkeys () as $fk )
        {
            $constrant = $fk->getFks ();
            $references[] = printf ( "
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
            var_dump ( $objColumn );
        }

    }

    /**
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return mixed
     */
    public function parseRelationEmtity ( \Classes\MakerFile $makerFile , \Classes\Db\DbTable $dbTable )
    {

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
                            . $makerFile->getClassName ( $objColumn->getName () );

                    if ( ! in_array ( $name , $this->arrFunc ) )
                    {
                        $this->arrFunc[ 'parents' ][] = array (
                            'class'    => $this->createClassNamespace($constrant).'_'.$makerFile->getClassName ( $constrant->getTable() ),
                            'function' => $name ,
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
                            . $makerFile->getClassName ( $objColumn->getName () );

                    if ( ! in_array ( $name , $this->arrFunc ) )
                    {
                        $this->arrFunc[ 'depends' ][] = array (
                            'class'    => $this->createClassNamespace($constrant).'_'.$makerFile->getClassName ( $constrant->getTable() ),
                            'function' => $name ,
                            'column'   => $objColumn->getName ()
                        );
                    }
                    unset( $name );
                }
            }
        }

        return $this->arrFunc;
    }

}
