<?php


namespace PragmaStorage;


trait ImportsBuffer {
	private array $imports = [];

	private function findInBuffer(int $import_id): iImport|null{
		return $this->imports[$import_id] ?? null;
	}

	private function findInBufferDeficit(int $store_id): iImport|null{
		foreach ($this->imports as $import)
			if ($import->isDeficit() && $import->getStoreId() === $store_id)
				return $import;
		return null;
	}

	private function addInBuffer(iImport $import): void {
		$this->imports[$import->getImportId()] = $import;
	}

	private function deleteFromBuffer(int $id): void {
		unset($this->imports[$id]);
	}

	function getAllImportsFromBuffer(): array {
		return array_merge([], $this->imports);
	}
}