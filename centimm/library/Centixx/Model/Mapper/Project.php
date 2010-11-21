<?php
class Centixx_Model_Mapper_Project extends Centixx_Model_Mapper_Abstract
{
	public function getProjectUsers(Centixx_Model_Project $model) 
	{
		$adapter = $this->getDbTable()->getAdapter();
		$q = $adapter
			->select()
			->from(array('u' => 'users'))
			->join(array('g' => 'groups'), 'g.group_id = u.user_group', null)
			->join(array('p' => 'projects'), 'p.project_id = g.group_project', null)
			->where('p.project_id = ?', $model->id)
		;
		$userMapper = Centixx_Model_Mapper_User::factory();
		return $userMapper->_fetchAll($q, null, $adapter);
	}
	
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->project_id)
			->setName($row->project_name)
			->setDateStart($row->project_start)
			->setDateEnd($row->project_stop)
			->setManager($row->project_manager)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'project_name'		=> $model->name,
			'project_manager'	=> $this->_findId($model->manager),
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

	/**
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Project
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Project();
	}
}