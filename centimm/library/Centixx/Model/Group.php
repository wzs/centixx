<?php
class Centixx_Model_Group extends Centixx_Model_Abstract
{
	protected $_id;
	protected $_name;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_manager;

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
	 * @param array<Centixx_Model_User> $users
	 * @return Centixx_Model_Group fluent interface
	 */
	public function setUsers($users)
	{
		$this->_users = array();
		foreach ($users as $user) {
			if ($user instanceof Centixx_Model_User) {
				$this->_users[$user->id] = $user;
			}
		}
		return $this;
	}

	/**
	 * Zwraca listę użytkowników przydzielonych do grupy
	 * @return array<Centixx_Model_User>
	 */
	public function getUsers($raw = false)
	{
		return $raw ?
			$this->_users :
			$this->_mapper->getRelatedSet($this, 'users', 'User', array('user_group = ?', $this->_id));
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
		$this->_manager = $manager;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getManager($raw = false)
	{
		return $raw ? $this->_manager : $this->_mapper->getRelated($this, 'manager', 'User');
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
		return $raw ? $this->_project : $this->_mapper->getRelated($this, 'project');
	}

	/**
	 * @param Centixx_Model_User $user
	 * @return bool zwraca true jesli podany uzytkownik jest szefem grupy
	 */
	public function isManager(Centixx_Model_User $user)
	{
		return $this->getManager() == $user;
	}

	public function __toString()
	{
		return $this->name;
	}
}