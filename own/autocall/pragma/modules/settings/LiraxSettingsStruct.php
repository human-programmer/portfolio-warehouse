<?php

namespace Autocall\Pragma;

use JetBrains\PhpStorm\Pure;

require_once __DIR__ . '/../../business_rules/settings/iLiraxSettingsStruct.php';


class LiraxSettingsStruct implements iLiraxSettingsStruct
{
    private int $target_number;
    private string $method = 'make2Calls';
    private int $inner_number = 5000;
    private int $from_number = 201;
    private int $id_shop = 0;
    private string $speech = '';
    private int $FirstInternal = 1;


    public function __construct(
        private string $token,
        private string $use_store,
        private string $use_number,
        private string $use_priory,
        private string $use_responsible,
        private int $application,
    )
    {
    }

    function getMethod(): string
    {
        return $this->method;
    }

    function getToken(): string
    {
        return $this->token;
    }

    function getInnerNumber(): int
    {
        return $this->inner_number;
    }

    function setInnerNumber(int $number): void
    {
        $this->inner_number = $number;
    }

    function getTargetNumber(): int
    {
        return $this->target_number;
    }

    function setTargetNumber(int $number): void
    {
        $this->target_number = $number;
    }

    function getFromNumber(): int
    {
        return $this->from_number;
    }

    function setFromNumber(int $number): void
    {
        $this->from_number = $number;
    }

    function getUseStore(): string
    {
        return $this->use_store;
    }

    function getUseNumber(): string
    {
        return $this->use_number;
    }

    function getUsePriory(): string
    {
        return $this->use_priory;
    }

    function getUseResponsible(): string
    {
        return $this->use_responsible;
    }

    function getApplication(): int
    {
        return $this->application;
    }


    private function getShopId(): int
    {
        return $this->id_shop;
    }

    public function setIdShop(int $id): void
    {
        $this->id_shop = $id;
    }


    function getSpeech(): string
    {
        return $this->speech;
    }

    function setSpeech(string $speech): void
    {
        $this->speech = $speech;
    }

    function getQuantityResponsible():string{
        return $this->use_responsible;
    }


    function getFirstInternal(): int
    {
        return $this->FirstInternal;
    }

    #[Pure] function getArrayToSave(): array
    {
        $result['token'] = $this->getToken();
        $result['use_store'] = $this->getUseStore();
        $result['use_number'] = $this->getUseNumber();
        $result['use_priory'] = $this->getUsePriory();
        $result['use_responsible'] = $this->getUseResponsible();
        $result['application'] = $this->getApplication();
        return $result;
    }

    function getHttpQuery(): string
    {
        $arr = $this->getArrayToQuery();
        $speech = $this->getSpeech();
        return http_build_query($arr) . "&speech=$speech";
    }

    private function getArrayToQuery(): array
    {

        $result['cmd'] = $this->getMethod();
        $result['token'] = $this->getToken();
        $result['to1'] = $this->getInnerNumber();
        $result['to2'] = $this->getTargetNumber();
        $result['from'] = $this->getFromNumber();
        $result['FirstInternal'] = $this->getFirstInternal();
        if ($this->getShopId())
            $result['idshop'] = $this->getShopId();
        return $result;
    }


    function useShops(): bool
    {
        return match ($this->getUseStore()) {
            'true' => true,
            'false' => false

        };
    }

    function usePipelineNumber(): bool
    {
        return match ($this->getUseNumber()) {
            'true' => true,
            'false' => false
        };
    }
}