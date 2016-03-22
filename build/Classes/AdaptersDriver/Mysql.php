<?php

namespace Classes\AdaptersDriver;

require_once 'Classes/AdaptersDriver/AbsractAdapter.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Mysql extends AbsractAdapter
{

    /**
     * @var int
     */
    protected $port = 3306;

    /**
     * @inheritDoc
     */
    protected function parseForeignKeys ()
    {
        // TODO: implement here
    }

    /**
     * @inheritDoc
     */
    protected function parseTables ()
    {
        // TODO: implement here
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOString ()
    {
        return sprintf (
            "mysql:host=%s;port=%s;dbname=%s" ,
            $this->host ,
            $this->port ,
            $this->database

        );
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOSocketString ()
    {
        return sprintf (
            "mysql:unix_socket=%s;dbname=%s" ,
            $this->socket ,
            $this->database

        );
    }

    /**
     * @inheritDoc
     *
     * @param string $databaseName
     *
     * @return \Classes\Db\DbTable[]
     */
    public function getTables ( $schema = 0 )
    {
        // TODO: implement here
        return array ();
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    public function getListNameTable ()
    {
        // TODO: implement here
        return array ();
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array[]
     */
    public function getListColumns ()
    {
        $strSchema = implode ( "', '" , $this->schema );

        return $this->getPDO ()->query (
            "select
                table_schema,
                table_name,
                column_name ,
                data_type,
                is_nullable,
                character_maximum_length AS max_length
            from information_schema.columns
            where table_schema IN ('$strSchema')
            order by table_name,ordinal_position"
        )->fetchAll ( \PDO::FETCH_ASSOC );
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

    /**
     * @inheritDoc
     *
     * @param $table
     * @param $column
     */
    public function getSequence ( $table , $column )
    {
        // TODO: Implement getSequence() method.
    }

}
