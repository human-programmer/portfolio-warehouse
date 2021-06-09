<?php


namespace FilesSystem\Pragma\Tests;


class TestFileSystem extends \FilesSystem\Pragma\FileSystem {
	static function moveFile(string $new_name, string $tmp_name): bool {
		return rename($tmp_name, $new_name);
	}
}