<?php

namespace Classes\AdaptersDriver;

use Classes\Db\Column;
use Classes\Db\Constrant;
use Classes\Db\DbTable;

require_once 'Classes/AdaptersDriver/AbsractAdapter.php';
require_once 'Classes/Db/Column.php';
require_once 'Classes/Db/Constrant.php';
require_once 'Classes/Db/DbTable.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Mysql extends AbsractAdapter
{

    /**
     * @var int
     */
    protected $port = 3306;

    /**
     * converts MySQL data types to Simple data types
     *
     * @param string $str
     *
     * @return string
     */
    protected function convertTypeToSimple ( $str )
    {
        $res = '';
        if ( preg_match ( '/(tinyint\(1\)|bit)/', $str ) ) {
            $res = 'boolean';
        }
        elseif ( preg_match ( '/(timestamp|blob|char|enum)/', $str ) ) {
            $res = 'string';
        }
        elseif ( preg_match ( '/(text)/', $str ) ) {
            $res = 'text';
        }
        elseif ( preg_match ( '/(decimal|numeric|float|double)/', $str ) ) {
            $res = 'float';
        }
        elseif ( preg_match ( '#^(?:tiny|small|medium|long|big|var)?(\w+)(?:\(\d+\))?(?:\s\w+)*$#', $str, $matches ) ) {
            $res = $matches[ 1 ];
        }
        elseif ( preg_match ( '/(date)/', $str ) ) {
            $res = 'date';
        }
        elseif ( preg_match ( '/(datetime)/', $str ) ) {
            $res = 'datetime';
        }
        else {
            print "Can't convert column type to Simple - Unrecognized type: $str";
        }

        return $res;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOString ()
    {
        return sprintf (
            "mysql:host=%s;port=%s;dbname=%s",
            $this->host,
            $this->port,
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
            "mysql:unix_socket=%s;dbname=%s",
            $this->socket,
            $this->database

        );
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    public function getListNameTable ()
    {
        if ( empty( $this->tableList ) ) {
            $this->tableList = $this->getPDO ()
                                    ->query (
                                        "show tables"
                                    )
                                    ->fetchAll ();
        }

        return $this->tableList;
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array[]
     */
    public function getListColumns ()
    {
        $sqlTables = !empty($this->tablesName)?"AND table_name IN ( $this->tablesName )":'';

        return $this->getPDO ()
                    ->query (
                        "select
                0 AS table_schema,
                table_name,
                column_name ,
                column_default,
                data_type,
                is_nullable,
                character_maximum_length AS max_length
            from information_schema.columns
            where table_schema IN ('{$this->database}') $sqlTables
            order by table_name,ordinal_position"
                    )
                    ->fetchAll ( \PDO::FETCH_ASSOC );
    }

    /**
     * @return array
     */
    public function getListConstrant ()
    {
        $sqlTables = !empty($this->tablesName)?"AND k.table_name IN ( $this->tablesName )":'';

        $objQuery = $this->getPDO ()
                    ->query (
                        "SELECT distinct
     i.constraint_type,
     k.constraint_name,
     -- k.table_schema,
     0 AS table_schema,
     k.table_name,
	 k.column_name,
     -- k.REFERENCED_TABLE_SCHEMA AS foreign_schema,
     0 AS foreign_schema,
	 k.REFERENCED_TABLE_NAME AS foreign_table,
	 k.REFERENCED_COLUMN_NAME AS foreign_column
FROM information_schema.TABLE_CONSTRAINTS as i
inner join information_schema.key_column_usage as k
ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME and k.TABLE_SCHEMA <> 'mysql'
WHERE
i.TABLE_SCHEMA IN ('{$this->database}') AND i.CONSTRAINT_TYPE IN ('FOREIGN KEY', 'PRIMARY KEY' ) $sqlTables
order by k.table_name;"
                    );
        return $objQuery?$objQuery->fetchAll ( \PDO::FETCH_ASSOC ):array();
    }

    /**
     * retorna o numero total de tabelas
     *
     * @return int
     */
    public function getTotalTables ()
    {
        if ( empty( $this->totalTables ) ) {

            $sqlTables = !empty($this->tablesName)?"AND table_name IN ( $this->tablesName )":'';

            $this->totalTables = $this->getPDO ()
                                      ->query (
                                          "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$this->database}' $sqlTables"
                                      )
                                      ->fetchColumn ();
        }

        return (int) $this->totalTables;
    }

    /**
     * @inheritDoc
     *
     * @param $table
     * @param $column
     *
     * @return string
     */
    public function getSequence ( $table, $column, $schema = 0 )
    {
        $return = $this->getPDO ()
                       ->query (
                           "select * from information_schema.columns where extra like '%auto_increment%' and  TABLE_SCHEMA='{$this->database}' AND TABLE_NAME='{$table}' AND COLUMN_NAME='{$column}';"
                       )
                       ->fetch ( \PDO::FETCH_ASSOC );

        if ( !$return ) {
            return;
        }

        return "{$return['TABLE_NAME']}_{$return['COLUMN_NAME']}_seq";
    }

}
