<?php


namespace Services\General;


require_once __DIR__ . '/../../../business_rules/general/module/iModule.php';
require_once __DIR__ . '/PragmaModule.php';
require_once __DIR__ . '/AmocrmModule.php';
require_once __DIR__ . '/Bitrix24Module.php';

class Module extends iModule {
	use PragmaModule, AmocrmModule, Bitrix24Module;
	public function __construct(array $model) {
		$this->pragmaInit($model);
		$this->amocrmInit($model);
		$this->bitrix24Init($model);
	}

	function toArray(): array {
		return array_merge(
			$this->getPragmaModel(),
			$this->getAmocrmModel(),
			$this->getBitrix24Model()
		);
	}
}