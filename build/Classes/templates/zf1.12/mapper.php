<?= "<?php\n" ?>
<?php $className = $objTables->getNamespace () . '_Mapper_' . \Classes\Maker\AbstractMaker::getClassName (
        $objTables->getName ()
    ) ?>

/**
* Application Mapper
*
* <?= $this->config->last_modify . "\n" ?>
*
* @package <?= $objTables->getNamespace () . "\n" ?>
* @subpackage Mapper
*
* @author    <?= $this->config->author . "\n" ?>
*
* @copyright <?= $this->config->copyright . "\n" ?>
* @license   <?= $this->config->license . "\n" ?>
* @link      <?= $this->config->link . "\n" ?>
* @version   <?= $this->config->version . "\n" ?>
*/

class <?= $className ?> extends <?= $this->config->namespace ? $this->config->namespace . "_" : "" ?>Model_<?= $objMakeFile->getFilesFixeds('parentClass')->getFileName() . "\n" ?>
{

    /**
     * Nome da tabela DbTable do model
     *
     * @var string
     * @access protected
     */
    protected $_tableClass = '<?= $objTables->getNamespace () ?>_DbTable_<?= \Classes\Maker\AbstractMaker::getClassName (
    $objTables->getName ()
) ?>';


    /* @TODO Codifique aqui */

}