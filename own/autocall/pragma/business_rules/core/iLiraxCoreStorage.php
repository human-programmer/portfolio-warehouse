<?php


namespace Autocall\Pragma;


interface iLiraxCoreStorage
{
    function getExistGeneralFile(): bool;

    function initFile(string $filename, string $data, int $time): void;

    function initFileUNLINK(string $filename, string $data, int $time): void;


}