<?php


namespace Autocall\Amocrm;

require_once __DIR__ . '/../../business_rules/getway/iCompanies.php';


class Companies implements iCompanies
{
    public function __construct(
        private int $contactId,
    )
    {
    }

    function getIdContact(): int
    {
        return $this->contactId;
    }
}