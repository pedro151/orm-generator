<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 29/11/16
 * Time: 14:11
 */

namespace Classes;


use Classes\Update\Content\GitHub;
use Classes\Update\Version;

require_once 'Update/Content/GitHub.php';
require_once 'Update/ProgressBar.php';

class Update
{

    private static $fileName  = "orm-generator";
    private static $separador = "-";
    private static $extencion = ".phar";
    private        $versionUpdate;
    private        $tempFileName;
    /**
     * @type GitHub
     */
    private $objGitHub;

    public function __construct ( $version = null )
    {
        $this->objGitHub = GitHub::getInstance ();
        if ( is_null ( $version ) )
        {
            $version = $this->objGitHub->getLastVersion ();
        }

        $this->versionUpdate = $version;
        $this->tempFileName = self::$fileName
                              . self::$separador
                              . $this->versionUpdate
                              . self::$extencion;

    }

    public function update ()
    {
        if ( Version::HasNewVersion () && ! Version::equalVersion ( $this->versionUpdate )
        )
        {
            $content = $this->objGitHub->getContent ( $this->objGitHub->getLastPhar () , true );
            if ( $content )
            {
                $this->objGitHub->putContent ( $this->tempFileName , $content );
            }
        } else
        {
            throw new \Exception ( "\033[0;31mError: Esta versão é a atual\033[0m\n" );
        }

        return $this;
    }

    public function downloadVersion ( $version )
    {
        if ( Version::existVersion ( $version ) && ! Version::equalVersion ( $version ) )
        {
            $content = $this->objGitHub->getContent ( $this->objGitHub->getPharByVersion ( $version ) , true );

            if ( $content )
            {
                $this->objGitHub->putContent ( $this->tempFileName , $content );
            }
        } else
        {
            if ( ! Version::existVersion ( $version ) )
            {
                throw new \Exception ( "\033[0;31mError: Esta versão não existe\033[0m\n" );
            }
            throw new \Exception ( "\033[0;31mError: Esta versão é a atual\033[0m\n" );
        }

        return $this;
    }

    public function modifyTempName ()
    {
        if ( file_exists ( realpath ( $this->tempFileName ) ) )
        {
            $fileName = realpath ( self::$fileName . self::$extencion );
            if ( file_exists ( $fileName ) )
            {
                unlink ( $fileName );
            }

            chmod ( $this->tempFileName , 0777 );
            rename ( $this->tempFileName , self::$fileName . self::$extencion );

        }

        return $this;
    }
}