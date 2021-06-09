<?php


namespace PragmaStorage;


interface iImports {
	function save(IImportStruct $import): void;
    function getImports (iStore $store) : array;
    function createImport(iStore $store, array $model): iImport;
    function getImport (int $import_id) : iImport;
    function deleteImport (iImport $import) : bool;
    function getDeficitImport(int $store_id): iImport;
    function createTravelsImport(ICreationTravelModel $travel): iImport;
}