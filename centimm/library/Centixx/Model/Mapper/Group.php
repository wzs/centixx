<?php
class Centixx_Model_Mapper_Group extends Centixx_Model_Mapper_Abstract
{
	/**
	 * Czy przy zapisie grupy ma być też zapisywany stan użytkowników znajdujących się w grupie
	 * Ze wzgledów wydajnościowych jest to domyślnie wyłaczone - zwlaszcza ze samo dodanie uzytkownika
	 * powoduje natychmiastowe zapisanie jego stanu
	 * @var bool
	 */
	protected $_saveUsers = false;

	/**
	 * Zwraca grupę, której przypisano danego użytkownika jako kierownika
	 * @param Centixx_Model_User|int $manager
	 * @return Centixx_Model_Group
	 */
	public function findByManager($manager)
	{
		return $this->findByField($this->_findId($manager), 'group_manager');
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Centixx/Model/Mapper/Centixx_Model_Mapper_Abstract::fillModel()
	 */
	protected function fillModel(Centixx_Model_Abstract $model, $row)
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
			//'group_project'		=> $this->_findId($model->project),
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
		if ($this->_saveUsers) {
			foreach ($model->users as $user) {
				$user->setGroup($model);
				$user->save();
			}
		}

		return $this;
	}

	/**
	 * Statyczna metoda fabrykująca
	 * @return Centixx_Model_Mapper_Group
	 */
	public static function factory()
	{
		return new Centixx_Model_Mapper_Group();
	}
}