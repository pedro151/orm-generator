<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 24/11/16
 * Time: 17:19
 */

namespace Classes\Update\Content;

use Classes\Update\ProtocolFileContent;

abstract class AbstractContent
{

    private static $opts = array (
        'http' => array (
            'method' => 'GET' ,
            'header' => array (
                'User-Agent: PHP'
            )
        )
    );

    /**
     * @type \Classes\Update\ProtocolFileContent
     */
    protected $objProtocol;
    /**
     * @type Content
     */
    private static $objContent;

    /**
     * @type array
     */
    private $content = array ();

    protected function __construct ()
    {
        $this->objProtocol = ProtocolFileContent::getInstance ();
        $this->init ();
    }

    protected function init (){ }

    /**
     * @return \Classes\Update\Content
     */
    public static function getInstance ()
    {
        if ( empty( self::$objContent ) )
        {
            self::$objContent = new static();
        }

        return self::$objContent;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function getContent ( $url )
    {
        if ( ! isset( $this->content[ $url ] ) )
        {
            $this->content[ $url ] = '';
            switch ( $this->objProtocol->getProtocol () )
            {
                case 'curl':
                    $this->content[ $url ] = $this->getCurlContent ( $url );
                    break;
                case 'file_content':
                    $this->content[ $url ] = $this->getFileContent ( $url );
                    break;
                case 'steam_content':
                    $this->content[ $url ] = $this->getStreamContent ( $url );
                    break;
            }
        }

        return $this->content[ $url ];
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getCurlContent ( $url )
    {
        $conn = curl_init ( $url );
        curl_setopt ( $conn , CURLOPT_RETURNTRANSFER , true );
        curl_setopt ( $conn , CURLOPT_BINARYTRANSFER , true );
        curl_setopt ( $conn , CURLOPT_USERAGENT , self::$opts[ 'http' ][ 'method' ] );
        $url_get_contents_data = ( curl_exec ( $conn ) );

        curl_close ( $conn );

        return $url_get_contents_data;
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getFileContent ( $url )
    {
        $context = stream_context_create ( self::$opts );

        return file_get_contents ( $url , false , $context );
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getStreamContent ( $url )
    {
        $context = stream_context_create ( self::$opts );

        return file_get_contents ( $url , false , $context );
    }
}