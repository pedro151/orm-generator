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
            $arrNames[] = AbstractMaker::getClassName ( $table->getSchema () );
        } else
        {
            $arrNames[] = AbstractMaker::getClassName ( $table->getDatabase() );
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
