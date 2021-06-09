<?php


namespace PragmaStorage\Test;

use PragmaStorage\ICreationTravelModel;
use PragmaStorage\ITravelModel;
use PragmaStorage\Travels;

require_once __DIR__ . '/../../TestPragmaFactory.php';

trait TravelsTestPrepare {
	use TravelCreator, ProductImportsCreator;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
		TestPragmaFactory::resetStoreApp();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		self::clearTravels();
	}

	protected function setUp(): void {
		parent::setUp();
		TestPragmaFactory::resetStoreApp();
	}

	private function compareTravelsModels(ITravelModel $expect, ITravelModel $actual): void {
		$this->assertEquals($expect->getTravelId(), $actual->getTravelId());
		$this->assertEquals($expect->getEndImportId(), $actual->getEndImportId());
		$this->compareCreationTravelsModels($expect, $actual);
	}

	private function compareCreationTravelsModels(ICreationTravelModel $expect, ICreationTravelModel $actual): void {
		$this->assertEquals($expect->getStartStoreId(), $actual->getStartStoreId());
		$this->assertEquals($expect->getEndStoreId(), $actual->getEndStoreId());
		$this->assertEquals($expect->getUserId(), $actual->getUserId());
		$this->assertEquals($expect->getTravelDate(), $actual->getTravelDate());
	}

	static function travels(): array {
		TestPragmaFactory::ifInitTest();
		return [[new Travels(TestPragmaFactory::getStoreApp())]];
	}
}