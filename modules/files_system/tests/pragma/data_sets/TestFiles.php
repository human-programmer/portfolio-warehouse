<?php


namespace FilesSystem\Pragma\Tests;


use FilesSystem\Pragma\FileStruct;
use FilesSystem\Pragma\IFileStruct;
use const FilesSystem\TYPE_FILE_IS_FILE;

class TestFiles {
	static function uniqueFileRequestStruct(string $name = 'test_file_name.txt', int $type = TYPE_FILE_IS_FILE, int $parent_id = null): IFileStruct {
		$model = self::uniqueFileRequest($name, $type, $parent_id);
		return FileStruct::createFromRequest($model);
	}
	static function uniqueFileRequest(string $name = null, int $type = TYPE_FILE_IS_FILE, int $parent_id = null): array {
		$name = $name ?? 'test_file_name.txt';
		return [
			'name' => $name,
			'tmp_name' => self::uniqueTestFile(),
			'error' => 0,
			'group' => '123123',
			'type' => $type,
			'parent_id' => $parent_id,
			'size' => 1000,
		];
	}

	private static function uniqueTestFile(): string {
		$dir = __DIR__ . "/files";
		try {
			mkdir($dir, 0777, true);
		} catch (\Exception $exception) {}
		$name = uniqid('test_file');
		$file_name = "$dir/$name";
		$content = 'test_content';
		$res = file_put_contents($file_name, $content);
		return $file_name;
	}

	static function clear(): void {
		$files = glob(__DIR__ . '/data_sets');
		foreach($files as $file)
			if(is_file($file))
				unlink($file);
	}
}