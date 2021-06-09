<?php


namespace PragmaStorage;


class EntityPipelines extends PragmaStoreDB {
	private int $pragma_account_id;
	private array $buffer;

	public function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	public function getPipelines(): array {
		if (isset($this->buffer))
			return $this->buffer;

		$this->loadPipelines();

		return $this->buffer;
	}

	private function loadPipelines() {
		$pipelines = self::getAmocrmPipelinesSchema();
		$statuses = self::getAmocrmStatusesSchema();
		$pragma_pipelines = self::getPipelinesSchema();
		$pragma_statuses = self::getStatusesSchema();
		$statuses_to_pipeline = self::getStatusesToPipelineSchema();

		$sql = "SELECT 
                    $pipelines.`amocrm_id` AS `pipeline_id`,
                    $pragma_pipelines.`id` AS `pragma_pipeline_id`,
                    $pragma_pipelines.`name` AS `pipeline_title`,
                    $pragma_pipelines.`sort` AS `pipeline_sort`,
       
                    $statuses.`amocrm_id` AS `status_id`,
                    $pragma_statuses.`id` AS `pragma_status_id`,
                    $pragma_statuses.`name` AS `status_title`,
                    $pragma_statuses.`color` AS `status_color`,
                    $pragma_statuses.`sort` AS `status_sort`
                FROM $pragma_pipelines
                    INNER JOIN $statuses_to_pipeline ON $statuses_to_pipeline.`pipeline_id` = $pragma_pipelines.`id`
                    INNER JOIN $pragma_statuses ON $pragma_statuses.`id` = $statuses_to_pipeline.`status_id`
                    INNER JOIN $statuses ON $statuses.`pragma_id` = $pragma_statuses.`id`
                    INNER JOIN $pipelines ON $pipelines.`pragma_id` = $pragma_pipelines.`id`
                WHERE $pragma_pipelines.`account_id` = $this->pragma_account_id";

		$statuses = self::query($sql);

		$this->buffer = self::press($statuses);
	}

	private static function press(array $statuses): array {
		foreach ($statuses as $status) {
			$pid = $status['pragma_pipeline_id'];

			$pipelines[$pid]['id'] = $status['pipeline_id'];
			$pipelines[$pid]['pragma_id'] = $status['pragma_pipeline_id'];
			$pipelines[$pid]['name'] = $status['pipeline_title'];
			$pipelines[$pid]['sort'] = $status['pipeline_sort'];

			$sid = $status['pragma_status_id'];

			$pipelines[$pid]['statuses'][$sid]['id'] = $status['status_id'];
			$pipelines[$pid]['statuses'][$sid]['pragma_id'] = $status['pragma_status_id'];
			$pipelines[$pid]['statuses'][$sid]['name'] = $status['status_title'];
			$pipelines[$pid]['statuses'][$sid]['color'] = $status['status_color'];
			$pipelines[$pid]['statuses'][$sid]['sort'] = $status['status_sort'];
		}

		foreach ($pipelines ?? [] as $key => $pipeline)
			$pipelines[$key]['statuses'] = array_values($pipeline['statuses']);

		return array_values($pipelines ?? []);
	}
}