<?php


namespace PragmaStorage\Exports;


class TravelLinkStruct implements \PragmaStorage\ITravelLinkStruct {
	function __construct(
		private int $travel_id,
		private int $export_id,
		private int $product_import_id) {}

	function getTravelId(): int {
		return $this->travel_id;
	}

	function getStartExportId(): int {
		return $this->export_id;
	}

	function getReceiveProductImportId(): int {
		return $this->product_import_id;
	}
}