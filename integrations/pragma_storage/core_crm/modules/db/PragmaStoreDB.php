<?php


namespace PragmaStorage;


use Generals\CRMDB;

require_once __DIR__ . '/../../../../../lib/db/CRMDB.php';

class PragmaStoreDB extends CRMDB {

	public function __construct() {
		parent::__construct();
	}

	protected static function to_date(int $date_time, string $format = 'Y-m-d H:i:s'): string {
		return date($format, $date_time);
	}
}