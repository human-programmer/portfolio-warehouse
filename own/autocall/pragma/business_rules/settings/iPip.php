<?php


namespace Autocall\Pragma;


interface iPip
{
    function savePipe(int $id):void;

    function deletePipe(int $id):void;

    function getPips(): array|null;

}