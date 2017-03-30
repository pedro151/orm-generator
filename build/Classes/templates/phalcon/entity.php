<?='<?php'?>

/**
 * Mvc Model
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package   <?=$objTables->getNamespace()?>\Entity
 * @subpackage Model
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 * @version   <?=$this->config->version."\n"?>
 */

namespace  <?=$objTables->getNamespace()?>\Entity;

use Phalcon\Validation;

abstract class <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends \<?=$this->config->namespace?$this->config->namespace."\\":""?>Models\<?=$objMakeFile->getFilesFixeds('parentClass')->getFileName() . "\n"?>
{

<?php foreach ($objTables->getColumns() as $column): ?>
    /**
    * column <?=$column->getName()."\n"?>
    *
<?php if($column->isPrimaryKey()):?>
    * @Primary
<?php endif ?>
<?php if($column->hasSequence()):?>
    * @Identity
<?php endif ?>
    * @Column(type="<?=$column->getTypeByConfig( $this->config )?>", nullable=<?=($column->isNullable () or $column->hasColumnDefault()) ? "true" : "false"?><?php
if ( $column->getMaxLength () ): ?>
, length=<?=$column->getMaxLength ()?>
<?php endif ?>, column="<?=$column->getName()?>" )
    */
    protected $<?=$column->getName()?>;

<?php endforeach;?>
    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

<?php foreach ($objTables->getColumns() as $column): ?>
<?php if(strtolower($column->getName()) == 'email'):?>
        $validator->add(
            'email',
            new \Phalcon\Validation\Validator\Email()
        );

<?php endif ?>
<?php endforeach;?>
        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        <?=$mapDependents."\n"?>
        <?=$mapParents."\n"?>
    }

<?php if($objTables->hasSchema()): ?>
    /**
     * Returns schema name where table mapped is located
     *
     * @return string
     */
    public function getSchema()
    {
        return '<?=$objTables->getSchema()?>';
    }

<?php endif ?>
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return '<?=$objTables->getName()?>';
    }

<?php if( $objTables->hasSequences() ) : ?>
    public function getSequenceName()
    {
<?php if ( 1 ==  count($objTables->getSequences() ) ) : ?>
    <?php $seqs = $objTables->getSequences();reset($seqs);$seq = current($seqs);?>
        return "<?=$seq->getSequence() ?>";
<?php endif ?>
    }

<?php endif ?>
<?php foreach ($objTables->getColumns() as $column): ?>
    /**
    * @return  <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () ) . "\n" ?>
    */
    public function set<?=$this->getClassName ( $column->getName () )?>( $<?=$column->getName()?> )
    {
        $this-><?=$column->getName()?> = $<?=$column->getName()?>;

        return $this;
    }

    /**
    * @return <?=$column->getType ( \Classes\Db\Column::TypePHP )."\n" ?>
    **/
    public function get<?=$this->getClassName ( $column->getName () )?>()
    {
        return (<?=$column->getType ( \Classes\Db\Column::TypePHP )?>) $this-><?=$column->getName()?>;
    }
<?php endforeach;?>
}