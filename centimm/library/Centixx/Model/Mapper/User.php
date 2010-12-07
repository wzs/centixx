<?php
class Centixx_Model_Mapper_User extends Centixx_Model_Mapper_Abstract
{

	/**
	 * Zwraca pełną nazwę roli użytkownika
	 * @param Centixx_Model_User $model
	 * @return string
	 */
	public function getRoleName(Centixx_Model_User $model)
	{
		//nie powinno mieć miejsca
		if ($model->role == null) {
			return 'Gość';
		}
		return Centixx_Model_Mapper_Role::factory()->find($model->role)->name;
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
	 * Zwraca listę wszystkich użytkowników, których można przypisać do grupy
	 * w szczególności nie są zwracanie użytkownicy już przypisani do danej grupy,
	 * kierownicy innych grup ani użytkownicy przypisani do innych stanowisk
	 *
	 * @param Centixx_Model_Group $excludedGroup
	 * @return array<Centixx_Model_User>
	 */
	public function fetchAvailableUsers(Centixx_Model_Group $excludedGroup)
	{
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
			->where('user_group != ? OR user_group IS NULL', $excludedGroup->id) //uzytkownicy juz przypisani do tej grupy
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
		->setRole($row->user_role)
		->setGroup($row->user_group)
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
			'user_role'			=> $model->role,
			'user_group'		=> $this->_findId($model->group),
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
		return $this;
	}

	/**
	 * @return Centixx_Model_Mapper_User
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_User();
	}

}