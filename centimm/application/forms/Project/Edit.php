<?php
class Application_Form_Project_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_Project
	 */
	protected $_project;

	public function rebuild()
	{
//		ZendX_JQuery::enableForm($this);
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'project_id', array(
			'value' => $this->_project->id,
		));

		$this->addElement('text', 'name', array(
			'label'		=> 'Nazwa',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Nazwa jest wymagana'),
		));
		
		/* //jQuery datepicker
		$datePickerStart = new ZendX_JQuery_Form_Element_DatePicker('dateStart', array(
			'label'		=> 'Data rozpoczęcia',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-mm-dd',
			'validators' => array(
				'date',
			),
			'errorMessages'  => array('Wymagana jest poprawna data rozpoczęcia'),
		));
		$this->addElement($datePickerStart);

		$datePicker = new ZendX_JQuery_Form_Element_DatePicker('dateEnd', array(
			'label'		=> 'Data zakończenia',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-dd-dd',
			'validators' => array(
				'date',
			),
			'errorMessages'  => array('Wymagana jest poprawna data zakończenia'),
		));
		$this->addElement($datePicker);
		*/

		$this->addElement('text', 'dateStart', array(
			'label'		=> 'Data rozpoczęcia',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-dd-dd',
			'validators' => array(
				'date',
			),
			'errorMessages'  => array('Wymagana jest poprawna data rozpoczęcia'),
		));
		$this->addElement('text', 'dateEnd', array(
			'label'		=> 'Data zakończenia',
			'required'	=> true,
			'maxLength' => 11,
			'dateFormat' => 'yyyy-dd-dd',
			'validators' => array(
				'date',
			),
			'errorMessages'  => array('Wymagana jest poprawna data zakończenia'),
		));
		
		$users = $this->_project->getUsers();
		if (count($users)) {
			$this->addElement('radio', 'manager', array(
				'label'	=> 'Kierownik projektu',
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
	 * @return Application_Form_Project_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('project', $array)) {
			$this->_project = $array['project'];
		}

		$this->rebuild();
		return $this;
	}
}