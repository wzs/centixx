<?php
class Centixx_Model_Mapper_User extends Centixx_Model_Mapper_Abstract
{
	/**
	 * Zwraca listę użytkowników, którzy nie są przypisani do danej grupy
	 * @param Centixx_Model_Group $excludedGroup
	 * @return array<Centixx_Model_User>
	 */
	public function fetchUsersFromOtherGroups(Centixx_Model_Group $excludedGroup)
	{
		$query = $this->getDbTable()->select()
			->where('user_group != ?', $excludedGroup->id)
			->orWhere('user_group IS NULL')
			->order('user_name');
		return $this->_setArrayKeys($this->fetchAll($query));
	}

	/**
	 * Jeśli tablica zawiera obiektu modeli,
	 * to zostanie przetworzona tak, aby identyfikator glowny obiektu byl kluczem w tabeli
	 * @param array<Centixx_Model_Abastrac> $array
	 * @return array
	 */
	protected function _setArrayKeys($array)
	{
		$newArray = array();
		foreach ($array as $el) {
			$newArray[$el->id] = $el;
		}
		return $newArray;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, Zend_Db_Table_Row_Abstract $row)
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

}