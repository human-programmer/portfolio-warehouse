<?php


namespace Services\Tests;


use Services\AccountsService;
use Services\General\iAccount;
use Services\General\iModule;
use Services\General\iNode;
use Services\ModulesService;

require_once __DIR__ . '/../TestFactory.php';

class AmocrmRESTApiTest extends \PHPUnit\Framework\TestCase {
	private static iModule $targetModule;
	private static iAccount $targetAccount;

	private static string $code = 'Dashboard';
	private static string $amocrmCode = 'testdashboard';
	private static string $clientId = 'a22c6304-f11d-42c5-8757-ed280baa358b';
	private static string $secretKey = '5CX1QZEMTW9V9AqwVsq7kVfO0s0Fsh95325JLwHyrNSDQBakCd40jESMdTyuDOEq';
	private static string $oauthCode = 'def50200c3c5590c663a16348b629e87920b45267e734dbb9262f3defb48368cacd800b86aed6b740464993b9ca46d83b6a8b4d648d143e7eb80689974a98e4450c772c068fe5b5ad94da962010a1362025d533fc8db4dd84a6ae675581098e3c9b6c032649967b8b888833bb48185d7587e6602ba652bca227da7a96eeba43dabad368ca1419ee642de9abd57bebb90fce850bd5937c5d99a87deb2125cdfb45d3c09680a0f998676093c32f0392c4d08fc181c5f84ace12deeaffa6fa8ff0db86a1c127494796b47bae5ad9bc816d30ebf7332b08dddf71f150a7b5a4257fb326acceed8091a260e655998cc1b309f711597d4563edde97adf961bd01fa3c742d69a3499c65874475e38c2da186044158273678cdf9556760efaf907378c79a54459a7c05c36faeac1dace97947f09cdc5e49f710b1a6a28f4f896e09f935553dc75a0f798c8a6a7583f4c83d1bdc1ccf7c106e26558d52c95eea051409fcc7153dc4955e25608fc16577a60e5c36dee43194289b5e639cd82f33b18f8011757654a9ed8ae36268363b0f7080ad318ba3cc75a12fab308f6128a35f6d7280c25c69f3a84c25d101aaea9f0b724ae7793e2e81803b869870c0cdaf631a226b35f5a1db3ad79d48fe77c67011da81e29d775dcef75cc71cb1d40642601408565';

	private static int $accountId = 28967662;
	private static string $subdomain = 'pragmadev';
	private static string $referer = 'pragmadev.amocrm.ru';

//	private static string $code = 'TEST_ALL';
//	private static string $amocrmCode = '';
//	private static string $clientId = 'd49b8bf7-cf97-47c7-ad81-c084f1d2e498';
//	private static string $secretKey = 'QVw1RrfAgCRkoqjieaZxKcq1bgZf8n3g3G42fDoQmFT608v9Pqp0KkrgZNQ6Dzdj';
//	private static string $oauthCode = 'def502001f08bdbb3588e5c4e25c1c4bc61cbaf445f26067d7c321175caa999eeb5031eba24532a54f80d07a2138d5fc1196cfcf8eb6f48dc8771ec107d420feae4f1c0fad0a8444fb909d96a859d70166ec70d0d38a95312d52b22c10a2f0abec45f353412be49ea16acfbab4694002fb72c1807c1e85559cfbbe49db834fc3d49e817476aa34f5702dd0916e10ce8e8038f9b5cd3c66030d9fbff93c69c1b975606403ea2856a49a545d458c741825158378da46ca1392f4b6e43b022f7474beb7c732686d2490091d945a2f0e7ab89cf902bd260f7fd26f25f20241ec4a0e369e1da110cb8185edb67f47e060fb61f9c5d5b799ece6d4599ec46c08dd03ef053c8fa046a22713c76864de66872b9ce843df8f38839482f9e115397539661a60cf189de94399a3d2c4216bbe7b7cb0d99f16e876ead46f03d1784a220db4dbf695e6146ec04e2a6b5e9731ae732eb3e6d5c58fe1b6122f66413c8d0fbe0bf01bab87025939c9980e1948128f1eaaf6d8c0bd31dd349e0c76fadb1690cae328c66de55490cf5269668ba3d5649407aedbbc56f57058e3ba670137db776906a5e0d97c4afe3b5d6698861a38889bdc560b1820630052202cfcee77ea12f769f10a621fe3bed143c2d32171bacf805001d9ee05efcb1669de4eaadf2aaf7be4e6';
//
//	private static int $accountId = 29440183;
//	private static string $subdomain = 'niktestgmailcom';
//	private static string $referer = 'niktestgmailcom.amocrm.ru';

	private static function setTargetModule(): void {
		self::$targetModule = TestModules::getTargetAmocrmModule(self::$amocrmCode, self::$clientId, self::$secretKey, self::$code);
	}

	private static function setTargetAccount(): void {
		$fromService = AccountsService::getSelf()->findAmocrmBySubdomain(self::$subdomain);
		self::$targetAccount = $fromService ?? self::createAccountDb();
	}

	private static function createAccountDb(): iAccount {
		return TestAccounts::createTargetAmocrmAccount(self::$subdomain, self::$accountId);
	}

	static function setUpBeforeClass(): void {
		TestFactory::initTest();
		parent::setUpBeforeClass();
		self::setTargetModule();
		self::setTargetAccount();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestAccounts::removeTestAccounts();
		TestModules::removeTestModules();
	}

	function testInstall(){
		$node = TestFactory::getNodesService()->amocrmInstall(self::$clientId, self::$referer, self::$oauthCode);
		$this->assertInstanceOf(iNode::class, $node);
		$this->checkLoaders($node);
	}

	private function checkLoaders(iNode $node): void {
		$this->assertEquals('works', $node->getLoaders()['status_name']);
	}

	function testRestQuery(){
		$node = TestFactory::getNodesService()->findPragmaNode(self::$targetModule->getCode(), self::$targetAccount->getPragmaAccountId());
		$account = $node->amocrmRestQuery('/api/v4/account', 'GET');
		$this->assertEquals($account['body']['id'], self::$accountId);
	}
}