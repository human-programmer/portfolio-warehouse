<?php


namespace PragmaStorage\Test;


use PragmaStorage\iProduct;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/ProductsDataSets.php';

class ProductsTest extends \PHPUnit\Framework\TestCase {
	use ProductsDataSets;

	private iProduct $current_product;
	private int $expected_category_id;
	private string $expected_article;
	private string $expected_title;
	private string $expected_selling_price;
	private array $model;

	static function setUpBeforeClass(): void {
		TestPragmaFactory::ifInitTest();
		parent::setUpBeforeClass();
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		self::clearDataSets();
	}

	/**
	 * @dataProvider getProductsForCreate
	 */
	function testCreateProduct(int $category_id, string $article, string $title, float $selling_price, array $model){
		$this->createProduct($category_id, $article, $title, $selling_price, $model);

		$this->checkFindProduct();
		$this->checkGetProduct();
		$this->checkProduct();
	}

	protected function createProduct (int $category_id, string $article, string $title, float $selling_price, array $model) : iProduct{
		$product = self::getProducts()->createProduct($category_id, $article, $title, $selling_price, $model);
		$this->assertTrue(!!$product);
		$this->current_product = $product;
		$this->expected_category_id = $category_id;
		$this->expected_article = self::formattingAsVarchar($article);
		$this->expected_title = self::formattingAsVarchar($title);
		$this->expected_selling_price = $selling_price;
		$model['unit'] = self::formattingAsVarchar($model['unit'] ?? '');
		$this->model = $model;
		return $product;
	}

	protected function checkFindProduct () : void {
		$fresh_product = self::getProducts()->findProduct($this->getCurrentProduct()->getArticle());
		$this->assertTrue(!!$fresh_product);
		$this->compareWithProduct($fresh_product);
	}

	protected function checkGetProduct () : void {
		$fresh_product = self::getProducts()->getProduct($this->getCurrentProduct()->getProductId());
		$this->assertTrue(!!$fresh_product);
		$this->compareWithProduct($fresh_product);
	}

	protected function checkProduct() : void {
		$this->checkProductId();
		$this->checkIsNotDeleted();
		$this->checkTitle();
		$this->checkArticle();
		$this->checkSellingPrice();
		$this->checkCategoryId();
		$this->checkUnit();
		$this->checkModel();
		$this->checkUpdate();
		$this->checkDelete();
	}

	private function checkProductId(){
		$this->assertTrue(!!$this->getCurrentProduct()->getProductId());
	}

	private function checkIsNotDeleted(){
		$this->assertFalse($this->getCurrentProduct()->isDeleted());
	}

	private function checkTitle(){
		$this->assertEquals($this->getExpectedTitle(), $this->getCurrentProduct()->getTitle());
	}

	private function checkArticle(){
		$this->assertEquals($this->getExpectedArticle(), $this->getCurrentProduct()->getArticle());
	}

	private function checkSellingPrice(){
		$this->assertEquals($this->getExpectedSellingPrice(), $this->getCurrentProduct()->getSellingPrice());
	}

	private function checkCategoryId(){
		$this->assertEquals($this->getExpectedCategoryId(), $this->getCurrentProduct()->getCategoryId());
	}

	private function checkUnit(){
		$this->assertEquals($this->getModel()['unit'], $this->getCurrentProduct()->getUnit());
	}

	private function checkModel (){
		$product = $this->getCurrentProduct();
		$model = $product->toArray();

		$this->assertEquals($product->getProductId(), $model['id']);
		$this->assertEquals($product->getCategoryId(), $model['category_id']);
		$this->assertEquals($product->getArticle(), $model['article']);
		$this->assertEquals($product->getTitle(), $model['title']);
		$this->assertEquals($product->getSellingPrice(), $model['selling_price']);
	}

	private function checkUpdate () : void {
		$this->checkUpdateAllVarChars();
		$this->checkUpdateAllFloats();
	}

	private function checkUpdateAllVarChars () : void {
		$dataSets = self::getSetsForUpdateVarchar();
		foreach ($dataSets as $varchar)
			$this->checkUpdateVarchars($varchar[0]);
	}

	private function checkUpdateVarchars (string $string) : void {
		$this->checkArticleUpdate($string);
		$this->checkTitleUpdate($string);
		$this->checkUnitUpdate($string);
	}

	private function checkUpdateAllFloats () : void {
		$dataSets = self::getFloatValues();
		foreach ($dataSets as $varchar)
			$this->checkSellingPriceUpdate($varchar[0]);
	}

	private function checkArticleUpdate(string $article){
		$product = $this->getCurrentProduct();
		$product->update(['article' => $article]);
		$this->compareProductWithInDb();
	}

	private function checkTitleUpdate(string $title){
		$product = $this->getCurrentProduct();
		$product->update(['title' => $title]);
		$this->compareProductWithInDb();
	}

	private function checkUnitUpdate(string $unit){
		$product = $this->getCurrentProduct();
		$product->update(['unit' => $unit]);
		$this->compareProductWithInDb();
	}

	private function checkSellingPriceUpdate(float $selling_price){
		$product = $this->getCurrentProduct();
		$product->update(['selling_price' => $selling_price]);
		$this->compareProductWithInDb();
	}

	private function compareProductWithInDb () : void {
		$fresh_product = self::getProducts()->getProduct($this->getCurrentProduct()->getProductId());
		$this->compareWithProduct($fresh_product);
	}

	private function compareWithProduct (iProduct $fresh_product) : void {
		$product = $this->getCurrentProduct();
		$this->assertEquals($product->getProductId(), $fresh_product->getProductId());
		$this->assertEquals($product->getTitle(), $fresh_product->getTitle());
		$this->assertEquals($product->getUnit(), $fresh_product->getUnit());
		$this->assertEquals($product->getCategoryId(), $fresh_product->getCategoryId());
		$this->assertEquals($product->getUnit(), $fresh_product->getUnit());
		$this->assertEquals($product->getSellingPrice(), $fresh_product->getSellingPrice());
		$this->assertEquals($product->getArticle(), $fresh_product->getArticle());
		$this->assertFalse($fresh_product->isDeleted());
	}

	private function checkDelete () : void {
		$product = $this->getCurrentProduct();
		$product->delete();
		$this->assertTrue($product->isDeleted());

		$fresh_product = self::getProducts()->findProduct($product->getArticle());
		$this->assertFalse(!!$fresh_product);
		$this->expectException(\Exception::class);
		self::getProducts()->getProduct($product->getProductId());
	}

	public function getCurrentProduct(): iProduct {
		return $this->current_product;
	}

	public function getExpectedCategoryId(): int {
		return $this->expected_category_id;
	}

	public function getExpectedArticle(): string {
		return $this->expected_article;
	}

	public function getExpectedTitle(): string {
		return $this->expected_title;
	}

	public function getExpectedSellingPrice(): string {
		return $this->expected_selling_price;
	}

	public function getModel(): array {
		return $this->model;
	}
}