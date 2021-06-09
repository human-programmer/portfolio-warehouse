<?php


namespace FilesSystem\Pragma;


use Configs\Configs;
use const FilesSystem\FAILED_TO_SAVE_FILE;

class FileSystem {
	function __construct(
		private int $module_id,
		private int $account_id) {
	}

	function saveContent(IFile $file, mixed $content): void {
		self::checkAndCreateDir($file->getSystemPath());
		self::saveFileContent($file, $content);
	}

	private static function saveFileContent(IFile $file, mixed $content): void {
		if(!static::saveFContent($file, $content))
			throw new \Exception("Failed to save file", FAILED_TO_SAVE_FILE);
	}

	private static function saveFContent(IFile $file, mixed $content): bool {
		return file_put_contents($file->getFullUniqueName(), $content);
	}

	function save (IFile $file, string $tmp_name): void {
		self::checkAndCreateDir($file->getSystemPath());
		self::saveLoadedFile($file->getFullUniqueName(), $tmp_name);
	}

	private static function saveLoadedFile(string $new_name, string $tmp_name): void {
		if(!static::moveFile($new_name, $tmp_name))
			throw new \Exception("Failed to save file", FAILED_TO_SAVE_FILE);
	}

	function moveFileToSave(IFile $file, string $tmp_name): bool {
		self::checkAndCreateDir($file->getSystemPath());
		return static::moveFile($file->getFullUniqueName(), $tmp_name);
	}

	static function moveFile(string $new_name, string $tmp_name): bool {
		return move_uploaded_file($tmp_name, $new_name);
	}

	function delete (IFile $file): void {
		unlink($file->getFullUniqueName());
	}

	function getPath(string $next_dirs = ''): string {
		return self::getFilesDir() . "/$this->module_id/$this->account_id/$next_dirs";
	}

	static function getFilesDir(): string {
		return Configs::rootCatalog() . self::getDirName();
	}

	private static function getDirName(): string {
		return Configs::isDev() ? 'pragma_files_dev' : 'pragma_files';
	}

	static function checkAndCreateDir(string $path): void {
		self::issetDir($path) || self::createDir($path);
	}

	static private function issetDir(string $path): bool {
		return is_dir($path);
	}

	static protected function createDir(string $path) {
		return mkdir($path, 0777, true);
	}
}