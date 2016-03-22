<?php
namespace Classes\Maker;

abstract class AbstractMaker
{
    const SEPARETOR = '_';

    /**
     * verifica se ja existe e cria as pastas em cascata
     *
     * @param $dir
     */
    public static function makeDir ( $dir )
    {
        if ( !is_dir ( $dir ) )
        {
            if ( !@mkdir ( $dir, 0755, true ) )
            {
                die( "error: could not create directory $dir\n" );
            }
        }
    }

    /**
     * @param $nameFile nome do arquivo a ser criado
     * @param $tplContent  Conteudo do Template
     */
    public static function makeSourcer ( $nameFile, $tplContent )
    {
        if ( !is_file ( $nameFile ) )
        {
            if ( !file_put_contents ( $nameFile, $tplContent ) )
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
        foreach ( explode ( self::SEPARETOR, $str ) as $part )
        {
            $temp .= ucfirst ( $part );
        }

        return $temp;
    }

    protected function getParsedTplContents ( $filePath, $vars = array () )
    {
        extract ( $vars );
        ob_start ();
        require $filePath;
        $data = ob_get_contents ();
        ob_end_clean ();

        return $data;
    }

}