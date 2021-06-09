<?php


namespace PragmaStorage;


require_once __DIR__ . '/ITravelLinkStruct.php';

interface ITravelLink extends ITravelLinkStruct {
    function getTravel(): ITravelModel;
    function findReceiveProductImport(): iProductImport|null;
    function findStartExport(): iExport|null;
    function setQuantity(float $quantity): void;
    function updateLinks(): void;
    function saveSelf(): void;
    function getTotalPurchasePrice(): float;
}