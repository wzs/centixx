<?php
class Centixx_Model_Mapper_Department extends Centixx_Model_Mapper_Abstract
{

	/**
	 * Zwraca dział zarządzany przez danego użytkownika
	 * @param Centixx_Model_User $manager
	 * @return Centixx_Model_Abstract
	 */
	public function findByManager(Centixx_Model_User $manager)
	{
		return $this->findByField($manager->id, 'department_manager');
	}

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->department_id)
			->setName($row->department_name)
			->setManager($row->department_manager)
		;
	}

	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'department_name'		=> $model->name,
			'department_manager'	=> $this->_findId($model->manager),
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
	 * @return Centixx_Model_Mapper_Department
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Department();
	}
}