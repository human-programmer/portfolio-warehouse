<?php


namespace TemplateEngine\Amocrm\Tests;


use Configs\Configs;
use FilesSystem\Pragma\File;
use FilesSystem\Pragma\IFile;
use TemplateEngine\Pragma\Tests\TestFactory;

class TestFile extends File {

	static function createFromFile(IFile $file): self {
		return new self([
			'id' => $file->getFileId(),
			'extension' => $file->getExtension(),
			'title' => $file->getTitle(),
			'size' => $file->getSize(),
			'type' => $file->getType(),
			'parent_id' => $file->getParentId(),
			'date_create' => '2021-05-28 17:52:16',
			'token' => $file->getToken(),
			'account_id' => TestFactory::getNode()->getAccount()->getPragmaAccountId(),
			'module_id' => TestFactory::getNode()->getModule()->getPragmaModuleId(),
		]);
	}

	protected static function getStartDomain(): string {
		return 'http://smart-dev.core_crm';
	}

	function getExternalLink(): string {
		$path = parent::getExternalLink();
		return str_replace('https://smart-dev.core_crm.by', 'http://smart-dev.core_crm', $path);
	}
}