<?php

if ( ! ini_get ( 'short_open_tag' ) )
{
    die( "please enable short_open_tag directive in php.ini\n" );
}

if ( ! ini_get ( 'register_argc_argv' ) )
{
    die( "please enable register_argc_argv directive in php.ini\n" );
}

\Phar::interceptFileFuncs();
function __autoload ( $class )
{
    $parts = \explode ( '\\' , $class );
    include __DIR__ . DIRECTORY_SEPARATOR
            . \implode ( DIRECTORY_SEPARATOR , $parts ) . '.php';
}

global $_path;

try
{
    $arrValid = array (
        'help' ,
        'database:' ,
        'schema:' ,
        'driver:' ,
        'framework:' ,
        'status:' ,
        'path:'
    );

    $_path = realpath ( dirname ( str_replace (
        'phar://'
        , '' , __DIR__
    ) ) );

    $configIni = $_path . '/configs/config.ini' ;
    $maker = new \Classes\MakerFile( new \Classes\Config( getopt ( null , $arrValid ) , $configIni ) );
    $maker->run ();

} catch ( \Exception $e )
{
    die( $e->getMessage () );
}

__halt_compiler();