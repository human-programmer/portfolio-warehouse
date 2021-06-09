<?php


namespace RestApi\Amocrm;


use Services\General\iNode;

require_once __DIR__ . '/AmoCRM_API.php';

class AmocrmGateway extends AmoCRM_API
{
    private iNode $node;

    public function __construct(iNode $node, \LogWriter $log_writer = null){
        $this->node = $node;
        parent::__construct($node, $log_writer);
    }
}