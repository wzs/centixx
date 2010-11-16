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

		if (null === ($data['project_id'] = $model->id)) {
			unset($data['project_id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('project_id' => $data['project_id']));
		}
		return $this;
	}
}