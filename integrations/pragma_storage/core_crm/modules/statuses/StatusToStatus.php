<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/statuses/iStatusToStatus.php';


class StatusToStatus extends PragmaStoreDB implements iStatusToStatus {
	private int $pragma_account_id;
	private array $links;

	public function __construct(int $pragma_account_id) {
		parent::__construct();

		$this->pragma_account_id = $pragma_account_id;
	}

	function getExportStatus(int $pipeline_id, int $entity_status_id): iStatus {
		$status_id = $this->findExportStatusId($pipeline_id, $entity_status_id);

		if (!$status_id)
			throw new \Exception("Export status not found for pipeline_id: $pipeline_id, status_id: $entity_status_id");

		return self::getStatuses()->getStatus($status_id);
	}

	function setExportStatusLinks(array $links): bool {
		$flag = true;

		$this->emptyLinks();

		foreach ($links as $link)
			$flag = $flag && $this->insertLink($link['export_status_id'], $link['entity_status_id'], $link['pipeline_id']);

		return $flag;
	}

	private function emptyLinks(): bool {
		$status_links = parent::getStorageStatusToStatusSchema();

		$sql = "DELETE FROM $status_links WHERE $status_links.`account_id` = $this->pragma_account_id";

		return self::execute($sql);
	}

	private function insertLink(int $export_status_id, int $entity_status_id, int $pragma_pipeline_id): bool {
		$status_links = parent::getStorageStatusToStatusSchema();

		$sql = "INSERT INTO $status_links (`export_status_id`, `entity_status_id`, `pipeline_id`, `account_id`)
                VALUES ($export_status_id, $entity_status_id, $pragma_pipeline_id, $this->pragma_account_id)
                ON DUPLICATE KEY UPDATE `account_id` = $this->pragma_account_id";

		return self::execute($sql);
	}

	private function findExportStatusId(int $pipeline_id, int $entity_status_id) {
		foreach ($this->getLinks() as $link)
			if ($link['entity_status_id'] === $entity_status_id && $link['pipeline_id'] === $pipeline_id)
				return $link['export_status_id'];

		return null;
	}

	public function getLinks(): array {
		if (isset($this->links))
			return $this->links;

		$this->loadLinks();

		return $this->links;
	}

	private function loadLinks() {
		$status_links = parent::getStorageStatusToStatusSchema();

		$sql = "SELECT 
                    $status_links.`export_status_id`,
                    $status_links.`entity_status_id`,
                    $status_links.`pipeline_id`
                FROM $status_links
                WHERE $status_links.`account_id` = $this->pragma_account_id";

		$this->links = self::query($sql);
	}

	static private function getStatuses(): iStatuses {
		return PragmaFactory::getStatuses();
	}
}