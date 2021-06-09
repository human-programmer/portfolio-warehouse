<?php


namespace Autocall\Amocrm;
require_once __DIR__ . '/../../business_rules/getway/iContact.php';


class Contact implements iContact
{
    public function __construct(
        private string $name,
        private int $Phone,
        private int $AttachmentLeadId,
    )
    {
    }


    function getName(): string
    {
        return $this->name;
    }


    function getPhone(): int
    {
        return $this->Phone;
    }

    function getLeadByContactId(): int
    {
        return $this->AttachmentLeadId;
    }
}