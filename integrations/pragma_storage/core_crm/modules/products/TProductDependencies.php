<?php


namespace PragmaStorage;


trait TProductDependencies {
	private IStoreApp $app;
	private function dependenciesInit(IStoreApp $app): void {
		$this->app = $app;
	}

	private function getProductImports(): iProductImports {
		return $this->app->getProductImports();
	}
}