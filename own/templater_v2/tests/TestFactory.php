<?php


namespace TemplateEngine\Tests;


use TemplateEngine\Pragma\Factory;
use TemplateEngine\Pragma\ITemplateDirs;

require_once __DIR__ . '/../pragma/Factory.php';
require_once __DIR__ . '/../../../modules/files_system/tests/pragma/TestFactory.php';

class TestFactory extends \FilesSystem\Pragma\Tests\TestFactory {
	static function getDirs(): ITemplateDirs {
		return Factory::getTemplateDirs();
	}
	static function initTestService(): void {
		Factory::initPragma();
	}
}