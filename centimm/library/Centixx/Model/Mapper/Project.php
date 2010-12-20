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
			$this->_updateManager($model); //wazne aby wykonac to przed updatem
			$table->update($data, $where);
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
		//TODO trzeba napisac jakas funkcje w sql'u ktora automatycznie poustawia odpowiednie prawa dostepu przy zmianach
		//tzn. dla wszystkich projektow znajdzie kierownikow i ustawi im user_role = 4 i analogicznie dla kierownikow grup

		$adapter = $this->getDbTable()->getAdapter();

		//usuwam poprzedniego managera
		$adapter->query("
UPDATE projects p
JOIN users u ON u.user_id = p.project_manager
SET u.user_role = ?
WHERE p.project_id = ?",
			array(Centixx_Acl::ROLE_USER, $model->id));

		//ustawiam nowego manadzera
		$adapter->query("
UPDATE users u
SET u.user_role = ?
WHERE u.user_id = ?",
			array(Centixx_Acl::ROLE_PROJECT_MANAGER, $this->_findId($model->manager)));

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