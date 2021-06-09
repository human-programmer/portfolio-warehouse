<?php


namespace Autocall\Amocrm;


interface iLead
{
    function getIdContact(): int;

    function getName(): string;

    function getIdPipeline(): int;

    function getIdResponsible():int;

}