<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/units/iUnits.php';
require_once __DIR__ . '/UnitsSchema.php';


class Units extends UnitsSchema implements iUnits
{
    function __construct(int $pragma_account_id)
    {
        parent::__construct($pragma_account_id);
    }

    function getUnits(): array
    {
       return $this->getUnit();
    }
}