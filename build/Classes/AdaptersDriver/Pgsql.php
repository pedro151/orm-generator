<?php

namespace Classes\AdaptersDriver;

use Classes\AdapterConfig\AbstractAdapter;
use Classes\Db\Column;
use Classes\Db\Constrant;
use Classes\Db\DbTable;

require_once 'Classes/AdaptersDriver/AbsractAdapter.php';
require_once 'Classes/Db/Column.php';
require_once 'Classes/Db/Constrant.php';
require_once 'Classes/Db/DbTable.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Pgsql extends AbsractAdapter
{

    /**
     * @var int
     */
    protected $port = 5432;

    /**
     * @type array|\string[]
     */
    protected $schema = array ( 'public' );

    protected $dataTypes = array (
        /* Numeric Types */
        'smallint'         => 'int' ,
        'integer'          => 'int' ,
        'bigint'           => 'float' ,
        'decimal'          => 'float' ,
        'numeric'          => 'float' ,
        'real'             => 'float' ,
        'double precision' => 'float' ,
        'serial'           => 'int' ,
        'bigserial'        => 'float' ,
        /* Monetary Types */
        'money'            => 'float' ,
        /* Character Types */
        'character varyin' => 'string' ,
        'varchar'          => 'string' ,
        'character'        => 'string' ,
        'char'             => 'string' ,
        'text'             => 'string' ,
        /* Binary Data Types */
        'bytea'            => 'string' ,
        /* Date/Time Types */
        'datatime'         => 'date' ,
        'date'             => 'date' ,

        /* Boolean Type */
        'boolean'          => 'boolean'
    );

    public function __construct ( AbstractAdapter $adapterConfig )
    {
        parent::__construct ( $adapterConfig );
        if ( $adapterConfig->hasSchemas () )
        {
            $this->schema = $adapterConfig->getSchemas ();
        }

    }

    /**
     * Retorna um Array com nome das tabelas
     *
     * @param void $schema
     *
     * @return string[]
     */
    public function getListNameTable ()
    {
        if ( empty( $this->tableList ) )
        {
            $strSchema = implode ( "', '" , $this->schema );

            $this->tableList = $this->getPDO ()->query (
                "SELECT table_schema,  table_name
             FROM information_schema.tables
             WHERE
              table_type = 'BASE TABLE'
              AND table_schema IN ( '" . $strSchema . "' )
              ORDER by
               table_schema,
               table_name
              ASC"
            )->fetchAll ();
        }

        return $this->tableList;
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array
     */
    public function getListColumns ()
    {
        $strSchema = implode ( "', '" , $this->schema );

        return $this->getPDO ()->query (
            "SELECT distinct
	c.table_schema,
	c.table_name,
	c.column_name ,
	c.data_type,
	is_nullable,
	character_maximum_length AS max_length
		FROM
		information_schema.tables AS st
		INNER JOIN  information_schema.columns AS c
		ON st.table_name=c.table_name and st.table_type = 'BASE TABLE'
		and  c.table_schema IN ('$strSchema')
		order by c.table_name asc"
        )->fetchAll ( \PDO::FETCH_ASSOC );
    }

    public function getListConstrant ()
    {
        $strSchema = implode ( "', '" , $this->schema );

        return $this->getPDO ()->query (
            "SELECT distinct
                tc.constraint_type,
                tc.constraint_name,
                tc.table_schema,
                tc.table_name,
                kcu.column_name,
		        ccu.table_schema AS foreign_schema,
                ccu.table_name AS foreign_table,
                ccu.column_name as foreign_column
                  FROM
                information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                      ON tc.constraint_name = kcu.constraint_name
                       AND tc.table_schema IN ('$strSchema')
                       AND tc.constraint_type IN ('FOREIGN KEY','PRIMARY KEY')
                    JOIN information_schema.constraint_column_usage AS ccu
                      ON tc.constraint_name  = ccu.constraint_name
                        AND tc.constraint_schema = ccu.constraint_schema
                    ORDER by tc.table_schema"
        )->fetchAll ( \PDO::FETCH_ASSOC );
    }

    /**
     * @inheritDoc
     */
    public function parseForeignKeys ()
    {
        foreach ( $this->getListConstrant () as $constrant )
        {

            if ( $constrant[ 'constraint_type' ] == "FOREIGN KEY"
                 or $constrant[ 'constraint_type' ] == "PRIMARY KEY"
            )
            {
                $schema = $constrant[ 'table_schema' ];
                $key = $constrant [ 'table_name' ];
                if ( isset( $this->objDbTables[ $schema ][ $key ] ) )
                {
                    $column = $this->objDbTables[ $schema ][ $key ]->getColumn ( $constrant[ "column_name" ] );
                    if ( $column )
                    {
                        $objConstrant = new Constrant();
                        $objConstrant->populate (
                            array (
                                'constrant' => $constrant[ 'constraint_name' ] ,
                                'schema'    => $constrant[ 'foreign_schema' ] ,
                                'table'     => $constrant[ 'foreign_table' ] ,
                                'column'    => $constrant[ 'foreign_column' ]
                            )
                        );


                        switch ( $constrant[ 'constraint_type' ] )
                        {
                            case "FOREIGN KEY":
                            {
                                $column->addRefFk ( $objConstrant );
                                break;
                            }
                            case"PRIMARY KEY":
                            {
                                $column->setPrimaryKey ( $objConstrant );
                                $column->setSequence (
                                    $this->getSequence (
                                        $schema . '.' . $key ,
                                        $constrant[ "column_name" ]
                                    )
                                );

                                break;
                            }
                        }
                    }
                }
                unset( $key , $column );
            }

            if ( $constrant[ 'constraint_type' ] == "FOREIGN KEY" )
            {
                $schema = $constrant[ 'foreign_schema' ];
                $key = $constrant [ 'foreign_table' ];
                if ( isset( $this->objDbTables[ $schema ][ $key ] ) )
                {
                    $column = $this->objDbTables[ $schema ][ $key ]->getColumn ( $constrant[ "foreign_column" ] );

                    if ( $column )
                    {
                        $objConstrantDependence = new Constrant();
                        $objConstrantDependence->populate (
                            array (
                                'constrant' => $constrant[ 'constraint_name' ] ,
                                'schema'    => $constrant[ 'table_schema' ] ,
                                'table'     => $constrant[ 'table_name' ] ,
                                'column'    => $constrant[ 'column_name' ]
                            )
                        );

                        $column->addDependece ( $objConstrantDependence );
                    }
                }
                unset( $key , $column );
            }


        }
    }

    /**
     * Retorna o Nome da Sequence da tabela
     *
     * @param $table
     * @param $column
     *
     * @return string
     */
    public function getSequence ( $table , $column )
    {
        $pdo = $this->getPDO ();
        $return1 = $pdo->query ( "SELECT pg_get_serial_sequence('$table', '$column');" )
                       ->fetchColumn ();

        if ( $return1 )
        {
            return $return1;
        }

        $dtbase = explode ( '.' , $table );;

        $stmt = $pdo->prepare (
            "SELECT adsrc FROM pg_attrdef AS att
            INNER JOIN pg_class AS c
              ON adrelid = c.oid AND att.adnum=1 AND c.relname = ?
            INNER JOIN pg_catalog.pg_namespace n
              ON n.oid = c.relnamespace and n.nspname=?"
        );

        $schema = isset( $dtbase[ 1 ] ) ? $dtbase[ 1 ] : 'public';

        $stmt->bindParam ( 1 , $schema );
        $stmt->bindParam ( 2 , $dtbase[ 0 ] );
        $stmt->execute ();
        $return2 = $stmt->fetchColumn ();
        if ( $return2 )
        {
            return preg_filter (
                array (
                    '/nextval\(\'/' ,
                    '/\'::regclass\)/'
                ) , '' , $return2
            );
        }

    }

    /**
     * @inheritDoc
     */
    public function parseTables ()
    {
        if ( ! empty( $this->objDbTables ) )
        {
            return $this->objDbTables;
        }

        foreach ( $this->getListColumns () as $table )
        {
            $schema = $table[ 'table_schema' ];
            $key = $table [ 'table_name' ];
            if ( ! isset( $this->objDbTables[ $schema ][ $key ] ) )
            {
                $this->objDbTables[ $schema ][ $key ] = new DbTable();
                $this->objDbTables[ $schema ][ $key ]->populate (
                    array (
                        'table'    => $table [ 'table_name' ] ,
                        'schema'   => $table[ 'table_schema' ] ,
                        'database' => $this->database
                    )
                );
            }

            $column = new Column();
            $column->populate (
                array (
                    'name'       => $table [ 'column_name' ] ,
                    'type'       => $this->convertTypeToPhp ( $table[ 'data_type' ] ) ,
                    'nullable'   => ( $table[ 'is_nullable' ] == 'YES' ) ,
                    'max_length' => $table[ 'max_length' ]
                )
            );

            $this->objDbTables[ $schema ][ $key ]->addColumn ( $column );
            $this->objDbTables[ $schema ][ $key ]->setNamespace (
                $this->config->createClassNamespace ( $this->objDbTables[ $schema ][ $key ] )
            );
        }

    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPDOString ()
    {
        return sprintf (
            "pgsql:host=%s;port=%s;dbname=%s" ,
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
            "pgsql:unix_socket=%s;dbname=%s" ,
            $this->socket ,
            $this->database

        );
    }

    /**
     * retorna o numero total de tabelas
     *
     * @return int
     */
    public function getTotalTables ()
    {
        if ( empty( $this->totalTables ) )
        {
            $strSchema = implode ( "', '" , $this->schema );

            $this->totalTables = $this->getPDO ()->query (
                "SELECT COUNT(table_name)  AS total
             FROM information_schema.tables
             WHERE
              table_type = 'BASE TABLE'
              AND table_schema IN ( '" . $strSchema . "' )"
            )->fetchColumn ();
        }

        return (int) $this->totalTables;
    }
}
