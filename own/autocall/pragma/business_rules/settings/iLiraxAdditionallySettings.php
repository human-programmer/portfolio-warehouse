<?php


namespace Autocall\Pragma;


interface iLiraxAdditionallySettings
{
    function getSettingsStruct(): iLiraxAdditionallySettingsStruct;

    function saveTimeResponsible(int $quantity): void;

    function saveWorkTime(array $array_calls): void;

    function saveQuantityCallClient(int $quantity): void;

    function saveNumberOfCallAttempts(array $array_calls): void;

    function saveArrayUsePipelineShops(array $array_pipeline): void;

    function saveArrayUsePipelineNumbers(array $array_pipeline): void;

    function saveArrayUsePriority(array $array_pipeline): void;

}