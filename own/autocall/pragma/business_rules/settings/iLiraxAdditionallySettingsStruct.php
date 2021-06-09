<?php


namespace Autocall\Pragma;


interface iLiraxAdditionallySettingsStruct
{

    function setTimeResponsible(int $quantity): void;
    function getTimeResponsible(): int;


    function setWorkTime(int $start, int $finish): void;
    function getWorkTime(): array;


    function setQuantityCallClient(array $quantity): void;
    function getQuantityCallClient(): array;


    function setNumber_of_call_attempts(array $array_calls): void;
    function getNumber_of_call_attempts(): array;


    function setArrayUsePipelineShops(array $array_pipeline): void;
    function getArrayUsePipelineShops(): array;


    function setArrayUsePipelineNumbers(array $array_pipeline): void;
    function getArrayUsePipelineNumbers(): array;


    function setArrayUsePriority(array $array_pipeline): void;
    function getArrayUsePriority(): array;

}