<?php


namespace FilesSystem\Pragma\Tests;


use FilesSystem\Pragma\FileStruct;
use FilesSystem\Pragma\IFile;
use FilesSystem\Pragma\IFileStruct;
use const FilesSystem\TYPE_FILE_IS_FILE;

require_once __DIR__ . '/../TestFactory.php';


trait FileTests {

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearDataSets();
	}

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::testInit();
	}

	protected function setUp(): void {
		parent::setUp();
		TestFiles::clear();
	}

	function checkDir(IFile $file, array $expected_files): void {
		$this->assertTrue($file->isDir());
		$files = TestFactory::getFiles()->getDirContent($file->getFileId());
		$this->assertCount(count($expected_files), $files);
		foreach ($files as $file)
			$this->compareFiles($expected_files[$file->getFileId()], $file);
	}

	private function checkDeletedActualFile(IFile $file): void {
		$this->assertFalse(is_file($file->getFullUniqueName()));
		$this->expectException(\Exception::class);
		TestFactory::getFiles()->getFile($file->getFileId());
	}

	private function checkIssetActualFile(IFile $file): void {
		$content = file_get_contents($file->getFullUniqueName());
		$this->assertEquals('test_content', $content);
		$actual_file = TestFactory::getFiles()->getFile($file->getFileId());
		$this->compareFiles($file, $actual_file);
	}

	private function compareFiles(IFile $expectFile, IFile $actualFile): void {
		$this->compareStructs($expectFile, $actualFile);
		$this->assertEquals($expectFile->getUniqueName(), $actualFile->getUniqueName());
		$this->assertEquals($expectFile->getExternalLink(), $actualFile->getExternalLink());
		$this->assertEquals($expectFile->getFullUniqueName(), $actualFile->getFullUniqueName());
		$this->assertEquals($expectFile->getSystemPath(), $actualFile->getSystemPath());
		$this->assertEquals($expectFile->getContent(), $actualFile->getContent());
		$this->assertEquals($expectFile->getExternalModel(), $actualFile->getExternalModel());
		$this->assertEquals($expectFile->getToken(), $actualFile->getToken());
		$this->assertEquals($expectFile->getAccountId(), $actualFile->getAccountId());
		$this->assertEquals($expectFile->getModuleId(), $actualFile->getModuleId());
	}

	private function compareStructs(IFileStruct $expectFile, IFileStruct $actualFile): void {
		$this->assertEquals($expectFile->getExtension(), $actualFile->getExtension());
		$this->assertEquals($expectFile->getTitle(), $actualFile->getTitle());
		$this->assertEquals($expectFile->getName(), $actualFile->getName());
		$this->assertEquals($expectFile->getSize(), $actualFile->getSize());
		$this->assertEquals($expectFile->getType(), $actualFile->getType());
		$this->assertEquals($expectFile->getParentId(), $actualFile->getParentId());
		$this->assertEquals($expectFile->isDir(), $actualFile->isDir());
	}

	static function uniqueFile(int $parent_id = null, int $type = TYPE_FILE_IS_FILE): IFile {
		$files = TestFactory::getFiles();
		$model = TestFiles::uniqueFileRequest(null, $type, $parent_id);
		$file_to_save = FileStruct::createFromRequest($model);
		return $files->createFromRequest($file_to_save, $model['tmp_name']);
	}
}