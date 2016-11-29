<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 24/11/16
 * Time: 17:19
 */

namespace Classes\Update;


class ProtocolFileContent
{
    /**
     * @type string
     */
    protected $protocol;
    /**
     * @type Content
     */
    private static $objProtocol;

    private function __construct ()
    {
        $this->parseContentProtocol ();
    }

    /**
     * @return \Classes\Update\ProtocolFileContent
     */
    public static function getInstance ()
    {
        if ( empty( self::$objProtocol ) )
        {
            self::$objProtocol = new ProtocolFileContent();
        }

        return self::$objProtocol;
    }

    /**
     * @return bool
     */
    public function hasProtocol ()
    {
        return ! empty( $this->protocol );
    }

    /**
     * @return string
     */
    public function getProtocol ()
    {
        return $this->protocol;
    }

    /**
     *
     */
    private function parseContentProtocol ()
    {
        if ( $this->hasEnabled ( 'file_content' ) )
        {
            $this->protocol = 'file_content';
        } elseif ( $this->hasEnabled ( 'steam_content' ) )
        {
            $this->protocol = 'steam_content';
        } elseif ( $this->hasEnabled ( 'curl' ) )
        {
            $this->protocol = 'curl';
        }
    }

    /**
     * @return bool
     */
    private function hasEnabled ( $type )
    {
        switch ( $type )
        {
            case 'curl':
                return function_exists ( 'curl_exec' );
                break;
            case 'file_content':
                return function_exists ( 'file_get_contents' );
                break;
            case 'steam_content':
                return function_exists ( 'fopen' )
                       && function_exists ( 'stream_get_contents' );
                break;
        }

    }

}