<?php

namespace Classes\AdapterConfig;

use Classes\AdapterMakerFile\ZendFrameworkOne\DbTable;
use Classes\AdapterMakerFile\ZendFrameworkOne\Entity;
use Classes\AdapterMakerFile\ZendFrameworkOne\Model;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class ZendFrameworkOne extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $framework = "zend_framework";

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
        if ( ! $this->config )
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
        if ( empty( $this->config[ 'framework-ini' ] ) )
        {
            throw new \Exception( "inform the .ini file in the 'framework-ini' existing configuration." );
        }

        $objConfig = new \Zend_Config_Ini(
            realpath ( $this->config[ 'framework-ini' ] ) , 'production'
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
        if ( $table->hasSchema () )
        {
            $arrNames[] = ucfirst ( $table->getSchema () );
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
