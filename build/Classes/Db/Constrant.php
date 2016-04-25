<?php

namespace Classes\Db;

/**
 * constrants do banco de dados
 * -foreingkey
 * -primarykey
 * -unique
 *
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/ORM-Generator
 */
class Constrant
{
    /**
     * constrants do banco de dados
     * -foreingkey
     * -primarykey
     * -unique
     *
     * @author Pedro Alarcao <phacl151@gmail.com>
     */
    final private function __construct ()
    {
    }

    public static function getInstance ()
    {
        return new self();
    }

    /**
     * @var string
     */
    protected $constrant;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $column;

    /**
     * @param $array
     *
     * @return Constrant
     */
    public function populate ( $array )
    {
        if ( isset( $array[ 'schema' ] ) )
        {
            $this->schema = $array[ 'schema' ];
        }

        $this->constrant = $array[ 'constrant' ];
        $this->table = $array[ 'table' ];
        $this->column = $array[ 'column' ];

        return $this;
    }

    public function getDatabase (){ }

    /**
     * @return string
     */
    public function getNameConstrant ()
    {
        return $this->constrant;
    }

    public function hasSchema ()
    {
        return (bool) $this->schema;
    }

    /**
     * @return string
     */
    public function getSchema ()
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getTable ()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getColumn ()
    {
        return $this->column;
    }

}
