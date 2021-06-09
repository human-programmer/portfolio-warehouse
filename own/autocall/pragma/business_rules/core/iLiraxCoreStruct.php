<?php


namespace Autocall\Pragma;


interface iLiraxCoreStruct
{

    function setStatus(int $NewStatus): void;
    function getStatus(): int;


    function getMode(): bool;
    function setMode(bool $mode): void;



}