<?php


namespace Autocall\Pragma;


interface iLiraxCore
{
    function getLiraxCoreStorage(): iLiraxCoreStorage;


    function setStatus(int $status): void;
    function getStatus(): int;


    function setMode(bool $mode): void;
    function getMode(): bool;

    function getPhoneStruct(): iLiraxCoreStruct;

    function getWorkTime(): int;


}