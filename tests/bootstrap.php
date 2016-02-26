<?php

function __autoload ( $class )
{
    $parts = explode ( '\\' , $class );
    $file = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR
            . implode ( DIRECTORY_SEPARATOR , $parts ) . '.php';
    if ( ! is_file ( $file ) )
    {
        $file = dirname ( __FILE__ ) . '../' . DIRECTORY_SEPARATOR
                . implode ( DIRECTORY_SEPARATOR , $parts ) . '.php';
    }
    require $file;
}