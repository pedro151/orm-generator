<?php

namespace Classes\AdaptersDriver;

use Classes\AdapterConfig\AbstractAdapter;

/**
 * Adapter com funcoes de analise das consultas
 *
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
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
     * @type \Classes\Db\DbTable[]
     */
    protected $objDbTables = array ();

    /**
     * @var AbstractAdapter
     */
    protected $config;

    /**
     * @type int
     */
    protected $totalTables;

    /**
     * Popula as ForeingKeys do banco nos objetos
     */
    protected abstract function parseForeignKeys ();

    /**
     * cria um Array com nome das tabelas
     */
    protected abstract function parseTables ();

    /**
     * retorna o numero total de tabelas
     *
     * @return int
     */
    public abstract function getTotalTables ();

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
    public abstract function getPDOString ();

    /**
     * @return string
     */
    public abstract function getPDOSocketString ();

    /**
     * Retorna um Array Assoc com a chave com nome da tabela e o valor com objeto tables
     *
     * @return \Classes\Db\DbTable[]
     */
    public function getTables ( $schema = 0 )
    {
        if ( !isset( $this->objDbTables[ $schema ] ) )
        {
            return array();
        }

        return $this->objDbTables[ $schema ];
    }

    /**
     * retorna a tabela especifica
     *
     * @param $nameTable Nome da tabela
     *
     * @return \Classes\Db\DbTable|null
     */
    public function getTable ( $nameTable, $schema = 0 )
    {
        if ( isset( $this->objDbTables[ $schema ][ trim ( $nameTable ) ] ) )
        {
            return $this->objDbTables[ $schema ][ trim ( $nameTable ) ];
        }

        return null;
    }

    /**
     * retorna multiplos arrays com dados da column em array
     *
     * @return array[]
     */
    public abstract function getListColumns ();

    /**
     * Retorna um Array com nome das tabelas
     *
     * @return string[]
     */
    public abstract function getListNameTable ();

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
     *
     * @return \PDO
     */
    public function getPDO ()
    {
        if ( is_null ( $this->_pdo ) )
        {
            if ( !empty( $this->socket ) )
            {
                $pdoString = $this->getPDOSocketString ();
            }
            else
            {
                $pdoString = $this->getPDOString ();
            }

            try
            {
                $this->_pdo = new \PDO (
                    $pdoString, $this->username, $this->password
                );
            }
            catch ( Exception $e )
            {
                die ( "pdo error: " . $e->getMessage () . "\n" );
            }
        }

        return $this->_pdo;
    }
}
