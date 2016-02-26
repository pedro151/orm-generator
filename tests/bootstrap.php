<?php

function loader($class)
{
    $parts = explode ( '\\', $class );
    $file = dirname ( __FILE__ ) .DIRECTORY_SEPARATOR. implode ( DIRECTORY_SEPARATOR, $parts ) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');