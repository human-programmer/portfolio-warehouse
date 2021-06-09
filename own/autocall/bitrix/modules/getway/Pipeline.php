<?php


namespace Autocall\Bitrix;


class Pipeline implements iPipeline
{


    public function __construct(
    private string $name,
    private int $id)
    {
    }

    function getName(): string
    {
        return $this->name;
    }

    function getId(): int
    {
        return $this->id;
    }
}