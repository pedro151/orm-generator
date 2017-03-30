<?php

namespace Classes\AdaptersDriver;

use Classes\AdapterConfig\AbstractAdapter;
use Classes\Db\Column;
use Classes\Db\Constrant;
use Classes\Db\DbTable;
use Classes\Db\Iterators\DbTables;

require_once 'Classes/Db/Iterators/DbTables.php';

/**
 * Adapter com funcoes de analise das consultas
 *
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
abstract class AbsractAdapter
{

    /**
     * @var void variavel com tipo de dados para serem convertida
     */
    protected $dataTypesToSimple;

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
    protected $tablesName;

    /**
     * @type string
     */
    protected $socket;

    /**
     * @type \Classes\Db\Iterators\DbTables[]
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
                                                 'column'    => $constrant[ 'foreign_column' ] ,
                                                 'database'  => $this->database
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
                                      $table_name ,
                                      $constrant[ "column_name" ] ,
                                      $schema
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
                          $this->database ,
                          $constrant[ 'table_schema' ]
                      );
            }
        }
    }

    /**
     * cria um Array com nome das tabelas
     */
    public function parseTables ()
    {
        if ( $this->hasTables () )
        {
            return $this->getAllTables ();
        }

        foreach ( $this->getListColumns () as $table )
        {
            $schema = $table[ 'table_schema' ];
            $key = $table [ 'table_name' ];
            if ( ! $this->hasTable ( $key , $schema ) )
            {
                $this->createTable ( $key , $schema );
            }

            $column = Column::getInstance ()
                            ->populate (
                                array (
                                    'name'       => $table [ 'column_name' ] ,
                                    'type'       => $this->convertTypeToSimple ( $table[ 'data_type' ] ) ,
                                    'nullable'   => (is_string($table[ 'is_nullable' ]) && strtolower($table[ 'is_nullable' ]) != 'no' ) ,
                                    'max_length' => $table[ 'max_length' ],
                                    'column_default' => $table ['column_default']
                                )
                            );

            $this->getTable ( $key , $schema )
                 ->addColumn ( $column )
                 ->setNamespace (
                     $this->config->createClassNamespace ( $this->getTable ( $key , $schema ) )
                 );
        }
    }

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
    abstract public function getSequence ( $table , $column , $schema = 0 );

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
        if ( isset( $this->dataTypesToPhp[ $str ] ) )
        {
            return $this->dataTypesToPhp[ $str ];
        }

        return 'string';
    }

    protected function convertTypeToSimple ( $str )
    {
        if ( isset( $this->dataTypesToSimple[ $str ] ) )
        {
            return $this->dataTypesToSimple[ $str ];
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
        if ( ! isset( $this->objDbTables[ strtoupper($schema) ] ) )
        {
            $this->objDbTables[ strtoupper($schema) ] = new DbTables();
        }

        $this->objDbTables[ strtoupper($schema) ][ trim ( $nameTable ) ] = DbTable::getInstance ()
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
     * @return \Classes\Db\Iterators\DbTables
     */
    public function getTables ( $schema = 0 )
    {
        if ( ! isset( $this->objDbTables[ strtoupper($schema) ] ) )
        {
            return array ();
        }

        return $this->objDbTables[ strtoupper($schema) ];
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
     * @param string $nameTable Nome da tabela
     *
     * @return \Classes\Db\DbTable
     */
    public function getTable ( $nameTable , $schema = 0 )
    {
        return $this->objDbTables[ strtoupper($schema) ][ trim ( $nameTable ) ];
    }

    /**
     * @param string     $nameTable
     * @param int|string $schema
     *
     * @return bool
     */
    public function hasTable ( $nameTable , $schema = 0 )
    {
        return isset( $this->objDbTables[ strtoupper($schema) ][ trim ( $nameTable ) ] );
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
        $this->tablesName = $adapterConfig->hasTablesName ()
            ? $adapterConfig->getListTablesName () : '';

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
                die ( "\033[0;31mPDO error: " . $e->getMessage () . "\033[0m\n" );
            }
        }

        return $this->_pdo;
    }
}
