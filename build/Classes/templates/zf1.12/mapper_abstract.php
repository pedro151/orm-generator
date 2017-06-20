<?='<?php'?>

/**
 * Application Mapper
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package   <?=$this->config->namespace?>Mapper
 * @subpackage Model
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 * @version   <?=$this->config->version."\n"?>
 */

abstract class <?=$this->config->namespace?$this->config->namespace."_":""?>Model_MapperAbstract
{

    /**
     * Nome da tabela DbTable do model
     *
     * @var string
     * @access protected
     */
    protected $_tableClass;

    /**
     * @var Zend_Db_Table_Abstract
     */
    private $_table;

    /**
     * Retorna a Dbtable da class model
     *
     * @return null|Zend_Db_Table_Abstract
     * @throws Zend_Db_Table_Row_Exception
     */
    public function getTable()
    {
        if ($this->_table === null) {
            $this->_table = new $this->_tableClass();
        }

        return $this->_table;
    }
}