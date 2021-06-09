<?php


namespace Autocall\Pragma;

require_once __DIR__ . '/../../business_rules/core/iLiraxCoreStruct.php';


class LiraxCoreStruct implements iLiraxCoreStruct
{

    public function __construct(
        private int $Status,
        private bool $Mode,
    )
    {
    }

    function setStatus(int $NewStatus): void
    {
        $this->Status = $NewStatus;
    }

    function getStatus(): int
    {
        return $this->Status;
    }

    function getMode(): bool
    {
        return $this->Mode;
    }

    function setMode(bool $mode): void
    {
        $this->Mode = $mode;
    }

}