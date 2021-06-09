<?php


namespace PragmaStorage;


interface iExportDetail extends iBasis {
    //Вернёт остаток от $quantity
    function addQuantity (float $quantity) : float;
    function setQuantity (float $quantity);
    function reduceQuantity (float $quantity) : float;
    function getQuantity () : float;
    function getProductImportId() : int;
    function getExportId() : int;
    function isDeficit() : bool;
    function isExported () : bool;
    function getProductImport(): iProductImport;
    function getTotalPurchasePrice(): float;
}