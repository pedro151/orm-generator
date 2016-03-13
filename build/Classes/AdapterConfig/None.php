<?php

namespace Classes\AdapterConfig;

use Classes\AdapterMakerFile\ZendFrameworkOne\DbTable;
use Classes\AdapterMakerFile\ZendFrameworkOne\Entity;
use Classes\AdapterMakerFile\ZendFrameworkOne\Model;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class None extends AbstractAdapter
{

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
            $this->arrConfig[ 'namespace' ],
            'Model'
        );
        if ( $table->hasSchema () )
        {
            $arrNames[] = ucfirst ( $table->getSchema () );
        }

        return implode ( '_', array_filter ( $arrNames ) );
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
