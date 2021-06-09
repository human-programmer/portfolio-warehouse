<?php


namespace Autocall\Amocrm;

require_once __DIR__ . '/../../business_rules/getway/iCustomers.php';


class Customers implements iCustomers
{
    public function __construct(private int $contactId,
    )
    {
    }


    function getIdContact(): int
    {
        return $this->contactId;
    }
}