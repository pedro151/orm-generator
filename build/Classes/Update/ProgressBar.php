<?php

namespace Classes\Update;

class ProgressBar
{

    private static $progressBar;

    private $max = 0;

    private $progress = 0;

    private function __construct (){ }

    /**
     * @return \Classes\Update\ProgressBar
     */
    public static function getInstance ()
    {
        if ( is_null ( self::$progressBar ) )
        {
            self::$progressBar = new ProgressBar();
        }

        return self::$progressBar;
    }

    public function setMaxByte ( $bytesMax )
    {
        $this->max = $bytesMax;

        return $this;
    }

    public function setProgress ( $progress )
    {
        $this->progress = $progress;

        return $this;
    }

    public function finish ()
    {
        echo "\n\033[1;32mDone!\033[0m\n";
        exit( 0 );
    }

    public function render ()
    {
        if ( $this->progress > 0 )
        {
            if ( ! isset( $this->max ) )
            {
                printf ( "\rUnknown filesize.. %2d kb done.." , $this->progress / 1024 );
            } else
            {
                $length = (int) ( ( $this->progress / $this->max ) * 100 );
                printf ( "\r[%-100s] %d%%" , str_repeat ( "=" , $length ) . ">" , $length);
            }
        }
    }

    public function clear ()
    {
        $this->max = 0;
        $this->progress = 0;

        return $this;
    }
}