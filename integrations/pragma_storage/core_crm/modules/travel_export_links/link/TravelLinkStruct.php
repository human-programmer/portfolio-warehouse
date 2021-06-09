<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../../business_rules/travel_export_link/ITravelLinkStruct.php';


class TravelLinkStruct implements \PragmaStorage\ITravelLinkStruct {

    private int $travel_id;
    private int $product_id;
    private int|null $product_import_id;
    private int|null $export_id;
    private float $quantity;

	function __construct(array $model){
	    $this->travel_id = $model['travel_id'];
	    $this->product_id = $model['product_id'];
	    $this->product_import_id = $model['receive_product_import_id'] ?? null;
	    $this->export_id = $model['send_export_id'] ?? null;
	    $this->quantity = $model['quantity'];
    }

	function getTravelId(): int {
		return $this->travel_id;
	}

    function getProductId(): int {
        return $this->product_id;
    }

	function getStartExportId(): int|null {
		return $this->export_id;
	}

	function getReceiveProductImportId(): int|null {
		return $this->product_import_id;
	}

    function setStartExportId(int $export_id): void {
        $this->validExportId($export_id);
        $this->export_id = $export_id;
    }

    private function validExportId(int $export_id): void {
        if($this->export_id === $export_id) return;

        if(!is_null($this->export_id))
            throw new \Exception("Travel link already exists");

        if($this->product_id !== $this->getProductIdOfExportId($export_id))
            throw new \Exception("Invalid export_id '$export_id' to link product '$this->product_id'");
    }

    private function getProductIdOfExportId(int $export_id): int{
	    return PragmaFactory::getExports()->getExport($export_id)->getProductId();
    }

    function setReceiveProductImportId(int $product_import_id): void {
        $this->validProductImportId($product_import_id);
        $this->product_import_id = $product_import_id;
    }

    private function validProductImportId(int $product_import_id): void {
        if($this->product_import_id === $product_import_id) return;

        if(!is_null($this->product_import_id))
            throw new \Exception("Travel link already exists");

        if($this->product_id !== $this->getProductIdOfProductImportId($product_import_id))
            throw new \Exception("Invalid product_import_id '$product_import_id' to link product '$this->product_id'");

    }

    private function getProductIdOfProductImportId(int $product_import_id): int{
        return PragmaFactory::getProductImports()->getProductImport($product_import_id)->getProductId();
    }

    function getQuantity(): float {
        return $this->quantity;
    }

    function setQuantity(float $quantity): void {
        $this->quantity = $quantity;
    }

    function toArray(): array {
        return [
            'travel_id' => $this->getTravelId(),
            'product_id' => $this->getProductId(),
            'receive_product_import_id' => $this->getReceiveProductImportId(),
            'send_export_id' => $this->getStartExportId(),
            'quantity' => $this->getQuantity(),
        ];
    }
}