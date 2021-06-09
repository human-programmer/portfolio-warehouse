<?php


namespace Autocall\Bitrix;


interface iLead
{
    function getIdContact(): int;
    function getName(): string;
}