<?php


namespace PragmaStorage;


interface iExports {
	function save (iExport $export) : bool;

	function getEntityExports(iEntity $entity): array;
	function getExport(int $export_id): iExport;
	function findExport(iEntity $entity, iProduct $product);
	function getExports(array $ids): array;
	function getDeficitExports(int $product_id): array;

	function createExports(iEntity $entity, array $models): array;
	function createExport(iEntity $entity, iProduct $product, float $quantity, float $selling_price): iExport;
	function createOrGetExport(iEntity $entity, iProduct $product): iExport;

	function saveExport(iExport $export): bool;
	function deleteExport(iExport $export): bool;
	function deleteEntityExport(iEntity $entity, iProduct $product): bool;
	function updateExports(int $product_id): bool;

    function createExportFromStruct(IExportModel $export_struct): iExport;
	function createLinkedTravelsExportModel(int $product_id): IExportModel;
}