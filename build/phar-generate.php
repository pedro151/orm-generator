<?php

if ( ! ini_get ( 'short_open_tag' ) )
{
    die( "\033[0;31mError: please enable short_open_tag directive in php.ini\033[0m\n" );
}

if ( ! ini_get ( 'register_argc_argv' ) )
{
    die( "\033[0;31mError: please enable register_argc_argv directive in php.ini\033[0m\n" );
}

if ( function_exists ( 'ini_set' ) ) {
    @ini_set ( 'display_errors', 1 );

    $memoryInBytes = function ( $value ) {
        $unit  = strtolower ( substr ( $value, -1, 1 ) );
        $value = (int) $value;
        switch ( $unit ) {
            case 'g':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'm':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'k':
                $value *= 1024;
        }

        return $value;
    };

    $memoryLimit = trim ( ini_get ( 'memory_limit' ) );
    // Increase memory_limit if it is lower than 1GB
    if ( $memoryLimit != -1 && $memoryInBytes( $memoryLimit ) < 1024 * 1024 * 1024 ) {
        @ini_set ( 'memory_limit', '1G' );
    }
    unset( $memoryInBytes, $memoryLimit );
}

\Phar::interceptFileFuncs ();

set_include_path (
    implode (
        PATH_SEPARATOR,
        array (
            realpath ( __DIR__ ),
            get_include_path (),
        )
    )
);

require_once 'Classes/MakerFile.php';
require_once 'Classes/Config.php';
require_once 'Classes/MakerConfigFile.php';

try {
    $_path = realpath (
        str_replace (
            'phar://',
            '',
            __DIR__
        )
    );

    $arrValid = array (
        'version',
        'help',
        'status',
        'init',
        'config-env:',
        'name-ini:',
        'database:',
        'schema:',
        'driver:',
        'tables:',
        'optional-classes:',
        'framework:',
        'path:',
        'clean-trash:',
        'update',
        'download:'
    );

    $arg = getopt ( null, $arrValid );
    if ( array_key_exists ( 'init', $arg ) ) {
        $maker = new \Classes\MakerConfigFile( $arg, dirname($_path) );
    }
    else {
        $maker = new \Classes\MakerFile( new \Classes\Config( $arg, dirname($_path), count ( $argv ) ) );
    }

    $maker->run ();

} catch ( \Exception $e ) {
    die( $e->getMessage () );
}

__halt_compiler();