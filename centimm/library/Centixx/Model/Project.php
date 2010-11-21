<?php
class Centixx_Model_Project extends Centixx_Model_Abstract
{
	protected $_resourceType = 'project';
	protected $_id;
	protected $_name;
	protected $_dateStart;
	protected $_dateEnd;

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
	 * @param Centixx_Model_User $manager
	 * @return Centixx_Model_Group provides fluent interface
	 */
	public function setManager(Centixx_Model_User $manager)
	{
		$this->_manager = $manager;
		return $this;
	}

	/**
	 * @return Centixx_Model_User
	 */
	public function getManager()
	{
		return $this->_manager;
	}
}