<?php

namespace Classes;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class CleanTrash
{
    /**
     * @type CleanTrash
     */
    private static $instance;

    /**
     * @type int
     */
    private $countFileDeleted=0;

    final private function __construct (){ }

    /**
     * @return \Classes\CleanTrash
     */
    public static function getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function scanDir ( $directory )
    {
        return array_diff ( preg_grep ( '*\.*' , scandir ( $directory ) ) , array (
            '..' , '.'
        ) );
    }

    /**
     * @param string                                 $path
     * @param \Classes\AdaptersDriver\AbsractAdapter $driver
     * @param int                                    $schema
     *
     * @return array
     */
    private function diffFiles ( $path , $driver , $schema = 0 )
    {
        $tablesName = $driver->getTables ( $schema )->toArrayFileName ();
        return array_diff ( $this->scanDir ( $path ) , $tablesName );
    }

    /**
     * @param string                                 $path
     * @param \Classes\AdaptersDriver\AbsractAdapter $driver
     * @param int                                    $schema
     *
     * @return int
     */
    public function run ( $path , $driver , $schema = 0 )
    {
        $count = 0;
        foreach ( $this->diffFiles ( $path , $driver , $schema ) as $fileDel )
        {
            if ( unlink ( $path . DIRECTORY_SEPARATOR . $fileDel ) )
            {
                ++ $count;
            }
        }

        $this->countFileDeleted += $count;

        return $count;
    }

    /**
     * @return int
     */
    public function getNumFilesDeleted ()
    {
        return $this->countFileDeleted;
    }

}