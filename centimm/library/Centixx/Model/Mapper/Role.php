<?php
class Centixx_Model_Mapper_Role extends Centixx_Model_Mapper_Abstract
{

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->role_id)
			->setName($row->role_name)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'role_name'		=> $model->name,
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
	 * @return Centixx_Model_Mapper_Role
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Role();
	}
}