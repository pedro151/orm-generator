<?php

namespace Classes\Db\Iterators;

use Classes\Db\DbTable;
use Classes\Maker\AbstractMaker;

class DbTables implements \ArrayAccess , \SeekableIterator , \Countable
{

    /**
     * @type DbTable[]
     */
    private $objDbTables = array ();

    private $position = 0;

    public function offsetExists ( $offset )
    {
        return isset( $this->objDbTables[ trim ( $offset ) ] );
    }

    public function offsetGet ( $offset )
    {
        return $this->objDbTables[ trim ( $offset ) ];
    }

    public function offsetSet ( $offset , $value )
    {
        return $this->objDbTables[ trim ( $offset ) ] = $value;
    }

    public function offsetUnset ( $offset )
    {
        unset( $this->objDbTables[ trim ( $offset ) ] );

        return $this;
    }

    public function count ()
    {
        return count ( $this->objDbTables );
    }

    /**
     * convert array
     */
    public function toArrayFileName ()
    {
        if ( ! empty( $this->toArrayFileName ) )
        {
            return $this->toArrayFileName;
        }

        foreach ( $this->objDbTables as $objDbTable )
        {
            $this->toArrayFileName[] = AbstractMaker::getClassName ( $objDbTable->getName () )
                                       . '.php';
        }

        return $this->toArrayFileName;
    }

    /* Method required for SeekableIterator interface */

    public function seek ( $position )
    {
        $current = array_keys ( $this->objDbTables );
        if ( ! isset( $this->objDbTables[ $current[ $position ] ] ) )
        {
            throw new OutOfBoundsException( "invalid seek position ($position)" );
        }

        $this->position = $position;
    }

    /* Methods required for Iterator interface */

    public function rewind ()
    {
        $this->position = 0;
    }

    public function current ()
    {
        $current = array_keys ( $this->objDbTables );

        return $this->objDbTables[ $current[ $this->position ] ];
    }

    public function key ()
    {
        $current = array_keys ( $this->objDbTables );

        return $current[ $this->position ];
    }

    public function next ()
    {
        ++ $this->position;
    }

    public function valid ()
    {
        $current = array_keys ( $this->objDbTables );

        return isset( $current[ $this->position ] );
    }
}