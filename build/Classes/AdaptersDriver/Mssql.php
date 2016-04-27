<?php

namespace Classes\AdaptersDriver;

require_once 'AbsractAdapter.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link https://github.com/pedro151/ORM-Generator
 */
class Mssql extends AbsractAdapter
{

    /**
     * @var int
     */
    protected $port = 1433;

    /**
     * @inheritDoc
     */
    protected function parseConstrants()
    {
        // TODO: implement here
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOString()
    {
        // TODO: implement here
        return "";
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOSocketString()
    {
        // TODO: implement here
        return "";
    }

    /**
     * @inheritDoc
     * @param string $databaseName
     * @return \Classes\Db\DbTable[]
     */
    public function getTables($schema = 0)
    {
        // TODO: implement here
        return array();
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    public function getListNameTable()
    {
        // TODO: implement here
        return array();
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array[]
     */
    public function getListColumns ()
    {
        // TODO: Implement getListColumns() method.
    }

    /**
     * retorna o numero total de tabelas
     *
     * @return int
     */
    public function getTotalTables ()
    {
        // TODO: Implement totalTables() method.
    }

    public function getSequence ( $table , $column )
    {
        // TODO: Implement getSequence() method.
    }

    public function getListConstrant ()
    {
        // TODO: Implement getListConstrant() method.
    }

}
