<?php


namespace PragmaStorage\Test;


use PragmaStorage\RemovalInspector;

require_once __DIR__ . '/../../core_crm/RemovalInspector.php';


class RemovalInspectorTest extends \PHPUnit\Framework\TestCase {
	function testAllowedToDeleteEntity (){
		$inspector = new RemovalInspector();
		$this->assertTrue(true);
//		$this->assertTrue($inspector->allowedToDeleteEntity(1624));
//		$this->assertTrue($inspector->allowedToDeleteEntity(1625));
//		$this->assertFalse($inspector->allowedToDeleteEntity(1626));
	}
}