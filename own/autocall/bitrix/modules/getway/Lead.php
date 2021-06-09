<?php


namespace Autocall\Bitrix;


class Lead implements iLead
{

    public function __construct(
        private int $idContact,
        private string $leadsName
    ){}

    function getIdContact(): int
    {
       return $this->idContact;
    }

    function getName(): string
    {
        return $this->leadsName;
    }
}