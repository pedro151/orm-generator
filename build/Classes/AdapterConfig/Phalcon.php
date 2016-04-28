<?php

namespace Classes\AdapterConfig;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/ORM-Generator
 */
class Phalcon extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $framework = "phalcon";

    const SEPARETOR = "/";

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

        return implode ( '/', array_filter ( $arrNames ) );
    }

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return \Classes\AdapterMakerFile\AbstractAdapter[]
     */
    public function getMakeFileInstances ()
    {
        return array (
        );
    }

}
