<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 24/11/16
 * Time: 17:19
 */

namespace Classes\Update\Content;

use Classes\Update\ProgressBar;
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
    public function getContent ( $url , $progress = false )
    {
        if ( ! isset( $this->content[ $url ] ) )
        {
            $this->content[ $url ] = '';
            switch ( $this->objProtocol->getProtocol () )
            {
                case 'curl':
                    $this->content[ $url ] = $this->getCurlContent ( $url , $progress );
                    break;
                case 'file_content':
                    $this->content[ $url ] = $this->getFileContent ( $url , $progress );
                    break;
                case 'steam_content':
                    $this->content[ $url ] = $this->getStreamContent ( $url , $progress );
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
    protected function getCurlContent ( $url , $progress = false )
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
    protected function getFileContent ( $url , $progress = false )
    {
        $context = stream_context_create ( self::$opts );
        if ( $progress )
        {
            stream_context_set_params ( $context , array (
                "notification" => array (
                    $this , 'stream_notification_callback'
                )
            ) );
        }

        return file_get_contents ( $url , false , $context );
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getStreamContent ( $url , $progress = false )
    {
        $context = stream_context_create ( self::$opts );
        if ( $progress )
        {
            stream_context_set_params ( $context , array (
                "notification" => array (
                    $this , 'stream_notification_callback'
                )
            ) );
        }

        return file_get_contents ( $url , false , $context );
    }

    public function putFileContent ( $url , $content )
    {
        // check if all is OK
        if ( file_put_contents ( $url , $content ) )
        {
            ProgressBar::getInstance ()->finish ();
        }
    }

    /**
     * Stream downloading
     *
     * @param $notification_code
     * @param $severity
     * @param $message
     * @param $message_code
     * @param $bytes_transferred
     * @param $bytes_max
     */
    public function stream_notification_callback ( $notificationCode , $severity , $message , $messageCode , $bytesTransferred , $bytesMax )
    {
        $objProgress = ProgressBar::getInstance ();
        switch ( $notificationCode )
        {
            case STREAM_NOTIFY_RESOLVE:
            case STREAM_NOTIFY_AUTH_REQUIRED:
            case STREAM_NOTIFY_FAILURE:
            case STREAM_NOTIFY_AUTH_RESULT:
                var_dump ( $notificationCode , $severity , $message , $messageCode , $bytesTransferred , $bytesMax );
                /* Ignore */
                break;
            case STREAM_NOTIFY_CONNECT:
                echo "\033[1;32mConnected...\033[0m\n";
                break;
            case STREAM_NOTIFY_REDIRECTED:
                $objProgress->clear ();
                break;
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $objProgress->clear ()->setMaxByte ( $bytesMax );
                break;
            case STREAM_NOTIFY_PROGRESS:
                $objProgress->setProgress ( $bytesTransferred )
                            ->render ();
                break;
            case STREAM_NOTIFY_COMPLETED:
                $objProgress->finish ();
                break;
        }

    }

}