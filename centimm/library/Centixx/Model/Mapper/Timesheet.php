<?php
class Centixx_Model_Mapper_Timesheet extends Centixx_Model_Mapper_Abstract
{

	public function findByUserDate($user, $date)
	{
		$table = $this->getDbTable();
		$query = $table->select()->where('timesheet_user = ?', $user)->where('timesheet_date = ?', $date);
		$result = $table->fetchAll($query);
		if (0 == count($result)) {
			return null;
		}
		return $this->_getNewModelInstance($result->current());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->timesheet_id)
			->setUser($row->timesheet_user)
			->setProject($row->timesheet_project)
			->setHours($row->timesheet_hours)
			->setDate($row->timesheet_date)
			->setDescr($row->timesheet_descr)
			->setAccepted($row->timesheet_accepted)
		;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::save()
	 */
	public function save(Centixx_Model_Abstract $model)
	{
		
		//var_dump($model->user, $model->project);
		//exit();
		//$user_id = $this->_findId($model->user);
		//var_dump($user_id);
		//$project_id = $this->_findId($model->project);
		//var_dump($project_id);
		
		$data = array(
			'timesheet_user'		=> $this->_findId($model->user),
			'timesheet_project'		=> $this->_findId($model->project),
			'timesheet_hours'		=> $model->hours,
			'timesheet_date'		=> $model->date,
			'timesheet_descr'		=> $model->descr,
			'timesheet_accepted'	=> $model->accepted,
		);
		
		//var_dump($data);
		
		//var_dump($model->id);
		//exit();

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
	 * @return Centixx_Model_Mapper_User
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Timesheet();
	}
}
