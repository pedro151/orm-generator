<?php

namespace Classes\AdapterConfig;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Phalcon extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $framework = "phalcon";

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
        );
    }

    /**
     * @param \Classes\AdapterMakerFile\AbstractAdapter $adapterFile
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return string[]
     */
    public function factoryRelationTables ( \Classes\AdapterMakerFile\AbstractAdapter $adapterFile, \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        return $this->arrFunc;
    }

    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return string[]
     */
    private function parseRelationDbTable ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {

    }

    /**
     * @param \Classes\MakerFile $makerFile
     * @param \Classes\Db\DbTable $dbTable
     *
     * @return array
     */
    public function parseRelationEmtity ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {

    }

}
