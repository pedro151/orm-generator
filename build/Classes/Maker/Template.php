<?php
namespace Classes\Maker;

class Template
{
    const SEPARETOR = '_';

    /**
     * verifica se ja existe e cria as pastas em cascata
     *
     * @param $dir
     */
    public static function makeDir ( $dir )
    {
        if ( ! is_dir ( $dir ) )
        {
            if ( ! @mkdir ( $dir , 0755 , true ) )
            {
                die( "error: could not create directory $dir\n" );
            }
        }
    }

    /**
     * @param $nameFile nome do arquivo a ser criado
     * @param $tplFile  Template
     */
    public static function makeSourcer ( $nameFile , $tplFile )
    {
        if ( ! is_file ( $nameFile ) )
        {
            if ( ! file_put_contents ( $nameFile , $tplFile ) )
            {
                die( "Error: could not write model file $nameFile." );
            }
        }

    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function getClassName ( $str )
    {
        $temp = '';
        foreach ( explode ( self::SEPARETOR , $str ) as $part )
        {
            $temp .= ucfirst ( $part );
        }

        return $temp;
    }

}