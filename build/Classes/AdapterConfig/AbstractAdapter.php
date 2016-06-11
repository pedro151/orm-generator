<?php

namespace Classes\AdapterConfig;

use Classes\Maker\AbstractMaker;

require_once "Classes/Maker/AbstractMaker.php";
require_once 'Exception.php';

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
abstract class AbstractAdapter
{

    protected $arrConfig = array (
        ############################# DATABASE
        //Driver do banco de dados
        'driver'   => null ,
        //Nome do banco de dados
        'database' => null ,
        //Host do banco
        'host'     => 'localhost' ,
        //Port do banco
        'port'     => '' ,
        //usuario do banco
        'username' => null ,
        //senha do banco
        'password' => null ,
        // lista de schemas do banco de dados
        'schema'   => array () ,

        'socket'          => null ,

        ########################### DOCS
        // autor que gerou o script
        'author'          => "Pedro" ,
        'license'         => "New BSD License" ,
        'copyright'       => "ORM Generator - Pedro151" ,
        'link'            => 'https://github.com/pedro151/orm-generator' ,
        // data que foi gerado o script
        'last_modify'     => null ,

        ########################## Ambiente/Arquivos

        // Nome do framework para o adapter
        'framework'       => null ,
        // namespace das classes
        'namespace'       => "" ,
        // caminho onde os arquivos devem ser criados
        'path'            => 'models' ,
        // flag para gerar pasta com o nome do driver do banco de dados
        'folder-database' => 0,
        // string com o nome da pastar personalizada
        'folder-name' => '',

        ############################## Comandos adicionais
        //flag para mostrar o status da execução ao termino do processo
        'status'          => false ,
        // flags para criar todas as tabelas ou nao
        'allTables'       => true ,
        //Lista de tabelas a serem ignoradas
        'ignoreTable'     => array () ,
    );

    /**
     * @var string[] um array com todos os campos obrigatorios
     */
    protected $attRequered = array (
        'driver'   => true ,
        'database' => true ,
        'host'     => true ,
        'username' => true ,
        'path'     => true
    );

    protected $arrFunc = array ();

    private $framworkFiles = array ();

    const SEPARETOR = "";

    /**
     * verifica se todos valores obrigatorios tem valor
     *
     * @return bool
     */
    protected function checkConfig ()
    {
        if ( array_diff_key ( $this->attRequered , array_filter ( $this->arrConfig ) ) )
        {
            return false;
        }

        return true;
    }

    /**
     * retorna os parametros da configuração do framework
     *
     * @return array
     */
    abstract protected function getParams ();

    /**
     * Popula as config do generater com as configuraçoes do framework
     *
     * @return mixed
     */
    abstract protected function parseFrameworkConfig ();

    /**
     * Cria Instancias dos arquivos que devem ser gerados
     *
     * @return \Classes\AdapterMakerFile\AbstractAdapter[]
     */
    abstract public function getMakeFileInstances ();

    abstract protected function init ();

    /**
     * retorna a base do Namespace
     *
     * @return array
     */
    protected function getBaseNamespace ()
    {
        return array (
            $this->arrConfig[ 'namespace' ] ,
            'Model'
        );
    }

    /**
     * @param \Classes\Db\DbTable|\Classes\Db\Constrant $table
     *
     * @return mixed
     */

    public function createClassNamespace ( $table )
    {
        $arrNames = $this->getBaseNamespace ();

        if ( isset( $this->arrConfig[ 'folder-database' ] )
             && $this->arrConfig[ 'folder-database' ]
        )
        {
            $arrNames[] = AbstractMaker::getClassName ( $this->arrConfig[ 'driver' ] );
        }

        if ( isset( $this->arrConfig[ 'folder-name' ] )
             && $this->arrConfig[ 'folder-name' ]
        )
        {
            $arrNames[] = AbstractMaker::getClassName ( $this->arrConfig[ 'folder-name' ] );
        }

        if ( $table->hasSchema () )
        {
            $arrNames[] = AbstractMaker::getClassName ( $table->getSchema () );
        } else
        {
            $arrNames[] = AbstractMaker::getClassName ( $table->getDatabase () );
        }

        return implode ( static::SEPARETOR , array_filter ( $arrNames ) );
    }

    public function __construct ( $array )
    {
        $array += array (
            'author'      => ucfirst ( get_current_user () ) ,
            'last_modify' => date ( "d-m-Y H:i:s." )
        );

        $this->setFrameworkFiles ( $array );
        $this->parseFrameworkConfig ();
        $this->setParams ( $this->getParams () );
        $this->setParams ( $array );
        $this->init ();
        if ( ! $this->isValid () )
        {
            $var = array_diff_key ( $this->attRequered , array_filter ( $this->arrConfig ) );
            throw new Exception( $var );
        }
    }

    /**
     * Set os arquivos de configuracao do framework
     *
     * @param $array
     */
    public function setFrameworkFiles ( $array )
    {
        $this->framworkFiles[ 'library' ] = isset( $array[ 'framework-path-library' ] )
            ? $array[ 'framework-path-library' ]
            : null;

        $this->framworkFiles[ 'ini' ] = isset( $array[ 'framework-ini' ] )
            ? $array[ 'framework-ini' ]
            : null;

        $this->framworkFiles[ 'environment' ] = isset( $array[ 'environment' ] )
            ? $array[ 'environment' ]
            : 'production';

    }

    protected function isValidFrameworkFiles ()
    {
        if ( ! is_file ( $this->framworkFiles[ 'ini' ] ) )
        {
            return false;
        }

        if ( ! is_dir ( $this->framworkFiles[ 'library' ] ) )
        {
            return false;
        }


        if ( ! isset ( $this->framworkFiles[ 'environment' ] )
             or empty( $this->framworkFiles[ 'environment' ] )
        )
        {
            return false;
        }
        set_include_path (
            implode (
                PATH_SEPARATOR ,
                array (
                    realpath ( $this->framworkFiles[ 'library' ] ) ,
                    get_include_path () ,
                )
            )
        );

        return true;
    }

    protected function getFrameworkIni ()
    {
        return $this->framworkFiles[ 'ini' ];
    }

    protected function getEnvironment ()
    {
        return $this->framworkFiles[ 'environment' ];
    }

    /**
     * Popula as variaveis de acordo com o arquivo de configuração do seu  framework
     */
    protected function isValid ()
    {
        return $this->checkConfig ();
    }

    private function setParams ( $array )
    {
        if ( count ( $array ) > 0 )
        {
            $this->arrConfig = array_filter ( $array ) + $this->arrConfig;
        }
    }

    /**
     * @return string
     */
    public function getDatabase ()
    {
        return $this->arrConfig[ 'database' ];
    }

    /**
     * @return bool
     */
    public function hasSchemas ()
    {
        return ! empty ( $this->arrConfig[ 'schema' ] );
    }

    /**
     * @return string[]
     */
    public function getSchemas ()
    {
        if ( is_string ( $this->arrConfig[ 'schema' ] ) )
        {
            return array ( $this->arrConfig[ 'schema' ] );
        }

        return $this->arrConfig[ 'schema' ];
    }

    public function setSchema ( $schema )
    {
        $this->arrConfig[ 'schema' ] = $schema;
    }

    /**
     * @return string
     */
    public function getHost ()
    {
        return $this->arrConfig[ 'host' ];
    }

    /**
     * @return int
     */
    public function getPort ()
    {
        return $this->arrConfig[ 'port' ];
    }

    /**
     * @return boolean
     */
    public function hasPort ()
    {
        return ! empty( $this->arrConfig[ 'port' ] );
    }

    /**
     * @return string
     */
    public function getSocket ()
    {
        return $this->arrConfig[ 'socket' ];
    }

    /**
     * @return string
     */
    public function getUser ()
    {
        return $this->arrConfig[ 'username' ];
    }

    /**
     * @return string
     */
    public function getPassword ()
    {
        return $this->arrConfig[ 'password' ];
    }

    /**
     * @return bool
     */
    public function isStatusEnabled ()
    {
        return (bool) $this->arrConfig[ 'status' ];
    }

    /**
     * @param $str
     *
     * @return string
     */
    public function __get ( $str )
    {
        $arr = array (
            'namespace' ,
            'framework' ,
            'author' ,
            'license' ,
            'copyright' ,
            'link' ,
            'last_modify' ,
            'path' ,
            'folder-database',
            'folder-name'
        );

        if ( in_array ( $str , $arr ) )
        {
            return $this->arrConfig[ strtolower ( $str ) ];
        }

        return;
    }

}
