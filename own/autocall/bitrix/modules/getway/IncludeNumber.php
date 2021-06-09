<?php


namespace Autocall\Bitrix;


class IncludeNumber implements iIncludeNumber
{
    public function __construct(private int $innerNumber)
    {
    }

    function innerPhone(int $id): int
    {
        return $this->innerNumber;
    }
}