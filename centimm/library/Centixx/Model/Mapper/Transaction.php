<?php
class Centixx_Model_Mapper_Transaction extends Centixx_Model_Mapper_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::save()
	 */
	public function save(Centixx_Model_Abstract $model)
	{
		$data = array(
			'transaction_id'		=> $model->id,
			'transaction_account'	=> $model->account,
			'transaction_value'		=> $model->value,
			'transaction_title'		=> $model->title,
			'transaction_date'		=> $model->date,
			'transaction_user'		=> $model->user,
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

	protected function fillModel(Centixx_Model_Abstract $model, $row)
	{
		$model
			->setId($row->transaction_id)
			->setAccount($row->transaction_account)
			->setValue($row->transaction_value)
			->setTitle($row->transaction_title)
			->setDate($row->transaction_date)
			->setUser($row->transaction_user)
		;
	}
	/**
	 * @return Centixx_Model_Mapper_User
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Transaction();
	}
	
	
}