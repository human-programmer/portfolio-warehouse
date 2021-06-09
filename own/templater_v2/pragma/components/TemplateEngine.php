<?php


namespace TemplateEngine\Pragma;


use FilesSystem\Pragma\FileStruct;
use FilesSystem\Pragma\IFile;
use FilesSystem\Pragma\IFileStruct;
use Services\Amocrm\iAmoEntityParams;

require_once __DIR__ . '/../business_rules/ITemplateEngine.php';

abstract class TemplateEngine implements ITemplateEngine {
	abstract protected function createContent(IFile $file, iAmoEntityParams $params): mixed;

	function createFile(IDocLinkToCreate $link, iAmoEntityParams $params): IFile {
		$file = $this->getFile($link->getTemplateFileId());
		$content = $this->createContent($file, $params);
		$parent_id = Factory::getTemplateDirs()->getCardDirId($link->getEntityId(), $link->getEntityType());
		$struct = $this->createNewFileStruct($file, $parent_id);
		return Factory::getFiles()->createFromContent($struct, $content);
	}

	protected function getFile(int $file_id): IFile {
		return Factory::getFiles()->getFile($file_id);
	}

	private function createNewFileStruct(IFile $file, int $parent_id): IFileStruct {
		return new FileStruct([
			'extension' => $file->getExtension(),
			'title' => $file->getTitle(),
			'size' => $file->getSize(),
			'type' => $file->getType(),
			'parent_id' => $parent_id,
		]);
	}

	function createDir(string $title, int $parent_id): IFile {
		return Factory::getFiles()->createDir($title, $parent_id);
	}
}