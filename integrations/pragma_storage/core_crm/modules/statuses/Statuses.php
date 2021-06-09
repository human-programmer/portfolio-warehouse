<?php


namespace PragmaStorage;


require_once __DIR__ . '/StatusesSchema.php';
require_once __DIR__ . '/Status.php';
require_once __DIR__ . '/../../business_rules/statuses/iStatuses.php';


class Statuses extends StatusesSchema implements iStatuses {
	private array $statuses;

	public function __construct(int $pragma_account_id) {
		parent::__construct($pragma_account_id);
	}

	public function getStatus(int $status_id): iStatus {
		foreach ($this->getStatuses() as $status)
			if ($status->getStatusId() === $status_id)
				return $status;

		throw new \Exception('Status not found: ' . $status_id);
	}

	function getStatusByCode(string $code): iStatus {
		foreach ($this->getStatuses() as $status)
			if ($status->getStatusCode() === $code)
				return $status;

		throw new \Exception('Status not found: ' . $code);
	}

	public function getStatuses(): array {
		if (isset($this->statuses))
			return $this->statuses;

		$this->loadStatuses();

		return $this->statuses;
	}

	private function loadStatuses() {
		$models = self::getStatusModels();

		foreach ($models as $model)
			$statuses[] = new Status($model['id'], $model['code'], $model['title'],);

		$this->statuses = $statuses ?? [];
	}
}