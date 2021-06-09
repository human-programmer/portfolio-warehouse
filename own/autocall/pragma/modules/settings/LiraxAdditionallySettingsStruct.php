<?php


namespace Autocall\Pragma;
require_once __DIR__ . '/../../business_rules/settings/iLiraxAdditionallySettingsStruct.php';


class LiraxAdditionallySettingsStruct implements iLiraxAdditionallySettingsStruct
{

    public function __construct(
        private $TimeResponsible,
        private $WorkTime,
        private $QuantityCallClient,
        private $Number_of_call_attempts,
        private $ArrayUsePipelineShops,
        private $ArrayUsePipelineNumbers,
        private $ArrayUsePriority
    )
    {
    }

    function setTimeResponsible(int $quantity): void
    {
        $this->TimeResponsible = $quantity;
    }

    function getTimeResponsible(): int
    {
        return $this->TimeResponsible;
    }

    function setWorkTime(int $start, int $finish): void
    {
        $this->WorkTime = $start;
    }

    function getWorkTime(): array
    {
        return $this->WorkTime;
    }

    function setQuantityCallClient(array $quantity): void
    {
        $this->QuantityCallClient = $quantity;
    }

    function getQuantityCallClient(): array
    {
        return $this->QuantityCallClient;
    }

    function setNumber_of_call_attempts(array $array_calls): void
    {
        $this->Number_of_call_attempts = $array_calls;
    }

    function getNumber_of_call_attempts(): array
    {
        return $this->Number_of_call_attempts;
    }

    function setArrayUsePipelineShops(array $array_pipeline): void
    {
        $this->ArrayUsePipelineShops = $array_pipeline;
    }

    function getArrayUsePipelineShops(): array
    {
        return $this->ArrayUsePipelineShops;
    }

    function setArrayUsePipelineNumbers(array $array_pipeline): void
    {
        $this->ArrayUsePipelineNumbers = $array_pipeline;
    }

    function getArrayUsePipelineNumbers(): array
    {
        return $this->ArrayUsePipelineNumbers;
    }

    function setArrayUsePriority(array $array_pipeline): void
    {
        $this->ArrayUsePriority = $array_pipeline;
    }

    function getArrayUsePriority(): array
    {
        return $this->ArrayUsePriority;
    }
}