<?php

namespace Classes\Db;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class DbTable
{
    /**
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
     * @var string
     */
    private $schema;

    /**
     * @var string
     */
    private $database;

    /**
     * @var \Classes\Db\Column[]
     */
    private $columns;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @type \Classes\Db\Column[]
     */
    private $primarykeys = array ();

    /**
     * @type \Classes\Db\Column[]
     */
    private $foreingkeys = array ();

    private $dependence = array ();

    /**
     * @type string[]
     */
    private $sequence = array ();

    public function populate ( $array )
    {
        $this->name = $array[ 'table' ];
        $this->schema = isset( $array[ 'schema' ] ) ? $array[ 'schema' ] : null;
        $this->database = $array[ 'database' ];

        return $this;
    }

    /**
     * @param \Classes\Db\Column $column
     */
    public function addColumn ( Column $column )
    {
        $this->columns[ $column->getName () ] = $column;

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return \Classes\Db\Column
     */
    public function getColumn ( $columnName )
    {
        if ( isset( $this->columns[ $columnName ] ) )
        {
            return $this->columns[ $columnName ];
        }

        return;
    }

    /**
     * @return \Classes\Db\Column[]
     */
    public function getPrimaryKeys ()
    {
        if ( ! count ( $this->primarykeys ) )
        {
            foreach ( $this->getColumns () as $column )
            {
                if ( $column->isPrimaryKey () )
                {
                    $this->primarykeys[] = $column;
                }
            }
        }

        return $this->primarykeys;
    }

    /**
     * @return \Classes\Db\Column[]
     */
    public function getForeingkeys ()
    {
        if ( ! count ( $this->foreingkeys ) )
        {
            foreach ( $this->getColumns () as $column )
            {
                if ( $column->isForeingkey () )
                {
                    $this->foreingkeys[] = $column;
                }
            }
        }

        return $this->foreingkeys;
    }

    /**
     * @return \Classes\Db\Column[]
     */
    public function getDependences ()
    {
        if ( ! count ( $this->dependence ) )
        {
            foreach ( $this->getColumns () as $column )
            {
                if ( $column->hasDependence() )
                {
                    $this->dependence[] = $column;
                }
            }
        }

        return $this->dependence;
    }

    /**
     * @return string[]
     */
    public function getSequences ()
    {
        if ( ! count ( $this->sequence ) )
        {
            foreach ( $this->getColumns () as $column )
            {
                if ( $column->hasSequence () )
                {
                    $this->sequence[] = $column->getSequence ();
                }
            }
        }

        return $this->sequence;
    }

    /**
     * @return \Classes\Db\Column[]
     */
    public function getColumns ()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSchema ()
    {
        return $this->schema;
    }

    /**
     * @return bool
     */
    public function hasSchema ()
    {
        return (bool) $this->schema;
    }

    /**
     * @return string
     */
    public function getDatabase ()
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getNamespace ()
    {
        return $this->namespace;
    }

    /**
     * @param string $Namespace
     */
    public function setNamespace ( $namespace )
    {
        $this->namespace = $namespace;
    }

}
