<?php

namespace Classes\AdapterMakerFile\Phalcon;

use Classes\AdapterMakerFile\AbstractAdapter;

/**
 * @author Pedro Alarcao <phacl151@gmail.com>
 * @link   https://github.com/pedro151/orm-generator
 */
class Peer extends AbstractAdapter
{

    public    $pastName      = 'Peer';
    protected $fileTpl       = "peer.php";
    protected $fileFixedData = array (
        'parentclass' => array (
            'name' => "AbstractPeer" ,
            'tpl'  => "peer_abstract.php"
        )
    );

    public function parseRelation ( \Classes\MakerFile $makerFile, \Classes\Db\DbTable $dbTable )
    {
        return array ();
    }
}
