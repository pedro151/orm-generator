<?php

namespace Classes\AdapterMakerFile;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151
 */
class DbTable extends AbstractAdapter
{

    /**
     * @var string
     */
    protected $pastName      = 'DbTable';
    protected $parentClass   = "TableAbstract";
    protected $parentFileTpl = "dbtable_abstract.tpl";
    protected $fileTpl       = "dbtable.tpl";

}
