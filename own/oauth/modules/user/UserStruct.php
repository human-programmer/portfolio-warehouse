<?php


namespace market;
require_once __DIR__ . '/../../buisness_rules/user/iUserStruct.php';

class UserStruct implements iUserStruct
{

    public function __construct(
        private string $Name,
        private string $Phone,
        private string $Email,
        private int $id
    )
    {
    }

    function getName(): string
    {
        return $this->Name;
    }

    function getPhone(): string
    {
        return $this->Phone;
    }

    function getEmail(): string
    {
        return $this->Email;
    }

    function getId(): int
    {
        return $this->id;
    }
}