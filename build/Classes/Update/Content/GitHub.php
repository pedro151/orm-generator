<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 28/11/16
 * Time: 11:39
 */

namespace Classes\Update\Content;

require_once 'AbstractContent.php';


class GitHub extends AbstractContent
{
    private static $tagsGithub  = "https://api.github.com/repos/pedro151/orm-generator/tags";
    private static $listVersion = array ();
    private        $phar        = "https://github.com/pedro151/orm-generator/blob/%s/bin/orm-generator.phar?raw=true";

    /**
     * @return mixed
     */
    public function getInfo ()
    {
        return json_decode ( $this->getContent ( self::$tagsGithub ) );
    }

    protected function init ()
    {
        if ( is_array ( $this->getInfo () ) )
        {
            foreach ( $this->getInfo () as $index => $objTag )
            {
                self::$listVersion[ preg_replace ( "/[^0-9.]/" , "" , $objTag->name ) ] = sprintf ( $this->phar , $objTag->name );
            }
        }
    }

    public function getLastVersion ()
    {
        return current ( array_keys ( self::$listVersion ) );
    }

    public function getLastPhar ()
    {
        reset(self::$listVersion );
        return current ( self::$listVersion );
    }

    public function getPharByVersion ( $version )
    {
        return self::$listVersion[ $version ];
    }

}