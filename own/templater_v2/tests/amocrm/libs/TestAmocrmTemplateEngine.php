<?php


namespace TemplateEngine\Amocrm\Tests;


use FilesSystem\Pragma\IFile;
use TemplateEngine\Amocrm\AmocrmTemplateEngine;

require_once __DIR__ . '/../TestFactory.php';
require_once __DIR__ . '/TestFile.php';
require_once __DIR__ . '/../../../amocrm/components/AmocrmTemplateEngine.php';

class TestAmocrmTemplateEngine extends AmocrmTemplateEngine {
	protected function getFile(int $file_id): IFile {
		$file = parent::getFile($file_id);
		return TestFile::createFromFile($file);
	}
}