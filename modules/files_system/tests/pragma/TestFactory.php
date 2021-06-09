<?php


namespace FilesSystem\Pragma\Tests;


use FilesSystem\Pragma\AccountVariables;
use FilesSystem\Pragma\IFiles;

require_once __DIR__ . '/../../pragma/Factory.php';
require_once __DIR__ . '/../../../../lib/services/tests/TestFactory.php';
require_once __DIR__ . '/data_sets/TestFiles.php';
require_once __DIR__ . '/data_sets/TestModules.php';
require_once __DIR__ . '/data_sets/TestAccounts.php';
require_once __DIR__ . '/data_sets/TestNodes.php';
require_once __DIR__ . '/files/TestFileSystem.php';
require_once __DIR__ . '/files/TestFilesStorage.php';

class TestFactory extends \FilesSystem\Pragma\Factory {

	static function testInit(): void {
		$logger = new \LogJSON('test_files', 'test_files', '');
		$node = TestNodes::uniqueNode();
		self::init($node, $logger);
		self::resetTestFiles();
		AccountVariables::setRootDorForTest("C:/Os/OSPanel/domains/smart-dev.core_crm/api/modules/files_system/tests/core_crm/data_sets/files");
	}

	static function clearDataSets(): void {
		TestNodes::clear();
		TestFiles::clear();
	}

	static function uniqueTestFile(): array {
		return TestFiles::uniqueFileRequest();
	}

	static function getFiles(): IFiles {
		if(isset(parent::$files)) return parent::$files;
		self::resetTestFiles();
		return parent::$files;
	}

	static function resetTestFiles(): void {
		parent::getFiles();
		$files = new TestFilesStorage(self::getNode());
		$system = new TestFileSystem(self::getNode()->getModule()->getPragmaModuleId(), self::getNode()->getAccount()->getPragmaAccountId());
		$files->setFileSystem($system);
		parent::$files = $files;
	}
}