<?php


namespace Templater\Pragma;


use Files\File;
use Generals\CRMDB;
use Generals\Functions\Date;
use Templater\Factory;

require_once __DIR__ . '/../business_rules/IDocLinks.php';
require_once __DIR__ . '/LinkedFile.php';
require_once __DIR__ . '/../../../../lib/generals/functions/Date.php';

class DocLinks extends CRMDB implements IDocLinks {
	public function __construct(protected int $pragmaAccountId) {
		parent::__construct();
	}

	protected static function saveFileLink(int $mainFileId, IDocLinkToCreate $link): IDocLink {
		$docLink = self::createIDocLink($mainFileId, $link);
		self::saveDocLink($docLink);
		return $docLink;
	}

	protected static function createIDocLink(int $mainFileId, IDocLinkToCreate $link): IDocLink {
		$model = $link->toArray();
		$model['file_id'] = $mainFileId;
		return new DocLink($model);
	}

	static function saveDocLink(IDocLink $link): void {
		$schema = self::getTemplaterFileLinksSchema();
		$sql = "INSERT INTO $schema (file_id, template_id, entity_id, entity_type, date_update)
				VALUES(:file_id, :template_id, :entity_id, :entity_type, :date_update)
				ON DUPLICATE KEY UPDATE
					file_id = VALUES(file_id),
					template_id = VALUES(template_id),
					entity_id = VALUES(entity_id),
					entity_type = VALUES(entity_type),
					date_update = VALUES(date_update)";
		$model = [
			'file_id' => $link->getFileId(),
			'template_id' => $link->getTemplateFileId(),
			'entity_id' => $link->getEntityId(),
			'entity_type' => $link->getEntityType(),
			'date_update' => Date::getStringTimeStamp(),
		];
		self::executeSql($sql, $model);
	}

	function getLinksOfEntity(string $entity_type, int $entity_id): array {
		$rows = $this->getDocLinksOfEntity($this->pragmaAccountId, $entity_type, $entity_id);
		foreach ($rows as $row) {
			$link = new DocLink($row);
			$file = new File($row);
			$result[] = new LinkedFile($file, $link);
		}
		return $result ?? [];
	}

	private function getDocLinksOfEntity(int $accountId, string $entity_type, int $entity_id): array {
		$type = self::escape($entity_type);
		$condition = "account_id = $accountId AND entity_id = $entity_id AND entity_type = $type";
		$sql = self::getSql($condition);
		return self::querySql($sql);
	}

	static function getDocLinks(int $accountId): array {
		$condition = "account_id = $accountId";
		$sql = self::getSql($condition);
		Factory::getLogWriter()->add('SQL', $sql);
		return self::querySql($sql);
	}

	private static function getSql(string $condition): string {
		$links = self::getTemplaterFileLinksSchema();
		$files = self::getPragmaFilesSchema();
		return "SELECT
					$files.id as file_id,
					$files.id,
					$files.title,
					$files.extension,
					$files.path,
					$files.size,
					$files.group_name AS `group`,
       				$links.template_id,
       				$links.entity_id,
       				$links.entity_type,
       				$links.date_update
				FROM $files
					INNER JOIN $links ON $links.file_id = $files.id
				WHERE $condition";
	}
}