<?php
require_once __DIR__ . '/../../buisness_rules/token/iToken.php';

class Token implements iToken
{
    public function __construct(
        private string $AccessToken,
        private string $RefreshToken,
        private int $LiveToken,
    )
    {
    }

    function getAccess(): string
    {
    }

    function getRefresh(): string
    {
        return $this->RefreshToken;
    }

    function getLive(): int
    {
        return $this->LiveToken;
    }


    function Token(): string
    {
        return $this->getAccess() . 'A' . $this->getLive() . 'R' . $this->getRefresh();
    }
}