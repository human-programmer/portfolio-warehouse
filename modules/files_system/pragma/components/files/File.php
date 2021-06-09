<?php


namespace FilesSystem\Pragma;


use Generals\Functions\Date;
use const FilesSystem\DAY_DIR_FORMAT;

require_once __DIR__ . '/../../business_rules/files/IFile.php';
require_once __DIR__ . '/FileStruct.php';

class File extends FileStruct implements IFile {

	private string $token;
	private int $account_id;
	private int $module_id;
	private int $date_create;

	public function __construct(array $model) {
		parent::__construct($model);
		$this->token = $model['token'] ?? throw new \Exception("Token is missing");
		$this->account_id = $model['account_id'];
		$this->module_id = $model['module_id'];
		$this->date_create = Date::getIntTimeStamp($model['date_create']);
	}

	function getUniqueName(): string {
		return $this->getFileId() . '.' . $this->getExtension();
	}

	function getExternalLink(): string {
		return AccountVariables::getDefaultExternalPath() . '?' . $this->getParams();
	}

	function getParams(): string {
		return 'file=' . $this->getFileId() . "&token=" . $this->getToken();
	}

	function getFullUniqueName(): string {
		return $this->getSystemPath() . '/' . $this->getUniqueName();
	}

	function getSystemPath(): string {
		return AccountVariables::rootDirByFile($this) . '/' . $this->getDayDir();
	}

	function getDayDir(): string {
		return date(DAY_DIR_FORMAT, $this->date_create);
	}

	function getContent(): mixed {
		return file_get_contents($this->getFullUniqueName());
	}

	function getExternalModel(): array {
		return [
			'id' => $this->getFileId(),
			'parent_id' => $this->getParentId(),
			'type' => $this->getType(),
			'extension' => $this->getExtension(),
			'title' => $this->getTitle(),
			'size' => $this->getSize(),
			'unique_name' => $this->getUniqueName(),
			'link' => $this->getExternalLink(),
			'token' => $this->getToken(),
		];
	}

	function getToken(): string {
		return $this->token;
	}

	function getAccountId(): int {
		return $this->account_id;
	}

	function getModuleId(): int {
		return $this->module_id;
	}

	function getAlias(): string {
		$title = str_replace(',', '', $this->getTitle());
		$title = str_replace('.', '', $title);
		return "$title." . $this->getExtension();
	}
}