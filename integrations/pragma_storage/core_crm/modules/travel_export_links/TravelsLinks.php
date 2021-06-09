<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/travel_export_link/ITravelLinks.php';
require_once __DIR__ . '/TravelsLinksLoader.php';


class TravelsLinks implements ITravelLinks {
	use TravelsLinksLoader;

	function __construct(private IStoreApp $app) {}

	function getTravelsLinks(int $travel_id): array {
		return $this->findAllOfTravelInBuffer($travel_id) ?? $this->getFromDb($travel_id);
	}

    function getOrCreateTravelsLink(int $travel_id, int $product_id): ITravelLink {
        $link = $this->findTravelLink($travel_id, $product_id);
        if($link) return $link;
        return $this->createTravelsLink($travel_id, $product_id);
    }

    function findTravelLink(int $travel_id, int $product_id): ITravelLink|null {
	    return $this->getTravelsLinks($travel_id)[$product_id] ?? null;
    }

    function getApp(): IStoreApp {
        return $this->app;
    }

    function getExportsTravelLink(int $export_id): ITravelLink {
        return $this->findExportsTravelLinkInBuffer($export_id) ?? $this->getFromDbByExport($export_id);
    }
}