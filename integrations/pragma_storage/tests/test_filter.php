<?php

require_once __DIR__ . '/../../../lib/db/CRMDB.php';
require_once __DIR__ . '/../core_crm/modules/CatalogExportsFilter.php';


$model = [
	'id' => 27340,
	'store_id' => null,
	'import_id' => null,
	'product_id' => null,
	'status_id' => null,
	'user_id' => null,
];

$filter = new \PragmaStorage\CatalogExportsFilter(82, $model);
$sql = $filter->getSql();
echo '';