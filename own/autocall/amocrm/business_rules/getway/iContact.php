<?php


namespace Autocall\Amocrm;


interface iContact
{
    function getName(): string;
    function getPhone(): int;
    function getLeadByContactId() : int;
}