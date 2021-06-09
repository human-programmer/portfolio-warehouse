<?php


namespace TemplateEngine\Pragma;


use Generals\CRMDB;
use Services\General\iNode;
use const FilesSystem\TYPE_FILE_IS_DIR;


require_once __DIR__ . '/../business_rules/ITemplateDirs.php';

class TemplateDirs extends CRMDB implements ITemplateDirs {
	function __construct(private iNode $node) {
		parent::__construct();
	}

	function getTemplatesDirId(): int {
		$name = $this->getTemplatesDirname();
		return $this->getOrCreateDir($name);
	}

	function getCardDirId(int $entity_id, string $entity_type): int {
		$dir_name = "$entity_id.$entity_type";
		return $this->getOrCreateDir($dir_name);
	}

	private function getOrCreateDir(string $dir_name): int {
		return $this->findDirId($dir_name) ?? $this->createDir($dir_name);
	}

	private function findDirId(string $dir_name): int|null {
		$dir_name = self::escape($dir_name);
		$dirs = self::getTemplaterDirsSchema();
		$files = self::getPragmaIndexedFilesSchema();
		$account_id = $this->node->getAccount()->getPragmaAccountId();
		$module_id = $this->node->getModule()->getPragmaModuleId();
		$type = TYPE_FILE_IS_DIR;
		$sql = "SELECT dir_id 
				FROM $dirs 
				WHERE dir_id IN (SELECT id 
								FROM $files 
								WHERE account_id = $account_id AND module_id = $module_id AND type = $type)
				  AND `name` = $dir_name";
		return self::querySql($sql)[0]['dir_id'] ?? null;
	}

	private function createDir(string $dir_name): int {
		$dir = Factory::getFiles()->createDir($dir_name);
		$id = $dir->getFileId();
		self::linkDir($id, $dir_name);
		return $id;
	}

	private function linkDir(int $id, string $dir_name): void {
		$dirs = self::getTemplaterDirsSchema();
		$dir_name = self::escape($dir_name);
		$sql = "INSERT INTO $dirs (`dir_id`, `name`) VALUES($id, $dir_name)";
		self::executeSql($sql);
	}

	private function getTemplatesDirname(): string {
		return $this->node->getAccount()->getPragmaAccountId() . '.templates';
	}
}