<?="<?php\n"?>

/**
 * Application Model Peer
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * Tabela definida por 'tablename'
 *
 * @package   <?=$objTables->getNamespace()?><?="\n"?>
 * @subpackage Peer
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 * @version   <?=$this->config->version."\n"?>
 */

class <?=$objTables->getNamespace()?>_Peer_<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () )?> extends <?=$objTables->getNamespace()?$objTables->getNamespace()."_":''?>Model_<?=\Classes\Maker\AbstractMaker::getClassName ( $objTables->getName () ) . "\n"?>
{
    /* @TODO Codifique aqui */
}
