<?php
define ( 'PREFIX' , 'dao-generator' );
define ( 'PHAR_FILE' , PREFIX . '.phar' );
define ( 'DEFAULT_STUB' , 'phar-generate.php' );
define ( 'BUILD_DIR' , realpath ( __DIR__ . '/build' ) );
define ( 'INCLUDE_EXTENSION' , '/\.php$/' );

try
{
    if ( file_exists ( PHAR_FILE ) )
    {
        unlink ( PHAR_FILE );
    }

    /****************************************
     * phar file creation
     ****************************************/
    $tarphar = new Phar( PHAR_FILE );
    $phar = $tarphar->convertToExecutable ( Phar::PHAR );
    $phar->startBuffering ();
    $phar->buildFromDirectory ( BUILD_DIR , INCLUDE_EXTENSION );
    $stub = $phar->createDefaultStub ( DEFAULT_STUB );
    $phar->setStub ( "#!/usr/bin/php\n". $stub );
    $phar->stopBuffering ();


} catch ( Exception $e )
{
    echo $e;
}