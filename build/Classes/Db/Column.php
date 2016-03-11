<?php

namespace Classes\Db;

/**
 * Colunas dos bancos
 *
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Column
{

    /**
     * Colunas dos bancos
     *
     * @author Pedro Alarcao <phacl151@gmail.com>
     */
    public function __construct ()
    {
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Classes\Db\Constrant[]
     */
    private $primarykey;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var boolean
     */
    private $nullable;

    /**
     * @var int
     */
    private $max_length;

    /**
     * @var \Classes\Db\Constrant[]
     */
    private $dependences;

    /**
     * @var \Classes\Db\Constrant
     */
    private $refForeingkey;

    /**
     * @type string
     */
    private $sequence;

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * popula o
     *
     * @param $array
     */
    public function populate ( $array )
    {
        $this->name = $array[ 'name' ];
        $this->type = $array[ 'type' ];
        $this->nullable = $array[ 'nullable' ];
        $this->max_length = $array[ 'max_length' ];

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPrimaryKey ()
    {
        return ! empty( $this->primarykey );
    }

    /**
     * @return boolean
     */
    public function isForeingkey ()
    {
        return ! empty( $this->refForeingkey );
    }

    /**
     * @return boolean
     */
    public function hasDependence ()
    {
        return ! empty( $this->dependences );
    }

    /**
     * @return string
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getComment ()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment ( $comment )
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param \Classes\Db\Constrant $primarykey
     */
    public function setPrimaryKey ( Constrant $primarykey )
    {
        $this->primarykey = $primarykey;

        return $this;
    }

    /**
     * @param \Classes\Db\Constrant $dependece
     */
    public function addDependece ( Constrant $dependece )
    {
        $this->dependences[] = $dependece;

        return $this;
    }

    /**
     * @param \Classes\Db\Constrant $reference
     */
    public function addRefFk ( Constrant $reference )
    {
        $this->refForeingkey = $reference;

        return $this;
    }

    /**
     * retorna as foreingkeys
     *
     * @return \Classes\Db\Constrant
     */
    public function getFks ()
    {
        return $this->refForeingkey;
    }

    /**
     * Retorna as dependencias da tabela
     *
     * @return \Classes\Db\Constrant[]
     */
    public function getDependences ()
    {
        return $this->dependences;
    }

    /**
     * @return bool
     */
    public function hasDependences ()
    {
        return (bool) count ( $this->dependences );
    }

    /**
     * Retorna a constrant da primarykey da tabela
     *
     * @return \Classes\Db\Constrant[]
     */
    public function getPrimaryKey ()
    {
        return $this->primarykey;
    }

    /**
     *
     */
    public function getMaxLength ()
    {
        return $this->max_length;
    }

    /**
     * @return bool
     */
    public function hasSequence ()
    {
        return (bool) $this->sequence;
    }

    /**
     * @return string
     */
    public function getSequence ()
    {
        return $this->sequence;
    }

    /**
     * @param string $sequence
     */
    public function setSequence ( $sequence )
    {
        $this->sequence = $sequence;
    }

    /**
     * @return boolean
     */
    public function isNullable ()
    {
        return $this->nullable;
    }

}
