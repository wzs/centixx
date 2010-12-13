<?php
class Centixx_Model_Department extends Centixx_Model_Abstract
{
	protected $_resourceType = 'department';

	protected $_id;
	protected $_name;

	/**
	 * @var Centixx_Model_User
	 */
	protected $_manager;

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
	 * Zwraca listę projektow przypisanych do działu
	 * @return array<Centixx_Model_Project>
	 */
	public function getProjects($raw = false)
	{
		return $raw
			? $this->_projects
			: $this->_mapper->getRelatedSet($this, 'projects', 'Project', array('project_department = ?', $this->_id));
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


	public function __toString()
	{
		return (string)$this->id;
	}

}