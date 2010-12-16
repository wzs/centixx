<?php
class Application_Form_User_Edit extends Zend_Form
{
	/**
	 * @var Centixx_Model_User
	 */
	protected $_user;

	protected $_roles;

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('hidden', 'user_id', array(
			'value' => $this->_user->id,
		));

		$this->addElement('text', 'name', array(
			'label'		=> 'Imię',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Imię jest wymagane'),
		));

		$this->addElement('text', 'surname', array(
			'label'		=> 'Nazwisko',
			'required'	=> true,
			'maxLength' => 64,
			'errorMessages'  => array('Nazwisko jest wymagane'),
		));

		$dbValidatorOptions = array(
			'table' => 'users',
			'field' => 'user_email',
		);
		if ($this->_user) {
			$dbValidatorOptions['exclude'] = array(
				'field' => 'user_id',
				'value' => $this->_user->id
			);
		}

		$this->addElement('text', 'email', array(
			'label'		=> 'E-mail',
			'required'	=> true,
			'validators'=> array(
				new Zend_Validate_EmailAddress(),
				new Zend_Validate_Db_NoRecordExists($dbValidatorOptions),
			),
		));

		$this->addElement('text', 'hourRate', array(
			'label' => 'Stawka',
			'validators' => array(
				new Zend_Validate_Float(),
			),
			'required' => true,
			'errorMessages'  => array('Wprowadź prawidłową stawkę'),
		));

		$this->addElement('text', 'account', array(
			'label' => 'Nr konta',
          	'required' => true,
			'errorMessages'  => array('Wprowadź prawidłowy nr konta'),
		));

		if ($this->_roles) {
			$this->addElement('select', 'role', array(
				'label' => 'Stanowisko',
				'multiOptions' => $this->_roles,
			));
		}

		$this->addElement('submit', 'submit', array(
			'label'		=> 'Zapisz',
			'ignore'	=> true,
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Zend_Form::isValid()
	 */
	public function isValid($data)
	{
		//nadpisałem, bo nie mogelm zmusic Zend_Validate_Identical do dzialania
//		$this->getElement('password_retype')
//			->addValidator(new Zend_Validate_Identical($data['password']));


		return parent::isValid($data);
	}

	/**
	 * Ustawia parametry formularza
	 * @param array $options
	 * @return Application_Form_User_Edit fluent interface
	 */
	public function setValues($array) {
		if (array_key_exists('user', $array)) {
			$this->_user = $array['user'];
		}

		if (array_key_exists('roles', $array)) {
			$this->_roles = $array['roles'];
		}

		$this->rebuild();
		return $this;
	}
}