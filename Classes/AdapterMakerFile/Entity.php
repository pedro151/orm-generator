<?php

namespace Classes\AdapterMakerFile;


/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/DAO-Generator
 */
class Entity extends AbstractAdapter
{

    /**
     * @var void
     */
    public    $pastName      = 'Entity';
    protected $parentClass   = "EntityAbstract";
    protected $parentFileTpl = "entity_abstract.tpl";
    protected $fileTpl       = "entity.tpl";

}
