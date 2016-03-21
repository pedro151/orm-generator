<?="<?php\n"?>

/**
 * Application Model DbTables
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * Tabela definida por 'tablename'
 *
 * @package   <?=$objTables->getNamespace()?>
 * @subpackage DbTable
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

class <?=$objTables->getNamespace()?>_DbTable_<?=\Classes\Maker\Template::getClassName ( $objTables->getName () )?> extends <?=$this->config->namespace?>Model_<?=$objMakeFile->getParentClass() . "\n"?>
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
    protected $_rowClass = '<?=$objTables->getNamespace()?>_<?=\Classes\Maker\Template::getClassName ( $objTables->getName () )?>';

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

	/**
     * Definir a lógica para os novos valores na chave primária.
     * Pode ser uma string, boolean true ou false booleano.
     *
     * @var mixed
     */
<?php if( $objTables->hasSequences() ) : ?>
	protected $_sequence = array(
<?php foreach( $objTables->getSequences() as $seq ): ?>
        '<?=$seq->getSequence() ?>',
<?php endforeach; ?>
    );
<?php else: ?>
    protected $_sequence = false;
<?php endif ?>

<?=$referenceMap?>

<?=$dependentTables?>

}
