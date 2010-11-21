<?php
/**
 * Definiuje ogólne reguły dostępu do zasobów rozumianych jako modele.
 * Dodatkowo, każdy model może mieć we własnym zakresie zdefiniowane reguły dostępu
 *
 * Sprawdzenie dostępu trzeba przeprowadzać ręcznie, poprzez wywołanie metody obiektu:
 * @example $resource->isAllowed($role, $privilage);
 * @author wzs
 */
class Centixx_Acl_ModelResource extends Centixx_Acl
{
	protected function  _initRules()
	{
		$roles 		= $this->_initRoles();
		$modules 	= $this->_initModelTypeResources();

		$this->deny(null, null);

		$this->deny(null, 'user', 'edit');
		$this->allow($roles[self::ROLE_HR], 'user', 'edit');

		$this->allow($roles[self::ROLE_USER], 'group', 'view');
		$this->allow($roles[self::ROLE_PROJECT_MANAGER], 'group', 'edit');


	}

	protected function _initModelTypeResources()
	{
		//wszystkie modele powinny tu zostac wymienione!
		//TODO może wygenerować Config na podstawie plików?
		$this->addResource('user');
		$this->addResource('group');
		$this->addResource('project');
	}
}