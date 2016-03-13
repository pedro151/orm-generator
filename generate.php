#!/usr/bin/php

<?php

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 */

$parts = array (
    '..',
    '..',
    'autoload'
);
$autoload = realpath (
    __DIR__ . DIRECTORY_SEPARATOR
    . implode ( DIRECTORY_SEPARATOR, $parts ) . '.php'
);
if ( is_file ( $autoload ) )
{
    require $autoload;
}
else
{
    $parts = array (
        'vendor',
        'autoload'
    );

    require realpath (
        __DIR__ . DIRECTORY_SEPARATOR
        . implode ( DIRECTORY_SEPARATOR, $parts ) . '.php'
    );
}


if ( !ini_get ( 'short_open_tag' ) )
{
    die( "please enable short_open_tag directive in php.ini\n" );
}

if ( !ini_get ( 'register_argc_argv' ) )
{
    die( "please enable register_argc_argv directive in php.ini\n" );
}

global $_path;

try
{
    $arrValid = array (
        'help',
        'config-ini',
        'database:',
        'schema:',
        'driver:',
        'framework:',
        'status:',
        'path:'
    );

    $_path = realpath ( __FILE__ );

    $maker = new \Classes\MakerFile( new \Classes\Config( getopt ( null, $arrValid ), $_path ) );
    $maker->run ();

}
catch ( \Exception $e )
{
    die( $e->getMessage () );
}
