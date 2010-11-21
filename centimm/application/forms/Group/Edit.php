<?php
class Application_Form_Group_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_Group
	 */
	protected $_group;

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

		$users = $this->_group->getUsers();
		if (count($users)) {
			$this->addElement('radio', 'manager', array(
				'label'	=> 'Kierownik grupy',
				'multiOptions' => $users,
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

		$this->rebuild();
		return $this;
	}
}