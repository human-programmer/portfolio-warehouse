<?php


namespace PragmaStorage;


interface iStatus extends iBasis
{
    function getStatusId() : int;

    function getStatusCode() : string;

    function getStatusTitle () : string;

    function isDetailed () : bool;

    function isExported(): bool;
}