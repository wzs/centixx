<?php
class Centixx_Model_Mapper_Permission extends Centixx_Model_Mapper_Abstract
{

	/**
	 * Zwraca aktywne, nadane danemu użytkownikowi uprawnienie danego typu (jeśli takie istnieje)
	 * @param Centixx_Model_User $user
	 * @param string $permissionType
	 * @return array<Centixx_Model_Permission>
	 */
	public function getPermissions(Centixx_Model_User $user, $permissionType)
	{
		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('p' => 'permissions'))
			->where('permission_to = ?', $user->id)
			->where('permission_type = ?', $permissionType)
			->where('permission_count > 0')
			->where('NOW() BETWEEN permission_starts AND permission_ends')
		;
		return $this->_fetchAll($query, null, $adapter);
	}

	/**
	 * W zaleznosci od wartosci count w permission - zmniejsza ja, badz usuwa rekord z bazy
	 * w zwiazku ze "zużyciem" danego uprawnienia
	 * @param Centixx_Model_User $user
	 * @param string $permissionType
	 */
	public function removePermissions(Centixx_Model_User $user, $permissionType)
	{
		$permission = array_pop($this->getPermissions($user, $permissionType));
		if ($permission->count == 1) {
			$this->getDbTable()->delete('permission_id = ' . $permission->id);
		} else {
			$adapter = $this->getDbTable()->getAdapter();
			return $adapter->query("UPDATE permissions
				SET permission_count = permission_count - 1
				WHERE permission_id = ?",
				$permission->id
			);
		}
	}

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->permission_id)
			->setFrom($row->permission_from)
			->setTo($row->permission_to)
			->setType($row->permission_type)
			->setDateStart($row->permission_starts)
			->setDateEnd($row->permission_ends)
			->setCount($row->permission_count)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{

		$data = array(
			'permission_from'		=> $this->_findId($model->from),
			'permission_to'			=> $this->_findId($model->to),
			'permission_type'		=> $model->type,
			'permission_ends'		=> $model->dateEnd,
			'permission_starts'		=> $model->dateStart,
			'permission_count'		=> $model->count,
		);


		$table = $this->getDbTable();
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);
			$table->update($data, $where);
		} else {

			//TODO zadbać o sprawdzanie pokrywających się okresów nadawanych zezwoleń
			$model->id = $table->insert($data);
		}

		return $this;
	}

	/**
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Permission
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Permission();
	}
}