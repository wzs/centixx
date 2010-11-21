<?php
class Application_Form_Group_AddUser extends Zend_Form
{
	protected $_users;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('select', 'new_user', array(
			'label'		=> 'Przypisz użytkownika do grupy',
			'required'	=> true,
			'multiOptions' => $this->_users,
		));

		$this->addelement('submit', 'submit', array(
			'label'		=> 'Dodaj',
			'ignore'	=> true,
		));
	}

	public function setValues($array) {
		if (array_key_exists('users', $array)) {
			$this->_users = $array['users'];
		}
		$this->rebuild();
		return $this;
	}

	public function hasUsers() {
		return count($this->_users) !== 0;
	}
}