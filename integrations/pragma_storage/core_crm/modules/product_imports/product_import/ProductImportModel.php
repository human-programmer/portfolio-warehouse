<?php


namespace PragmaStorage;


use Generals\Functions\Date;

class ProductImportModel implements IProductImportModel {
	private int $product_import_id;
	private int|null $import_id;
	private int $product_id;
	private float $import_quantity;
	private float $purchase_price;
	private int $source;
	private float $free_balance_quantity;
	private float $balance_quantity;
	private int $import_date;
	private int $date_create;
	private bool $deleted;

	function __construct(array $model) {
		$this->product_import_id = $model['product_import_id'] ?? 0;
		$this->import_id = $model['import_id'] ?? null;
		$this->product_id = $model['product_id'];
		$this->purchase_price = round($model['purchase_price'] ?? 0.0, 3);
		$this->source = $model['source'] ?? 0;
		$this->import_quantity = round($model['import_quantity'] ?? 0.0, 3);
		$this->free_balance_quantity = $model['free_balance_quantity'];
		$this->balance_quantity = $model['balance_quantity'];
		$this->deleted = $model['deleted'] ?? false;
		$this->import_date = self::fetchDate($model, 'import_date');
		$this->date_create = self::fetchDate($model, 'date_create');
	}
	static function fetchDate(array $model, string $field_name): int{
		if(!isset($model[$field_name])) return time();
		return gettype($model[$field_name]) === "integer" ? $model[$field_name] : Date::getIntTimeStamp($model[$field_name]);

	}
	function getProductImportId(): int {
		return $this->product_import_id;
	}
	function getImportId(): int|null {
		return $this->import_id;
	}
	function getProductId(): int {
		return $this->product_id;
	}
	function getImportQuantity(): float {
		return $this->import_quantity;
	}
	function getPurchasePrice(): float {
		return $this->purchase_price;
	}
	function setPurchasePrice(float $price): void {
		$this->purchase_price = $price;
	}
	function getSource(): int {
		return $this->source;
	}
	function getFreeBalanceQuantity(): float {
		return $this->free_balance_quantity;
	}
	function getBalanceQuantity(): float {
		return $this->balance_quantity;
	}
	protected function setFreeBalanceQuantity(float $quantity): void {
		$this->free_balance_quantity = $quantity;
	}
	protected function setBalanceQuantity(float $quantity): void {
		$this->balance_quantity = $quantity;
	}
	function isDeficit(): bool {
		return $this->getSource() === DEFICIT_SOURCE;
	}
	function setQuantity(float $quantity): void {
		$dif = $quantity - $this->import_quantity;
		$this->import_quantity += $dif;
		$this->free_balance_quantity += $dif;
		$this->balance_quantity += $dif;
	}
	function getImportDate(): int {
		return $this->import_date;
	}
	function getDateCreate(): int {
		return $this->date_create;
	}
	protected function setImportQuantity(float $quantity): void {
		$this->import_quantity = $quantity;
	}
	function isDeleted(): bool {
		return $this->deleted;
	}
	protected function setIsDeleted(): void {
		$this->deleted = true;
	}
	function toArray(): array {
		return [
			'product_import_id' => $this->getProductImportId(),
			'import_id' => $this->getImportId(),
			'product_id' => $this->getProductId(),
			'import_date' => $this->getImportDate(),
			'import_quantity' => $this->getImportQuantity(),
			'purchase_price' => $this->getPurchasePrice(),
			'source' => $this->getSource(),
			'is_deficit' => $this->isDeficit(),
			'free_balance_quantity' => $this->getFreeBalanceQuantity(),
		];
	}
}