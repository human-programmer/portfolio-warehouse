<?php


namespace Generals;


use Configs\Configs;

trait CoreCRMDB
{
    static function getAccountsSchema() : string {
        return '`' . self::getCrmDb() . '`.`account`';
    }

    static function getCrmNamesSchema() : string {
        return '`' . self::getCrmDb() . '`.`crm_name`';
    }

    static function getAccountsValuesSchema() : string {
        return '`' . self::getCrmDb() . '`.`account_values`';
    }

    static function getEntitiesSchema() : string {
        return '`' . self::getCrmDb() . '`.`entities`';
    }

    static function getEntitiesToEntitiesSchema() : string {
        return '`' . self::getCrmDb() . '`.`entities_to_entities`';
    }

    static function getEntitiesToStatusSchema() : string {
        return '`' . self::getCrmDb() . '`.`entities_to_status`';
    }

    static function getEntitiesToUserSchema() : string {
        return '`' . self::getCrmDb() . '`.`entities_to_user`';
    }

    static function getGroupsSchema() : string {
        return '`' . self::getCrmDb() . '`.`entity_groups`';
    }

	static function getFieldsSchema() : string {
		return '`' . self::getCrmDb() . '`.`fields`';
	}

    static function getEnumValuesSchema() : string {
        return '`' . self::getCrmDb() . '`.`enum_values`';
    }

    static function getStringFieldsValuesSchema() : string {
        return '`' . self::getCrmDb() . '`.`string_values`';
    }

    static function getEnumsSchema() : string {
        return '`' . self::getCrmDb() . '`.`enums`';
    }

    static function getPipelinesSchema() : string {
        return '`' . self::getCrmDb() . '`.`pipelines`';
    }

    static function getStatusesSchema() : string {
        return '`' . self::getCrmDb() . '`.`statuses`';
    }

    static function getStatusesToPipelineSchema() : string {
        return '`' . self::getCrmDb() . '`.`statuses_to_pipeline`';
    }

    static function getStatusDurationSchema() : string {
        return '`' . self::getCrmDb() . '`.`status_duration`';
    }

    static function getOnOffCrmDatesSchema() : string {
        return '`' . self::getCrmDb() . '`.`on_off_dates`';
    }

    static protected function getCrmDb () : string {
        return Configs::getDbNames()->getCoreCrm();
    }

}