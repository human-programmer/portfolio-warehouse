<?php


namespace FilesSystem\Pragma;


use Generals\CRMDB;

class FilesSchema extends CRMDB {
	public function __construct(private int $module_id, private int $account_id) {
		parent::__construct();
	}

	static function getFileModel (int $file_id): array {
		$sql = self::sql("id = $file_id");
		$model = self::querySql($sql)[0] ?? null;
		if(!$model) throw new \Exception("File not found: '$file_id'");
		return $model;
	}

	function getDirContent(int $dir_id): array {
		$relations = self::getPragmaRelationContainsSchema();
		$condition = "id IN (SELECT child_id FROM $relations WHERE parent_id = $dir_id)";
		$sql = $this->sql($condition);
		return self::querySql($sql);
	}

	function deleteFile(int $file_id): void {
		$files = self::getPragmaIndexedFilesSchema();
		$sql = "DELETE FROM $files WHERE account_id = $this->account_id AND module_id = $this->module_id AND id = $file_id";
		self::executeSql($sql);
	}

	private static function sql(string $condition): string {
		$files = self::getPragmaIndexedFilesSchema();
		return "SELECT
					id,
					parent_id,
       				account_id,
       				module_id,
       				type,
       				title,
       				extension,
       				size,
       				date_create,
       				token
				FROM $files 
				WHERE $condition";
	}

	static function getParentId(int $file_id): int|null {
		$files = self::getPragmaIndexedFilesSchema();
		$sql = "SELECT parent_id FROM $files WHERE id = $file_id";
		return self::querySql($sql)[0]['parent_id'] ?? null;
	}

	function saveFile(IFileStruct $file): int {
		$files = self::getPragmaIndexedFilesSchema();
		$sql = "INSERT INTO $files (account_id, module_id, parent_id, type, title, extension, size, token)
				VALUES(:account_id, :module_id, :parent_id, :type, :title, :extension, :size, :token)";
		$model = $this->assembleFileModel($file);
		self::executeSql($sql, $model);
		return self::last_id();
	}

	function assembleFileModel(IFileStruct $file): array {
		return [
			'account_id' => $this->account_id,
			'module_id' => $this->module_id,
			'parent_id' => $file->getParentId(),
			'type' => $file->getType(),
			'title' => $file->getTitle(),
			'extension' => $file->getExtension(),
			'size' => $file->getSize(),
			'token' => self::generateToken(),
		];
	}

	static function generateToken($length = 32) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}
}