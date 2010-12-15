<?php
class Application_Form_Permission_AddCeo extends Zend_Form
{
	/**
	 * @var array<Centixx_Model_User>
	 */
	protected $_users;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		if (count($this->_users)) {
			$this->addElement('select', 'to', array(
				'label'	=> 'Pracownik kadr',
				'multiOptions' => $this->_users,
				'required' => true,
				'errorMessages'  => array('Wybierz pracownika'),
			));
		}

		$dateStart = $this->createElement('text', 'dateStart', array(
			'label'		=> 'Ważne od:',
			'class'		=> 'datepicker',
			'title' => 'rrrr-mm-dd',
			'required'	=> true,
			'maxLength' => 11,
			'errorMessages'  => array('Wymagana jest poprawna data'),
		));
		$dateStart->addValidator('date', false, array('format' => 'yyyy-mm-dd'));
		$this->addElement($dateStart);

		$dateEnd = $this->createElement('text', 'dateEnd', array(
			'label'		=> 'Ważne od:',
			'class'		=> 'datepicker',
			'title' => 'rrrr-mm-dd',
			'required'	=> true,
			'maxLength' => 11,
			'errorMessages'  => array('Wymagana jest poprawna data'),
		));
		$dateEnd->addValidator('date', false, array('format' => 'yyyy-mm-dd'));
		$this->addElement($dateEnd);

		$this->addElement('submit', 'submit', array(
			'label'		=> 'Nadaj',
			'ignore'	=> true,
		));
	}

	/**
	 * Ustawia parametry formularza
	 * @param array $options
	 * @return Application_Form_Group_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('users', $array)) {
			$this->_users = $array['users'];
		}

		$this->rebuild();
		return $this;
	}

	public function isValid($data) {

		$dateValidator = new Centixx_Validate_CompareDates(array(
			'compareType' 	=> Centixx_Validate_CompareDates::COMPARE_TYPE_LATER,
			'comparedDate'  => $data['dateStart'],
		));

		$this->getElement('dateEnd')->addValidator($dateValidator);
		return parent::isValid($data);
	}
}