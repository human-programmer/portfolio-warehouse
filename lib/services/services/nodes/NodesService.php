<?php


namespace Services;


use Services\General\iNode;


require_once __DIR__ . '/../../business_rules/general/node/iNodesService.php';
require_once __DIR__ . '/../Service.php';
require_once __DIR__ . '/entity/Node.php';

require_once __DIR__ . '/PragmaNodes.php';
require_once __DIR__ . '/AmocrmNodes.php';
require_once __DIR__ . '/Bitrix24Nodes.php';

class NodesService extends Service implements General\iNodesService {
	use PragmaNodes, AmocrmNodes, Bitrix24Nodes;
	private static self $inst;

	static function getSelf(): NodesService {
		if(isset(self::$inst))
			return self::$inst;
		self::$inst = new self();
		return self::$inst;
	}

	private static function createStructs(array $models): array {
		foreach ($models as $model)
			$result[] = self::createStruct($model);
		return $result ?? [];
	}

	private static function createStruct(array $model): iNode {
		return new Node($model);
	}

	function getNodesOfAccount(int $pragma_account_id): array {
		return [];
	}
}