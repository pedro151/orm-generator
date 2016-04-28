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

namespace  <?=$objTables->getNamespace()?>;

use Phalcon\Mvc\Model;

class <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends Model
{

<?php foreach ($objTables->getColumns() as $column): ?>
    /**
    * column <?=$column->getName()."\n"?>
    *
    */
    protected $<?=$column->getName()?>;

<?php endforeach;?>

    public function initialize()
    {
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