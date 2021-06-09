<?php


namespace PragmaStorage;

require_once __DIR__ . '/../business_rules/IStoreApp.php';
require_once __DIR__ . '/entities/Entities.php';
require_once __DIR__ . '/stores/Stores.php';
require_once __DIR__ . '/statuses/Statuses.php';
require_once __DIR__ . '/statuses/StatusToStatus.php';
require_once __DIR__ . '/products/Products.php';
require_once __DIR__ . '/product_imports/ProductImports.php';
require_once __DIR__ . '/imports/Imports.php';
require_once __DIR__ . '/exports/Exports.php';
require_once __DIR__ . '/export_details/ExportDetails.php';
require_once __DIR__ . '/categories/Categories.php';
require_once __DIR__ . '/travels/Travels.php';
require_once __DIR__ . '/categories_to_stores/CategoriesToStores.php';
require_once __DIR__ . '/travel_export_links/TravelsLinks.php';


class StoreApp implements IStoreApp {
	private int $pragma_account_id;

	private Categories $categories;
	private ExportDetails $export_details;
	private Exports $exports;
	private Imports $imports;
	private ProductImports $product_imports;
	private Products $products;
	private Statuses $statuses;
	private StatusToStatus $status_to_status;
	private Stores $stores;
	private Travels $travels;
	private CategoriesToStores $categoriesToStores;
	private StorePriorities $storePriorities;
	private TravelsLinks $travelsLinks;

	private Entities $entities;

	function __construct(int $pragma_account_id) {
		$this->pragma_account_id = $pragma_account_id;

		$this->entities = new Entities($pragma_account_id);

		$this->categories = new Categories($this);
		$this->export_details = new ExportDetails($pragma_account_id);
		$this->exports = new Exports($this);
		$this->imports = new Imports($pragma_account_id);
		$this->product_imports = new ProductImports($this);
		$this->products = new Products($this);
		$this->statuses = new Statuses($pragma_account_id);
		$this->status_to_status = new StatusToStatus($pragma_account_id);
		$this->stores = new Stores($this);
		$this->travels = new Travels($this);
		$this->categoriesToStores = new CategoriesToStores($this);
		$this->storePriorities = new StorePriorities($this);
		$this->travelsLinks = new TravelsLinks($this);

		$this->products->addHandler($this->categoriesToStores->getProductCreationHandler());
	}


	function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	function getCategories(): iCategories {
		return $this->categories;
	}

	function getExportDetails(): iExportDetails {
		return $this->export_details;
	}

	function getExports(): iExports {
		return $this->exports;
	}

	function getImports(): iImports {
		return $this->imports;
	}

	function getProductImports(): iProductImports {
		return $this->product_imports;
	}

	function getProducts(): iProducts {
		return $this->products;
	}

	function getStatuses(): iStatuses {
		return $this->statuses;
	}

	function getStatusToStatus(): StatusToStatus {
		return $this->status_to_status;
	}

	function getStores(): iStores {
		return $this->stores;
	}

	function getEntities(): Entities {
		return $this->entities;
	}

	function getTravels(): Travels {
		return $this->travels;
	}

	function getCategoriesToStores(): ICategoriesToStores {
		return $this->categoriesToStores;
	}

	function getStorePriorities(): IStorePriorities {
		return $this->storePriorities;
	}

    function getTravelLinks(): ITravelLinks {
        return $this->travelsLinks;
    }
}