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
 * @version   <?=$this->config->version."\n"?>
 */

namespace  <?=$this->config->namespace?$this->config->namespace."\\":""?>Models;

abstract class AbstractPeer
{
    protected static $className;

    final private function __construct(){}

   /**
    * Name of the object for static instance
    *
    * @return string
    */
    static public function getClassName()
    {
        return static::$className;
    }

   /**
    * instance of the object
    *
    * @return static
    */
    static public function getObject()
    {
         $className = static::getClassName();

         return new $className();
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