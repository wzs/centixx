<?php
class Centixx_Model_Mapper_Project extends Centixx_Model_Mapper_Abstract
{
	public function getProjectUsers(Centixx_Model_Project $model)
	{
		$adapter = $this->getDbTable()->getAdapter();
		$q = $adapter
			->select()
			->from(array('u' => 'users'))
//			->join(array('g' => 'groups'), 'g.group_id = u.user_group', null)
//			->join(array('p' => 'projects'), 'p.project_id = g.group_project', null)
			->where('u.user_project = ?', $model->id)
		;
		$userMapper = Centixx_Model_Mapper_User::factory();
		return $userMapper->_fetchAll($q, null, $adapter);
	}

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->project_id)
			->setName($row->project_name)
			->setDateStart(new Zend_Date($row->project_start, Zend_Date::ISO_8601))
			->setDateEnd(new Zend_Date($row->project_stop, Zend_Date::ISO_8601))
			->setManager($row->project_manager)
			->setDepartment($row->project_department)
		;
	}
	
	public function __toString()
	{
		return '';
	}
	
	public function fetchForDate($date)
	{
		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
			->select()
			->from(array('p' => 'projects'))
			->where("project_start <= ?", $date)
			->where("? <= project_stop", $date)
		;
		//var_dump($query->__toString());

		return $this->_fetchAll($query, null, $adapter);
	}

	public function save(Centixx_Model_Abstract $model)
	{

		$data = array(
			'project_name'			=> $model->name,
			'project_manager'		=> $this->_findId($model->manager),
			'project_department'	=> $this->_findId($model->department),
			'project_start'			=> $model->dateStart,
			'project_stop'			=> $model->dateEnd,
		);


		$table = $this->getDbTable();
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);

			$table->update($data, $where);

			$this->_updateManager($model); //wazne aby wykonac to przed updatem
		} else {
			$model->id = $table->insert($data);
		}
		$this->_updateUsers($model);

		return $this;
	}

	/**
	 * Aktualizuje pole projektu w uzyt
	 * @param Centixx_Model_Project $model
	 */
	protected function _updateUsers(Centixx_Model_Project $model)
	{
		if (!count($model->users)) {
			return;
		}

		$a = array();
		foreach ($model->users as $user ) {
			$a[$user->id] = $user->id;
		}

		$adapter = $this->getDbTable()->getAdapter()->query("UPDATE `users` SET `user_project` = NULL WHERE `user_project` = ?",
			array($model->id));

		$in = join(',', $a);
		$adapter = $this->getDbTable()->getAdapter()->query("UPDATE `users` SET `user_project` = ? WHERE `user_id` IN ($in)",
			array($model->id));
	}

	/**
	 * Dokonuje wszelkich zmian zwiazanych ze zmiana kierownika projektu
	 * @param Centixx_Model_Group $model
	 */
	protected function _updateManager(Centixx_Model_Project $model)
	{

		$adapter = $this->getDbTable()->getAdapter();

		$oldManager = $model->getOldManager();
		if ($oldManager instanceof Centixx_Model_User) {
			$oldManager = $oldManager->id;
		}

		$newManager = $model->getManager();
		if ($newManager instanceof Centixx_Model_User) {
			$newManager = $newManager->id;
		}

		if ($oldManager) {
			//usuwam poprzedniego managera
			$adapter->query("DELETE FROM users_roles WHERE user_id = ? AND role_id = ?",
			array($oldManager, Centixx_Acl::ROLE_PROJECT_MANAGER));
		}

		if ($newManager) {
			//ustawiam nowego
			$adapter->query("INSERT INTO users_roles SET user_id = ?, role_id = ? ON DUPLICATE KEY UPDATE role_id = role_id",
			array($newManager, Centixx_Acl::ROLE_PROJECT_MANAGER));
		}
	}

	public function delete(Centixx_Model_Abstract $model)
	{
		//usuwam managera
		$this->getDbTable()->getAdapter()->query("
			UPDATE users
			SET user_role = DEFAULT
			WHERE user_project = ?",
			array($model->id));

		return $this->getDbTable()->delete($this->_getPrimaryKey() . ' = ' . $model->id);
	}

	/**
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Project
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Project();
	}
}