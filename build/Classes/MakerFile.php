<?php

namespace Classes;

use Classes\AdapterMakerFile\AbstractAdapter;
use Classes\Maker\AbstractMaker;

require_once 'Classes/AdapterMakerFile/AbstractAdapter.php';
require_once 'Classes/Maker/AbstractMaker.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class MakerFile extends AbstractMaker
{

    /**
     * @type string[]
     */
    public $location = array ();

    /**
     * caminho de pastas Base
     *
     * @type string
     */
    private $baseLocation = '';

    /**
     * @type \Classes\AdapterConfig\AbstractAdapter
     */
    private $config;

    /**
     * @type \Classes\AdaptersDriver\AbsractAdapter
     */
    private $driver;

    private $countDir;
    private $max;

    private $msgReservedWord = "\033[0mPlease enter the value for reserved word \033[0;31m'%index%' \033[1;33m[%config%]:\033[0m ";

    public function __construct ( Config $config )
    {
        $this->config = $config->getAdapterConfig ();
        $this->parseReservedWord ( $this->getConfig () );
        $this->driver = $config->getAdapterDriver ( $this->getConfig () );
        $this->parseLocation ( $config->_basePath );
    }

    /**
     * @param AdapterConfig\AbstractAdapter $config
     */
    public function parseReservedWord ( AdapterConfig\AbstractAdapter $config )
    {
        $palavrasReservadas = $config->reservedWord;
        if ( !$palavrasReservadas ) {
            return;
        }

        $schema      = $config->getSchemas ();
        $db          = $config->getDatabase ();
        $hasSchema   = array_intersect ( $schema, array_flip ( $palavrasReservadas ) );
        $hasDatabase = in_array ( $db, $palavrasReservadas );
        if ( !( $hasSchema or $hasDatabase ) ) {
            return;
        }

        echo "- database has reserved words\n";
        foreach ( $palavrasReservadas as $index => $config ) {
            $attribs = array (
                "%index%"  => $index,
                "%config%" => $config
            );
            echo strtr ( $this->msgReservedWord, $attribs );
            $line = trim ( fgets ( STDIN ) );
            if ( !empty( $line ) ) {
                $this->getConfig ()->reservedWord[ $index ] = $line;
            }
        }
    }

    /**
     * @param array $arrFoldersName
     *
     * @return string
     */
    private function filterLocation ( $arrFoldersName )
    {
        foreach ( $arrFoldersName as $index => $folderName ) {
            $arrFoldersName[ $index ] = $this->getConfig ()
                                             ->replaceReservedWord ( $folderName );
        }

        return implode ( DIRECTORY_SEPARATOR, array_filter ( $arrFoldersName ) );
    }

    /**
     * Analisa os caminhos das pastas base
     */
    public function parseLocation ( $basePath )
    {

        $arrBase = array (
            $basePath,
            $this->config->path
        );

        $this->baseLocation = $this->filterLocation ( $arrBase );

        # pasta com nome do driver do banco
        $driverBase = '';
        if ( (bool) @$this->config->{"folder-database"} ) {
            $classDriver = explode ( '\\', get_class ( $this->driver ) );
            $driverBase  = end ( $classDriver );
        }
        $folderName = '';
        if ( (bool) @$this->config->{"folder-name"} ) {
            $folderName = $this->getClassName ( trim ( $this->config->{"folder-name"} ) );
        }

        if ( $this->config->hasSchemas () ) {

            $schemas = $this->config->getSchemas ();
            foreach ( $schemas as $schema ) {
                $arrUrl = array (
                    $this->baseLocation,
                    $driverBase,
                    $folderName,
                    $this->getClassName ( $schema )
                );

                $this->location[ $schema ] = $this->filterLocation ( $arrUrl );
                unset( $arrUrl );
            }

        }
        else {
            $url            = array (
                $this->baseLocation,
                $driverBase,
                $folderName,
                $this->getClassName (
                    $this->getConfig ()
                         ->getDatabase ()
                )
            );
            $this->location = array ( $this->filterLocation ( $url ) );
            unset( $url );
        }
    }

    /**
     * @return AdapterConfig\AbstractAdapter
     */
    public function getConfig ()
    {
        return $this->config;
    }

    /* Get current time */
    public function startTime ()
    {
        echo "Starting..\n";
        $this->startTime = microtime ( true );
    }

    private function getRunTime ()
    {
        return round ( ( microtime ( true ) - $this->startTime ), 3 );
    }

    /**
     * Executa o Make, criando arquivos e Diretorios
     */
    public function run ()
    {
        $this->startTime ();
        $this->driver->runDatabase ();
        $this->max       = $this->driver->getTotalTables () * $this->countDiretory ();
        $cur             = 0;
        $numFilesCreated = 0;
        $numFilesIgnored = 0;
        foreach ( $this->location as $schema => $location ) {
            foreach ( $this->factoryMakerFile () as $objMakeFile ) {
                $path = $location . DIRECTORY_SEPARATOR . $objMakeFile->getPastName ();
                self::makeDir ( $path );
                if ( $objMakeFile->getParentFileTpl () != '' ) {
                    $fileAbstract = $this->baseLocation
                                    . DIRECTORY_SEPARATOR
                                    . $objMakeFile->getParentClass ()
                                    . '.php';

                    $tplAbstract = $this->getParsedTplContents ( $objMakeFile->getParentFileTpl () );
                    self::makeSourcer ( $fileAbstract, $tplAbstract, true );
                    unset( $fileAbstract, $tplAbstract );
                }

                foreach ( $this->driver->getTables ( $schema ) as $key => $objTables ) {
                    $file = $path . DIRECTORY_SEPARATOR . self::getClassName ( $objTables->getName () ) . '.php';

                    $tpl = $this->getParsedTplContents (
                        $objMakeFile->getFileTpl (),
                        $objMakeFile->parseRelation ( $this, $objTables ),
                        $objTables,
                        $objMakeFile

                    );
                    if ( self::makeSourcer ( $file, $tpl, $objMakeFile->isOverwrite () ) ) {
                        ++$numFilesCreated;
                    }
                    else {
                        ++$numFilesIgnored;
                    }

                    $this->countCreatedFiles ( $cur );
                }
            }
        }

        $this->reportProcess ( $numFilesCreated, $numFilesIgnored );
        echo "\n\033[1;32mSuccessfully process finished!\n\033[0m";
    }

    private function countCreatedFiles ( &$cur )
    {
        ++$cur;
        $total = ( $cur / $this->max ) * 100;
        printf ( "\r Creating: \033[1;32m%6.2f%%\033[0m", $total );
    }

    private function reportProcess ( $numFilesCreated = 0, $numFilesIgnored = 0 )
    {
        if ( $this->config->isStatusEnabled () ) {
            $databases  = count ( $this->location );
            $countDir   = $this->countDiretory ();
            $totalTable = $this->driver->getTotalTables ();
            $totalFiles = $numFilesIgnored + $numFilesCreated;
            echo "\n------";
            printf ( "\n\r-Files generated/updated: \033[1;33m%s\033[0m", $numFilesCreated );
            printf ( "\n\r-Files not upgradeable: \033[1;33m%s\033[0m", $numFilesIgnored );
            printf ( "\n\r-Total files analyzed: \033[1;33m%s of %s\033[0m", $totalFiles, $this->max );
            printf ( "\n\r-Diretories: \033[1;33m%s\033[0m", $databases * $countDir );
            printf ( "\n\r-Scanned tables: \033[1;33m%s\033[0m", $totalTable );
            printf ( "\n\r-Execution time: \033[1;33m%ssec\033[0m", $this->getRunTime () );
            echo "\n------";
        }
    }

    /**
     * Instancia os Modulos de diretorios e tampletes
     *
     * @return AbstractAdapter[]
     */
    public function factoryMakerFile ()
    {
        return $this->config->getMakeFileInstances ();
    }

    /**
     * conta o numero de diretorios que serao criados
     *
     * @return int
     */
    public function countDiretory ()
    {
        if ( null === $this->countDir ) {
            $this->countDir = 1;
            foreach ( $this->factoryMakerFile () as $abstractAdapter ) {
                if ( $abstractAdapter->hasDiretory () ) {
                    ++$this->countDir;
                }
            }
        }

        return $this->countDir;
    }

    /**
     *
     * parse a tpl file and return the result
     *
     * @param String $tplFile
     *
     * @return String
     */
    protected function getParsedTplContents ( $tplFile, $vars = array (), \Classes\Db\DbTable $objTables = null,
                                              $objMakeFile = null )
    {

        $arrUrl = array (
            __DIR__,
            'templates',
            $this->config->framework,
            $tplFile
        );

        $filePath = implode ( DIRECTORY_SEPARATOR, filter_var_array ( $arrUrl ) );

        extract ( $vars );
        ob_start ();
        require $filePath;
        $data = ob_get_contents ();
        ob_end_clean ();

        return $data;
    }

}