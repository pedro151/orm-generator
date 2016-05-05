<?='<?php'?>

/**
 * Mvc Model
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

namespace  <?=$objTables->getNamespace()?>\Entity;

use Phalcon\Mvc\Model;

class <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends Model
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
<?php if($objTables->hasSchema()): ?>
    public function getSchema()
    {
        return '<?=$objTables->getSchema()?>';
    }

<?php endif ?>
    public function initialize()
    {
        parent::initialize();
        //$this->hasMany('id', '<?=$objTables->getNamespace()?><?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?>', 'robots_id');
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





}