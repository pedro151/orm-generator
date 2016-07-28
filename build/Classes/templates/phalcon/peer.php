<?='<?php'?>

/**
 * Mvc Model
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package   <?=$objTables->getNamespace()?>\Peer
 * @subpackage Model
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

namespace  <?=$objTables->getNamespace()?>\Peer;

abstract class <?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends \<?=$this->config->namespace?$this->config->namespace."\\":""?>Models\<?=$objMakeFile->getParentClass() . "\n"?>
{
    /**
    * Name of the object for static instance
    *
    * @var string $className
    */
    protected static $className = '<?=$objTables->getNamespace()?>\<?=$this->getClassName ( $objTables->getName () )?>';

}