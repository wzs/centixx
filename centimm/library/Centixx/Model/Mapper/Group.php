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

	/**
	 * Zwraca listę grup nieprzypisanych do danego projektu
	 * @param Centixx_Model_Project $excludedProject
	 * @return array<Centixx_Model_Group>
	 */
	public function fetchFreeGroups(Centixx_Model_Project $excludedProject)
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
			$table->update($data, $where);
		} else {
			$model->id = $table->insert($data);
		}

		//zapisanie użytkowników przypisanych do grupy
		if ($this->_saveUsers) {
			foreach ($model->users as $user) {
				$user->setGroup($model);
				$user->save();
			}
		}

		$this->_updateManager($model);

		return $this;
	}

	/**
	 * Dokonuje wszelkich zmian zwiazanych ze zmiana kierownika grupy
	 * @param Centixx_Model_Group $model
	 */
	protected function _updateManager(Centixx_Model_Group $model)
	{
		$adapter = $this->getDbTable()->getAdapter();

		//usuwam poprzedniego managera
		$adapter->query("UPDATE `users` SET `user_role` = ? WHERE `user_group` = ? AND `user_role` = ?", 
			array(Centixx_Acl::ROLE_USER, $model->id, Centixx_Acl::ROLE_GROUP_MANAGER));
		
		//ustawiam nowego manadzera
		$adapter->query("UPDATE `users` SET `user_role` = ? WHERE `user_id` = ?", 
			array(Centixx_Acl::ROLE_GROUP_MANAGER, $this->_findId($model->manager)));
		
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