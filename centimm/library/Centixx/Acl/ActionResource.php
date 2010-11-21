<?php
/**
 * Definiuje reguły dostępu dla zasobów rozumianych jako akcje w kontrolerach
 * @author wzs
 *
 */
class Centixx_Acl_ActionResource extends Centixx_Acl
{
	/**
	 * znak oddzielajacy w nazwie zasobu kontroler od akcji
	 * @var string
	 */
	const SEPARATOR = ':';

	/**
	 * Czy ma być dozwolony dostęp w przypadku, gdy zasób nie został jawnie dodany do ACL'a
	 * UWAGA: aby zdefiniować regułę dostępu do zasobu, trzeba go najpierw zainicjować w _initModuleResources()
	 * @var bool
	 */
	const DEFAULT_ALLOW = true;

	/**
	 * Definuje i dodaje do ACL'a zasoby rozumiane jako akcje w kontrolerach
	 * @return array<Zend_Acl_Resource>
	 */
	protected function _initModuleResources()
	{
		//TODO wygenerowanie tablicy na podstawie pliku i zapisanie do configu
		//statyczna lista modułów i akcji

		/**
		 * Tablica służąca do wygenerowania dostępnych zasobów.
		 * Kluczami są nazwy kontrolerów, a wartościami: tablice z nazwami akcji
		 * @var array
		 */
		$controllers = array(
			'auth' 		=> array('index', 'logout'),
			'error'		=> array('error'),
			'groups' 	=> array('index', 'show', 'add_user', 'edit', 'delete'),
			'index'		=> array('index'),
			'users' 	=> array('index', 'show', 'edit'),
		);

		//inicjuję zasoby dla kazdej kombinacji
		$resources = array();
		foreach ($controllers as $controller => $actions) {
			foreach ($actions as $action) {
				$resName = $controller . self::SEPARATOR . $action;
				$res = new Zend_Acl_Resource($resName);
				$resources[$resName] = $res;
				$this->addResource($res);
			}
		}
		return $res;
	}

	protected function  _initRules()
	{
		$roles 		= $this->_initRoles();
		$modules 	= $this->_initModuleResources();

		$this->deny(null, null);

		$this->allow(null, 'index:index');

		$this->allow(null, 'error:error');
		$this->allow(self::ROLE_USER, 'groups:index');
		$this->allow(self::ROLE_USER, 'groups:show');
		$this->allow(self::ROLE_USER, 'users:index');
		$this->allow(self::ROLE_USER, 'users:show');

		$this->allow(self::ROLE_PROJECT_MANAGER, 'groups:edit');
		$this->allow(self::ROLE_PROJECT_MANAGER, 'groups:delete');
		$this->allow(self::ROLE_PROJECT_MANAGER, 'groups:add_user');


		$this->allow(self::ROLE_USER, 'users:edit'); //tmp


		$this->allow(null, 'auth:index');
		$this->deny(self::ROLE_USER, 'auth:index');

		$this->deny(null, 'auth:logout');
		$this->allow(self::ROLE_USER, 'auth:logout');
	}
}