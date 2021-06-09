<?php


namespace Services\General;


interface iUsersService {
	function createUser(iUserToCreate $user): iUser;
	function findByPhone(string $phone): iUser|null;
	function findByEmail(string $email): iUser|null;
	function findByPragmaId(string $id): iUser|null;
}