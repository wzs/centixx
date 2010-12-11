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
		$this->_initRoles();
		$this->_initModelTypeResources();

		//odcinamy dostep do wszystkiego
		//każdy model sam określa zasady dostępu do niego w metodzie _customAssertion()
		$this->deny(null, null);

//		$this->deny(null, 'user', 'edit');
//		$this->allow($roles[self::ROLE_HR], 'user', 'edit');
//
//		$this->allow($roles[self::ROLE_USER], 'group', 'view');
//		$this->allow($roles[self::ROLE_PROJECT_MANAGER], 'group', 'edit');
//
//		$this->allow($roles[self::ROLE_PROJECT_MANAGER], 'project', 'view');
//		$this->allow($roles[self::ROLE_PROJECT_MANAGER], 'project', 'edit');


		$this->_initPageAccessRules();
	}

	/**
	 * Inicjowanie wszystkich modeli obiektowych używanych w serwisie
	 */
	protected function _initModelTypeResources()
	{
		//TODO może wygenerować Config na podstawie plików?
		$this->addResource('user');
		$this->addResource('group');
		$this->addResource('project');
	}

	/**
	 * Inicjuje zasoby reprezentujące strony dostępne przez menu.
	 * Potrzebne <strong>wyłacznie</strong> do renderowania odpowiedniego menu
	 * przez Zend_Navigation
	 */
	protected function _initPageResources()
	{
		//zasoby reprezentujące dostęp do podstron
		$this->addResource('page-groups');
		$this->addResource('page-projects');
		$this->addResource('page-users');
		$this->addResource('page-admin');
		$this->addResource('page-login');
		$this->addResource('page-logout');
		$this->addResource('page-index');
		$this->addResource('page-timesheet');
		$this->addResource('page-permissions');

	}

	/**
	 * Inicjuje reguły dostępu dla kontrolerów (bez akcji)
	 *
	 * na potrzeby min. Zend_Navigation
	 */
	protected function _initPageAccessRules()
	{
		$this->_initPageResources();

		$this->allow(null, 'page-index');

		//widocznosc stron logowania / wylogowania
		$this->allow(null, 'page-login');
		$this->deny(self::ROLE_LOGGED_USER, 'page-login');

		$this->deny(null, 'page-logout');
		$this->allow(self::ROLE_LOGGED_USER, 'page-logout');

		//widocznosc swoich grup i uzytkownikow dla menadzera
		$this->allow(self::ROLE_GROUP_MANAGER, 'page-groups');
		$this->allow(self::ROLE_GROUP_MANAGER, 'page-users');

		//widczonosc listy uzytkownika dla kadr
		$this->allow(self::ROLE_HR, 'page-users');

		//widocznosc swoich projektow dla menadzera projektu
		$this->allow(self::ROLE_PROJECT_MANAGER, 'page-projects');

		$this->allow(self::ROLE_ADMIN, 'page-admin');

		$this->allow(self::ROLE_USER, 'page-timesheet');


		$this->allow(self::ROLE_CEO, 'page-permissions');

	}
}