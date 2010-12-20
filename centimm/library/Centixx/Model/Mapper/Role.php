<?php
class Centixx_Model_Mapper_Role extends Centixx_Model_Mapper_Abstract
{
	/**
	 * Zwraca listę roli przypisanych danemu użytkownikowi
	 * @param Centixx_Model_User $model
	 * @return array<Centixx_Model_Role>
	 */
	public function fetchByUser(Centixx_Model_User $model)
	{
		$query = $this->getDbTable()
			->select()
			->from(array('u' => 'users'), null)
			->joinLeft(array('ur' => 'users_roles'), 'ur.user_id = u.user_id', null)
			->join(array('r' => 'roles'), 'r.role_id = ur.role_id')
			->where('u.user_id = ?', array($model->id))
		;
		return $this->_fetchAll($query);
	}

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->role_id)
			->setName($row->role_name)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'role_name'	=> $model->name,
		);

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
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Role
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Role();
	}
}