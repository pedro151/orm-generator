<?= "<?php\n" ?>
<?php $className = $objTables->getNamespace () . '_Entity_' . \Classes\Maker\AbstractMaker::getClassName (
        $objTables->getName ()
    ) ?>

/**
 * Application Entity
 *
 * <?= $this->config->last_modify . "\n" ?>
 *
 * @package <?= $objTables->getNamespace () . "\n" ?>
 * @subpackage Entity
 *
 * @author    <?= $this->config->author . "\n" ?>
 *
 * @copyright <?= $this->config->copyright . "\n" ?>
 * @license   <?= $this->config->license . "\n" ?>
 * @link      <?= $this->config->link . "\n" ?>
 * @version   <?= $this->config->version . "\n" ?>
 */

/**
 * Abstract class for entity
 */

abstract class <?= $className ?> extends <?= $this->config->namespace ? $this->config->namespace
                                                                        . "_" : "" ?>Model_<?= $objMakeFile->getParentClass (
) . "\n" ?>
{

<?php foreach ( $objTables->getColumns () as $column ): ?>
    /**
     * Database constraint in the column <?= $column->getName () . "\n" ?>
     *
     */
    const <?= strtoupper ( $column->getName () ) ?> = '<?= $objTables->getName () ?>.<?= $column->getName () ?>';
<?php endforeach; ?>

    /**
     * Nome da tabela DbTable do model
     *
     * @var string
     * @access protected
     */
    protected $_tableClass = '<?= $objTables->getNamespace () ?>_DbTable_<?= \Classes\Maker\AbstractMaker::getClassName (
    $objTables->getName ()
) ?>';

    /**
     * @see <?= $this->config->namespace ?>Model_EntityAbstract::$_columnsList
     */
    protected $_columnsList = array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
        self::<?= strtoupper ( $column->getName () ) ?> => '<?= strtolower (
            \Classes\Maker\AbstractMaker::getClassName ( $column->getName () )
    ) ?>',
<?php endforeach; ?>
    );

    /**
     * @see <?= $this->config->namespace ?>Model_EntityAbstract::$_filters
     */
    protected $_filters = array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
<?php
    $filters = null;
    switch ( ucfirst ( $column->getType () ) ) {
        case 'String':
            $filters = 'StripTags", "StringTrim';
            break;
        case 'Float':
            $filters = 'Digits';
            break;
        case 'Date':
            break;
        default:
            $filters = ucfirst ( $column->getType () );
            break;
    }
    ?>
    '<?= $column->getName () ?>' => array (
            <?= ( !empty( $filters ) ) ? "\"{$filters}\"\n" : null; ?>
        ),
<?php endforeach; ?>
    );

    /**
     * @see <?= $this->config->namespace ?>Model_EntityAbstract::$_validators
     */
    protected $_validators= array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
<?php
    $validators = array ();

    $validators[] = $column->isNullable () ? "'allowEmpty' => true" : "'NotEmpty'";

    switch ( ucfirst ( $column->getType () ) ) {
        case 'String':
            if ( $column->getMaxLength () ) {
                $validators[] = "array( 'StringLength', array( 'max' => " . $column->getMaxLength () . " ) )";
            }

            break;
        case 'Boolean':
            break;
        default:
            $name         = ucfirst ( $column->getType () );
            $validators[] = "'$name'";
            break;
    }
$validators = implode ( ", ", $validators ) ?>
    '<?= $column->getName () ?>' => array (
            <?= ( !empty( $validators ) ) ? "{$validators}\n" : null ?>
        ),
<?php endforeach; ?>
    );

<?php if ( $objTables->hasPrimaryKey () ): ?>
    /**
    * Nome da Primary Key
    *
    * @var string
    * @access protected
    */
   protected $_primary = array(
<?php foreach ( $objTables->getPrimarykeys () as $pks ) : ?>
        '<?= $pks->getName () ?>',
<?php endforeach ?>
    );
<?php endif ?>

<?php foreach ( $parents as $parent ): ?>
    /**
     * Parent relation <?= \Classes\Maker\AbstractMaker::getClassName ( $parent[ 'table' ] ) . "\n" ?>
     *
     * - CONSTRAINT in DB <?= $parent[ 'name' ] . "\n" ?>
     *
     * @var <?= $parent[ 'variable' ] . "\n" ?>
     */
    protected $_parent_<?= $parent[ 'variable' ] ?>;

<?php endforeach; ?>
<?php foreach ( $depends as $depend ): ?>
    /**
     * Depends relation <?= \Classes\Maker\AbstractMaker::getClassName ( $depend[ 'table' ] ) . "\n" ?>
     *
     * - CONSTRAINT in DB <?= $depend[ 'name' ] . "\n" ?>
     *
     * @var <?= $depend[ 'variable' ] . "\n" ?>
     */
    protected $_depend_<?= $depend[ 'variable' ] ?>;

<?php endforeach; ?>
<?php foreach ( $objTables->getColumns () as $column ): ?>
    /**
     *
     * Sets column <?= $column->getName () . "\n" ?>
     *
<?php if ( $column->equalType ( 'date' ) ): ?>
     * Stored in ISO 8601 format.
     *
     * @param string|Zend_Date $<?= $column->getName () . "\n" ?>
<?php else: ?>
     * @param <?= $column->getType () ?> $<?= $column->getName () . "\n" ?>
<?php endif; ?>
     * @return <?= $className . "\n" ?>
     */
    public function set<?= \Classes\Maker\AbstractMaker::getClassName ( $column->getName () ) ?>($<?= $column->getName (
    ) ?>)
    {
<?php switch ( $column->getType () ):
        case 'date': ?>
            if (! empty($<?= $column->getName () ?>))
            {
                if (! $<?= $column->getName () ?> instanceof Zend_Date)
                {
                    $<?= $column->getName () ?> = new Zend_Date($<?= $column->getName () ?>);
                }

                $this-><?= $column->getName () ?> = $<?= $column->getName () ?>->toString(Zend_Date::ISO_8601);
            }

<?php break ?>
<?php case 'boolean': ?>
            $this-><?= $column->getName () ?> = $<?= $column->getName () ?> ? true : false;

<?php break ?>
<?php default: ?>
            $<?= $column->getName () ?> = (<?= ucfirst ( $column->getType () ) ?>) $<?= $column->getName () ?> ;
            $input = new Zend_Filter_Input($this->_filters, $this->_validators, array('<?= $column->getName (
            ) ?>'=>$<?= $column->getName () ?> ));

            if(!$input->isValid ('<?= $column->getName () ?>'))
            {
                $errors =  $input->getMessages ();
                foreach ( $errors['<?= $column->getName () ?>'] as $key => $value )
                {
                    throw new Exception ( $value );
                }
            }

            $this-><?= $column->getName () ?>  = $<?= $column->getName () ?> ;

<?php break ?>
<?php endswitch ?>
        return $this;
    }

    /**
     * Gets column <?= $column->getName () . "\n" ?>
     *
<?php if ( $column->equalType ( 'date' ) ): ?>
     * @param boolean $returnZendDate
     * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
<?php else: ?>
     * @return <?= $column->getType () . "\n" ?>
<?php endif; ?>
     */
    public function get<?= \Classes\Maker\AbstractMaker::getClassName (
        $column->getName ()
    ) ?>(<?php if ( $column->equalType ( 'date' ) ): ?>$returnZendDate = false <?php endif; ?>)
    {
<?php if ( $column->equalType ( 'date' ) ): ?>
        if ($returnZendDate)
        {
            if ($this->_data['<?= $column->getName () ?>'] === null)
            {
                return null;
            }

            return new Zend_Date($this-><?= $column->getName () ?>, Zend_Date::ISO_8601);
        }
<?php endif; ?>
        return $this-><?= $column->getName () ?>;
    }

<?php endforeach; ?>
<?php foreach ( $parents as $parent ): ?>
    /**
     * Gets parent <?= $parent[ 'table' ] . "\n" ?>
     *
     * @return <?= $parent[ 'class' ] . "\n" ?>
     */
    public function get<?= $parent[ 'function' ] ?>()
    {
        if ($this->_parent_<?= $parent[ 'variable' ] ?> === null)
        {
            $this->_parent_<?= $parent[ 'variable' ] ?> = $this->findParentRow('<?= $objTables->getNamespace (
    ) ?>_DbTable_<?= \Classes\Maker\AbstractMaker::getClassName (
        $parent[ 'table' ]
    ) ?>', '<?= \Classes\Maker\AbstractMaker::getClassName ( $parent[ 'variable' ] ) ?>');
        }

        return $this->_parent_<?= $parent[ 'variable' ] ?>;
    }

<?php endforeach; ?>


<?php foreach ( $depends as $depend ): ?>
    /**
     * Gets dependent <?= $depend[ 'table' ] . "\n" ?>
     *
     * @return <?= $depend[ 'class' ] . "\n" ?>
     */
    public function get<?= $depend[ 'function' ] ?>()
    {
        if ($this->_depend_<?= $depend[ 'variable' ] ?> === null)
        {
            $this->_depend_<?= $depend[ 'variable' ] ?> = $this->findDependentRowset('<?= $objTables->getNamespace (
    ) ?>_DbTable_<?= \Classes\Maker\AbstractMaker::getClassName ( $depend[ 'table' ] ) ?>');
        }

        return $this->_depend_<?= $depend[ 'variable' ] ?>;
    }

<?php endforeach; ?>

}