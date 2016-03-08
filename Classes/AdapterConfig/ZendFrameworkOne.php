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
        if ( file_exists ( 'Zend/Config/Ini.php' ) )
        {
            require_once 'Zend/Config/Ini.php';
            $this->config = new Zend_Config_Ini(
                APPLICATION_PATH
                . '/configs/application.ini', APPLICATION_ENV
            );

            $this->config = $this->config->toArray ();
        }

    }

    public function createClassNamespace ( $table )
    {
        $arrNames = array (
            $this->arrConfig[ 'namespace' ],
            'Model'
        );
        if ( $table->hasSchema () )
        {
            $arrNames[] = ucfirst ( $table->getSchema () );
        }

        return implode ( self::SEPARETOR, array_filter ( $arrNames ) );
    }

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return AbstractAdapter[]
     */
    public function getMakeFileInstances ()
    {
        return array (
            DbTable::getInstance (),
            Entity::getInstance (),
            Model::getInstance ()
        );
    }

}
