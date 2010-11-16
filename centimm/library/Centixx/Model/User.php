<?php
class Centixx_Model_User extends Centixx_Model_Abstract
{
	protected $_id;
	protected $_name;
	protected $_surname;
	protected $_password;
	protected $_email;
	protected $_role;

	/**
	 * @var Centixx_Model_Group|int
	 */
	protected $_group;

	/**
	 * Stawka godzinowa
	 * @var float
	 */
	protected $_hour_rate;

	/**
	 * Numer konta
	 * @var int
	 */
	protected $_account;

	public function setName($name)
	{
		if (is_string($name)) {
			$this->_name = $name;
		} else {
			throw new Centixx_Model_Exception("Invalid property for name");
		}
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setSurname($name)
	{
		if (is_string($name)) {
			$this->_surname = $name;
		} else {
			throw new Centixx_Model_Exception("Invalid property for surname");
		}
		return $this;
	}
	public function getSurname()
	{
		return $this->_surname;
	}

	public function setEmail($email)
	{
		$validator = new Zend_Validate_EmailAddress();
		if ($validator->isValid($email)) {
			$this->_email = $email;
		} else {
			throw new Centixx_Model_Exception("Invalid property for email");
		}
		return $this;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setRole($roleId)
	{
		$this->_role = $roleId;
		return $this;
	}

	public function getRole()
	{
		return $this->_role;
	}

	public function setGroup($group)
	{
		$this->_group = $group;
		return $this;
	}

	/**
	 * Zwraca grupe w ktorej pracuje uzytkownik
	 * @return Centixx_Model_Group
	 * @param bool $raw - czy ma byc pominiete ładowanie zewnetrznego obiektu
	 */
	public function getGroup($raw = false)
	{
		return $raw ? $this->_group : $this->_mapper->getRelated($this, 'group', 'Group');
	}

	public function profileHref()
	{
		//TODO sprawdzic jak uzywac helperow z zewnatrz
		$helper = new Zend_View_Helper_Url();
		return $helper->url(array('controller' => 'users', 'action' => 'show', 'id' => $this->id));
	}

	public function __toString()
	{
		return $this->getName() . ' ' . $this->getSurname();
	}


}