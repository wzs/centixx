<?php
abstract class Centixx_Model_Abstract implements Zend_Acl_Resource_Interface, Centixx_Model_Interface
{
	const CLASS_PATH_SEPARATOR = '_';
	const CLASS_PREFIX = 'Centixx_Model_';

	/**
	 * Dostep do zasobu nie powinien być zabroniony
	 * @var int
	 */
	const ASSERTION_FAILURE = -1;

	/**
	 * Nie rozstrzygnięto, czy udzielić dostępu do zasobu
	 * @var int
	 */
	const ASSERTION_INCONCLUSIVE = 0;

	/**
	 * Dostep do zasobu powinien być udzielony
	 * @var int
	 */
	const ASSERTION_SUCCESS = 1;

	const ACTION_CREATE 	= 'create';
	const ACTION_READ 		= 'read';
	const ACTION_UPDATE  	= 'update';
	const ACTION_DELETE 	= 'delete';

	/**
	 * @var Centixx_Model_Mapper_Abstract
	 */
	protected $_mapper = null;

	protected $_resourceType = null;

	public function __construct($options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}

		//ustawiam mappera
		$parts = explode(self::CLASS_PATH_SEPARATOR, get_class($this));
		$classShortName = $parts[count($parts) - 1];
		$this->_mapper = Centixx_Model_Mapper_Abstract::factory($classShortName);

	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (!method_exists($this, $method)) {
			throw new Centixx_Model_Exception('Invalid property');
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if (!method_exists($this, $method)) {
			throw new Centixx_Model_Exception('Invalid property');
		}
		return $this->$method();
	}

	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setId($id)
	{
		$validator = new Zend_Validate_Int();
		if ($validator->isValid($id)) {
			$this->_id = $id;
		} else {
			throw new Centixx_Model_Exception("Invalid property for id");
		}
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Utrwala obiekt w bazie danych przy pomocy mappera
	 * @return Centixx_Model_Abstract fluent interface
	 */
	public function save()
	{
		if ($this->_mapper == null) {
			throw new Exception('Mapper nie został ustawiony dla ' . get_class());
		}
		$this->_mapper->save($this);
		return $this;
	}

	/**
	 * Usuwa obiekt z bazy danych przy pomocy mappera
	 * @return Centixx_Model_Abstract fluent interface
	 */
	public function delete()
	{
		if ($this->_mapper == null) {
			throw new Exception('Mapper nie został ustawiony dla ' . get_class());
		}
		$this->_mapper->delete($this);
		return $this;
	}

	/**
	 * Zwraca tekstową reprezentację modelu
	 */
	public function __toString()
	{
		return $this->id;
	}

	/**
	 * Zwraca tablicową reprezentację istotnych parametrów modelu
	 *
	 * @return array
	 */
	public function toArray()
	{
		/**
		 * Enter Parametry, które nie powinny być uwzględniane w eksporcie modelu do tablicy
		 * @var array<string>
		 */
		$excluded = array('_mapper', '_resourceType', '_dateFormat');

		$tmp = array();
		foreach ($this as $key => $value) {
			if (!in_array($key, $excluded)) {
				//pomijam poczatkowy znak podkreslenia
				$propName = substr($key, 1);

				//wywoluje gettera
				$getterName = 'get' . camelCase($propName, true);
				if (method_exists($this, $getterName)) {
					$tmp[$propName] = call_user_method($getterName, $this);
				}
			}
		}
		return $tmp;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Acl/Resource/Zend_Acl_Resource_Interface::getResourceId()
	 */
	public function getResourceId()
	{
		return $this->_resourceType;
	}

	/**
	 *
	 * @param Zend_Acl_Role_Interface|null $role
	 * @param string|null $privilege
	 * @return int
	 */
	protected function _customAclAssertion($role, $privilage = null)
	{
		return self::ASSERTION_INCONCLUSIVE;
	}

	/**
	 * Sprawdza, czy podana rola ma dostep do tego obiektu
	 * z uwzglednieniem globalnej ACL jak i specyficznych warunków
	 *
	 * @param Zend_Acl_Role_Interface $role
	 * @param string|null $privilege
	 * @param Zend_Acl|null $acl
	 * @return bool
	 */
	public function isAllowed($role, $privilege = null, Zend_Acl $acl = null)
	{
		if ($acl == null) {
			$acl = Zend_Registry::getInstance()->get('Zend_Acl');
		}

		if (!$acl->has($this)) {
			$acl->addResource($this);
		}

		//domyślna rola
		if ($role == null) {
			$role = new Zend_Acl_Role(Centixx_Acl::ROLE_GUEST);
		}


		/*
		 * Reguły szczegółowe, zdefiniowane w modelu,
		 * są ważniejsze od reguł zdefiniowanych w ACL.
		 *
		 * Jeśli reguła szczegółowa nie rozstrzyga (zwraca ASSERTION_INCONCLUSIVE),
		 * to dopiero wtedy używane są reguły ogólne
		 */
		try {
			$result = $this->_customAclAssertion($role, $privilege);
			if ($result == self::ASSERTION_SUCCESS) {
				return true;
			} else if ($result == self::ASSERTION_FAILURE) {
				return false;
			}
		} catch (Centixx_Model_Exception $e) {
		}
		return $acl->isAllowed($role, $this, $privilege);
	}

	/**
	 * Zwraca kanoniczny adres URL do danego obiektu
	 * @param string $action akcja do wykonania - domyślnie wyświetlenie obiektu: 'show'
	 * @return string URL
	 */
	public function getUrl($actionName = 'show')
	{
		//odwoluje się do metody mappera, bo zwykłe get_class() nie zadziała w PHP < 5.3 (brak LSB)
		$controllerName = strtolower($this->_mapper->getModelName() . 's');
		return Zend_View_Helper_Url::url(array('controller' => $controllerName, 'action' => $actionName, 'id' => $this->id));
	}
}