<?php


namespace PragmaStorage;


class CategoryStoreLinkStruct implements ICategoryStoreLinkStruct {
	private int $store_id;
	private int $category_id;
	private int $link_status;

	function __construct(array $model) {
		$this->store_id = $model['store_id'];
		$this->category_id = $model['category_id'];
		$this->link_status = $model['link_status'];
	}
	function getStoreId(): int {
		return $this->store_id;
	}
	function getCategoryId(): int {
		return $this->category_id;
	}
	function getLinkStatus(): int {
		return $this->link_status;
	}
}