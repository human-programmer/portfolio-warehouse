<?php


namespace PragmaStorage;


interface ITravelLinks {
    function getExportsTravelLink(int $export_id): ITravelLink;
    function getOrCreateTravelsLink(int $travel_id, int $product_id): ITravelLink;
	function getTravelsLinks(int $travel_id): array;
    function findTravelLink(int $travel_id, int $product_id): ITravelLink|null;
}