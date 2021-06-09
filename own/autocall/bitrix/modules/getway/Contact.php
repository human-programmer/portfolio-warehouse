<?php


namespace Autocall\Bitrix;


class Contact implements iContact
{
    public function __construct(
        private string $name,
        private int $Phone,
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
}