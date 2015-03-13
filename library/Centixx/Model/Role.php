<?php
class Centixx_Model_Role extends Centixx_Model_Abstract
{
	protected $_resourceType= 'role';
	protected $_id;
	protected $_name;

	/**
	 * @param string $name
	 * @throws Centixx_Model_Exception
	 * @return Centixx_Model_Role provides fluent interface
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

	public function __toString()
	{
		return $this->name;
	}
}