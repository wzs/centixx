<?php
class Application_Form_Group_AddUser extends Zend_Form
{
	protected $_users;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'action', array(
			'value' => 'aa',
		));

		$this->addElement('select', 'new_user', array(
			'label'		=> 'Dodaj użytkownika do grupy',
			'required'	=> true,
			'multiOptions' => $this->_users,
		));

		$this->addelement('submit', 'submit', array(
			'label'		=> 'Dodaj',
			'ignore'	=> true,
		));
	}

	public function setUsers($users) {
		$this->_users = $users;
		$this->rebuild();
		return $this;
	}
}