<?php

namespace market;

interface iValidation
{
    function createCode(): void;

    function deleteCode(): void;

    function getCode(): int;

    function checkCode(int $code): int;


}