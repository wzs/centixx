<?php
class Centixx_Model_Mapper_Group extends Centixx_Model_Mapper_Abstract
{
	/**
	 * Czy przy zapisie grupy ma być też zapisywany stan użytkowników znajdujących się w grupie
	 * Ze wzgledów wydajnościowych jest to domyślnie wyłaczone - zwlaszcza ze samo dodanie uzytkownika
	 * powoduje natychmiastowe zapisanie jego stanu
	 * @var bool
	 */
	protected $_saveUsers = false;


	public function getGroupUsers(Centixx_Model_Group $model)
	{
		$adapter = $this->getDbTable()->getAdapter();
		$q = $adapter
			->select()
			->from(array('u' => 'users'))
			->where('u.user_group = ?', $model->id)
		;
		$userMapper = Centixx_Model_Mapper_User::factory();
		return $userMapper->_fetchAll($q, null, $adapter);
	}

	/**
	 * Zwraca listę grup nieprzypisanych do danego projektu
	 * @param Centixx_Model_Project $excludedProject
	 * @return array<Centixx_Model_Group>
	 */
	public function fetchFreeGroups(Centixx_Model_Project $excludedProject)
	{
		/*
		 * uzywana jest taka konstrukcja, bo  chce wykonac proste
		 * zapytanie sql z joinami bez posredniego udzialu Zend_Db_Table
		 */

		$adapter = $this->getDbTable()->getAdapter();
		$query = $adapter
		->select()
		->from(array('g' => 'groups'))
		->where('group_project != ? OR group_project IS NULL', $excludedProject->id)
		->order('group_name')
		;
		return $this->_fetchAll($query, null, $adapter);
	}

	/**
	 * Zwraca grupę, której przypisano danego użytkownika jako kierownika
	 * @param Centixx_Model_User|int $manager
	 * @return Centixx_Model_Group
	 */
	public function findByManager($manager)
	{
		return $this->findByField($this->_findId($manager), 'group_manager');
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
		->setId($row->group_id)
		->setName($row->group_name)
		->setProject($row->group_project)
		->setManager($row->group_manager)
		;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::save()
	 */
	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'group_name'		=> $model->name,
			'group_project'		=> $this->_findId($model->project),
			'group_manager'		=> $this->_findId($model->manager),
		);

		$table = $this->getDbTable();
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);
			try {
				$table->update($data, $where);
			} catch (Exception $e) {
				debug($e);
			}
		} else {
			$model->id = $table->insert($data);
		}

		$this->_updateUsers($model);
		$this->_updateManager($model);

		return $this;
	}

	/**
	 * Aktualizuje pole projektu w uzyt
	 * @param Centixx_Model_Group $model
	 */
	protected function _updateUsers(Centixx_Model_Group $model)
	{
		if (!count($model->users)) {
			return;
		}

		$a = array();
		foreach ($model->users as $user ) {
			$a[$user->id] = $user->id;
		}

		$adapter = $this->getDbTable()->getAdapter()->query("UPDATE `users` SET `user_group` = NULL WHERE `user_group` = ?",
		array($model->id));

		$in = join(',', $a);
		$adapter = $this->getDbTable()->getAdapter()->query("UPDATE `users` SET `user_group` = ? WHERE `user_id` IN ($in)",
		array($model->id));
	}

	/**
	 * Dokonuje wszelkich zmian zwiazanych ze zmiana kierownika grupy
	 * @param Centixx_Model_Group $model
	 */
	public function _updateManager(Centixx_Model_Group $model)
	{
		$adapter = $this->getDbTable()->getAdapter();

		if ($model->getOldManager()) {
			//usuwam poprzedniego managera
			$adapter->query("DELETE FROM users_roles WHERE user_id = ? AND role_id = ?",
			array($model->getOldManager()->getId(), Centixx_Acl::ROLE_GROUP_MANAGER));
		}

		if ($model->getManager()) {
			//ustawiam nowego
			$adapter->query("INSERT INTO users_roles SET user_id = ?, role_id = ? ON DUPLICATE KEY UPDATE role_id = role_id",
			array($model->getManager()->getId(), Centixx_Acl::ROLE_GROUP_MANAGER));
		}
	}

	public function delete(Centixx_Model_Abstract $model)
	{
		//usuwam managera
		$this->getDbTable()->getAdapter()->query("
			UPDATE users
			SET user_role = DEFAULT
			WHERE user_group = ?",
			array($model->id));

		return $this->getDbTable()->delete($this->_getPrimaryKey() . ' = ' . $model->id);
	}

	/**
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Group
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Group();
	}
}