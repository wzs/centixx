<?php
class Centixx_Model_Transaction extends Centixx_Model_Abstract implements Zend_Acl_Role_Interface
{
	protected $_resourceType = 'transaction';
	protected $_id;
	protected $_account;
	protected $_value;
	protected $_title;
	protected $_date;
	protected $_user;

	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}
	
	
	
	public function setAccount($account)
	{
			$this->_account = $account;

		return $this;
	}

	public function getAccount()
	{
		return $this->_account;
	}
	
	
	
	public function setValue($value)
	{
		$this->_value = $value;
		return $this;
	}

	public function getValue()
	{
		return $this->_value;
	}
	
	
	
	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	
	
	public function setDate($date)
	{
		$this->_date = $date;
		return $this;
	}

	public function getDate()
	{
		return $this->_date;
	}
	
	public function setUser($user)
	{
		$this->_user = $user;
		return $this;
	}

	public function getUser()
	{
		return $this->_user;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Acl/Role/Zend_Acl_Role_Interface::getRoleId()
	 */
	public function getRoleId()
	{
		return $this->_role;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Centixx_Model_Abstract::_customAclAssertion()
	 */
    protected function _customAclAssertion($role, $privilage = null)
    {
		if ($role instanceof Centixx_Model_User) {
//			 //swój profil można edytować
//			if ($role->id == $this->_id) {
//				return self::ASSERTION_SUCCESS;
//			}

			//HR może edytować/dodawac profil użytkownika
			if ($role->getRole() == Centixx_Acl::ROLE_HR) {

				//dla ustawienia CEO potrzebna jest cesja
				if ($privilage == self::ACTION_ADD_CEO) {
					return $role->hasPermission(self::ACTION_ADD_CEO) ? self::ASSERTION_SUCCESS : self::ASSERTION_FAILURE;
				}

				return self::ASSERTION_SUCCESS;
			}

			//kierownik grupy moze ogladac profil podwladnego
			if ($privilage == self::ACTION_SHOW && $role->getRole() == Centixx_Acl::ROLE_GROUP_MANAGER
				&& $this->group->manager->id == $role->id) {
				return self::ASSERTION_SUCCESS;
			}

			//kierownik projektu moze ogladac profil podwladnego
			if ($privilage == self::ACTION_SHOW && $role->getRole() == Centixx_Acl::ROLE_PROJECT_MANAGER
				&& $this->group->project->manager->id == $role->id) {
				return self::ASSERTION_SUCCESS;
			}

			//TODO sprawdzic czy dziala prawidlowo po dodaniu modelu department
			//kierownik dzialu moze ogladac wszystkich swoich podwladnych
			if ($privilage == self::ACTION_SHOW && $role->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF
				&& in_array($this->role, array(Centixx_Acl::ROLE_USER, Centixx_Acl::ROLE_GROUP_MANAGER, Centixx_Acl::ROLE_PROJECT_MANAGER))) {
				return self::ASSERTION_SUCCESS;
			}

		}
		return parent::_customAclAssertion($role, $privilage);
    }
}