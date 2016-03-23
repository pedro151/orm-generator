<?php

set_include_path (
    implode (
        PATH_SEPARATOR ,
        array (
            realpath ( __DIR__ ) ,
            realpath ( __DIR__ ).'/../../build/' ,
            get_include_path () ,
        )
    )
);

require __DIR__ . '/../../vendor/autoload.php';