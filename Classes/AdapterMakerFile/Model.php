<?php

namespace Classes\AdapterMakerFile;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151
 */
class Model extends AbstractAdapter
{
    /**
     * @var void
     */
    public    $pastName      = 'Model';
    protected $parentClass   = "";
    protected $parentFileTpl = "";
    protected $fileTpl       = "model.tpl";

}
