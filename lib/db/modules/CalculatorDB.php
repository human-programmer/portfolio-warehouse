<?php


namespace Generals;


use Configs\Configs;

trait CalculatorDB {

	static function getVirtualFormulasSchema () : string {
		return '`' . self::getCalculatorDb() . '`.`entities_formulas`';
	}

	static function getCustomFormulasSchema () : string {
		return '`' . self::getCalculatorDb() . '`.`formula`';
	}

	static function getCalculatorDb () : string {
		return Configs::getDbNames()->getCalculator();
	}
}