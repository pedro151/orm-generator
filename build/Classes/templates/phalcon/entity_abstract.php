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

use Phalcon\Mvc\Model;

abstract class AbstractEntity extends \Phalcon\Mvc\Model
{
    protected static $_cache = array();

    /**
     * Implement a method that returns a string key based
     * on the query parameters
     */
    protected static function _createKey($parameters)
    {
        $uniqueKey = array();

        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ':' . $value;
            } else {
                if (is_array($value)) {
                    $uniqueKey[] = $key . ':[' . self::_createKey($value) .']';
                }
            }
        }

        return join(',', $uniqueKey);
    }
}