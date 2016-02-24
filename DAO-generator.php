#!/usr/bin/php
<?php

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 */
function __autoload ( $class )
{
    $parts = explode ( '\\' , $class );
    require implode ( DIRECTORY_SEPARATOR , $parts ) . '.php';
}

if ( ! ini_get ( 'short_open_tag' ) )
{
    die( "please enable short_open_tag directive in php.ini\n" );
}

if ( ! ini_get ( 'register_argc_argv' ) )
{
    die( "please enable register_argc_argv directive in php.ini\n" );
}

try
{
    $arrValid = array (
        'help' ,
        'database:' ,
        'driver:' ,
        'framework:' ,
        'path:'
    );

    $maker = new \Classes\MakerFile( new \Classes\Config( getopt ( null , $arrValid ) ) );
    $maker->run ();

} catch ( \Exception $e )
{
    die( $e->getMessage () );
}
