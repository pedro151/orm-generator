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
                die( "\033[0;31mError: could not create directory $dir\033[0m\n" );
            }
        }
    }

    /**
     * @param string $nameFile nome do arquivo a ser criado
     * @param string $tplContent Conteudo do Template
     * @param bool $overwrite Sobrescreve o arquivo ja existente
     *
     * @return boolean
     */
    public static function makeSourcer ( $nameFile, $tplContent, $overwrite = false )
    {
        if ( !$overwrite && is_file ( $nameFile ) )
        {
            return false;
        }

        if ( !file_put_contents ( $nameFile, $tplContent ) )
        {
            die( "\033[0;31mError: could not write model file $nameFile.\033[0m\n" );
        }

        return true;
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
            $temp .= ucfirst ( strtolower( $part ) );
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