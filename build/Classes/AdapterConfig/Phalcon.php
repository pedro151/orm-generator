<?php

namespace Classes\AdapterConfig;

use Classes\AdapterMakerFile\Phalcon\Entity;
use Classes\AdapterMakerFile\Phalcon\Model;

require_once "Classes/AdapterConfig/AbstractAdapter.php";
require_once "Classes/AdapterMakerFile/Phalcon/Entity.php";
require_once "Classes/AdapterMakerFile/Phalcon/Model.php";

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Phalcon extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $framework = "phalcon";

    const SEPARETOR = "\\";

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
     * @inheritdoc
     */
    protected function getBaseNamespace ()
    {
        return array (
            $this->arrConfig[ 'namespace' ] ,
            'Models'
        );
    }
    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return \Classes\AdapterMakerFile\AbstractAdapter[]
     */
    public function getMakeFileInstances ()
    {
        return array (
            Entity::getInstance () ,
            Model::getInstance ()
        );
    }

}
