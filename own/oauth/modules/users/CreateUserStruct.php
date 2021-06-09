<?php


namespace oAuth;


class CreateUserStruct extends \Services\General\iUserToCreate
{
    public function __construct(private string  $name, private string $phone)
    {
    }

    function getName(): string|null
    {
        return $this->name;
    }

    function getSurname(): string|null
    {
        return null;
    }

    function getMiddleName(): string|null
    {
        return null;
    }

    function getEmail(): string|null
    {
        return null;
    }

    function getPhone(): string|null
    {
        return $this->phone;
    }

    function getLang(): string|null
    {
        return null;
    }
}