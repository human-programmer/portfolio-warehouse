<?php


namespace Autocall\Pragma;


interface iLirax
{
    function getLiraxSettingsStruct(): iLiraxSettingsStruct;

    function call(): void;

    function getUserSIPs():string;

    function IsFreeUsers(string $responsible, string $amo): array;

}