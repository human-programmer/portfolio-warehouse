<?php


namespace PragmaStorage;


trait TravelsLinksBuffer {
	private array $travels_links = [];

	private function findAllOfTravelInBuffer(int $travel_id): array|null {
		return $this->travels_links[$travel_id] ?? null;
	}

	private function addLinkInBuffer(ITravelLink $link): void {
		$this->travels_links[$link->getTravelId()][$link->getProductId()] = $link;
	}

	private function setTravelLoadedInBuffer(int $travel_id): void {
		$this->travels_links[$travel_id] = $this->travels_links[$travel_id] ?? [];
	}

    private function findExportsTravelLinkInBuffer(int $export_id): ITravelLink|null {
	    $links = $this->getAllLinksFromBuffer();
	    foreach($links as $link)
	        if($link->getStartExportId() === $export_id)
	            return $link;
        return null;
    }

    private function getAllLinksFromBuffer(): array {
	    return array_merge(...array_values($this->travels_links));
    }
}