<?php
class Centixx_Model_Project extends Centixx_Model_Abstract
{
	protected $_resourceType = 'project';

	/**
	 * W jaki sposób ma być formatowana data
	 * @var string
	 */
	protected $_dateFormat = 'Y-MM-dd';

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

	/**
	 * @var Centixx_Model_Department
	 */
	protected $_department;

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
	 * @return Centixx_Model_Project provides fluent interface
	 */
	public function setManager($manager)
	{
		if ($manager != '')
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

	/**
	 * @param Centixx_Model_Department|int $department
	 * @return Centixx_Model_Project provides fluent interface
	 */
	public function setDepartment($department)
	{
		$this->_department = $department;
		return $this;
	}

	/**
	 * @return Centixx_Model_Department
	 */
	public function getDepartment($raw = false)
	{
		$ret = $raw
			? $this->_department
			: $this->_mapper->getRelated($this, 'department', 'Department');
		return $ret;
	}

	public function setDateStart($dateStart)
	{
		if (!$dateStart instanceof Zend_Date) {
			//parsowanie daty w formie yyyy-mm-dd
			$tmp = explode('-', $dateStart);
			$dateStart = new Zend_Date(array('year' => $tmp[0], 'month' => $tmp[1], 'day' => $tmp[2]));
		}

		$this->_dateStart = $dateStart;
		return $this;
	}

	/**
	 * Zwraca datę rozpoczęcia projektu
	 * @param bool $raw czy ma być zwrócone jako Zend_Date
	 * @return Zend_Date|string
	 */
	public function getDateStart($format = false)
	{
		if (!$format) {
			$format = $this->_dateFormat;
		}
		return $this->_dateStart->toString($format);
	}

	public function setDateEnd($dateEnd)
	{
		if (!$dateEnd instanceof Zend_Date) {
			//parsowanie daty w formie yyyy-mm-dd
			$tmp = explode('-', $dateEnd);
			$dateEnd = new Zend_Date(array('year' => $tmp[0], 'month' => $tmp[1], 'day' => $tmp[2]));
		}
		$this->_dateEnd = $dateEnd;
		return $this;
	}

	/**
	 * Zwraca datę zakonczenia projektu
	 * @param bool $raw czy ma być zwrócone jako Zend_Date
	 * @return Zend_Date|string
	 */
	public function getDateEnd($raw = false)
	{
		return $raw ? $this->_dateEnd : $this->_dateEnd ? $this->_dateEnd->toString($this->_dateFormat) : null;
	}

	/**
	 * Zwraca listę wszystkich pracowników pracujących w projekcie
	 * @return array<Centixx_Model_User>
	 */
	public function getUsers()
	{
		if ($this->_users == null) {
			$this->_users = $this->_mapper->getProjectUsers($this);
		}
		return $this->_users;
	}

	/**
	 * Set users
	 * @param Centixx_Model_User $users
	 * @throws Centixx_Model_Exception
	 */
	public function setUsers($users)
	{
		if (!is_array($users)) {
			throw new Centixx_Model_Exception('Users must be an array');
		}

		$this->_users = array();
		foreach ($users as $user) {
			if (!$user instanceof Centixx_Model_User) {
				$user = new Centixx_Model_User(array('id' => $user));
			}
			$this->_users[] = $user;
		}
		return $this;
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
		return (string)$this->name;
	}

    protected function _customAclAssertion($role, $privilage = null)
    {
    	if ($role instanceof Centixx_Model_User) {

    		if ($role->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF) {

    			//kierownik działu ma dostep do wszelkich działan na projekcie nalezacym do jego dzialu
    			if ($this->department->manager->id == $role->id) {
					return self::ASSERTION_SUCCESS;
    			}
			}

    	}

    	return parent::_customAclAssertion($role, $privilage);
    }
}