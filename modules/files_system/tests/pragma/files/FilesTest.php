<?php


namespace FilesSystem\Pragma\Tests;

use \FilesSystem\Pragma\IFile;
use FilesSystem\Pragma\FileStruct;
use const FilesSystem\TYPE_FILE_IS_DIR;

require_once __DIR__ . '/FileTests.php';

class FilesTest extends \PHPUnit\Framework\TestCase {
	use FileTests;

	function testCreateFromRequest(){
		$files = TestFactory::getFiles();
		$model = TestFiles::uniqueFileRequest();
		$file_to_save = FileStruct::createFromRequest($model);
		$file = $files->createFromRequest($file_to_save, $model['tmp_name']);
		$this->assertInstanceOf(IFile::class, $file);
		$this->compareStructs($file_to_save, $file);
		$this->checkIssetActualFile($file);
	}

	function testGetFile(){
		$file1 = self::uniqueFile();
		$file2 = self::uniqueFile();
		$files = TestFactory::getFiles();
		$actual_file1 = $files->getFile($file1->getFileId());
		$actual_file12 = $files->getFile($file2->getFileId());
		$this->compareFiles($file1, $actual_file1);
		$this->compareFiles($file2, $actual_file12);
	}

	function testDelete(){
		$file1 = self::uniqueFile();
		$file2 = self::uniqueFile();
		TestFactory::getFiles()->delete($file1->getFileId());
		$this->checkDeletedActualFile($file1);
	}

	function testGetFiles(){
		$dir = self::uniqueFile(null, TYPE_FILE_IS_DIR);
		$dir1 = self::uniqueFile($dir->getFileId(), TYPE_FILE_IS_DIR);
		$dir1_file1 = self::uniqueFile($dir1->getFileId());
		$dir1_file2 = self::uniqueFile($dir1->getFileId());
		$dir_file1 = self::uniqueFile($dir->getFileId());
		$dir_file2 = self::uniqueFile($dir->getFileId());

		$expectedContents = [
			$dir1_file1->getFileId() => $dir1_file1,
			$dir1_file2->getFileId() => $dir1_file2,
		];
		$this->checkDir($dir1, $expectedContents);

		$expectedContents[$dir1->getFileId()] = $dir1;
		$expectedContents[$dir_file1->getFileId()] = $dir_file1;
		$expectedContents[$dir_file2->getFileId()] = $dir_file2;

		$this->checkDir($dir, $expectedContents);
	}

	function testCreateFromContent(): void {
		$files = TestFactory::getFiles();
		$model = TestFiles::uniqueFileRequest();
		$file_to_save = FileStruct::createFromRequest($model);
		$file = $files->createFromContent($file_to_save, 'test_content');
		$this->assertInstanceOf(IFile::class, $file);
		$this->compareStructs($file_to_save, $file);
		$this->checkIssetActualFile($file);
	}
}