<?php


namespace TemplateEngine\Amocrm\Tests;


use TemplateEngine\Amocrm\AmoEntityParams;
use TemplateEngine\Tests\TestDataSets;

require_once __DIR__ . '/TestFactory.php';

class AmoEntityParamsTest extends \PHPUnit\Framework\TestCase {
	function testCreate(){
		$model = self::randomParamsModel();
		$struct = new AmoEntityParams($model);
		$this->assertEquals($model['entities'], $struct->getEntities());
		$this->assertEquals($model['managers'], $struct->getManagers());
		$this->assertEquals($model['customFields'], $struct->getCustomFields());
	}

	private static function randomParamsModel(): array {
		return [
			'entities' => TestDataSets::randomArr(),
			'managers' => TestDataSets::randomArr(),
			'customFields' => TestDataSets::randomArr(),
		];
	}
}