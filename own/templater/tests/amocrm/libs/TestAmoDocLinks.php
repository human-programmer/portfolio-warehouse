<?php


namespace Templater\Amocm\Tests;


use Files\iFile;

require_once __DIR__ . '/../TestFactory.php';
require_once __DIR__ . '/TestFile.php';
require_once __DIR__ . '/../../../amocrm/modules/AmoDocLinks.php';

class TestAmoDocLinks extends \Templater\Amocrm\AmoDocLinks {
	protected static function findFile(int $fileId): iFile|null {
		$file = parent::findFile($fileId);
		if(!$file) return null;
		return new TestFile($file->getModel());
	}
}