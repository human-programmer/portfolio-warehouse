<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImport;
use PragmaStorage\iStore;

require_once __DIR__ . '/ImportsDataSets.php';

class ImportsTest extends \PHPUnit\Framework\TestCase {
	use ImportsDataSets;

	private iStore $current_store;
	private iImport $current_import;
	private array $current_model;

	public static function setUpBeforeClass(): void {
		TestPragmaFactory::ifInitTest();
		parent::setUpBeforeClass();
	}

	public static function tearDownAfterClass(): void {
		self::clearStores();
		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider dataSetsForCreate
	 */
	function testCreateImport (iStore $store, array $model){
		$this->createImport($store, $model);
		$this->checkGetImport();
		$this->checkImport();
	}

	private function createImport(iStore $store, array $model) : void {
		$this->current_store = $store;
		$import = self::getImports()->createImport($store, $model);
		$this->assertInstanceOf(iImport::class, $import);

		$model['data_provider'] = self::formattingAsVarchar($model['data_provider'] ?? '');
		$this->current_model = $model;
		$this->current_import = $import;
	}

	protected function checkGetImport () : void {
		$import = self::getImports()->getImport($this->getCurrentImport()->getImportId());
		$this->assertEquals($this->getCurrentImport()->getImportId(), $import->getImportId());
		$this->assertEquals($this->getCurrentImport()->getStoreId(), $import->getStoreId());
	}

	protected function checkImport () : void {
		$this->checkFields();
		$this->checkUpdate();
		$this->checkDelete();
	}

	private function checkFields() : void {
		$this->checkImportId();
		$this->checkStoreId();
		$this->checkIsNotExported();
		$this->checkModel();
	}

	private function checkStoreId() : void {
		$this->assertEquals($this->getCurrentStore()->getStoreId(), $this->getCurrentImport()->getStoreId());
	}

	private function checkImportId() : void {
		$this->assertTrue(!!$this->getCurrentImport()->getImportId());
	}

	private function checkIsNotExported() : void {
		$this->assertFalse($this->getCurrentImport()->isExported());
	}

	private function checkModel() : void {
		$model = $this->getCurrentImport()->toArray();

		$this->assertEquals($this->getCurrentImport()->getImportId(), $model['import_id']);
		$this->assertEquals($this->getCurrentImport()->getStoreId(), $model['store_id']);
		$this->assertIsString($model['import_date']);
		$this->assertIsString($model['provider']);
		$this->assertIsInt($model['number']);
	}

	private function checkUpdate() : void {
		$sets = self::dataSetsForUpdate();
		foreach ($sets as $set)
			$this->checkUpdateFromSet($set);
	}

	private function checkUpdateFromSet (array $set) : void {
		$model = self::getModelFromSet($set);
		$this->getCurrentImport()->update($model);

		if(isset($model['store_id']))
			$this->current_store = $this->getStores()->getStore($model['store_id']);

		$this->current_model['data_provider'] = self::formattingAsVarchar($model['data_provider'] ?? '');
		$this->checkFields();
	}

	private function checkDelete() : void {
		$import = $this->getCurrentImport();
		$import->delete();
		$this->assertTrue($import->isDeleted());

		$this->expectException(\Exception::class);
		self::getImports()->getImport($import->getImportId());
	}

	public function getCurrentImport(): iImport {
		return $this->current_import;
	}

	public function getCurrentModel(): array {
		return $this->current_model;
	}

	public function getCurrentStore(): iStore {
		return $this->current_store;
	}
}