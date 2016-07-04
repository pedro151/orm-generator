<?="<?php\n"?>

/**
 * Data Model implementation for this class
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package <?=$objTables->getNamespace()?>
 * @see  <?=$objTables->getNamespace()?>/Model/<?=$this->getClassName ( $objTables->getName () )?>. "\n"?>
 *
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

class <?=$this->getClassName ( $objTables->getName () )?> extends <?=$objTables->getNamespace()?>\Entity\<?=$this->getClassName ( 'Abstract'.$objTables->getName () ). "\n"?>
{

<?php foreach ($objTables->getColumns() as $column): ?>
    public function set<?=$this->getClassName ( $column->getName () )?>( $<?=$column->getName()?> )
    {
        //  throw new \InvalidArgumentException('message');
        $this-><?=$column->getName()?> = $<?=$column->getName()?>;
    }

<?php endforeach;?>
<?php foreach ($objTables->getColumns() as $column): ?>
    /**
     * @return <?=$column->getType () ?>
     **/
    public function get<?=$this->getClassName ( $column->getName () )?>()
    {
        // Convert the value to <?=$column->getType () ?> before be used
        return (<?=$column->getType () ?>) $this-><?=$column->getName()?>;
    }

<?php endforeach;?>

     /* Codifique aqui */
}
