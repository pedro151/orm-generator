<?="<?php\n"?>
<?php $className = $objTables->getNamespace(). '_Entity_' . $this->getClassName ( $objTables->getName () )?>

/**
 * Application Entity
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package <?=$objTables->getNamespace()."\n"?>
 * @subpackage Entity
 *
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

/**
 * Abstract class for entity
 */
require_once dirname(__FILE__) . '/../../EntityAbstract.php';

abstract class <?=$className?> extends <?=$this->config->namespace?>Model_<?=$objMakeFile->getParentClass() . "\n"?>
{

<?php foreach ($objTables->getColumns() as $column): ?>
    /**
    * Database constraint in the column <?=$column->getName()."\n"?>
    *
    */
    const <?=strtoupper($column->getName())?> = '<?=$objTables->getName()?>.<?=$column->getName()?>';
<?php endforeach;?>

    /**
    * Nome da tabela DbTable do model
    *
    * @var string
    * @access protected
    */
    protected $_tableClass = '<?=$objTables->getNamespace()?>_DbTable_<?=$this->getClassName ( $objTables->getName () )?>';

    /**
     * @see <?=$this->config->namespace?>Model_EntityAbstract::$_filters
     */
    protected $_filters = array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
<?php
    $filters = null;
    switch ( ucfirst ( $column->getType () ) )
    {
        case 'String':
            $filters = 'StripTags", "StringTrim';
            break;
        case 'Float':
            $filters = 'Digits';
            break;
        default:
            $filters = ucfirst ( $column->getType () );
            break;
    }
?>
        '<?=$column->getName()?>'=>array (
            <?=( !empty( $filters ) ) ? "\"{$filters}\"\n" : null; ?>
        ),
<?php endforeach;?>
    );

    /**
     * @see <?=$this->config->namespace?>Model_EntityAbstract::$_validators
     */
    protected $_validators= array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
<?php
    $validators = array ();

    $validators[] = $column->isNullable () ? "'allowEmpty' => true" : "'NotEmpty' => true";

    switch ( ucfirst ( $column->getType () ) )
    {
        case 'String':
            if ( $column->getMaxLength () )
            {
                $validators[] = "array( 'StringLength', array( 'max' => " . $column->getMaxLength () . " ) )";
            }

            break;
        case 'Boolean':
            break;
        default:
            $name = ucfirst ( $column->getType () );
            $validators[] = "'$name'";
            break;
    }
    $validators = implode ( ", ", $validators )?>
        '<?= $column->getName () ?>' => array (
             <?=( !empty( $validators ) ) ? "{$validators}\n" : null?>
        ),
<?php endforeach; ?>
    );


    /**
    * Nome da Primary Key
    *
    * @var string
    * @access protected
    */
<?php if(count($objTables->getPrimarykeys())>1):?>
    protected $_primary = array(
    <?php foreach($objTables->getPrimarykeys() as $pks) : ?>
            '<?=$pks->getName()?>',
    <?php endforeach ?>
    );
<?php elseif(count($objTables->getPrimarykeys())==1): ?>
<?php $pk = $objTables->getPrimarykeys() ?>
    protected $_primary = '<?=$pk[0]->getName()?>';
<?php endif ?>

<?php foreach ($parents as $parent): ?>
    /**
    * Parent relation <?=$this->getClassName($parent['column']) . "\n"?>
    *
    * @var <?=$parent['class'] . "\n"?>
    */
    protected $_<?=$this->getClassName($parent['column'])?>;

<?php endforeach;?>
<?php foreach ($depends as $depend): ?>
    /**
     * Parent relation <?=$this->getClassName($depend['table']) . "\n"?>
     *
     * @var <?=$depend['class'] . "\n"?>
     */
     protected $_<?=$depend['table']?>;

<?php endforeach;?>
<?php foreach ($objTables->getColumns() as $column): ?>
    /**
    *
    * Sets column <?=$column->getName()."\n"?>
    *
<?php if ($column->getType()=='date'): ?>
    * Stored in ISO 8601 format.
    *
    * @param string|Zend_Date $<?=$column->getName() . "\n"?>
<?php else: ?>
    * @param <?=$column->getType()?> $<?=$column->getName() . "\n"?>
<?php endif; ?>
    * @return <?=$className . "\n"?>
    */
    public function set<?=$this->getClassName($column->getName())?>($<?=$column->getName()?>)
    {
<?php switch ( $column->getType () ):
        case 'date': ?>
        if (! empty($<?=$column->getName()?>))
        {
            if (! $<?=$column->getName()?> instanceof Zend_Date)
            {
                $<?=$column->getName()?> = new Zend_Date($<?=$column->getName()?>);
            }

            $this-><?=$column->getName()?> = $<?=$column->getName()?>->toString(Zend_Date::ISO_8601);
        }

<?php break ?>
<?php case 'boolean': ?>
        $this-><?=$column->getName()?> = $<?=$column->getName()?> ? true : false;

<?php break ?>
<?php default: ?>
        $<?=$column->getName()?> = (<?=ucfirst($column->getType())?>) $<?=$column->getName()?> ;
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array('<?=$column->getName()?> '=>$<?=$column->getName()?> ));
        if(!$input->isValid ('<?=$column->getName()?> '))
        {
            $errors =  $input->getMessages ();
            foreach ( $errors['<?=$column->getName()?> '] as $key => $value )
            {
                throw new Exception ( $value );
            }
        }

        $this-><?=$column->getName()?>  = $<?=$column->getName()?> ;

<?php break ?>
<?php endswitch ?>
	    return $this;
    }

    /**
    * Gets column <?=$column->getName() . "\n"?>
    *
<?php if ($column->getType()=='date'): ?>
    * @param boolean $returnZendDate
    * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
<?php else: ?>
    * @return <?=$column->getType() . "\n"?>
<?php endif; ?>
    */
    public function get<?=$this->getClassName($column->getName())?>(<?php if ($column->getType()=='date'): ?>$returnZendDate = false <?php endif;?>)
    {
<?php if ($column->getType()=='date'): ?>
        if ($returnZendDate)
        {
            if ($this->_data['<?=$column->getName()?>'] === null)
            {
                return null;
            }

            return new Zend_Date($this-><?=$column->getName()?>, Zend_Date::ISO_8601);
        }

<?php endif; ?>
        return $this-><?=$column->getName()?>;
    }

<?php endforeach; ?>
<?php foreach ($parents as $parent): ?>
    /**
    * Gets parent <?=$this->getClassName($parent['column']) . "\n"?>
    *
    * @return <?=$parent['class'] . "\n"?>
    */
    public function get<?=$parent['function']?>()
    {
        if ($this->_<?=$parent['column']?> === null)
        {
            $this->_<?=$parent['column']?> = $this->findParentRow('<?=$objTables->getNamespace()?>_DbTable_<?=$this->getClassName($parent['table'])?>', '<?=$this->getClassName($parent['column'])?>');
        }

        return $this->_<?=$parent['column']?>;
    }

<?php endforeach; ?>
<?php foreach ($depends as $value): ?>
    /**
    * Gets dependent <?=$this->getClassName($depend['column']) . "\n"?>
    *
    * @return <?=$depend['class'] . "\n"?>
    */
    public function get<?=$depend['function']?>()
    {
        if ($this->_<?=$depend['column']?> === null)
        {
            $this->_<?=$depend['column']?> = $this->findParentRow('<?=$objTables->getNamespace()?>_DbTable_<?=$this->getClassName($depend['table'])?>', '<?=$this->getClassName($depend['column'])?>');
        }

      return $this->_<?=$depend['column']?>;
    }

<?php endforeach; ?>
    /**
    * Retorna a Dbtable da class model
    *
    * @return <?=$objTables->getNamespace()?>_DbTable_<?=$this->getClassName ( $objTables->getName () ).'\n'?>
    */
    public function getTable()
    {
        if ($this->_table === null) {
            $this->setTable(new <?=$objTables->getNamespace()?>_DbTable_<?=$this->getClassName ( $objTables->getName () )?>());
        }

        return $this->_table;
    }

    /**
    * @see Zend_Db_Adapter::fetchAll
    */
    public static function fetchAll ( $where = null , $order = null , $count = null , $offset = null )
    {
        $name = __CLASS__;
        $InstanceObject = new $name();
        return $InstanceObject->getTable()->fetchAll ( $where , $order , $count , $offset );
    }
}