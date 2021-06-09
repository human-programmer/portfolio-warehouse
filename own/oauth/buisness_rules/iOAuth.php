<?php


interface iOAuth
{

    function isExistsToken(): bool;


    function Token(): iToken;


    function createToken(): void;


    function validate(string $token): bool;


}