<?php


namespace PragmaStorage;



require_once __DIR__ . '/../../../business_rules/exports/IExportModel.php';
require_once __DIR__ . '/../../priorities/PrioritiesIterator.php';

class ExportStruct implements IExportModel {
	private int $export_id;
	private int|null $entity_id;
	private int $product_id;
	private float $quantity;
	private float $selling_price;
	private int $status_id;
	private int $client_type;
	private array|null $store_priorities;

	function __construct(array $model) {
		$this->entity_id = $model['pragma_entity_id'] ?? null;
		$this->product_id = $model['product_id'];
		$this->quantity = round($model['quantity'] ?? 0, 3);
		$this->selling_price = round($model['selling_price'] ?? 0, 3);
		$this->status_id = $model['status_id'];
		$this->export_id = $model['id'] ?? 0;
		$this->client_type = $model['client_type'];
		$this->store_priorities = self::fetchPriorities($model);
	}

	function getExportId(): int {
		return $this->export_id;
	}

	function getEntityId(): int|null {
		return $this->entity_id;
	}

	function getProductId(): int {
		return $this->product_id;
	}

	function getQuantity(): float {
		return $this->quantity;
	}

	function setQuantity(float $quantity) {
		$this->quantity = round($quantity, 3);
	}

	function getSellingPrice(): float {
		return $this->selling_price;
	}

	function setSellingPrice(float $price): void {
		$this->selling_price = round($price, 3);
	}

	function getStatusId(): int {
		return $this->status_id;
	}

	function setStatus(int $status_id): bool {
		$this->status_id = $status_id;
		return true;
	}

    function getAvailablePriorities(): array {
        $priorities = $this->getPriorities();
        $available = $this->getAvailableStoresId();
        if(!$available || !count($available)) return $priorities;
        $result = self::filterStores($priorities, $available);
        return count($result) ? $result : throw new \Exception("The filter returned an empty result");
    }

    private static function filterStores(array $stores, array $store_id): array {
        foreach($stores as $store)
            if(array_search($store->getStoreId(), $store_id) !== false)
                $result[] = $store;
        return $result ?? [];
    }

	function getPriorities(): array {
        if(isset($this->store_priorities) && is_array($this->store_priorities))
            return $this->store_priorities;
        $this->loadStorePriorities();
        return $this->store_priorities;
	}

	private function loadStorePriorities(): void {
		$id = $this->getExportId();
		$this->store_priorities = PragmaFactory::getStoreApp()->getStorePriorities()->getPriorities([$id])[$id];
	}

	function setPriorities(array $priorities): void {
		$this->store_priorities = $priorities;
	}

	function toArray(): array {
		return [
				'id' => $this->getExportId(),
				'pragma_entity_id' => $this->getEntityId(),
				'product_id' => $this->getProduct()->getProductId(),
				'quantity' => $this->getQuantity(),
				'selling_price' => $this->getSellingPrice(),
				'status_id' => $this->getStatusId(),
				'store_priorities' => $this->getPriorities(),
			];
	}

	function getClientType(): int {
		return $this->client_type;
	}

	static function fetchPriorities(array $model): array|null {
		if(!isset($model['store_priorities'])) return null;
		$priorities = is_array($model['store_priorities']) ? $model['store_priorities'] : [];
		foreach ($priorities as $priority)
			$res[] = is_array($priority) ? StorePriorities::createPriorityInst($priority) : $priority;
		return $res ?? [];
	}

	function getHighestPriority(): IStoreExportPriority {
		return $this->getPriorities()[0];
	}

    function getAvailableStoresId(): array|null {
	    if($this->isTravelsType()) return [$this->getTravelsStartStoreId()];
	    return null;
    }

    private function isTravelsType(): bool {
        return $this->getClientType() === STORE_SOURCE;
    }

    private function getTravelsStartStoreId(): int {
	    return $this->getTravel()->getStartStoreId();
    }

    private function getTravel(): ITravel {
        $link = PragmaFactory::getStoreApp()->getTravelLinks()->getExportsTravelLink($this->getExportId());
        return PragmaFactory::getStoreApp()->getTravels()->getTravel($link->getTravelId());
    }
}