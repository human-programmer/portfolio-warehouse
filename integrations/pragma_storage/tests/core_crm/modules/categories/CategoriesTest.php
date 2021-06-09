<?php


namespace PragmaStorage\Test;

use PragmaStorage\iCategory;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/CategoriesDataSets.php';

class CategoriesTest extends \PHPUnit\Framework\TestCase {
	use CategoriesDataSets;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	/**
	 * @dataProvider getCategoriesTitles
	 */
	function testCreateCategory (string $title){
		if(!trim($title))
			$this->expectException(\Exception::class);
		$category = TestPragmaFactory::getTestCategories()->createCategory($title);

		$title = substr(trim($title), 0, 256);
		if($title) {
			$this->assertEquals(trim($title), $category->getTitle());
			$this->checkCategoriesModel($category, $title);
			$this->checkCategory($category);
		}
	}

	protected function checkCategoriesModel (iCategory $category, string $expectedTitle) : void {
		$model = $category->toArray();
		$this->assertEquals($expectedTitle, $model['title']);
		$this->assertEquals($category->getCategoryId(), $model['category_id']);

	}

	protected function checkCategory (iCategory $category) : void {
		$this->assertFalse($category->isDeleted());
		$this->assertTrue(!!$category->getCategoryId());

		$this->checkFindCategory($category->getTitle());
		$this->checkGetCategory($category->getCategoryId());
		$this->checkUpdate($category);
		$this->checkDelete($category);
	}

	private function checkFindCategory (string $title) : void {
		$changed_category = self::getCategories()->findCategory($title);
		$this->assertTrue(!!$changed_category);
		$this->assertEquals($title, $changed_category->getTitle());
	}

	private function checkGetCategory (int $id) : void {
		$category = self::getCategories()->getCategory($id);
		$this->assertTrue(!!$category);
	}

	private function checkUpdate (iCategory $category) : void {
		$new_name = 'new name';
		$category->update(['title' => $new_name]);
		$this->assertEquals($new_name, $category->getTitle());
		$this->checkFindCategory($new_name);
	}

	private function checkDelete(iCategory $category) : void {
		$category->update(['title' => 'test delete category . ' . time()]);
		$category->delete();
		$this->assertTrue($category->isDeleted());
		$search = self::getCategories()->findCategory($category->getTitle());
		$this->assertFalse(!!$search);
	}
}