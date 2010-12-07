<?php
class Centixx_Model_Mapper_Permission extends Centixx_Model_Mapper_Abstract
{
	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->permission_id)
			->setFrom($row->permission_from)
			->setFor($row->permission_for)
			->setType($row->permission_type)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'project_from'		=> $this->_findId($model->from),
			'project_for'		=> $this->_findId($model->for),
			'project_type'		=> $model->type,
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
	 * @return Centixx_Model_Mapper_Permission
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Permission();
	}
}