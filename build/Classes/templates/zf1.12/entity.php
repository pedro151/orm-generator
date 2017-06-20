<?= "<?php\n" ?>
<?php $classNameModel = $objTables->getNamespace () . '_' . \Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () ) ?>
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
                                                                        . "_" : "" ?>Model_<?= $objMakeFile->getFilesFixeds('parentClass')->getFileName() . "\n" ?>
{
    /**
    * Database constraint in the columns
    *
    */
<?php foreach ( $objTables->getColumns () as $column ): ?>
    const <?= strtoupper ( $column->getName () ) ?> = '<?=$column->getName ()?>';
<?php endforeach; ?>

    /**
     * Nome da tabela Mapper do model
     *
     * @var string
     * @access protected
     */
    protected $_mapperClass = '<?= $objTables->getNamespace () ?>_Mapper_<?= \Classes\Maker\AbstractMaker::getClassName (
    $objTables->getName ()
) ?>';

    /**
     * @see <?= $this->config->namespace ?>Model_EntityAbstract::$_filters
     */
    protected $_filters = array(
<?php foreach ( $objTables->getColumns () as $column ): ?>
<?php
    $filters = null;
    switch ( strtolower ( $column->getType () ) ) {
        case 'string':
            $filters = 'StripTags", "StringTrim';
            break;
        case 'float':
            $filters = 'Digits';
            break;
        case 'date':
            break;
        case 'timestamp':
            break;
        case 'datetime':
            break;
        case 'boolean':
                $filters='Int';
            break;
        default:
            $filters = ucfirst ( $column->getType () );
            break;
    }
    ?>
        self::<?= strtoupper($column->getName ()) ?> => array (
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

    switch ( strtolower ( $column->getType () ) ) {
        case 'date':
            break;
        case 'timestamp':
            break;
        case 'datetime':
            break;
        case 'string':
            if ( $column->getMaxLength () ) {
                $validators[] = "array( 'StringLength', array( 'max' => " . $column->getMaxLength () . " ) )";
            }

            break;
        case 'boolean':
                $validators[] = "'Int'";
            break;
        default:
            $name         = ucfirst ( $column->getType () );
            $validators[] = "'$name'";
            break;
    }
$validators = implode ( ", ", $validators ) ?>
        self::<?= strtoupper($column->getName ()) ?> => array (
            <?= ( !empty( $validators ) ) ? "{$validators}\n" : null ?>
        ),
<?php endforeach; ?>
    );

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
    public function set<?= \Classes\Maker\AbstractMaker::getClassName ( $column->getName () ) ?>( $<?= $column->getName (
    ) ?> )
    {
<?php switch ( strtolower( $column->getType ( \Classes\Db\Column::TypeDefault )  ) ):
        case 'timestamp':
        case 'date':
        case 'datetime':?>
            if (! empty($<?= $column->getName () ?>))
            {
                if (! $<?= $column->getName () ?> instanceof Zend_Date)
                {
                    $<?= $column->getName () ?> = new Zend_Date($<?= $column->getName () ?>);
                }
<?php if( $column->equalType ( 'date' ) ): ?>
                $<?= $column->getName () ?>->setOptions(array('format_type' => 'php'));
<?php endif ?>
<?php $format =  '\'Y-MM-dd\'' ?>
<?php if( $column->equalType ( 'date' ) ) { $format =  '\'Y-m-d\''; } ?>
                $<?= $column->getName () ?> = $<?= $column->getName () ?>->toString( <?=$format?> );
            }
<?php if($column->isNullable ()):?>
            else{
                $<?= $column->getName () ?> = null;
            }
<?php endif ?>
        $this->_data['<?= $column->getName () ?>'] = $<?= $column->getName () ?> ;
<?php break;
        case 'boolean':
if(!$column->isNullable ()):?>
        $this->_data['<?= $column->getName () ?>'] =  intval( $<?= $column->getName () ?> );
<?php endif;  break;?>
<?php default: ?>
<?php if(!$column->isNullable ()):?>
        $this->_data['<?= $column->getName () ?>']  =  (<?= ucfirst ( $column->getType () ) ?>) $<?= $column->getName () ?> ;
<?php else: ?>
        $this->_data['<?= $column->getName () ?>']  = $<?= $column->getName () ?> ;
<?php endif ?>
<?php break ?>
<?php endswitch ?>

        return $this;
    }

    /**
     * Gets column <?= $column->getName () . "\n" ?>
     *
<?php if ( $column->equalType ( 'date' ) or $column->equalType ( 'datetime' ) or  $column->equalType ( 'timestamp' ) ): ?>
     * @param boolean $returnZendDate
     * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
<?php else: ?>
     * @return <?= $column->getType () . "\n" ?>
<?php endif; ?>
     */
    public function get<?= \Classes\Maker\AbstractMaker::getClassName (
        $column->getName ()
    ) ?>(<?php if ( $column->equalType ( 'date' ) or $column->equalType ( 'datetime' ) or  $column->equalType ( 'timestamp' ) ): ?>$format = false <?php endif; ?>)
    {
<?php switch ( strtolower( $column->getType ( \Classes\Db\Column::TypeDefault ) ) ):
        case 'timestamp':
        case 'date':
        case 'datetime':?>
        if ($format)
        {
            if (!isset($this->_data['<?= $column->getName () ?>']) or $this->_data['<?= $column->getName () ?>'] === null)
            {
                return null;
            }

            $objDate = new Zend_Date($this->_data['<?= $column->getName () ?>'], $format );

            return $objDate->toString($format);
        }
<?php break ?>
<?php endswitch ?>
        return isset($this->_data['<?= $column->getName () ?>'])?$this->_data['<?= $column->getName () ?>']:null;
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