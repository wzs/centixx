<?php
class Centixx_Model_Mapper_Project extends Centixx_Model_Mapper_Abstract
{

	protected function fillModel(Centixx_Model_Abstract $model, Zend_Db_Table_Row_Abstract $row)
	{
		$model
			->setId($row->project_id)
			->setName($row->project_name)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'project_name'		=> $model->name,
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