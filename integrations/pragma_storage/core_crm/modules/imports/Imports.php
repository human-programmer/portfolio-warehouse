<?php


namespace PragmaStorage;


require_once __DIR__ . '/ImportsSchema.php';
require_once __DIR__ . '/Import.php';
require_once __DIR__ . '/../../business_rules/imports/iImports.php';
require_once __DIR__ . '/ImportsBuffer.php';


class Imports extends ImportsSchema implements iImports {
	use ImportsBuffer;

	public function __construct(int $pragma_account_id) {
		parent::__construct($pragma_account_id);
	}

	function getImports(iStore $store): array {
		$models = parent::get_store_import($store->getStoreId());

		foreach ($models as $model)
			$imports[] = $this->findInBuffer($model['import_id']) ?? $this->_create($model);

		return $imports ?? [];
	}

	function save(IImportStruct $import): void {
		parent::update_import($import);
	}

	function createImport(iStore $store, array $model): iImport {
		$import_id = parent::create_import($store->getStoreId(), $model);
		return $this->getImport($import_id);
	}

	function getImport(int $import_id): iImport {
		return $this->findInBuffer($import_id) ?? $this->fetchImport($import_id);
	}

	function deleteImport(iImport $import): bool {
		$this->deleteFromDb($import);
		$this->deleteFromBuffer($import->getImportId());
		return true;
	}

	private function deleteFromDb(iImport $import): void {
		if (!parent::delete_import($import->getImportId()))
			throw new \Exception('Failed to delete import: ' . $import->getImportId());
	}

	private function fetchImport(int $import_id): iImport {
		$import_model = parent::get_import($import_id);
		if(!$import_model)
			throw new \Exception("Import '$import_id' not found");
		return $this->_create($import_model);
	}

	function getDeficitImport(int $store_id): iImport {
		$import = $this->findInBufferDeficit($store_id);
		if($import) return $import;
		$model = $this->getDeficitImportModel($store_id);
		return $this->_create($model);
	}

	private function _create(array $import_model) {
		$import = $this->findInBuffer($import_model['import_id']) ?? new Import($this, $import_model);
		$this->addInBuffer($import);
		return $import;
	}

	function createTravelsImport(ICreationTravelModel $travel): iImport {
		$id = $this->createTravelsImportRow($travel);
		return $this->getImport($id);
	}
}