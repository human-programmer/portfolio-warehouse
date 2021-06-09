<?php


namespace FilesSystem\Pragma;


trait FilesRemover {

	private function deleteAllById(int $id): void {
		$file = $this->getFile($id);
		$this->removeDorContent($file);
		$this->singleRemove($file);
	}

	private function removeDorContent(IFile $file): void {
		if(!$file->isDir()) return;
		$contentFiles = $this->getDirContent($file->getFileId());
		foreach ($contentFiles as $file)
			$this->singleRemove($file);
	}

	private function singleRemove(IFile $file): void {
		$this->schema->deleteFile($file->getFileId());
		$file->isDir() || $this->system->delete($file);
	}
}