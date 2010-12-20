<?php
class Centixx_Model_Group extends Centixx_Model_Abstract
{
	protected $_resourceType= 'group';
	protected $_id;
	protected $_name;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_manager;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_oldManager;

	/**
	 * @var Centixx_Model_Project
	 */
	protected $_project;

	/**
	 * Użytkownicy przypisani do grupy
	 * @var array<Centixx_Model_User> $users
	 */
	protected $_users;

	/**
	 * Ustawia użytkowników dla grupy (kasuje poprzednią zawartość!)
	 * @param array<Centixx_Model_User> $users
	 * @return Centixx_Model_Group fluent interface
	 */
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
			$this->_users[$user->id] = $user;
		}
		return $this;
	}

	/**
	 * Przypisuje użytkownika do grupy
	 * Uwaga! Metoda natychmiast zapisuje stan uzytkownika!
	 * @param Centixx_Model_User $user
	 * @return Centixx_Model_Group fluent interface
	 * @deprecated
	 */
	public function addUser(Centixx_Model_User $user)
	{
		$user->setGroup($this)->save();
		return $this;
	}

	/**
	 * Zwraca listę użytkowników przydzielonych do grupy
	 * @return array<Centixx_Model_User>
	 */
	public function getUsers()
	{
		if ($this->_users == null) {
			$this->_users = $this->_mapper->getGroupUsers($this);
		}
		return $this->_users;
	}

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
	 * @param Centixx_Model_User $manager
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setManager($manager)
	{
		if ($manager == '') {
			return $this;
		}

		if (!$manager instanceof Centixx_Model_User) {
			$manager = new Centixx_Model_User(array('id' => $manager));
		}

		if ($this->manager) {
			$this->oldManager = $this->manager;
		}

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

	public function setOldManager($manager)
	{
		$this->_oldManager = $manager;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getOldManager()
	{
		return $this->_oldManager;
	}

	/**
	 * @param Centixx_Model_Project $project
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setProject($project)
	{
		$this->_project = $project;
		return $this;
	}

	/**
	 * @return Centixx_Model_Project
	 */
	public function getProject($raw = false)
	{
		return $raw ? $this->_project : $this->_mapper->getRelated($this, 'project', 'Project');
	}

	/**
	 * @param Centixx_Model_User $user
	 * @return bool zwraca true jesli podany uzytkownik jest szefem grupy
	 */
	public function isManager(Centixx_Model_User $user)
	{
		return $this->getManager()->id == $user->id;
	}

	public function __toString()
	{
		return (string)$this->name;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Centixx_Model_Abstract::_customAclAssertion()
	 */
	protected function _customAclAssertion($role, $privilage = null)
	{

		if ($role instanceof Centixx_Model_User) {

			//kierownik projektu moze dowolnie zmieniac grupy nalezace do jego projektu
			if ($role->hasRole(Centixx_Acl::ROLE_PROJECT_MANAGER) && $this->project->manager->id == $role->id) {
				return self::ASSERTION_SUCCESS;
			}
		}
		return parent::_customAclAssertion($role, $privilage);
	}
}