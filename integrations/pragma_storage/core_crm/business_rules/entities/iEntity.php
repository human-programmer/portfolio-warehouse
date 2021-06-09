<?php


namespace PragmaStorage;


interface iEntity {
    function delete();
    function updateStatus();
    function getOwnedExports () : array;
    function setExport(iProduct $product, float $selling_price, float $quantity) : bool;
    function setChangedExportValues () : void;
    function getAllExports () : array;
    function getExportStatus();
    function getPragmaEntityId() : int;
    function findResponsibleUserId ();
}