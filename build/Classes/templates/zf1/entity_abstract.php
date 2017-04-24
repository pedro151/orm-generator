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
 * @version   <?=$this->config->version."\n"?>
 */

abstract class <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract extends Zend_Db_Table_Row_Abstract
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
    * @var Zend_Filter_Input
    */
    protected $_input;

    /**
     * Inicializa funcionalidades comuns em classes de modelo
     */
    public function init()
    {
        $this->_input = new Zend_Filter_Input($this->getFilters(), $this->getValidator() , null , array() );
        $this->_input->setDefaultEscapeFilter ( new Zend_Filter_HtmlEntities( ENT_COMPAT, "<?=$this->config->charset?>" ) );
    }

    /**
     * @return bool
     */
    public function isValid ()
    {
        $this->_input->setData($this->_data);

        return $this->_input->isValid();
    }

    /**
     * @return array
     */
    public function getMessages ()
    {
        return $this->_input->getMessages();
    }

    protected function __process()
    {
        $this->_input->setData($this->_data);
        $this->_input->process();
        $this->_data = $this->_input->getEscaped();
    }

    /**
     * @return array
     */
    public function getErrors ()
    {
        return $this->_input->getErrors();
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
            if ($var == strtolower($thevar)) {
                return $column;
            }
        }

        return null;
    }

    /**
    * @param array $data
    * @return <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract
    */
    public static function getIntance($data = array())
    {
        $name =  get_called_class();
        return new $name( $data );
    }

    /**
     * Filtra o nome das colunas para gerar os getters/setters
     *
     * @param string $columnName
     * @return string
     * @access protected
     */
    protected function columnNameFilter ( $columnName )
    {
        $columnName = preg_replace_callback ( '/_(.)/' ,
            function ( $matches )
            {
                return ucfirst ( $matches[ 1 ] );
            } ,
            strtolower ( $columnName ) );

        return ucfirst ( $columnName );
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
    public static function __callStatic($method, array $args)
    {
        $matches = array();
        $result = null;

        if (preg_match('/^find(One)?By(\w+)?$/', $method, $matches)) {
            $InstanceObject = self::getIntance();
            $methods = get_class_methods($InstanceObject);
            $check = 'set' . $matches[2];

            $fieldName = $InstanceObject->varNameToColumn($matches[2]);

            if (! in_array($check, $methods)) {
                throw new Exception(
                    "Campo {$matches[2]} invalido na solicitação para a tabela"
                );
            }

            if ($matches[1] != '') {
                $dados = $InstanceObject->getTable()->fetchRow(array($fieldName.'=?'=> $args[0]));
                if(!empty($dados)){
                    $InstanceObject->_data = $dados->toArray();
                }
                $result = $InstanceObject;
            } else {
                $result = $InstanceObject->getTable()->fetchAll(array($fieldName.'=?'=> $args[0]));
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

        if (in_array($name, $this->getTable()->info('cols')))
		{
			if (!isset($this->_data[$name]) or !($this->_data[$name] === $value) )
			{
				$this->_modifiedFields[$name] = true;
			}
            $this->_data[$name] = $value;
        }
        else
        {
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

        if ( in_array ( $name, $this->getTable ()->info ( 'cols' ) ) ) {
            return isset( $this->_data[ $name ] ) ? $this->_data[ $name ] : null;
        }
        elseif ( method_exists ( $this, $method ) ) {
            return $this->$method();
        }
        else {
            return null;
        }
    }

    /**
     * Array com values dos set deste model.
     *
     * @param array $data
     * @return <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract
     */
    public function populate ( array $data )
    {
        $methods = get_class_methods ( $this );
        foreach ( $data as $key => $value )
        {
            $key = preg_replace_callback ( '/_(.)/', create_function (
                    '$matches', 'return ucfirst($matches[1]);'
                ), strtolower( $key ) );
            $method = 'set' . ucfirst ( $key );

            if ( in_array ( $method, $methods ) && !in_array($key, (array) $this->_primary) )
            {
                $this->$method ( $value );
            }
        }

        return $this;
    }

     /**
     * @return array
     */
    public function toArrayGets ()
    {
        $render = array ();
        $methods = get_class_methods ( $this );
        foreach ( $this->_data as $key => $value )
        {
            $key2 = preg_replace_callback ( '/_(.)/' , create_function (
                '$matches' , 'return ucfirst($matches[1]);'
            ) , strtolower ( $key ) );
            $method = 'get' . ucfirst ( $key2 );

            if ( in_array ( $method , $methods ) )
            {
                $render[ $key ] = $this->$method ();
            }
        }

        return $render;
    }


    /**
     * Retorna o nome da coluna da primary key
     *
     * @see <?=$this->config->namespace?$this->config->namespace."_":""?>Model_DbTable_TableAbstract::getPrimaryKeyName()
     * @return string|array The name or array of names which form the primary key
     */
    public function getPrimaryKeyName()
    {
        return $this->getTable()->getPrimaryKeyName();
    }

    /**
     * @param int $primarykey
     *
     * @return <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract
     */
    public function find ( $primarykey )
    {
       $obj = self::retrieve ( $primarykey );
       if(is_object($obj)){
           $this->_data = self::retrieve ( $primarykey )->toArray();
       }
       return $this;
    }

    /**
     * @see Zend_Db_Table_Rowset_Abstract::fetchAll
     *
     * @return <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract[]
     */
    public function fetchAll ( $where = null , $order = null , $count = null , $offset = null )
    {
       return self::retrieveAll ( $where , $order , $count , $offset );
    }

    /**
     * Retorna o objeto pela primary key
     *
     * @param int|array $primary_key
     * @return <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract
     */
    public static function retrieve ( $primarykey )
    {
        $primarykey = !is_array($primarykey)?(array)$primarykey:$primarykey;
        $dbTable = self::getIntance()->getTable();
        $imput = call_user_func_array(array($dbTable , "find" ), $primarykey);
        return  $imput->current();
    }

    /**
     * insere os dados independente se possui primary key ou nao
     *
     * @return int primary key
     */
    public function insert()
    {
        $this->__process();
        return $this->_doInsert();
    }

    /**
     * atualiza os dados independente se possui primary key ou nao
     *
     * @return int primary key
     */
    public function update ()
    {
        $this->__process();
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

    /**
    * @see Zend_Db_Table_Rowset_Abstract::fetchAll
    */
    public static function retrieveAll ( $where = null , $order = null , $count = null , $offset = null )
    {
        return self::getIntance()->getTable()->fetchAll ( $where , $order , $count , $offset );
    }

    /**
     * Retorna a Dbtable da class model
     *
     * @return null|Zend_Db_Table_Abstract
     * @throws Zend_Db_Table_Row_Exception
     */
    public function getTable()
    {
        if ($this->_table === null) {
            $this->setTable(new $this->_tableClass());
        }

        return $this->_table;
    }

    /**
     * @param string $columnName
     *
     * @return array
     */
    public function getValidator($columnName = null)
    {
        if(isset($this->_validators[$columnName]))
        {
            return $this->_validators[$columnName];
        }

        return $this->_validators;
    }

    /**
     * @param string $columnName
     *
     * @return array
     */
    public function getFilters($columnName = null)
    {
        if(isset($this->_filters[$columnName]))
        {
            return $this->_filters[$columnName];
        }
        return $this->_filters;
    }

}