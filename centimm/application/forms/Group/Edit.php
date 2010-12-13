<?php
class Application_Form_Group_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_Group
	 */
	protected $_group;

	/**
	 * @var array<Centixx_Model_User>
	 */
	protected $_availableUsers;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'group_id', array(
			'value' => $this->_group->id,
		));

		$this->addElement('text', 'name', array(
			'label'		=> 'Nazwa',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Nazwa jest wymagana'),
		));

		if ($this->_group->id) {
			$this->addElement('select', 'manager', array(
				'label'	=> 'Kierownik:',
				'multiOptions' => array('' => ' - ') + $this->_group->users,
			));
		}

		if (count($this->_availableUsers)) {
			$this->addElement('multiCheckbox', 'users', array(
				'label'	=> 'Użytkownicy',
				'multiOptions' => $this->_availableUsers,
				'required' => true,
			));
		}

		$this->addElement('submit', 'submit', array(
			'label'		=> 'Zapisz',
			'ignore'	=> true,
		));
	}

	/**
	 * Ustawia parametry formularza
	 * @param array $options
	 * @return Application_Form_Group_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('group', $array)) {
			$this->_group = $array['group'];
		}

		if (array_key_exists('availableUsers', $array)) {
			$this->_availableUsers = $array['availableUsers'];
		}

		$this->rebuild();
		return $this;
	}
}