<?php
class Centixx_Model_Mapper_Group extends Centixx_Model_Mapper_Abstract
{
	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, Zend_Db_Table_Row_Abstract $row)
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

		$table = $this->_dbTable;
		if ($model->id) {
			$pk = $this->_getPrimaryKey();
			$where = $table->getAdapter()->quoteInto($pk . ' = ?', $model->id);
			$table->update($data, $where);
		} else {
			$table->insert($data);
		}

		//zapisanie użytkowników przypisanych do grupy
		$userMapper = new Centixx_Model_Mapper_User();
		foreach ($model->users as $user) {
			$user->setGroup($model);
			$userMapper->save($user);
		}

		return $this;
	}
}