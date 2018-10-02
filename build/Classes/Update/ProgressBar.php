<?php

namespace Classes\Update;

class ProgressBar
{

    private static $finished = 0;
    private static $progressBar;

    private $max = 0;

    private $progress = 0;

    private $progresslength = 0;

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
        $this->calcule ();

        return $this;
    }

    public function getProgress ()
    {
        return $this->progresslength;
    }

    public function calcule ()
    {
        if ( $this->progress > 0 )
        {
            $this->progresslength = round( $this->progress * 100 / $this->max );
            //$this->progresslength = (int) ( ( $this->progress / $this->max ) * 100 );
        }
    }

    public function finish ()
    {
        if ( $this->getProgress () >= 99 && !self::$finished)
        {
            echo "\n\033[1;32mDone!\033[0m\n";
            self::$finished = 1;
        }

    }

    public function render ()
    {
        if ( $this->progress > 0 && !self::$finished)
        {
            if ( ! isset( $this->max ) )
            {
                printf ( "\rUnknown filesize.. %2d kb done.." , $this->progress / 1024 );
            } else
            {
                $length = $this->getProgress ();
                printf ( "\r[%-100s] %d%%" , str_repeat ( "=" , $length )
                                             . ">" , $length );
            }
        }

        return $this;
    }

    public function clear ()
    {
        $this->max = 0;
        $this->progress = 0;

        return $this;
    }
}