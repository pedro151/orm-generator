<?="<?php\n"?>

/**
 * Application Model DbTables
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * Tabela definida por 'tablename'
 *
 * @package   <?=$objTables->getNamespace()?><?="\n"?>
 * @subpackage DbTable
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 * @version   <?=$this->config->version."\n"?>
 */

class <?=$objTables->getNamespace()?>_DbTable_<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends <?=$this->config->namespace?$this->config->namespace."_":""?>Model_<?=$objMakeFile->getParentClass() . "\n"?>
{
    /**
     * Nome da tabela do banco de dados
     *
     * @var string
     * @access protected
     */
    protected $_name = '<?=$objTables->getName()?>';
<?php if($objTables->hasSchema()): ?>

    /**
     * Schema da tabela do banco de dados
     *
     * @var string
     * @access protected
     */
    protected $_schema = '<?=$objTables->getSchema()?>';
<?php endif ?>

    /**
     * Nome do objeto quando retornado so um valor das consultas
     *
     * @var string
     * @access protected
     */
    protected $_rowClass = '<?=$objTables->getNamespace()?>_<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?>';

<?php if( $objTables->hasPrimaryKey() ):?>
	/**
     * Nome da Primary Key
     *
     * @var
     * @access protected
     */
    protected $_primary = array(
<?php foreach($objTables->getPrimaryKeys() as $pks):?>
        '<?=$pks->getName()?>',
<?php endforeach; ?>
    );
<?php endif ?>
<?php if($this->config->{'folder-name'}):?>
    /**
    * Initialize database adapter.
    *
    * @return void
    * @see Zend_Db_Table_Abstract::_setupDatabaseAdapter
    * @throws Zend_Db_Table_Exception
    * @throws Zend_Exception
    */
    protected function _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get( '<?=($this->config->{'folder-name'})?>');
        parent::_setupDatabaseAdapter();
    }
<?php endif ?>
<?php if( $objTables->hasSequences() ) : ?>

	/**
     * Definir a lógica para os novos valores na chave primária.
     * Pode ser uma string, boolean true ou false booleano.
     *
     * @var mixed
     */
<?php if ( 1 ==  count($objTables->getSequences() ) ) : ?>
<?php if(strpos($this->config->driver, 'pgsql')): ?>
<?php $seqs = $objTables->getSequences();reset($seqs);$seq = current($seqs);?>
    protected $_sequence = '<?=$seq->getSequence() ?>';
<?php else: ?>
    protected $_sequence = true;
<?php endif ?>
<?php endif ?>
<?php else: ?>
    protected $_sequence = false;
<?php endif ?>

    <?=$referenceMap?>

    <?=$dependentTables?>

}
