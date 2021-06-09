<?php


namespace PragmaStorage\Test;


use PragmaStorage\Travels;

require_once __DIR__ . '/TravelsTestPrepare.php';

class TravelsTest extends \PHPUnit\Framework\TestCase {
	use TravelsTestPrepare;

	/**
	 * @dataProvider travels
	 */
	function testCreateTravel(Travels $travels): void {
	    $travels = new Travels(TestPragmaFactory::getStoreApp());
		$travelModel = self::uniqueTravelModel();
		$travel = $travels->createTravel($travelModel);
		$this->compareCreationTravelsModels($travelModel, $travel);
		$dif_time = time() - $travel->getCreationDate();
		$this->assertTrue($dif_time <= 2);
		$this->assertTrue($dif_time >= 0);
		$this->assertTrue(!!$travel->getTravelId());
	}

	/**
	 * @dataProvider travels
	 */
	function testGetTravel(Travels $travels): void {
		$expectTravel = self::uniqueTravel();
		$travel = $travels->getTravel($expectTravel->getTravelId());
		$this->assertFalse($expectTravel === $travel);
		$this->compareTravelsModels($expectTravel, $travel);
		$this->assertTrue(!!$travel->getTravelId());
		$this->assertTrue(!!$travel->getEndImportId());
	}

	/**
	 * @dataProvider travels
	 */
	function testGetTravelFromBuffer(Travels $travels): void {
		$expectTravel = self::uniqueTravel();
		$travel1 = $travels->getTravel($expectTravel->getTravelId());
		$travel2 = $travels->getTravel($expectTravel->getTravelId());
		$travel3 = $travels->getTravel($expectTravel->getTravelId());

		$this->assertFalse($expectTravel === $travel1);
		$this->assertTrue($travel1 === $travel2);
		$this->assertTrue($travel1 === $travel3);

		$this->compareTravelsModels($expectTravel, $travel1);
		$this->assertTrue(!!$travel1->getTravelId());
		$this->assertTrue(!!$travel1->getEndImportId());
	}
}