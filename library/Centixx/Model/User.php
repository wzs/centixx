<?php
class Centixx_Model_User extends Centixx_Model_Abstract implements Zend_Acl_Role_Interface
{
	const ACTION_ADD_CEO = 'add_ceo';

	protected $_resourceType = 'user';
	protected $_id;
	protected $_name;
	protected $_surname;
	protected $_password;
	protected $_email;
	protected $_address;

	/**
	 * @var string zahashowana postać hasła
	 */
	protected $_hashedPassword;

	/**
	 * @var array<Centixx_Model_Roles>
	 */
	protected $_roles;

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

	public function getId()
	{
		return $this->_id;
	}

	protected $_db;

	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->_db = Zend_Registry::get('db');
	}


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
		$config = Zend_Registry::get('config');
		$salt = $config['security']['passwordSalt'];

		$this->_password = $password;
		$this->hashedPassword = md5($salt . $password);

		return $this;
	}

	/**
	 * Zwraca zahaszowaną postać hasła
	 * @return string
	 */
	public function getHashedPassword()
	{
		return $this->_hashedPassword;
	}

	/**
	 * Ustawia zahashowaną postać hasla
	 * @param string $password
	 */
	public function setHashedPassword($password)
	{
		$this->_hashedPassword = $password;
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

	public function setAddress($address)
	{
		$this->_address = trim(strip_tags($address));
		return $this;
	}

	public function getAddress()
	{
		return $this->_address;
	}

	/**
	 * Zwraca numeryczną reprezentację roli,
	 * którą przyjął użytkownik w aktywnej sesji
	 *
	 * @return int
	 * @deprecated użytkownik może mieć kilka ról, zamiast tej metody należy użyć getRoles.
	 * Obecnie nie należy się na niej opierać, zwraca tylko jedną, "najwyższą" rolę
	 */
	public function getRole()
	{
		//		trigger_error('Należy użyć getRoles()!');
		return array_pop($this->roles);
	}

	/**
	 * Zwraca tekstowy opis roli użytkownika
	 * return string
	 * @deprecated nie należy tego używać, jako że user może mieć przypisanych kilka roli
	 */
	public function getRoleName()
	{
		//		trigger_error('Należy użyć getRoles()!');
		return $this->_mapper->getRoleName($this->getRole());
	}

	/**
	 * Zwraca listę roli które są przypisane użytkownikowi
	 */
	public function getRoles()
	{
		if ($this->_roles == null) {
			$this->_roles = Centixx_Model_Mapper_Role::factory()->fetchByUser($this);
		}
		return $this->_roles;
	}

	/**
	 * Ustawia dostępne dla użytkownika role
	 * @param array<Centixx_Model_Role> $roles
	 * @throws Centixx_Model_Exception
	 */
	public function setRoles($roles)
	{
		if (!is_array($roles)) {
			throw new Centixx_Model_Exception('Roles must be an array');
		}

		$this->_roles = array();
		foreach ($roles as $role) {
			if (!$role instanceof Centixx_Model_Role) {
				$role = new Centixx_Model_Role(array('id' => $role));
			}
			$this->_roles[$role->id] = $role;
		}
		return $this;
	}

	/**
	 * Sprawdza, czy użytkownik ma możliwość użycia roli
	 * @param int|Centixx_Model_Role $role
	 */
	public function hasRole($role)
	{
		if ($role instanceof Centixx_Model_Role) {
			$role = $role->id;
		}

		return array_key_exists($role, $this->getRoles());
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
		return $this->role->id;
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
	 * Sprawdza, czy użytkownik jest podwładnym wobec $superior
	 * @param Centixx_Model_User $superior
	 * @return bool
	 */
	public function isInferiorTo(Centixx_Model_User $user) {
		//CEO jest zwierzchnikiem wszystkich pracowników, poza innymi CEO
		if ($user->hasRole(Centixx_Acl::ROLE_CEO) && !$this->hasRole(Centixx_Acl::ROLE_CEO)) {
			return true;
		}

		//szef działu jest zwierzchnikiem ludzi pracujących w jego dziale
		if ($user->hasRole(Centixx_Acl::ROLE_DEPARTMENT_CHIEF) && $this->getProject() != null
		&& $this->getProject()->getDepartment()->getManager()->getId() == $user->id) {
			return true;
		}

		//szef projektu jest zwierzchnikiem ludzi pracujących w jego projekcie
		if ($user->hasRole(Centixx_Acl::ROLE_PROJECT_MANAGER) && $this->getProject() != null
		&& $this->getProject()->getManager()->getId() == $user->id) {
			return true;
		}

		//szef grupy jest zwierzchnikiem ludzi pracujących w jego grupie
		if ($user->hasRole(Centixx_Acl::ROLE_GROUP_MANAGER) && $this->getGroup() != null
		&& $this->getGroup()->getManager()->getId() == $user->id) {
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Centixx_Model_Abstract::_customAclAssertion()
	 */
	protected function _customAclAssertion($role, $privilage = null)
	{
		if ($role instanceof Centixx_Model_User) {
			//HR może edytować/dodawac profil użytkownika
			if ($role->hasRole(Centixx_Acl::ROLE_HR)) {

				//dla ustawienia CEO potrzebna jest cesja
				if ($privilage == self::ACTION_ADD_CEO) {
					return $role->hasPermission(self::ACTION_ADD_CEO) ? self::ASSERTION_SUCCESS : self::ASSERTION_FAILURE;
				}

				return self::ASSERTION_SUCCESS;
			}

			//przelozony moze ogladac profil podwladnego
			if ($privilage == self::ACTION_READ && $this->isInferiorTo($role)) {
				return self::ASSERTION_SUCCESS;
			}
		}
		return parent::_customAclAssertion($role, $privilage);
	}

	//TODO: model to nie miejsce na takie rzeczy
	public function generateTransaction($user, $date) {

		$datearray = split('-',$date);
		$month = $datearray[1];
		if ($month == 1) {
			$month = 12;
		}
		else{
			$month = $month - 1;
		}

		$result = $this->_db->query(
            "SELECT users.user_hour_rate AS hour_rate, SUM( timesheets.timesheet_hours ) AS time ".
				"FROM users, timesheets ".
				"WHERE timesheets.timesheet_user = ? ".
				"AND timesheets.timesheet_accepted = 1 ".
				"AND MONTH( timesheets.timesheet_date ) = ? ",
		array($user->getId(), $month)
		);



		if($row = $result->fetch()){

			$transactionAccount = $user->getAccount();
			$transactionValue = ($row->hour_rate) * ($row->time);
			$transactionTitle = 'Wynagrodzenie za miesiąc '.getMonthName($month);
			$transactionDate = $datearray[0]."-";
			$transactionDate = $transactionDate.$month;
			$transactionDate = $transactionDate."-";
			$transactionDate = $transactionDate."01";

			$result2 = $this->_db->query(
            "SELECT t.transaction_id ".
				"FROM transactions t ".
				"WHERE t.transaction_user = ? ".
				"AND t.transaction_date = ? ",
			array($user->getId(), $transactionDate)
			);

			if(!$row1 = $result2->fetch()){

				if($transactionValue > 0){

					$result1 = $this->_db->query(
	            "INSERT INTO transactions (transaction_user, transaction_account , transaction_value, ".
				"transaction_title, transaction_date) VALUES (?, ?, ?, ?, ? )",
					array($user->getId(), $transactionAccount, $transactionValue, $transactionTitle, $transactionDate)
					);
				}

			}

		}
		return $result1;
	}
}
