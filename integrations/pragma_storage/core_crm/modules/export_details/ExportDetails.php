<?php


namespace PragmaStorage;


require_once __DIR__ . '/ExportDetailsSchema.php';
require_once __DIR__ . '/ExportDetail.php';
require_once __DIR__ . '/../../business_rules/export_details/iExportDetails.php';


class ExportDetails extends ExportDetailsSchema implements iExportDetails {
	private array $export_details = [];

	public function __construct(int $pragma_account_id) {
		parent::__construct($pragma_account_id);
	}

	function getProductImportExportDetails(iProductImport $product_import): array {
		$models = parent::getProductImportDetailModels($product_import->getProductImportId());

		foreach ($models as $model) {
			$export = self::getExports()->getExport($model['product_export_id']);

			$details[] = $this->findInBuffer($export, $product_import);
		}

		return $details ?? [];
	}

	function addQuantityToExportDetail(iExport $export, iProductImport $product_import, float $quantity): float {
		if ($product_import->getFreeBalanceQuantity() <= 0)
			return $quantity;

		return $this->getExportDetail($export, $product_import)->addQuantity($quantity);
	}

	function getExportDetail(iExport $export, iProductImport $product_import): iExportDetail {
		return $this->findInBuffer($export, $product_import) ?? $this->createExportDetail($export, $product_import);
	}

	private function createExportDetail(iExport $export, iProductImport $product_import): iExportDetail {
		$export_detail = $this->_create($product_import, $export, 0);

		$this->addDetailInBuffer($export_detail);

		return $export_detail;
	}

	function getExportDetails(iExport $export): array {
		$export_id = $export->getExportId();

		if (isset($this->export_details[$export_id]))
			return $this->export_details[$export_id];

		$this->load_export_details($export);

		return $this->export_details[$export_id] ?? [];
	}

	private function load_export_details(iExport $export) {
		$models = parent::getCurrentDetailModels($export->getExportId());

		foreach ($models as $model) {
			$product_import = self::getProductImports()->getProductImport($model['product_import_id']);

			$export_details[] = $this->_create($product_import, $export, $model['quantity']);
		}

		$this->addDetailsInBuffer($export_details ?? []);
	}

	private function addDetailsInBuffer (array $details) : void {
		foreach ($details as $detail)
			$this->addDetailInBuffer($detail);
	}

	private function addDetailInBuffer(iExportDetail $exportDetail) : void {
		if(!$this->issetDetailInBuffer($exportDetail))
			$this->export_details[$exportDetail->getExportId()][] = $exportDetail;
	}

	private function issetDetailInBuffer (iExportDetail $exportDetail) : bool {
		$details = $this->export_details[$exportDetail->getExportId()] ?? [];
		foreach ($details as $detail)
			if($exportDetail->getExportId() === $detail->getExportId() && $exportDetail->getProductImportId() === $detail->getProductImportId())
				return true;
		return false;
	}

	private function findInBuffer(iExport $export, iProductImport $productImport) {
		$details = $this->getExportDetails($export);

		foreach ($details as $detail)
			if ($detail->getProductIMportId() === $productImport->getProductImportId())
				return $detail;

		return null;
	}

	function clearDetails(int $export_id): bool {
		$flag = $this->clearProductExportDetails($export_id);

		if (!$flag)
			throw new \Exception('Failed to clear details for export: ' . $export_id);

		$this->export_details[$export_id] = [];

		return true;
	}

	private function _create(iProductImport $productImport, iExport $export, float $quantity): iExportDetail {
		return new ExportDetail($export, $productImport, $quantity);
	}

	function deleteDetail(iExportDetail $detail): bool {
		if (!parent::deleteProductExportDetail($detail->getProductImportId(), $detail->getExportId()))
			throw new \Exception('failed to delete export_detail');

		return $this->deleteFromBuffer($detail);
	}

	private function deleteFromBuffer(iExportDetail $detail): bool {
		$export_id = $detail->getExportId();
		$product_import_id = $detail->getProductImportId();

		for ($i = count($this->export_details[$export_id] ?? []) - 1; $i >= 0; --$i)
			if ($this->export_details[$export_id][$i]->getProductImportId() === $product_import_id)
				array_splice($this->export_details[$export_id], $i, 1);

		return true;
	}

	function save(iExportDetail $detail): void {
		parent::updateProductExportDetail($detail->getProductImportId(), $detail->getExportId(), $detail->getQuantity());
	}

	private function getProductImports(): iProductImports {
		return PragmaFactory::getProductImports();
	}

	private function getExports(): iExports {
		return PragmaFactory::getExports();
	}
}