<?php


namespace PragmaStorage;


interface ITravels {
	function getTravel(int $travel_id): ITravel;
	function createTravel(ICreationTravelModel $model): ITravel;
	function cancelTravel(int $travel_id): void;
}