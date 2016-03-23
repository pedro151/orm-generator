<?php

namespace Classes\AdapterConfig;

use Classes\AdapterMakerFile\ZendFrameworkOne\DbTable;
use Classes\AdapterMakerFile\ZendFrameworkOne\Entity;
use Classes\AdapterMakerFile\ZendFrameworkOne\Model;
use Classes\Maker\AbstractMaker;

require_once "Classes/Maker/AbstractMaker.php";
require_once "Classes/AdapterConfig/AbstractAdapter.php";
require_once "Classes/AdapterMakerFile/ZendFrameworkOne/DbTable.php";
require_once "Classes/AdapterMakerFile/ZendFrameworkOne/Entity.php";
require_once "Classes/AdapterMakerFile/ZendFrameworkOne/Model.php";

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class ZendFrameworkOne extends AbstractAdapter
{

    private $config;

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
        if ( ! $this->config or !$this->isValidFrameworkFiles ())
        {
            return array ();
        }

        return array (
            //Driver do banco de dados
            'driver'   => $this->config[ 'adapter' ] ,
            //Nome do banco de dados
            'database' => $this->config[ 'params' ][ 'dbname' ] ,
            //Host do banco
            'host'     => $this->config[ 'params' ][ 'host' ] ,
            //Port do banco
            'port'     => isset( $this->config[ 'params' ][ 'port' ] )
                ? $this->config[ 'params' ][ 'port' ] : '' ,
            //usuario do banco
            'username' => $this->config[ 'params' ][ 'username' ] ,
            //senha do banco
            'password' => $this->config[ 'params' ][ 'password' ] ,
        );
    }

    protected function parseFrameworkConfig ()
    {
        if(!$this->isValidFrameworkFiles ()){
            return;
        }

        $frameworkIni = $this->getFrameworkIni ();

        require_once 'Zend/Config/Ini.php';

        $objConfig = new \Zend_Config_Ini(
            realpath ( $frameworkIni ) , $this->getEnvironment ()
        );

        $arrConfig = $objConfig->toArray ();

        if ( isset( $arrConfig[ 'resources' ][ 'db' ] ) )
        {
            $this->config = $arrConfig[ 'resources' ][ 'db' ];
        }
    }

    public function createClassNamespace ( $table )
    {
        $arrNames = array (
            $this->arrConfig[ 'namespace' ] ,
            'Model'
        );

        if ( isset( $this->arrConfig['folder-database'] )
             && $this->arrConfig['folder-database']
        )
        {
            $arrNames[] = AbstractMaker::getClassName ( $this->arrConfig['driver'] );
        }

        if ( $table->hasSchema () )
        {
            $arrNames[] = AbstractMaker::getClassName ( $table->getSchema () );
        } else
        {
            $arrNames[] = AbstractMaker::getClassName ( $table->getDatabase () );
        }

        return implode ( self::SEPARETOR , array_filter ( $arrNames ) );
    }

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return AbstractAdapter[]
     */
    public function getMakeFileInstances ()
    {
        return array (
            DbTable::getInstance () ,
            Entity::getInstance () ,
            Model::getInstance ()
        );
    }

}
