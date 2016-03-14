#!/usr/bin/php

<?php

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 */

if ( ! ini_get ( 'short_open_tag' ) )
{
    die( "please enable short_open_tag directive in php.ini\n" );
}

if ( ! ini_get ( 'register_argc_argv' ) )
{
    die( "please enable register_argc_argv directive in php.ini\n" );
}

set_include_path (
    implode (
        PATH_SEPARATOR ,
        array (
            realpath ( dirname ( __FILE__ ) . "/build/" ) ,
            get_include_path () ,
        )
    )
);

require_once 'Classes/MakerFile.php';
require_once 'Classes/Config.php';

try
{
    $arrValid = array (
        'help' ,
        'config-ini' ,
        'database:' ,
        'schema:' ,
        'driver:' ,
        'framework:' ,
        'status:' ,
        'path:'
    );

    $_path = realpath ( __FILE__ );

    $maker = new \Classes\MakerFile( new \Classes\Config( getopt ( null , $arrValid ) , $_path ) );
    $maker->run ();

} catch ( \Exception $e )
{
    die( $e->getMessage () );
}
