<?php

namespace Classes\AdapterConfig;

use Classes\AdapterMakerFile\ZendFramework112\DbTable;
use Classes\AdapterMakerFile\ZendFramework112\Entity;
use Classes\AdapterMakerFile\ZendFramework112\Model;
use Classes\AdapterMakerFile\ZendFramework112\Peer;

require_once "Classes/AdapterConfig/AbstractAdapter.php";
require_once "Classes/AdapterMakerFile/ZendFramework112/DbTable.php";
require_once "Classes/AdapterMakerFile/ZendFramework112/Entity.php";
require_once "Classes/AdapterMakerFile/ZendFramework112/Model.php";
require_once "Classes/AdapterMakerFile/ZendFramework112/Peer.php";

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class ZendFramework112 extends AbstractAdapter
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
        if ( ! $this->config or ! $this->isValidFrameworkFiles () )
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
            'charset'   => isset( $this->config[ 'params' ][ 'charset' ] )
                ? $this->config[ 'params' ][ 'charset' ] : 'UTF8' ,
            //usuario do banco
            'username' => $this->config[ 'params' ][ 'username' ] ,
            //senha do banco
            'password' => $this->config[ 'params' ][ 'password' ] ,
        );
    }

    protected function parseFrameworkConfig ()
    {
        if ( ! $this->isValidFrameworkFiles () )
        {
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

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return \Classes\AdapterMakerFile\AbstractAdapter[]
     */
    public function getMakeFileInstances ()
    {

        $instances = array ();
        if ( $this->hasOptionalClasses () )
        {
            foreach ( $this->getOptionalClasses () as $optionalClass )
            {
                $Name = ucfirst ( $optionalClass );
                $className = "Classes\\AdapterMakerFile\\ZendFramework112\\{$Name}";
                if(method_exists($className,'getInstance')){
                    $instances[] = $className::getInstance ();
                }
            }
        }

        return array_merge ( array (
            DbTable::getInstance () ,
            Entity::getInstance () ,
            Model::getInstance ()
        ) , $instances );
    }

}
