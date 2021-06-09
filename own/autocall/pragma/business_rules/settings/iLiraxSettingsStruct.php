<?php


namespace Autocall\Pragma;

// поля только для обзения с бд
//
interface iLiraxSettingsStruct
{
    function getMethod() : string;
    function getToken() : string;
    function getInnerNumber() : int;//to1
    function getTargetNumber() : int;//to2
    function getFromNumber() : int;
    function getSpeech() : string;
    function getApplication() : int;
    function getFirstInternal() : int;
    function getQuantityResponsible() : string;
    function getHttpQuery() : string;


    function useShops() : bool;
    function usePipelineNumber() : bool;

    function setInnerNumber(int $number) : void;
    function setTargetNumber(int $number) : void;//to2
    function setFromNumber(int $number) : void;
    function setSpeech(string $speech) : void;
    function setIdShop(int $id) : void;

}