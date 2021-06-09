<?php


namespace FilesSystem\Pragma;


require_once __DIR__ . '/../finding_path/FindingPath.php';

trait FilesSaver {

	private function saveFile(IFile $file, mixed $content): void {
		$this->system->saveContent($file, $content);
	}

	private function moveFile(IFile $file, string $tmp_name): void {
		if(!$this->system->moveFileToSave($file, $tmp_name))
			throw new \Exception("Failed to move file");
	}

	private function indexFile(IFileStruct $file): IFile {
		$file_id = $this->schema->saveFile($file);
		$model = $this->schema->getFileModel($file_id);
		$file = $this->createInstance($model);
		FindingPath::updateParents($file);
		return $file;
	}
}