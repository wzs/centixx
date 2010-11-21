<?php
abstract class Centixx_Acl extends Zend_Acl
{

	//stałe odpowiadają roles.role_id w bazie danych
	const ROLE_GUEST 			= 	0;
	const ROLE_USER 			= 	1;
	const ROLE_ADMIN 			= 	2;
	const ROLE_GROUP_MANAGER 	= 	3;
	const ROLE_PROJECT_MANAGER 	= 	4;
	const ROLE_DEPARTMENT_CHIEF = 	5;
	const ROLE_HR 				= 	6;
	const ROLE_ACCOUNTANT 		= 	7;
	const ROLE_CEO 				= 	8;

	public function __construct()
	{
		$this->_initRules();
	}

	/**
	 * Definiuje "sztywne" reguły ACL'a
	 */
	abstract protected function _initRules();

	/**
	 * Definiuje i dodaje role (z hierarchią dziedziczenia) do ACL'a
	 * @return array<Zend_Acl_Role>
	 */
	protected function _initRoles()
	{
		//ugly, but fast
		$roles = array();
		for ($i=0; $i <= 8; $i++) {
			$roles[$i] = new Zend_Acl_Role($i);
		}

		//TODO sprawdzic czy to jest poprawne
		$this->addRole($roles[self::ROLE_GUEST]);
		$this->addRole($roles[self::ROLE_USER], $roles[self::ROLE_GUEST]);
		$this->addRole($roles[self::ROLE_ADMIN], $roles[self::ROLE_USER]);
		$this->addRole($roles[self::ROLE_GROUP_MANAGER], $roles[self::ROLE_USER]);
		$this->addRole($roles[self::ROLE_PROJECT_MANAGER], $roles[self::ROLE_GROUP_MANAGER]);
		$this->addRole($roles[self::ROLE_DEPARTMENT_CHIEF], $roles[self::ROLE_PROJECT_MANAGER]);
		$this->addRole($roles[self::ROLE_HR], $roles[self::ROLE_USER]);
		$this->addRole($roles[self::ROLE_ACCOUNTANT], $roles[self::ROLE_USER]);
		$this->addRole($roles[self::ROLE_CEO], $roles[self::ROLE_PROJECT_MANAGER]);

		return $roles;
	}
}