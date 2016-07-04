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
 */

namespace  <?=$objTables->getNamespace()?>\Entity;

abstract class <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends \Phalcon\Mvc\Model
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
    * @Column(type="<?=$column->getType()?>", nullable=<?=$column->isNullable () ? "true" : "false"?><?php
if ( $column->getMaxLength () ): ?>
, length=<?=$column->getMaxLength ()?>
<?php endif ?>)
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
<?php foreach ($objTables->getColumns() as $column): ?>
<?php if(strtolower($column->getName()) == 'email'):?>
        $this->validate(
            new \Phalcon\Mvc\Model\Validator\Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );

<?php endif ?>
<?php endforeach;?>
        return $this->validationHasFailed() != true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        <?=$mapParents."\n"?>
        <?=$mapDependents."\n"?>
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
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return \<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?>[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return \<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )."\n"?>
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }


<?php foreach ($objTables->getColumns() as $column): ?>
    public function set<?=$this->getClassName ( $column->getName () )?>( $<?=$column->getName()?> )
    {
        $this-><?=$column->getName()?> = $<?=$column->getName()?>;
    }

<?php endforeach;?>
<?php foreach ($objTables->getColumns() as $column): ?>
    /**
     * @return <?=$column->getType ()."\n" ?>
     **/
    public function get<?=$this->getClassName ( $column->getName () )?>()
    {
        return (<?=$column->getType () ?>) $this-><?=$column->getName()?>;
    }

<?php endforeach;?>
}