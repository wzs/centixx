<?php
class Centixx_Model_Mapper_Timesheet extends Centixx_Model_Mapper_Abstract
{

		/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->user_id)
			->setUser($row->user_email)
			->setProject($row->user_name)
			->setHours($row->user_surname)
			->setDate($row->user_role)
			->setDescr($row->user_group)
			->setAccepted($row->user_project)
		;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::save()
	 */
	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'timesheet_user'		=> $this->_findId($model->user),
			'timesheet_project'		=> $this->_findId($model->project),
			'timesheet_hours'		=> $model->hours,
			'timesheet_date'		=> $model->date,
			'timesheet_descr'		=> $model->descr,
			'timesheet_accepted'	=> $model->descr,
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
	 * @return Centixx_Model_Mapper_User
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Timesheet();
	}

}
