<?php

namespace Classes\AdaptersDriver;

use Classes\AdapterConfig\AbstractAdapter;
use Classes\Db\Constrant;
use Classes\Db\DbTable;

/**
 * Adapter com funcoes de analise das consultas
 *
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/ORM-Generator
 */
abstract class AbsractAdapter
{

    /**
     * @var void variavel com tipo de dados para serem convertida
     */
    protected $dataTypes;

    /**
     * @type \PDO
     */
    protected $_pdo;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $host;

    /**
     * @type string
     */
    protected $username;

    /**
     * @type string
     */
    protected $password;

    /**
     * @var string
     */
    protected $database;

    /**
     * @type string
     */
    protected $socket;

    /**
     * @type \Classes\Db\DbTable[][]
     */
    private $objDbTables = array ();

    /**
     * @var AbstractAdapter
     */
    protected $config;

    /**
     * @type int
     */
    protected $totalTables;

    /**
     * analisa e popula as Foreing keys, Primary keys e dependencias do banco nos objetos
     */
    protected function parseConstrants ()
    {
        foreach ( $this->getListConstrant () as $constrant )
        {

            $schema = $constrant[ 'table_schema' ];
            $table_name = $constrant [ 'table_name' ];
            $this->populateForeignAndPrimaryKeys ( $constrant , $table_name , $schema );
            unset( $table_name , $schema );

            if ( $constrant[ 'constraint_type' ] == "FOREIGN KEY" )
            {
                $schema = $constrant[ 'foreign_schema' ];
                $table_name = $constrant [ 'foreign_table' ];
                $this->populateDependece ( $constrant , $table_name , $schema );
                unset( $table_name , $schema );
            }
        }
    }

    /**
     * @param array  $constrant
     * @param string $table_name
     * @param int    $schema
     */
    private function populateForeignAndPrimaryKeys ( $constrant , $table_name , $schema = 0 )
    {
        if ( $this->hasTable ( $table_name , $schema ) )
        {
            $table = $this->getTable ( $table_name , $schema );
            if ( $table->hasColumn ( $constrant[ "column_name" ] ) )
            {
                $objConstrant = Constrant::getInstance ()
                                         ->populate (
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
                        $table->getColumn ( $constrant[ "column_name" ] )
                              ->addRefFk ( $objConstrant );
                        break;
                    case"PRIMARY KEY":
                        $table->getColumn ( $constrant[ "column_name" ] )
                              ->setPrimaryKey ( $objConstrant )
                              ->setSequence (
                                  $this->getSequence (
                                      $schema . '.' . $table_name ,
                                      $constrant[ "column_name" ]
                                  )
                              );
                        break;
                }
            }
        }
    }

    /**
     * @param array  $constrant
     * @param string $table_name
     * @param int    $schema
     */
    private function populateDependece ( $constrant , $table_name , $schema = 0 )
    {
        if ( $this->hasTable ( $table_name , $schema ) )
        {
            $table = $this->getTable ( $table_name , $schema );
            if ( $table->hasColumn ( $constrant[ "foreign_column" ] ) )
            {
                $table->getColumn ( $constrant[ "foreign_column" ] )
                      ->createDependece (
                          $constrant[ 'constraint_name' ] ,
                          $constrant[ 'table_name' ] ,
                          $constrant[ 'column_name' ] ,
                          $constrant[ 'table_schema' ]
                      );
            }
        }
    }

    /**
     * cria um Array com nome das tabelas
     */
    abstract protected function parseTables ();

    /**
     * retorna o numero total de tabelas
     *
     * @return int
     */
    abstract public function getTotalTables ();

    /**
     * Retorna o Nome da Sequence da tabela
     *
     * @param $table
     * @param $column
     *
     * @return string
     */
    abstract public function getSequence ( $table , $column );

    /**
     * @return array
     */
    abstract public function getListConstrant ();

    /**
     * @param string $str
     *
     * @return string
     */
    protected function convertTypeToPhp ( $str )
    {
        if ( isset( $this->dataTypes[ $str ] ) )
        {
            return $this->dataTypes[ $str ];
        }

        return 'string';
    }

    /**
     * @return string
     */
    abstract public function getPDOString ();

    /**
     * @return string
     */
    abstract public function getPDOSocketString ();

    /**
     * @param     $nameTable
     * @param int $schema
     *
     * @return \Classes\Db\DbTable
     */
    public function createTable ( $nameTable , $schema = 0 )
    {
        $this->objDbTables[ $schema ][ trim ( $nameTable ) ] = DbTable::getInstance ()
                                                                      ->populate (
                                                                          array (
                                                                              'table'    => $nameTable ,
                                                                              'schema'   => $schema ,
                                                                              'database' => $this->database
                                                                          )
                                                                      );

        return $this;
    }

    /**
     * Retorna um Array Assoc com a chave com nome da tabela e o valor com objeto tables
     *
     * @return \Classes\Db\DbTable[]
     */
    public function getTables ( $schema = 0 )
    {
        if ( ! isset( $this->objDbTables[ $schema ] ) )
        {
            return array ();
        }

        return $this->objDbTables[ $schema ];
    }

    public function getAllTables ()
    {
        return $this->objDbTables;
    }

    public function hasTables ()
    {
        return ! empty( $this->objDbTables );
    }

    /**
     * retorna a tabela especifica
     *
     * @param $nameTable Nome da tabela
     *
     * @return \Classes\Db\DbTable
     */
    public function getTable ( $nameTable , $schema = 0 )
    {
        return $this->objDbTables[ $schema ][ trim ( $nameTable ) ];
    }

    /**
     * @param string     $nameTable
     * @param int|string $schema
     *
     * @return bool
     */
    public function hasTable ( $nameTable , $schema = 0 )
    {
        return isset( $this->objDbTables[ $schema ][ trim ( $nameTable ) ] );
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array[]
     */
    abstract public function getListColumns ();

    /**
     * Retorna um Array com nome das tabelas
     *
     * @return string[]
     */
    abstract public function getListNameTable ();

    /**
     * @param \Classes\AdapterConfig\AbstractAdapter $adapterConfig
     */
    public function __construct ( AbstractAdapter $adapterConfig )
    {
        $this->config = $adapterConfig;
        $this->host = $adapterConfig->getHost ();
        $this->database = $adapterConfig->getDatabase ();
        $this->port = $adapterConfig->hasPort () ? $adapterConfig->getPort ()
            : $this->port;
        $this->username = $adapterConfig->getUser ();
        $this->password = $adapterConfig->getPassword ();
        $this->socket = $adapterConfig->getSocket ();

    }

    /**
     * Executa as consultas do banco de dados
     */
    public function runDatabase ()
    {
        $this->parseTables ();
        $this->parseConstrants ();
    }

    /**
     *
     * @return \PDO
     */
    public function getPDO ()
    {
        if ( is_null ( $this->_pdo ) )
        {
            if ( ! empty( $this->socket ) )
            {
                $pdoString = $this->getPDOSocketString ();
            } else
            {
                $pdoString = $this->getPDOString ();
            }

            try
            {
                $this->_pdo = new \PDO (
                    $pdoString , $this->username , $this->password
                );
            } catch ( \Exception $e )
            {
                die ( "pdo error: " . $e->getMessage () . "\n" );
            }
        }

        return $this->_pdo;
    }
}
