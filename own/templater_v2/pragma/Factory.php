<?php


namespace TemplateEngine\Pragma;


use Services\General\iDocTemplateService;

require_once __DIR__ . '/../../../modules/files_system/pragma/Factory.php';
require_once __DIR__ . '/components/DocLink.php';
require_once __DIR__ . '/components/TemplateDirs.php';

class Factory extends \FilesSystem\Pragma\Factory {
	static function initPragma(): void {
		\Services\Factory::init(
			self::getNode()->getModule()->getCode(),
			self::getNode()->getAccount()->getDomain(),
			self::getLogWriter()
		);
	}

	static function getTemplateDirs(): ITemplateDirs {
		return new TemplateDirs(self::getNode());
	}

	static function getDocxService(): iDocTemplateService {
		return \Services\Factory::getTemplateService();
	}
}