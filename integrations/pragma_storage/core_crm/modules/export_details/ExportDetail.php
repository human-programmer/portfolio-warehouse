<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/export_details/iExportDetail.php';


class ExportDetail implements iExportDetail {
	private iExport $product_export;
	private iProductImport $product_import;
	private float $quantity;
	private bool $deleted = false;

	public function __construct(iExport $product_export, iProductImport $product_import, float $quantity = null) {
		$this->product_export = $product_export;
		$this->product_import = $product_import;
		$this->quantity = $quantity ?? 0;
	}

	public function getProductImportId(): int {
		return $this->product_import->getProductImportId();
	}

	//возвращает остаток от $quantity
	public function reduceQuantity(float $quantity): float {
		$detail_quantity = $this->quantity;

		if ($detail_quantity - $quantity > 0)
			$this->setQuantity($detail_quantity - $quantity);
		else
			$this->delete();

		return $detail_quantity - $quantity > 0 ? 0 : $quantity - $detail_quantity;
	}

	public function addQuantity(float $quantity): float {
		if ($this->isDeficit()) {
			$this->setQuantity($this->quantity + $quantity);
			return 0;
		}
		$free = $this->product_import->getFreeBalanceQuantity();
		if (!$free) return $quantity;

		if ($quantity - $free < 0)
			$this->setQuantity($this->quantity + $quantity);
		else
			$this->setQuantity($this->quantity + $free);

		return $quantity - $free < 0 ? 0 : $quantity - $free;
	}

	public function update(array $model): bool {
		$this->setQuantity($model['quantity']);
		return true;
	}

	public function delete(): bool {
		$flag = self::getExportDetails()->deleteDetail($this);

		if (!$flag)
			throw new \Exception('Failed to delete ProductExportDetail');

		$this->quantity = 0;
		$this->deleted = true;
		$this->getProductImport()->updateBalance();
		return true;
	}

	public function isDeficit(): bool {
		return $this->product_import->isDeficit();
	}

	public function isDeleted(): bool {
		return $this->deleted;
	}

	public function getQuantity(): float {
		return $this->quantity;
	}

	private function getExportDetails(): iExportDetails {
		return PragmaFactory::getExportDetails();
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		return ['product_export_id' => $this->getExportId(), 'product_import_id' => $this->getProductImportId(), 'quantity' => $this->getQuantity(),];
	}

	function getExportId(): int {
		return $this->product_export->getExportId();
	}

	function isExported(): bool {
		return $this->getExport()->isExported();
	}

	public function getExport(): iExport {
		return $this->product_export;
	}

	public function getProductImport(): iProductImport {
		return $this->product_import;
	}

	function setQuantity(float $quantity) {
		if ($this->quantity === $quantity)
			return;
		$this->quantity = $quantity;
		$this->save();
		$this->product_import->updateBalance();
	}

	function save(): void {
		$this->getExportDetails()->save($this);
	}

	function getTotalPurchasePrice(): float {
	    return $this->getQuantity() * $this->getProductImport()->getPurchasePrice();
    }
}