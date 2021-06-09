<?php


namespace FilesSystem\Pragma;

use const FilesSystem\TYPE_FILE_IS_DIR;
use const FilesSystem\TYPE_FILE_IS_FILE;

require_once __DIR__ . '/../../business_rules/files/IFileStruct.php';

class FileStruct implements IFileStruct {
	private string $extension;
	private string $title;
	private int $file_id;
	private int $size;
	private int $type;
	private int|null $parent_id;

	static function createFromRequest(array $file_params): IFileStruct {
		$name = self::splitName($file_params['name']);
		$model['extension'] = $name['extension'];
		$model['title'] = $name['title'];
		$model['size'] = $file_params['size'];
		$model['type'] = $file_params['type'] ?? TYPE_FILE_IS_FILE;
		$model['parent_id'] = $file_params['parent_id'] ?? null;
		return new self($model);
	}

	static function createNewDir(string $title, int $parent_id = null): IFileStruct {
		$model['extension'] = '';
		$model['title'] = $title;
		$model['size'] = 0;
		$model['type'] = TYPE_FILE_IS_DIR;
		$model['parent_id'] = $parent_id;
		return new self($model);
	}

	function __construct(array $model) {
		$this->file_id = $model['id'] ?? 0;
		$this->extension = $model['extension'];
		$this->title = $model['title'];
		$this->size = $model['size'];
		$this->type = $model['type'];
		$this->parent_id = $model['parent_id'] ?? null;
	}

	protected function setFileId(int $id): void {
		$this->file_id = $id;
	}

	function getFileId(): int {
		return $this->file_id;
	}

	function getExtension(): string {
		return $this->extension;
	}

	function getTitle(): string {
		return $this->title;
	}

	function getName(): string {
		return "$this->title.$this->extension";
	}

	function getSize(): int {
		return $this->size;
	}

	function getType(): int {
		return $this->type;
	}

	function getParentId(): int|null {
		return $this->parent_id;
	}

	function isDir(): bool {
		return $this->getType() === TYPE_FILE_IS_DIR;
	}

	private static function splitName(string $name): array {
		$arr = explode('.', $name);
		$extension = $arr[count($arr) - 1];
		unset($arr[count($arr) - 1]);		return ['extension' => $extension, 'title' => implode('.', $arr)];
	}
}