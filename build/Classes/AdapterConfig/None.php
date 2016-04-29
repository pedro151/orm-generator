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
 * @link   https://github.com/pedro151/ORM-Generator
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

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return \Classes\AdapterMakerFile\AbstractAdapter[]
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
