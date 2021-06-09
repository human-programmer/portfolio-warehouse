<?php


namespace FilesSystem\Pragma;


use Services\General\iNode;
use const FilesSystem\FILE_NOT_FOUND;

require_once __DIR__ . '/../../business_rules/files/IFiles.php';
require_once __DIR__ . '/FileStruct.php';
require_once __DIR__ . '/File.php';
require_once __DIR__ . '/files/FilesSchema.php';
require_once __DIR__ . '/files/FileSystem.php';
require_once __DIR__ . '/files/FilesSaver.php';
require_once __DIR__ . '/files/FilesRemover.php';

class Files implements IFiles {
	use FilesSaver, FilesRemover;

	protected FilesSchema $schema;
	protected FileSystem $system;

	public function __construct(private iNode $node) {
		$this->schema = new FilesSchema($node->getModule()->getPragmaModuleId(), $node->getAccount()->getPragmaAccountId());
		$this->system = new FileSystem($node->getModule()->getPragmaModuleId(), $node->getAccount()->getPragmaAccountId());
	}

	function createFromRequest(IFileStruct $file, string $file_location): IFile {
		$file = $this->indexFile($file);
		$this->moveFile($file, $file_location);
		return $file;
	}

	function createFromContent(IFileStruct $file, mixed $content): IFile {
		$file = $this->indexFile($file);
		$this->saveFile($file, $content);
		return $file;
	}

	function delete(int|array $id): void {
		$id = is_array($id) ? $id : [$id];
		foreach ($id as $i)
			$this->singleDelete($i);
	}

	private function singleDelete(int $id): void {
		$this->deleteAllById($id);
	}

	static function sendFile(int $file_id, string $token): void {
		$file = self::getTargetFile($file_id);
		if($file->getToken() !== $token)
			throw new \Exception("Invalid token");
		self::sendFileAs($file->getFullUniqueName(), $file->getAlias());
	}

	static function getTargetFile(int $file_id): IFile {
		$model = FilesSchema::getFileModel($file_id);
		return new File($model);
	}

	private static function sendFileAs (string $file_name, string $alias): void {
		if(!file_exists($file_name))
			throw new \Exception("File not exists", FILE_NOT_FOUND);

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		header('Content-Type: ' . finfo_file($finfo, $file_name));
		finfo_close($finfo);
		header('Content-Disposition: attachment; filename='. $alias);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_name));

		ob_clean();
		flush();
		readfile($file_name);
	}

	function getFile(int $file_id): IFile {
		$file_model = $this->schema->getFileModel($file_id);
		return $this->createInstance($file_model);
	}

	function createInstance(array $model): IFile {
		return new File($model);
	}

	function getDirContent(int $dir_id): array {
		$models = $this->schema->getDirContent($dir_id);
		foreach ($models as $model)
			$result[] = $this->createInstance($model);
		return $result ?? [];
	}

	function getDirContentModels(int $dir_id): array {
		$files = $this->getDirContent($dir_id);
		foreach ($files as $file)
			$result[] = $file->getExternalModel();
		return $result ?? [];
	}

	function createDir(string $title, int $parent_id = null): IFile {
		$struct = FileStruct::createNewDir($title, $parent_id);
		return $this->indexFile($struct);
	}
}