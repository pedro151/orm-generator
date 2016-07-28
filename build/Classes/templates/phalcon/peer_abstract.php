<?='<?php'?>

/**
 * Mvc Model
 *
 * <?=$this->config->last_modify."\n"?>
 *
 * @package   <?=$this->config->namespace?>\Models
 * @subpackage Model
 * @author    <?=$this->config->author."\n"?>
 *
 * @copyright <?=$this->config->copyright."\n"?>
 * @license   <?=$this->config->license."\n"?>
 * @link      <?=$this->config->link."\n"?>
 */

namespace  <?=$this->config->namespace?$this->config->namespace."\\":""?>Models;

abstract class AbstractPeer
{
    static $className;

   /**
    * Name of the object for static instance
    *
    * @return string
    */
    public function getClassName(){
        return static::$className;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        $className = static::getClassName();
        return $className::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return static
     */
    public static function findFirst($parameters = null)
    {
        $className = static::getClassName();
        return $className::findFirst($parameters);
    }

}