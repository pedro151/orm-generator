<?='<?php'?>

/**
 * Application Entity
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package   <?=$this->config->namespace?>Model
 * @subpackage Model
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

abstract class <?=$this->config->namespace?>Model_EntityAbstract extends Zend_Db_Table_Row_Abstract
{

    /**
    * Nome da tabela DbTable do model
    *
    * @var string
    * @access protected
    */
    protected $_tableClass;
        
    /**
     * Cria os Filtros para inserir  dados nos sets
     * 
     * @var array
     * @access protected
     */
    protected $_filters=array();
    
    /**
     * Cria as validações para inserir dados nos sets
     * 
     * @var array
     * @access protected
     */
    protected $_validators=array();

    /**
     * Inicializa funcionalidades comuns em classes de modelo
     */
    public function init()
    {
    }

     /**
     * lista Associativa em array das colunas
     *
     * @var array
     * @access protected
     */
    protected $_columnsList;

    /**
     * converte coluna do banco de dados para nome da função em PHP dos setter/getter
     * @param string $column
     * @return string|null
     */
    protected function varNameToColumn($thevar)
    {
        foreach ($this->_columnsList as $column => $var) {
            if ($var == $thevar) {
                return $column;
            }
        }

        return null;
    }


    /**
     * Filtra o nome das colunas para gerar os getters/setters
     *
     * @param string $columnName
     * @return string
     * @access protected
     */
    protected function columnNameFilter($columnName)
    {
    	 $columnName = preg_replace_callback('/_(.)/',
                                         function ($matches) {
                                           return ucfirst($matches[1]);},
                                          $columnName);

          return ucfirst($columnName);
    }



    /**
     * Reconhece metodos como:
     * <code>findBy&lt;field&gt;()</code>
     * <code>findOneBy&lt;field&gt;()</code>
     *
     * @method array findBy<field>()
     * @method Zend_Db_Table_Row findOneBy<field>()
     * @param string $method
     * @throws Exception se o metodo nao existir
     * @param array $args
     */
    public function __call($method, array $args)
    {
        $matches = array();
        $result = null;

        if (preg_match('/^find(One)?By(\w+)?$/', $method, $matches)) {
            $methods = get_class_methods($this);
            $check = 'set' . $matches[2];

            $fieldName = $this->varNameToColumn($matches[2]);

            if (! in_array($check, $methods)) {
                throw new Exception(
                    "Campo {$matches[2]} invalido na solicitação para a tabela"
                );
            }

            if ($matches[1] != '') {
                $dados = $this->getTable()->fetchRow(array($fieldName.'=?'=> $args[0]));
                if(!empty($dados)){
                    $this->_data = $dados->toArray();
                }
                $result = $this;
            } else {
                $result = $this->getTable()->fetchAll(array($fieldName.'=?'=> $args[0]));
            }

            return $result;
        }

        throw new Exception("Método '$method()' não reconhecido");
    }

    /**
     *  __set() é executado quando a gravação de dados
     * inacessíveis sobrecarregando-a para apoiar a definição de colunas
     *
     * Example:
     * <code>class->column_name='foo'</code> ou <code>class->ColumnName='foo'</code>
     *  irá executar a função <code>class->setColumnName('foo')</code>
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception se a propriedade/coluna nao existir
     */
    public function __set($name, $value)
    {
        $method = 'set' . $this->columnNameFilter($name);

        if (! method_exists($this, $method) ) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Metodo \"{$method}\" não existe na classe.");
        }

        if (!empty($name) && array_key_exists($name, $this->_columnsList)) 
		{
			if (!isset($this->_data[$name]) || !($this->_data[$name] === $value) )
			{
				$this->_modifiedFields[$name] = true;
			}
            $this->_data[$name] = $value;
        }else{
            $this->$method($value);
        }

    }

    /**
     * __get() quando á uma leitura de variavel tem uma sobrecarga incapacitando leituras invalidas
     *
     * Example:
     * <code>$foo=class->column_name</code> ou <code>$foo=class->ColumnName</code>
     *  irá executar a função <code>$foo=class->getColumnName()</code>
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception se a propriedade/coluna nao existir
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' .  $this->columnNameFilter($name);

        if (! method_exists($this, $method) ) {
			throw new Zend_Db_Table_Row_Exception("Specified column \"{$name}\" is not in the row");
        }

        if (!empty($name) && array_key_exists($name, $this->_columnsList)) {
            if (!array_key_exists($name, $this->_data)) {
                return null;
            }
        return $this->_data[$name];
        }else{
            return $this->$method();
        }
    }

    /**
     * Array com values dos set deste model.
     *
     * @param array $data
     * @return ApplicationModel_Sma_ModelAbstract
     */
    public function populate ( array $data )
    {
	$methods = get_class_methods ( $this );
	foreach ( $data as $key => $value )
	{
	    $key = preg_replace_callback ( '/_(.)/', create_function (
			    '$matches', 'return ucfirst($matches[1]);'
		    ), $key );
	    $method = 'set' . ucfirst ( $key );

	    if ( in_array ( $method, $methods ) )
	    {
		$this->$method ( $value );
	    }
	}
	return $this;
    }


    /**
     * Retorna o nome da coluna da primary key
     *
     * @see <?=$this->config->namespace?>Model__DbTable_TableAbstract::getPrimaryKeyName()
     * @return string|array The name or array of names which form the primary key
     */
    public function getPrimaryKeyName()
    {
        return $this->getTable()->getPrimaryKeyName();
    }

    /**
     * Retorna um array associativo de pares de valores de colunas se a
     * primary key é um array de valores, ou o valor da primary key se não for array
     *
     * @return any|array
     */
    public function getPrimaryKey()
    {
        $primary_key = $this->getPrimaryKeyName();

        if (is_array($primary_key)) {
            $result = array();
            foreach ($primary_key as $key) {
                $result[$key] = $this->$key;
            }

            return $result;
        } else {
            return $this->$primary_key;
        }

    }

    /**
     * Retorna o objeto pela primary key
     *
     * @param int|array $primary_key
     * @return <?=$this->config->namespace?>Model__ModelAbstract
     */
public function find ( $primary_key )
	{
		$primary_key	 = (array) $primary_key;
		$primary_keyIn	 = $this->getPrimaryKeyName ();

		$where = array();
		foreach ( $primary_key as $key => $PK_Value )
		{
			if ( is_int ( $key ) && count ( $primary_key ) === 1 )
			{
				$where[$primary_keyIn[1] . '=?'] = $PK_Value;
			}
			elseif ( is_string ( $key ) && in_array ( $key, $primary_keyIn ) )
			{
				$where[$key . '=?'] = $PK_Value;
			}
			else
			{
				throw new Exception ( "verifique o valor e o campo da primary key da tabela " . get_class ( $this ) . " o nome ou os campo esta incorreto.\n" );
			}
		}

		if ( count ( $where ) > 0 )
		{
			$dados = $this->getTable ()->fetchRow ( $where );
			if ( !empty ( $dados ) )
			{
				$this->_data = $dados->toArray ();
			}
		}
		return $this;
	}
	
	/**
	 * insere os dados independente se possui primary key ou nao
	 * 
	 * @return int primary key
	 */
	public function insert()
    {
       return $this->_doInsert();
    }
	
	public function update ()
	{
		$this->_cleanData = $this->_data;
		return $this->_doUpdate ();
	}

	/**
	 * @see Zend_Db_Table_Row_Abstract::save()
	 */
    public function save()
    {
        $primary_keyIn = $this->getPrimaryKeyName();

        $result = array();
        foreach ($primary_keyIn as $key => $primary_key) {
            $result[$key]=false;
            if(!empty($this->$primary_key))
                $result[$key] = true;
        }

        if (!in_array(false, $result)) {
            $this->_cleanData = $this->_data;
        }
        
        if(count($this->_modifiedFields) > 0 ){
			return parent::save ();
		}
    }

}