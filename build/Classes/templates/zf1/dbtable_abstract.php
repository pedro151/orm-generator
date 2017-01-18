<?='<?php'?>

/**
 * Application Model DbTables
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * Classe Abstract pai de todas as tabelas
 *
 * @package   <?=$this->config->namespace?>Model
 * @subpackage DbTable
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 * @version   <?=$this->config->version."\n"?>
 *
 * @abstract
 */

/**
 * Zend DB Table Abstract class
 */

abstract class <?=$this->config->namespace?$this->config->namespace."_":""?>Model_TableAbstract extends Zend_Db_Table_Abstract
{
    /**
     * Nome da tabela do banco de dados
     *
     * @var string
     * @access protected
     */
    protected $_name;

    /**
     * Schema da tabela do banco de dados
     *
     * @var string
     * @access protected
     */
    protected $_schema;

    /**
     * Nome do objeto quando retornado so um valor das consultas
     *
     * @var string
     * @access protected
     */
    protected $_rowClass;

    /**
     * Nome da Primary Key
     *
     * @var string|array
     * @access protected
     */
    protected $_primary;

    /**
     * Retorna nome da primary key da column
     *
     * @return string|array
     */
    public function getPrimaryKeyName ()
    {
        return $this->_primary;
    }

    /**
     * Retorna nome da tabela
     *
     * @return string
     */
    public function getTableName ()
    {
        return $this->_name;
    }

    /**
     * Retorna nome do Schema
     *
     * @return string
     */
    public function getTableSchema ()
    {
        return $this->_schema;
    }

    /**
     * @param array $config
     * @return Model_TableAbstract
     */
    public static function getIntance($config = array())
    {
        $name =  get_called_class();
        return new $name( $config );
    }

    /**
     * Retorna o numero de linhas na tabela com o parametro opcional WHERE
     *
     * @param $where mixed Where é um parametro opcional da query
     *
     * @return int
     */
    public function countAllRows ( $where = '' )
    {
        $query = $this->select ()->from ( $this->_name , 'count(*) AS all_count' );

        if ( ! empty( $where ) && is_string ( $where ) )
        {
            $query->where ( $where );
        } elseif ( is_array ( $where ) && isset( $where[ 0 ] ) )
        {
            foreach ( $where as $i => $v )
            {
                /**
                 * Checks if you're passing an PDO escape statement
                 * ->where('price > ?', $price)
                 */
                if ( isset( $v[ 1 ] ) && is_string ( $v[ 0 ] ) && count ( $v ) == 2 )
                {
                    $query->where ( $v[ 0 ] , $v[ 1 ] );
                } elseif ( is_string ( $v ) )
                {
                    $query->where ( $v );
                }
            }
        } else
        {
            throw new Exception( "Você deve passar int na chave do array." );
        }


        return $this->getAdapter ()->query ( $query )->fetchColumn ();
    }

}
