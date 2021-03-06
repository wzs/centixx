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
		$this->addResource('department');

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
		$this->addResource('page-timesheetacc');
		$this->addResource('page-accounting');
		$this->addResource('page-permissions');
		$this->addResource('page-reports');
		$this->addResource('page-profile');

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

		$this->allow(self::ROLE_LOGGED_USER, 'page-profile');


		//widocznosc swoich grup dla menadzera projektu
		$this->allow(self::ROLE_PROJECT_MANAGER, 'page-groups');


		//widczonosc listy uzytkownika dla kadr
		$this->allow(self::ROLE_HR, 'page-users');

		$this->allow(self::ROLE_ACCOUNTANT, 'page-accounting');

		//widocznosc swoich projektow dla menadzera projektu
		$this->allow(self::ROLE_DEPARTMENT_CHIEF, 'page-projects');

		$this->allow(self::ROLE_ADMIN, 'page-admin');

		$this->allow(self::ROLE_USER, 'page-timesheet');

		$this->allow(self::ROLE_GROUP_MANAGER, 'page-timesheetacc');


		$this->allow(self::ROLE_CEO, 'page-permissions');

		$this->allow(self::ROLE_CEO, 'page-reports');
		$this->allow(self::ROLE_DEPARTMENT_CHIEF, 'page-reports');
		$this->allow(self::ROLE_PROJECT_MANAGER, 'page-reports');

	}
}