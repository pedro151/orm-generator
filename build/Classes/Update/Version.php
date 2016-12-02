<?php
namespace Classes\Update;

use Classes\Update\Content\GitHub;

require_once 'Content/GitHub.php';
require_once 'ProtocolFileContent.php';

class Version
{
    private static $_currentVersion = "1.5.0";

    private static $lastVersion;

    /**
     * @return string
     */
    public static function getVersion ()
    {
        return static::$_currentVersion;
    }

    /**
     * @return bool
     */
    public static function HasNewVersion ()
    {
        self::$lastVersion = GitHub::getInstance ()->getLastVersion ();

        return self::$lastVersion > static::$_currentVersion;
    }

    /**
     * @return string
     */
    public function messageHasNewVersion ()
    {
        if ( self::HasNewVersion () )
        {
            return "\033[0;31mThere is a new version " . self::$lastVersion
                   . " available\033[0m \n";
        }
    }

    /**
     * @param $version
     *
     * @return bool
     */
    public static function equalVersion ( $version )
    {
        return $version === self::getVersion ();
    }

    public static function existVersion ( $version )
    {
        return GitHub::getInstance ()->hasPharByVersion ( $version );
    }

}