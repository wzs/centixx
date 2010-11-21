<?php
class Centixx_Model_Project extends Centixx_Model_Abstract
{
	protected $_resourceType = 'project';
	protected $_id;
	protected $_name;
	
	/**
	 * Lista wszystkich pracowników przypisanych do projektu.
	 * LazyLoading!
	 * @var array<Centixx_Model_User>
	 */
	protected $_users = array();
	
	/**
	 * @var Zend_Date
	 */
	protected $_dateStart;

	/**
	 * @var Zend_Date
	 */
	protected $_dateEnd;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_manager;

	protected $_groups;

	/**
	 * @param string $name
	 * @throws Centixx_Model_Exception
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setName($name)
	{
		if (is_string($name)) {
			$this->_name = $name;
		} else {
			throw new Centixx_Model_Exception("Invalid property for name");
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Zwraca listę grup przypisanych do projektu
	 * @return array<Centixx_Model_Group>
	 */
	public function getGroups($raw = false)
	{
		return $raw 
			? $this->_groups
			: $this->_mapper->getRelatedSet($this, 'groups', 'Group', array('group_project = ?', $this->_id));
	}

	/**
	 * Ustawia grupy przypisane do projektu (kasuje poprzednią zawartość!)
	 * @param array<Centixx_Model_Group> $groups
	 * @return Centixx_Model_Project fluent interface
	 */
	public function setGroups($groups)
	{
		$this->_groups = array();
		foreach ($groups as $group) {
			if ($group instanceof Centixx_Model_Group) {
				$this->_groups[$group->id] = $group;
			}
		}
		return $this;
	}

	/**
	 * @param Centixx_Model_User|int $manager
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setManager($manager)
	{
		$this->_manager = $manager;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getManager($raw = false)
	{
		$ret = $raw
		? $this->_manager
		: $this->_mapper->getRelated($this, 'manager', 'User');
		return $ret;
	}

	public function setDateStart($dateStart)
	{
		if (!$dateStart instanceof Zend_Date) {
			$dateStart = new Zend_Date($dateStart, Zend_Date::ISO_8601);
		}

		$this->_dateStart = $dateStart;
		return $this;
	}

	public function getDateStart($format = null)
	{
		return $this->_dateStart;
	}

	public function setDateEnd($dateEnd)
	{
		if (!$dateEnd instanceof Zend_Date) {
			$dateEnd = new Zend_Date($dateEnd, Zend_Date::ISO_8601);
		}
		$this->_dateEnd = $dateEnd;
		return $this;
	}

	public function getDateEnd($format = null)
	{
		return $this->_dateEnd;
	}
	
	/**
	 * Zwraca listę wszystkich pracowników pracujących w projekcie
	 * @return array<Centixx_Model_User>
	 */
	public function getUsers()
	{
		if ($this->_users == null) {
			// zamiast wersji iterującej po wszystkich grupach,
			// wersja o nizszej zlozonosci obliczeniowej, oparta o jedno zapytanie sql
			/*
			$groups = $this->getGroups();
			foreach ($groups as $group) {
				$this->_users += $group->getUsers();
			}
			*/
			$this->_users = $this->_mapper->getProjectUsers($this);
		}
		return $this->_users;
	}
	
	/**
	 * Przypisuje grupę do projektu
	 * Uwaga! Metoda natychmiast zapisuje stan grupy!
	 * @param Centixx_Model_Group $user
	 * @return Centixx_Model_Project fluent interface
	 */
	public function addGroup(Centixx_Model_Group $group)
	{
		$group->setProject($this)->save();
		return $this;
	}

	public function __toString()
	{
		return $this->name;
	}
	
    protected function _customAclAssertion($role, $privilage = null)
    {
		return self::ASSERTION_INCONCLUSIVE;
    }
}