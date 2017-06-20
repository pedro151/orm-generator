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

abstract class <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityAbstract
{

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
        $this->_input->setDefaultEscapeFilter ( new Zend_Filter_StripTags( ENT_COMPAT, "<?=$this->config->charset?>" ) );
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

    private static function CamelCase ( $name ) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    /**
     *  __set() é executado quando a gravação de dados
     * inacessíveis sobrecarregando-a para apoiar a definição de colunas
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception se a propriedade/coluna nao existir
     */
    public function __set($name, $value)
    {
        $method = 'set' . self::CamelCase ( $name );
        if (! method_exists($this, $method) ) {
            throw new <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityException("Metodo \"{$method}\" não existe na classe.");
        }

        $this->$method($value);
    }

    /**
     * __get() quando á uma leitura de variavel tem uma sobrecarga incapacitando leituras invalidas
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception se a propriedade/coluna nao existir
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . self::CamelCase ( $name );
        if (! method_exists($this, $method) ) {
			throw new <?=$this->config->namespace?$this->config->namespace."_":""?>Model_EntityException("Metodo \"{$method}\" não existe na classe.");
        }

        return $this->$method();
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
            $method = 'set' . self::CamelCase ( $key );
            if ( in_array ( $method, $methods ) )
            {
                $this->$method ( $value );
            }
        }

        return $this;
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