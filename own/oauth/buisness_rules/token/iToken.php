<?php


interface iToken
{
    function getAccess(): string;

    function getRefresh(): string;

    function getLive(): int;

    function Token():string;
}