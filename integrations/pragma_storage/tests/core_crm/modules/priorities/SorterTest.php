<?php


namespace PragmaStorage\Exports\Tests;


use PragmaStorage\iExport;
use PragmaStorage\iImport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\iStore;
use PragmaStorage\IStoreExportPriority;
use PragmaStorage\ITravelLinkStruct;
use PragmaStorage\PrioritiesIterator;
use PragmaStorage\Test\TestPragmaFactory;


require_once __DIR__ . '/../../TestPragmaFactory.php';

class SorterTest extends \PHPUnit\Framework\TestCase {
	use PrioritiesCreator;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
		TestPragmaFactory::resetStoreApp();
	}

	function testSortProductImportsByPriority(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();

		$productImport1_1 = $this->createProductImportMock($store1);
		$productImport1_2 = $this->createProductImportMock($store1);
		$productImport1_3 = $this->createProductImportMock($store1);

		$productImport2_1 = $this->createProductImportMock($store2);
		$productImport2_2 = $this->createProductImportMock($store2);
		$productImport2_3 = $this->createProductImportMock($store2);

		$productImport3_1 = $this->createProductImportMock($store3);
		$productImport3_2 = $this->createProductImportMock($store3);
		$productImport3_3 = $this->createProductImportMock($store3);

		$priority1 = self::uniquePriorityModel(0, $store2);
		$priority2 = self::uniquePriorityModel(1, $store3);
		$priority3 = self::uniquePriorityModel(2, $store1);

		$priorities = [$priority1, $priority2, $priority3];
		$productImports = [$productImport1_1, $productImport2_2, $productImport3_3, $productImport1_2, $productImport2_3, $productImport3_1, $productImport1_3, $productImport2_1, $productImport3_2];
		$iterator = new PrioritiesIterator($priorities);

		$sorted = $iterator->sortProductImports($productImports);

		$this->assertEquals($store2->getStoreId(), $sorted[0]->findStoreId());
		$this->assertEquals($store2->getStoreId(), $sorted[1]->findStoreId());
		$this->assertEquals($store2->getStoreId(), $sorted[2]->findStoreId());
		$this->assertEquals($store3->getStoreId(), $sorted[3]->findStoreId());
		$this->assertEquals($store3->getStoreId(), $sorted[4]->findStoreId());
		$this->assertEquals($store3->getStoreId(), $sorted[5]->findStoreId());
		$this->assertEquals($store1->getStoreId(), $sorted[6]->findStoreId());
		$this->assertEquals($store1->getStoreId(), $sorted[7]->findStoreId());
		$this->assertEquals($store1->getStoreId(), $sorted[8]->findStoreId());
	}

	function testSortProductImportsByPriorityAndDate(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();

		$productImport1_1 = $this->createProductImportMock($store1, 2);
		$productImport1_2 = $this->createProductImportMock($store1, 3);
		$productImport1_3 = $this->createProductImportMock($store1, 1);

		$productImport2_1 = $this->createProductImportMock($store2, 2);
		$productImport2_2 = $this->createProductImportMock($store2, 3);
		$productImport2_3 = $this->createProductImportMock($store2, 1);

		$productImport3_1 = $this->createProductImportMock($store3, 2);
		$productImport3_2 = $this->createProductImportMock($store3, 3);
		$productImport3_3 = $this->createProductImportMock($store3, 1);

		$priority1 = self::uniquePriorityModel(0, $store2);
		$priority2 = self::uniquePriorityModel(1, $store3);
		$priority3 = self::uniquePriorityModel(2, $store1);

		$priorities = [$priority1, $priority2, $priority3];
		$productImports = [$productImport1_1, $productImport2_2, $productImport3_3, $productImport1_2, $productImport2_3, $productImport3_1, $productImport1_3, $productImport2_1, $productImport3_2];
		$iterator = new PrioritiesIterator($priorities);

		$sorted = $iterator->sortProductImports($productImports);

		$this->assertTrue($productImport2_3 === $sorted[0]);
		$this->assertTrue($productImport2_1 === $sorted[1]);
		$this->assertTrue($productImport2_2 === $sorted[2]);

		$this->assertTrue($productImport3_3 === $sorted[3]);
		$this->assertTrue($productImport3_1 === $sorted[4]);
		$this->assertTrue($productImport3_2 === $sorted[5]);

		$this->assertTrue($productImport1_3 === $sorted[6]);
		$this->assertTrue($productImport1_1 === $sorted[7]);
		$this->assertTrue($productImport1_2 === $sorted[8]);
	}

	function testSortProductsImportsWithoutPriorities(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();

		$productImport1_1 = $this->createProductImportMock($store1, 2);
		$productImport1_2 = $this->createProductImportMock($store1, 3);
		$productImport1_3 = $this->createProductImportMock($store1, 1);

		$productImport2_1 = $this->createProductImportMock($store2, 5);
		$productImport2_2 = $this->createProductImportMock($store2, 6);
		$productImport2_3 = $this->createProductImportMock($store2, 4);

		$productImport3_1 = $this->createProductImportMock($store3, 8);
		$productImport3_2 = $this->createProductImportMock($store3, 9);
		$productImport3_3 = $this->createProductImportMock($store3, 7);

		$productImports = [$productImport1_1, $productImport2_2, $productImport3_3, $productImport1_2, $productImport2_3, $productImport3_1, $productImport1_3, $productImport2_1, $productImport3_2];
		$iterator = new PrioritiesIterator([]);

		$sorted = $iterator->sortProductImports($productImports);

		$this->assertTrue($productImport1_3 === $sorted[0]);
		$this->assertTrue($productImport1_1 === $sorted[1]);
		$this->assertTrue($productImport1_2 === $sorted[2]);

		$this->assertTrue($productImport2_3 === $sorted[3]);
		$this->assertTrue($productImport2_1 === $sorted[4]);
		$this->assertTrue($productImport2_2 === $sorted[5]);

		$this->assertTrue($productImport3_3 === $sorted[6]);
		$this->assertTrue($productImport3_1 === $sorted[7]);
		$this->assertTrue($productImport3_2 === $sorted[8]);
	}

	function testSortStoresByPriority(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();
		$store4 = self::getUniqueStore();
		$store5 = self::getUniqueStore();

		$priority1 = self::uniquePriorityModel(0, $store2);
		$priority2 = self::uniquePriorityModel(1, $store3);
		$priority3 = self::uniquePriorityModel(2, $store1);
		$priority4 = self::uniquePriorityModel(3, $store5);
		$priority5 = self::uniquePriorityModel(4, $store4);

		$priorities = [$priority1, $priority2, $priority3, $priority4, $priority5];
		$iterator = new PrioritiesIterator($priorities);
		$stores = [$store1, $store2, $store3, $store4, $store5,];
		$sortedStores = $iterator->sortStores($stores);

		$this->assertTrue($store2 === $sortedStores[0]);
		$this->assertTrue($store3 === $sortedStores[1]);
		$this->assertTrue($store1 === $sortedStores[2]);
		$this->assertTrue($store5 === $sortedStores[3]);
		$this->assertTrue($store4 === $sortedStores[4]);
	}

	function testSortStoresByPriorityWithoutPriorities(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();
		$store4 = self::getUniqueStore();
		$store5 = self::getUniqueStore();

		$iterator = new PrioritiesIterator([]);
		$stores = [$store4, $store3, $store1, $store5,$store2, ];
		$sortedStores = $iterator->sortStores($stores);

		$this->assertTrue($store1 === $sortedStores[0]);
		$this->assertTrue($store2 === $sortedStores[1]);
		$this->assertTrue($store3 === $sortedStores[2]);
		$this->assertTrue($store4 === $sortedStores[3]);
		$this->assertTrue($store5 === $sortedStores[4]);
	}

	function testSortExports(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();

		$export1 = self::getUniqueExport();
		$export2 = self::getUniqueExport();

		$export3 = self::getUniqueExport();
		$export4 = self::getUniqueExport();

		$priority1 = self::uniquePriorityModel(2, $store1, $export1);
		$priority2 = self::uniquePriorityModel(0, $store2, $export1);
		$export1->setPriorities([$priority1, $priority2]);

		$priority3 = self::uniquePriorityModel(0, $store1, $export2);
		$priority4 = self::uniquePriorityModel(2, $store2, $export2);
		$export2->setPriorities([$priority3, $priority4]);

		$priority5 = self::uniquePriorityModel(2, $store1, $export3);
		$priority6 = self::uniquePriorityModel(0, $store2, $export3);
		$export3->setPriorities([$priority5, $priority6]);

		$priority7 = self::uniquePriorityModel(0, $store1, $export4);
		$priority8 = self::uniquePriorityModel(2, $store2, $export4);
		$export4->setPriorities([$priority7, $priority8]);

		$priorities = [
			$priority1,
			$priority2,
			$priority3,
			$priority4,
			$priority5,
			$priority6,
			$priority7,
			$priority8,
		];
		$exports = [
			$export1,
			$export2,
			$export3,
			$export4,
		];
		$iterator = new PrioritiesIterator($priorities);
		$sortedExports = $iterator->sortExports($exports, $store1->getStoreId());
		$this->assertCount(4, $sortedExports);
		$sortedExports2 = $iterator->sortExports($exports, 0);
		$this->assertCount(4, $sortedExports);
		$this->assertCount(4, $sortedExports2);

		$this->assertTrue($export2 === $sortedExports[0]);
		$this->assertTrue($export4 === $sortedExports[1]);
		$this->assertTrue($export1 === $sortedExports[2]);
		$this->assertTrue($export3 === $sortedExports[3]);
	}

	function testSortExportsWithoutPriorities(): void {
		$export1 = self::getUniqueExport();
		$export2 = self::getUniqueExport();

		$export3 = self::getUniqueExport();
		$export4 = self::getUniqueExport();

		$exports = [
			$export3,
			$export4,
			$export2,
			$export1,
		];
		$iterator = new PrioritiesIterator([]);
		$sortedExports = $iterator->sortExports($exports, 0);
		$this->assertCount(4, $sortedExports);

		$this->assertTrue($export1 === $sortedExports[0]);
		$this->assertTrue($export2 === $sortedExports[1]);
		$this->assertTrue($export3 === $sortedExports[2]);
		$this->assertTrue($export4 === $sortedExports[3]);
	}

	private function createProductImportMock(iStore $store, int $date_create = null): iProductImport {
		$date_create = $date_create ?? time();
		return new ProductImportMock($store, $date_create);
	}

	private function createExportMock(IStoreExportPriority $priority, int $date_create = null): iExport {
		$date_create = $date_create ?? time();
		return new ExportMock($priority, $date_create);
	}
}

class ProductImportMock implements iProductImport {
	function __construct(
		public iStore $store,
		public int $date_create,
	) {
	}

	function setQuantity(float $new_quantity): void {
		// TODO: Implement setQuantity() method.
	}

	function getProductImportId(): int {
		// TODO: Implement getProductImportId() method.
	}

	function getProductId(): int {
		// TODO: Implement getProductId() method.
	}

	function getImportId(): int|null {
		// TODO: Implement getImportId() method.
	}

	function getImportQuantity(): float {
		// TODO: Implement getImportQuantity() method.
	}

	function getPurchasePrice(): float {
		// TODO: Implement getPurchasePrice() method.
	}

	function getSource(): int {
		// TODO: Implement getSource() method.
	}

	function isDeficit(): bool {
		// TODO: Implement isDeficit() method.
	}

	function getFreeBalanceQuantity(): float {
		// TODO: Implement getFreeBalanceQuantity() method.
	}

	function getBalanceQuantity(): float {
		// TODO: Implement getBalanceQuantity() method.
	}

	function isDeleted(): bool {
		// TODO: Implement isDeleted() method.
	}

	function delete(): bool {
		// TODO: Implement delete() method.
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		// TODO: Implement toArray() method.
	}

	function update(array $model): bool {
		// TODO: Implement update() method.
	}

	function getProduct(): iProduct {
		// TODO: Implement getProduct() method.
	}

	function getExportDetails(): array {
		// TODO: Implement getExportDetails() method.
	}

	function getOwnedExports(): array {
		// TODO: Implement getOwnedExports() method.
	}

	function getExportQuantity(): float {
		// TODO: Implement getExportQuantity() method.
	}

	function getImportDate(): int {
		return $this->date_create;
	}

	function updateBalance(): void {
		// TODO: Implement updateBalance() method.
	}

	function isExported(): bool {
		// TODO: Implement isExported() method.
	}

	function findImport(): iImport|null {
		// TODO: Implement findImport() method.
	}

	function save(): bool {
		// TODO: Implement save() method.
	}

	function findStoreId(): null|int {
		return $this->store->getStoreId();
	}

	function finTravelLink(): ITravelLinkStruct|null {
		// TODO: Implement finTravelLink() method.
	}

	function getDateCreate(): int {
		return $this->date_create;
	}

	function getExportItems(): array {
		// TODO: Implement getExportItems() method.
	}

    function setPurchasePrice(float $price): void {
        // TODO: Implement setPurchasePrice() method.
    }
}

class ExportMock implements iExport {

	public function __construct(public IStoreExportPriority|null $priority, public int $date_create) {}

	function getExportId(): int {
		// TODO: Implement getExportId() method.
	}

	function getEntityId(): int|null {
		// TODO: Implement getEntityId() method.
	}

	function getProductId(): int {
		// TODO: Implement getProductId() method.
	}

	function getQuantity(): float {
		// TODO: Implement getQuantity() method.
	}

	function setQuantity(float $quantity) {
		// TODO: Implement setQuantity() method.
	}

	function getSellingPrice(): float {
		// TODO: Implement getSellingPrice() method.
	}

	function setSellingPrice(float $price): void {
		// TODO: Implement setSellingPrice() method.
	}

	function getStatusId(): int {
		// TODO: Implement getStatusId() method.
	}

	function setStatus(int $status_id): bool {
		// TODO: Implement setStatus() method.
	}

	function getPriorities(): array {
		// TODO: Implement getPriorities() method.
	}

	function setPriorities(array $priorities): void {
		// TODO: Implement setPriorities() method.
	}

	function getHighestPriority(): IStoreExportPriority {
		// TODO: Implement getHighestPriority() method.
	}

	function getClientType(): int {
		// TODO: Implement getClientType() method.
	}

	function isDeleted(): bool {
		// TODO: Implement isDeleted() method.
	}

	function delete(): bool {
		// TODO: Implement delete() method.
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		// TODO: Implement toArray() method.
	}

	function update(array $model): bool {
		// TODO: Implement update() method.
	}

	function getDetails(): array {
		// TODO: Implement getDetails() method.
	}

	function getProduct(): iProduct {
		// TODO: Implement getProduct() method.
	}

	function getDetailsQuantity(): float {
		// TODO: Implement getDetailsQuantity() method.
	}

	function saveDeletedEntity(): void {
		// TODO: Implement saveDeletedEntity() method.
	}

	function getPrioritySort(?int $store_id): int|null {
		// TODO: Implement getPrioritySort() method.
	}

	function updateDetails(iProductImport $productImport = null): bool {
		// TODO: Implement updateDetails() method.
	}

	function isExported(): bool {
		// TODO: Implement isExported() method.
	}

	function finTravelLink(): ITravelLinkStruct|null {
		// TODO: Implement finTravelLink() method.
	}

	function getProductsImports(): array {
		// TODO: Implement getProductsImports() method.
	}

	function setDeleted(): void {
		// TODO: Implement setDeleted() method.
	}

    function getAvailablePriorities(): array {
        // TODO: Implement getAvailablePriorities() method.
    }

    function setAvailableStoresId(?array $available_stores_id): void {
        // TODO: Implement setAvailableStoresId() method.
    }

    function getTotalPurchasePrice(): float {
        // TODO: Implement getTotalPurchasePrice() method.
    }
}