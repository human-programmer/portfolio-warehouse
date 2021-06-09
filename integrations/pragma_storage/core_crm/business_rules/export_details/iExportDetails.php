<?php


namespace PragmaStorage;


interface iExportDetails
{
    function addQuantityToExportDetail (iExport $export, iProductImport $product_import, float $quantity) : float;

    function getProductImportExportDetails (iProductImport $product_import) : array;

    function getExportDetails(iExport $export): array;

    function getExportDetail (iExport $export, iProductImport $product_import) : iExportDetail;

    function clearDetails(int $export_id) : bool;

    function deleteDetail (iExportDetail $detail) : bool;

	function save(iExportDetail $detail): void;
}