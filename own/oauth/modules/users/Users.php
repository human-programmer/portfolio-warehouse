<?php


namespace oAuth;



use JetBrains\PhpStorm\Pure;
use market\smsP;
use Services\General\iUser;
use Services\General\iUserToCreate;

class Users
{
    static function getUser(string $name, string $phone): iUser{
        $struct = new CreateUserStruct($name, $phone);
        $user = self::_getUser($struct);
        return $user ?? throw new \Exception("user not found");
    }

    static private function _getUser(iUserToCreate $struct): iUser|null{
        try {
            return Factory::getUsersService()->createUser($struct);
        } catch (\Exception $e) {
            return Factory::getUsersService()->findByPhone($struct->getPhone());
        }
    }


}