<?php


namespace PragmaStorage;


trait TravelsBuffer {
	private array $travels;

	private function findTravelInBuffer(int $travel_id): ITravel|null {
		return $this->travels[$travel_id] ?? null;
	}

	private function addTravelInBuffer(ITravel $travel): void {
		$this->travels[$travel->getTravelId()] = $travel;
	}

	private function removeTravelFromBuffer(int $travel_id): void {
		unset($this->travels[$travel_id]);
	}
}