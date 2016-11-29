<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 29/11/16
 * Time: 14:11
 */

namespace Classes;


use Classes\Update\Content\GitHub;

require_once 'Update/Content/GitHub.php';
require_once 'Update/ProgressBar.php';

class Update
{

    private $version;
    /**
     * @type GitHub
     */
    private $objGitHub;

    public function __construct ()
    {
        $this->objGitHub = GitHub::getInstance ();
        $var = $this->objGitHub->getLastPhar ();
        $this->objGitHub->putFileContent ( "teste.phar" , $this->objGitHub->getContent ( $var, true ) );
        exit();
        //$fileDownload = Download::createFromString($this->objGitHub->getContent($var));
        //echo var_dump($fileDownload->sendDownload("teste.phar",true));exit();
    }
}