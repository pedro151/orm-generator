<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/02/16
 * Time: 11:11
 */

namespace Classes\AdapterConfig;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link https://github.com/pedro151/ORM-Generator
 */
class Exception extends \Exception
{
    private $msg = "As configuracoes a seguir sao obrigatorias: \033[0;31m%value%\033[0m";

    public function __construct ( $array , $code = 0 )
    {
        $attribs = implode ( ', ' , array_keys ( $array ) );
        parent::__construct ( str_replace ( "%value%" , $attribs , $this->msg ) , (int) $code );
    }
}