<?php
class Centixx_Model_User extends Centixx_Model_Abstract implements Zend_Acl_Role_Interface
{
	protected $_resourceType = 'user';
	protected $_id;
	protected $_name;
	protected $_surname;
	protected $_password;
	protected $_email;
	protected $_role = 1;

	const ACTION_ADD_CEO = 'add_ceo';

	/**
	 * @var Centixx_Model_Group|int
	 */
	protected $_group;

	/**
	 * @var Centixx_Model_Project|int
	 */

	protected $_project;


	/**
	 * Stawka godzinowa
	 * @var float
	 */
	protected $_hourRate;

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

	public function getPassword()
	{
		return $this->_password;
	}

	public function setPassword($password)
	{
		$this->_password = $password;
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
		if ($roleId != '') {
			$this->_role = $roleId;
		}
		return $this;
	}

	public function getRole()
	{
		return $this->_role;
	}

	public function getRoleName()
	{
		return $this->_mapper->getRoleName($this);
	}

	public function setProject($project)
	{
		$this->_project = $project;
		return $this;
	}

	/**
	 * Zwraca projekt w ktorym pracuje uzytkownik
	 * @return Centixx_Model_Project
	 * @param bool $raw - czy ma byc pominiete ładowanie zewnetrznego obiektu
	 */
	public function getProject($raw = false)
	{
		return $raw ? $this->_project : $this->_mapper->getRelated($this, 'project', 'Project');
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

	public function getAccount()
	{
		return $this->_account;
	}

	/**
	 * @param int $account
	 */
	public function setAccount($account)
	{
		$this->_account = $account;
		return $this;
	}

	public function setHourRate($hourRate)
	{
		$this->_hourRate = $hourRate;
		return $this;
	}


	public function getHourRate()
	{
		return $this->_hourRate;
	}

	public function __toString()
	{
		return $this->getName() .  ' ' . $this->getSurname();
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
	 * Sprawdza, czy użytkownik ma aktywne, nadane uprawnienia na wykonanie danego typu akcji
	 * Jeśli tak, to zwraca ich listę
	 *
	 * @param string $permissionType
	 * @return array<Centixx_Model_Permission>
	 */
	public function hasPermission($permissionType = null)
	{
		$p = Centixx_Model_Mapper_Permission::factory()->getPermissions($this, $permissionType);
		return $p;
	}

	/**
	 * Zmienijsza liczbe / usuwa zezwolenie
	 * @param string $permissionType
	 */
	public function removePermission($permissionType)
	{
		Centixx_Model_Mapper_Permission::factory()->removePermissions($this, $permissionType);
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
			if ($privilage == self::ACTION_READ && $role->getRole() == Centixx_Acl::ROLE_GROUP_MANAGER
				&& $this->group->manager->id == $role->id) {
				return self::ASSERTION_SUCCESS;
			}

 			//kierownik projektu moze ogladac profil podwladnego
			if ($privilage == self::ACTION_READ && $role->getRole() == Centixx_Acl::ROLE_PROJECT_MANAGER
				&& $this->group->project->manager->id == $role->id) {
				return self::ASSERTION_SUCCESS;
			}

			//TODO sprawdzic czy dziala prawidlowo po dodaniu modelu department
			//kierownik dzialu moze ogladac wszystkich swoich podwladnych
			if ($privilage == self::ACTION_READ && $role->getRole() == Centixx_Acl::ROLE_DEPARTMENT_CHIEF
				&& in_array($this->role, array(Centixx_Acl::ROLE_USER, Centixx_Acl::ROLE_GROUP_MANAGER, Centixx_Acl::ROLE_PROJECT_MANAGER))) {
				return self::ASSERTION_SUCCESS;
			}

		}
		return parent::_customAclAssertion($role, $privilage);
    }
}