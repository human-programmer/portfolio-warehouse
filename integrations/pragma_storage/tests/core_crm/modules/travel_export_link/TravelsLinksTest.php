<?php


namespace PragmaStorage;


use PragmaStorage\Test\ExportsCreator;
use PragmaStorage\Test\TestPragmaFactory;
use PragmaStorage\Test\TravelCreator;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class TravelsLinksTest extends \PHPUnit\Framework\TestCase {
    use TravelCreator, ExportsCreator, TravelLinksCreator;

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        TestPragmaFactory::ifInitTest();
    }

    function testGetTravelsLinksEmpty(): void {
        $travel = self::uniqueTravel();
        $links_store = self::createTravelsLinks();
        $this->checkTravelsLinks($links_store, $travel, []);
    }

    private function checkTravelsLinks(ITravelLinks $store, ITravel $travel, array $expect_links): void {
        $this->checkTargetTravelsLinks($store, $travel->getTravelId(), $expect_links);
        $store2 = self::createTravelsLinks();
        $this->checkTargetTravelsLinks($store2, $travel->getTravelId(), $expect_links);
    }

    private function checkTargetTravelsLinks(ITravelLinks $store, int $travel_id, array $expect_links): void {
        $actual_links = $store->getTravelsLinks($travel_id);
        $this->assertCount(count($expect_links), $actual_links);
        foreach($expect_links as $expect_link) {
            $actual_link = self::getLink($actual_links, $expect_link);
            $this->compareLinks($expect_link, $actual_link);
        }
    }

    private function compareLinks(ITravelLinkStruct $expect, ITravelLinkStruct $actual): void {
        $this->assertEquals($expect->getTravelId(), $actual->getTravelId());
        $this->assertEquals($expect->getProductId(), $actual->getProductId());
        $this->assertEquals($expect->getStartExportId(), $actual->getStartExportId());
        $this->assertEquals($expect->getReceiveProductImportId(), $actual->getReceiveProductImportId());
    }

    private static function getLink(array $links, ITravelLinkStruct $sample_link): ITravelLinkStruct {
        foreach($links as $link)
            if($link->getProductId() === $sample_link->getProductId() && $link->getTravelId() === $sample_link->getTravelId())
                return $link;
    }

    static function createTravelsLinks(): TravelsLinks {
        return new TravelsLinks(TestPragmaFactory::getStoreApp());
    }
}