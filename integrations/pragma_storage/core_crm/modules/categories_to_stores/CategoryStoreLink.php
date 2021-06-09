<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/categories_to_stores/ICategoryStoreLink.php';
require_once __DIR__ . '/CategoryStoreLinkStruct.php';


class CategoryStoreLink extends CategoryStoreLinkStruct implements ICategoryStoreLink {
	function __construct(private IStoreApp $app, array $model) {
		parent::__construct($model);
	}

	function getStore(): iStore {
		return $this->app->getStores()->getStore($this->getStoreId());
	}

	function getCategory(): iCategory {
		return $this->app->getCategories()->getCategory($this->getCategoryId());
	}
}