<?php
class Centixx_Model_Mapper_User extends Centixx_Model_Mapper_Abstract
{

	/**
	 * Zwraca pełną nazwę roli użytkownika
	 * @param Centixx_Model_User $model
	 * @return string
	 */
	public function getRoleName($model)
	{
		//nie powinno mieć miejsca
		if ($model == null) {
			return 'Gość';
		}
		return Centixx_Model_Mapper_Role::factory()->find($model->id)->name;
	}

	/**
	 * Zwraca użytkownika na podstawie adresu email
	 * @param string $email
	 * @return Centixx_Model_Abstract
	 */
	public function findByEmail($email)
	{
		return $this->findByField($email, 'user_email');
	}

	/**
	 * Zwraca listę wszystkich użytkowników pełniacych daną rolę
	 * @param int $roleId
	 * @return array<Centixx_Model_User>
	 */
	public function fetchUsersByRole($roleId)
	{
		$query = $this->getDbTable()
			->select()
			->from(array('r' => 'roles'), null)
			->joinLeft(array('ur' => 'users_roles'), 'ur.role_id = r.role_id', null)
			->join(array('u' => 'users'), 'u.user_id = ur.user_id')
			->where('r.role_id = ?', $roleId)
		;
		return $this->_fetchAll($query);
	}

	/**
	 * Pobiera liste uzytkowników do wyswietlenia na liscie do projektu
	 * (nalezacych do danego projektu - jesli podany, oraz nieprzydzielonych do zadnych projektów)
	 * @param Centixx_Model_Project|null $project
	 */
	public function fetchForProject($project = null)
	{

		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('ur' => 'users_roles'))
			->join(array('u' => 'users'), 'u.user_id = ur.user_id')
			->where("ur.role_id = ?", array(Centixx_Acl::ROLE_USER))
			->where("u.user_project IS NULL")
		;

		if ($project instanceof Centixx_Model_Project) {
			$query = $query->orWhere('u.user_project = ?', array($project->id));
		}

		return $this->_fetchAll($query, null, $adapter);
	}

	/**
	 * Zwraca liste uzytkowników nie przypisanych do zadnej grupy (ale przypisanych do tego samego projektu)
	 * + uzytkowników z tej samej grupy
	 * @param Centixx_Model_Group $group
	 */
	public function fetchForGroup(Centixx_Model_Group $group )
	{
		if (!$group->id) {
			$group->id = 0;
		}

		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('u' => 'users'))
			->where("user_role = ".Centixx_Acl::ROLE_USER." OR (user_role = ".Centixx_Acl::ROLE_GROUP_MANAGER." AND user_group = ".$group->id.") ")
			->where("user_project = ?", array($group->project->id))
			->where('user_group IS NULL OR user_group = ?', array($group->id));

		return $this->_fetchAll($query, null, $adapter);
	}

	/**
	 * Zwraca listę wszystkich użytkowników, których można przypisać do grupy
	 * w szczególności nie są zwracanie użytkownicy już przypisani do danej grupy,
	 * kierownicy innych grup ani użytkownicy przypisani do innych stanowisk
	 *
	 * @param Centixx_Model_Group $excludedGroup
	 * @return array<Centixx_Model_User>
	 * @deprecated używać fetchForGroup()
	 */
	public function fetchAvailableUsers($excludedGroup = null)
	{
		if ($excludedGroup == null) {
			$excludedGroup = 0;
		} else {
			$excludedGroup = $excludedGroup->id;
		}

		/*
		 * uzywana jest taka konstrukcja, bo chce wykonac proste
		 * zapytanie sql z joinami bez posredniego udzialu Zend_Db_Table
		 */
		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('u' => 'users'))
			->joinLeft(array('g' => 'groups'), 'u.user_id = g.group_manager', array())
			->where('g.group_id IS NULL') // kierownicy innych zespolow
			->where('user_group != ? OR user_group IS NULL', $excludedGroup) //uzytkownicy juz przypisani do tej grupy
			->where("user_role IN (".Centixx_Acl::ROLE_USER.", ".Centixx_Acl::ROLE_GROUP_MANAGER.") ") //tylko code-monkeys
			->order('user_name')
		;
		return $this->_fetchAll($query, null, $adapter);
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->user_id)
			->setEmail($row->user_email)
			->setName($row->user_name)
			->setSurname($row->user_surname)
			->setHashedPassword($row->user_password)
			->setGroup($row->user_group)
			->setProject($row->user_project)
			->setHourRate($row->user_hour_rate)
			->setAccount($row->user_account)
			->setAddress($row->user_address)
		;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::save()
	 */
	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'user_email'		=> $model->email,
			'user_name'			=> $model->name,
			'user_surname'		=> $model->surname,
			'user_hour_rate'	=> $model->hourRate,
			'user_group'		=> $this->_findId($model->group),
			'user_project'		=> $this->_findId($model->project),
			'user_account'		=> $model->account,
			'user_address'		=> $model->address,
		);

		if ($model->password != null) {
			$config = Zend_Registry::get('config');
			$data['user_password'] = md5($config['security']['passwordSalt'] . $model->password);
		}

		$table = $this->getDbTable();
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);
			$table->update($data, $where);
		} else {
			$model->id = $table->insert($data);
		}
		$this->_updateRoles($model);

		return $this;
	}

	protected function _updateRoles(Centixx_Model_User $model)
	{
		if (!count($model->getRoles())) {
			return;
		}

		$a = array();
		$insertValues = array();
		foreach ($model->getRoles() as $role) {
			$a[$role->id] = $role->id;
			$insertValues[] = "({$model->id}, {$role->id})";
		}

		$in = join(',', $a);
		$adapter = $this->getDbTable()->getAdapter()->query("DELETE FROM `users_roles` WHERE `user_id` = ?",
			array($model->id));

		$adapter = $this->getDbTable()->getAdapter()->query("INSERT INTO users_roles (user_id, role_id) VALUES " . join(', ', $insertValues));
	}

	/**
	 * @return Centixx_Model_Mapper_User
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_User();
	}

}