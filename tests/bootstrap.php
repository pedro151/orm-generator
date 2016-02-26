<?php

function __autoload ( $class )
{
    $parts = explode ( '\\', $class );
    require dirname ( __FILE__ ) .DIRECTORY_SEPARATOR. implode ( DIRECTORY_SEPARATOR, $parts ) . '.php';
}