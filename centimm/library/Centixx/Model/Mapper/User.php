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
	 * Zwraca listę wszystkich użytkowników, którzy nie są przypisani do danej grupy
	 * UWAGA: wbrew temu co sugeruje nazwa - zwraca tez użytkownikow przypisanych do innych grup!
	 *
	 * @param Centixx_Model_Group $excludedGroup
	 * @return array<Centixx_Model_User>
	 */
	public function fetchUngroupedUsers(Centixx_Model_Group $excludedGroup)
	{
		/*
		 * uzywana jest taka konstrukcja, bo nie chce wykonac proste
		 * zapytanie sql z joinami bez posredniego udzialu Zend_Db_Table
		 *
		 * wersja z Zend_Db_Table - wykomentowana niżej
		 */

		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('u' => 'users'))
//			->joinLeft(array('g' => 'groups'), 'u.user_id = g.group_manager', array())
//			->where('g.group_id IS NULL') // nie sa zwracani kierownicy innych zespolow
			->where('user_group != ? OR user_group IS NULL', $excludedGroup->id)
			->order('user_name')
		;
		return $this->_fetchAll($query, null, $adapter);
	}
	/*
	public function fetchUngroupedUsers(Centixx_Model_Group $excludedGroup)
	{
		$query = $this->getDbTable()->select()
			->where('user_group != ?', $excludedGroup->id)
			->orWhere('user_group IS NULL')
			->order('user_name');
		return $this->fetchAll($query);
	}
	*/

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

		$table = $this->_dbTable;
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);
			$table->update($data, $where);
		} else {
			$table->insert($data);
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