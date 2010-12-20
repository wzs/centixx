<?php
class Application_Form_Project_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_Project
	 */
	protected $_project;

	/**
	 * @var array<Centixx_Model_User>
	 */
	protected $_availableUsers;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('text', 'name', array(
			'label'		=> 'Nazwa',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Nazwa jest wymagana'),
		));

		$dateStart = $this->createElement('text', 'dateStart', array(
			'label'		=> 'Data rozpoczęcia',
			'class'		=> 'datepicker',
			'title' => 'rrrr-mm-dd',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-mm-dd',
			'errorMessages'  => array('Wymagana jest poprawna data rozpoczęcia'),
		));
		$dateStart->addValidator('date', false, array('format' => 'yyyy-mm-dd'));
		$this->addElement($dateStart);

		$dateEnd = $this->createElement('text', 'dateEnd', array(
			'label'		=> 'Data zakończenia',
			'class'		=> 'datepicker',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-mm-dd',
			'errorMessages'  => array('Wymagana jest poprawna data zakończenia'),
		));
		$dateEnd->addValidator('date', false, array('format' => 'yyyy-mm-dd'));
		$this->addElement($dateEnd);

		if ($this->_project->id) {
			$this->addElement('select', 'manager', array(
				'label'	=> 'Kierownik',
				'multiOptions' => array('' => ' - ') + $this->_project->getUsers(),
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
	 * @return Application_Form_Project_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('project', $array)) {
			$this->_project = $array['project'];
		}

		if (array_key_exists('availableUsers', $array)) {
			$this->_availableUsers = $array['availableUsers'];
		}

		$this->rebuild();

		return $this;
	}
}