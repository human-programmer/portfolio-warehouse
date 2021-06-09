<?php


namespace PragmaStorage;


interface iSettings
{
    function setFractional(string $fractional): void;

    function getFractional(): bool;

    function setStock(int $id): void;

    function getStock(): int;

    function getFirstStockId(): int;

}